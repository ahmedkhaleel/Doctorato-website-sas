<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\CustomerLoginToken;
use App\Models\DemoRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Watches the customer portal for the three abuse shapes we've seen
 * in production-like data:
 *
 *   1. Token interception
 *      Magic link consumed from a different IP than the one that
 *      requested it. Real customers do switch networks (request on
 *      mobile data, click on home wifi) so we use a loose check:
 *      different /16 prefix flagged, identical prefix silently
 *      allowed. The flag is RECORD-ONLY — we don't block, because
 *      the false-positive rate is too high for an auth-blocking
 *      decision. The admin sees it in the activity log.
 *
 *   2. Session ID hijack
 *      Same portal session being used from two IPs within 5 minutes
 *      of each other. This is a high-confidence signal of a stolen
 *      session cookie. We invalidate the session and force re-auth.
 *
 *   3. Resource enumeration
 *      A single customer hitting more than N invoice URLs (or any
 *      authenticated read endpoint) in a 5-minute window. Legit
 *      customers click a couple of invoices; an attacker with the
 *      session cookie often dumps everything. Rate-limited via a
 *      cache counter, not the DB, to keep the hot path cheap.
 *
 * Detector outputs:
 *   - activity_logs row with action='portal.abuse_signal'
 *   - admin email via AdminExceptionNotifier (signature-throttled
 *     so a flood doesn't fill the inbox)
 *   - For (2) only, returns false from check() so the middleware
 *     can refuse the request.
 */
class PortalAbuseDetector
{
    public const SIGNAL_TOKEN_IP_MISMATCH = 'token_ip_mismatch';
    public const SIGNAL_SESSION_HIJACK    = 'session_hijack';
    public const SIGNAL_ENUMERATION       = 'enumeration';

    /** Per-customer URL hits in a window before we treat it as scraping. */
    public const ENUMERATION_THRESHOLD = 30;
    public const ENUMERATION_WINDOW_MIN = 5;

    /**
     * Record token consumption + flag mismatch. Called from
     * CustomerPortalController::authenticate() right after consume().
     * Returns the signal name when one fired, null otherwise.
     */
    public function onTokenConsumed(CustomerLoginToken $token, string $consumedIp, ?string $userAgent): ?string
    {
        // Persist the consumed-at IP for forensics regardless of mismatch.
        $token->forceFill([
            'consumed_ip' => $consumedIp,
            'consumed_ua' => $userAgent ? substr($userAgent, 0, 255) : null,
        ])->save();

        if (!$token->ip_address || $token->ip_address === $consumedIp) {
            return null;
        }
        if ($this->samePrefix($token->ip_address, $consumedIp)) {
            return null;
        }

        $this->emit(self::SIGNAL_TOKEN_IP_MISMATCH, [
            'token_id' => $token->id,
            'email' => $token->email,
            'requested_ip' => $token->ip_address,
            'consumed_ip' => $consumedIp,
        ]);

        return self::SIGNAL_TOKEN_IP_MISMATCH;
    }

    /**
     * Per-request check called from CustomerAuth middleware.
     * Returns false when the session should be killed.
     */
    public function checkSession(string $sessionId, int $customerId, string $currentIp): bool
    {
        // Cache key holds the FIRST IP seen for this session.
        $key = "portal.session_ip.{$sessionId}";
        $firstIp = Cache::get($key);

        if ($firstIp === null) {
            // First request on this session — record + allow.
            Cache::put($key, $currentIp, now()->addHours(2));
            return true;
        }

        if ($firstIp === $currentIp || $this->samePrefix($firstIp, $currentIp)) {
            return true;
        }

        // IP rotated mid-session and the /16 doesn't match. Treat
        // as hijack.
        $this->emit(self::SIGNAL_SESSION_HIJACK, [
            'customer_id' => $customerId,
            'original_ip' => $firstIp,
            'rotated_to' => $currentIp,
        ]);

        return false;
    }

    /**
     * Per-request enumeration counter. Increments a 5-minute cache
     * window per customer; returns false once the threshold is hit.
     * Cheap (one cache::increment) so safe on the hot path.
     */
    public function checkEnumeration(int $customerId): bool
    {
        $key = "portal.enum.{$customerId}";
        $count = Cache::get($key, 0);

        if ($count === 0) {
            // First hit in this window — start the counter.
            Cache::put($key, 1, now()->addMinutes(self::ENUMERATION_WINDOW_MIN));
            return true;
        }

        Cache::put($key, $count + 1, now()->addMinutes(self::ENUMERATION_WINDOW_MIN));

        if ($count + 1 > self::ENUMERATION_THRESHOLD) {
            // Only emit on the FIRST breach in the window to avoid
            // flooding the activity log with one row per request.
            if ($count + 1 === self::ENUMERATION_THRESHOLD + 1) {
                $this->emit(self::SIGNAL_ENUMERATION, [
                    'customer_id' => $customerId,
                    'count' => $count + 1,
                    'window_min' => self::ENUMERATION_WINDOW_MIN,
                ]);
            }
            return false;
        }

        return true;
    }

    /**
     * Compare two IPs by their /16 network prefix for v4 (first two
     * octets) or /48 for v6. Coarse enough to absorb a customer
     * switching wifi → mobile (different /32) but tight enough that
     * an attacker on a different ISP gets flagged.
     */
    protected function samePrefix(string $a, string $b): bool
    {
        if ($a === $b) return true;

        // IPv4 — compare first two octets.
        if (filter_var($a, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($b, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $aParts = explode('.', $a);
            $bParts = explode('.', $b);
            return $aParts[0] === $bParts[0] && $aParts[1] === $bParts[1];
        }

        // IPv6 — compare the first three hextets (/48). Cloudflare
        // hands out a /48 per ISP edge.
        if (filter_var($a, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) && filter_var($b, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $aExp = $this->expandV6($a);
            $bExp = $this->expandV6($b);
            return substr($aExp, 0, 14) === substr($bExp, 0, 14);
        }

        return false;
    }

    protected function expandV6(string $ip): string
    {
        $bin = inet_pton($ip);
        if ($bin === false) return $ip;
        $hex = bin2hex($bin);
        return implode(':', str_split($hex, 4));
    }

    /** Write to activity log + email admin (throttled). */
    protected function emit(string $signal, array $context): void
    {
        ActivityLog::create([
            'user_id' => null,
            'action' => 'portal.abuse_signal',
            'description' => "Portal abuse signal: {$signal} — " . json_encode($context, JSON_UNESCAPED_UNICODE),
        ]);

        Log::warning('portal.abuse_signal', array_merge(['signal' => $signal], $context));

        // Best-effort admin notifier — silent on failure so a
        // misconfigured SMTP doesn't break portal traffic.
        try {
            app(\App\Exceptions\AdminExceptionNotifier::class)
                ->notifySignal("portal_abuse_{$signal}", $context);
        } catch (\Throwable $e) {
            // swallow — we already logged above
        }
    }
}
