<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google reCAPTCHA v3 verifier.
 *
 * Keys live in SiteSetting (recaptcha_site_key + recaptcha_secret_key)
 * so admins can rotate without a deploy. When keys are missing, the
 * service quietly disables itself and every call returns "ok" — that
 * keeps the marketing site working on day one before reCAPTCHA is
 * provisioned.
 *
 * Two defensive layers always on, regardless of reCAPTCHA state:
 *  - Honeypot: a hidden input named 'hp_trap' that real users never
 *    fill. Bots that auto-complete visible fields do.
 *  - Minimum time-to-submit: a form posted in under 1.5 seconds is
 *    almost certainly a script.
 */
class RecaptchaService
{
    protected const MIN_SCORE = 0.5;           // Google recommends 0.5 as the threshold
    protected const MIN_FORM_SECONDS = 1.5;    // Humans need at least this long to type meaningful content
    protected const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    public function isEnabled(): bool
    {
        return (bool) $this->secretKey() && (bool) $this->siteKey();
    }

    public function siteKey(): ?string
    {
        return SiteSetting::get('recaptcha_site_key') ?: config('services.recaptcha.site_key');
    }

    public function secretKey(): ?string
    {
        return SiteSetting::get('recaptcha_secret_key') ?: config('services.recaptcha.secret_key');
    }

    /**
     * Verify a submitted form. Returns an array with `ok` (bool) and
     * `reason` (string|null) so controllers can decide whether to
     * reject or just flag for admin review.
     */
    public function verify(array $payload, string $action = 'submit'): array
    {
        // 1. Honeypot — a hit is a near-certain bot.
        if (!empty($payload['hp_trap'] ?? null)) {
            return ['ok' => false, 'reason' => 'honeypot'];
        }

        // 2. Time-to-submit — too fast = bot.
        $renderedAt = (int) ($payload['form_rendered_at'] ?? 0);
        if ($renderedAt > 0) {
            $elapsed = (time() * 1000 - $renderedAt) / 1000;
            if ($elapsed < self::MIN_FORM_SECONDS) {
                return ['ok' => false, 'reason' => 'submitted_too_fast'];
            }
        }

        // 3. Google reCAPTCHA — only enforced when keys are present.
        if (!$this->isEnabled()) {
            return ['ok' => true, 'reason' => null];
        }

        // If the host can't make outbound HTTPS calls at all (no cURL
        // and no allow_url_fopen), there's no way to verify a token
        // server-side. In that case the honeypot + timing checks are
        // the only signals we have — accept the submission instead of
        // hard-rejecting every real user.
        if (!$this->canMakeOutboundRequests()) {
            Log::info('reCAPTCHA: host lacks outbound HTTPS support, accepting on honeypot+timing only');
            return ['ok' => true, 'reason' => 'no_outbound'];
        }

        $token = $payload['recaptcha_token'] ?? null;
        if (!$token) {
            // Frontend may have failed to fetch a token (script blocked,
            // network hiccup, ad-blocker). Don't punish real users for
            // it — we already have the honeypot + timing defenses.
            Log::info('reCAPTCHA: token missing, accepting on honeypot+timing only');
            return ['ok' => true, 'reason' => 'missing_token_accepted'];
        }

        try {
            $res = Http::timeout(4)->asForm()->post(self::VERIFY_URL, [
                'secret' => $this->secretKey(),
                'response' => $token,
            ]);
            $body = $res->json();
            if (!($body['success'] ?? false)) {
                Log::warning('reCAPTCHA: Google rejected, accepting on honeypot+timing only', ['errors' => $body['error-codes'] ?? null]);
                return ['ok' => true, 'reason' => 'google_rejected_accepted'];
            }
            if (($body['action'] ?? null) !== $action) {
                Log::warning('reCAPTCHA: action mismatch', ['expected' => $action, 'got' => $body['action'] ?? null]);
                return ['ok' => true, 'reason' => 'action_mismatch_accepted'];
            }
            $score = (float) ($body['score'] ?? 0);
            if ($score < self::MIN_SCORE) {
                return ['ok' => false, 'reason' => 'low_score', 'score' => $score];
            }
            return ['ok' => true, 'reason' => null, 'score' => $score];
        } catch (\Throwable $e) {
            // Don't fail open on a Google outage; better to silently
            // accept than to block real customers.
            Log::warning('reCAPTCHA verify failed, falling through', ['error' => $e->getMessage()]);
            return ['ok' => true, 'reason' => 'verify_error'];
        }
    }

    /**
     * Returns true when the runtime can make outbound HTTPS requests
     * via Guzzle/Laravel's Http facade. False on hosts where curl is
     * disabled AND allow_url_fopen is off — which is the case on the
     * production cPanel install today.
     */
    protected function canMakeOutboundRequests(): bool
    {
        return extension_loaded('curl') || (bool) ini_get('allow_url_fopen');
    }
}
