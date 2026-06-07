<?php

namespace App\Services;

/**
 * Form bot-filter service.
 *
 * reCAPTCHA was removed in June 2026 — the production cPanel host has
 * neither cURL nor allow_url_fopen, so the server could never verify
 * a token with Google. The widget was just a CSP/cookie liability for
 * zero security benefit. Class is kept (and the method name preserved)
 * so callers don't need to change.
 *
 * Two defensive layers, both fully self-contained:
 *  - Honeypot: a hidden input named 'hp_trap' that real users never
 *    fill. Bots that auto-complete visible fields do.
 *  - Minimum time-to-submit: a form posted in under 1.5 seconds is
 *    almost certainly a script.
 */
class RecaptchaService
{
    protected const MIN_FORM_SECONDS = 1.5;

    /**
     * Always false — reCAPTCHA is disabled site-wide. Kept so the
     * HandleInertiaRequests middleware can still call it and get a
     * clean negative without throwing.
     */
    public function isEnabled(): bool
    {
        return false;
    }

    public function siteKey(): ?string
    {
        return null;
    }

    public function secretKey(): ?string
    {
        return null;
    }

    /**
     * Verify a submitted form against honeypot + timing only.
     * Returns ['ok' => bool, 'reason' => string|null].
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

        return ['ok' => true, 'reason' => null];
    }
}
