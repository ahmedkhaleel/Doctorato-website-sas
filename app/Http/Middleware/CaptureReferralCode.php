<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * Captures ?ref=CODE on any public page and stores it in a 30-day
 * cookie. The /demo form reads the cookie back into a hidden field
 * so attribution survives the visitor browsing a few pages between
 * clicking the share link and actually filling out the form.
 *
 * Rules:
 *   - First-touch wins. Once a cookie exists we don't overwrite it,
 *     so a customer sharing the link in two channels can't game the
 *     attribution by visiting via channel A and then channel B.
 *   - Format-validated server-side: must match DOC-[A-Z2-9]{8}.
 *     Anything else is silently dropped to prevent setting the
 *     cookie via a malicious ?ref= payload.
 *   - HTTP-only + SameSite=Lax cookie, signed/encrypted via Laravel's
 *     EncryptCookies (it's a regular Cookie::queue, so it goes
 *     through the framework's encryption layer automatically).
 *   - Not active on admin/portal/API endpoints (registered only on
 *     the public web group).
 */
class CaptureReferralCode
{
    public const COOKIE_NAME = 'doc_ref';
    public const LIFETIME_MINUTES = 60 * 24 * 30; // 30 days
    public const CODE_PATTERN = '/^DOC-[A-Z2-9]{8}$/';

    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref');
        if ($code && !$request->cookies->has(self::COOKIE_NAME)) {
            $code = strtoupper(trim((string) $code));
            if (preg_match(self::CODE_PATTERN, $code)) {
                Cookie::queue(
                    self::COOKIE_NAME,
                    $code,
                    self::LIFETIME_MINUTES,
                    '/',
                    null,
                    null,   // secure → auto from config
                    true,   // httpOnly
                    false,
                    'lax',
                );
            }
        }

        return $next($request);
    }
}
