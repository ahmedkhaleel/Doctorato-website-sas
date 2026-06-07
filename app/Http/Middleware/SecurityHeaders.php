<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Adds a tight set of security response headers to every web request.
 *
 *   - Strict-Transport-Security: tells browsers to refuse plain HTTP
 *     for 1 year. Safe because Cloudflare is already serving 443-only.
 *     `preload` is NOT set because we want to be able to walk this
 *     back if the cert ever expires; preload is permanent.
 *
 *   - X-Frame-Options: SAMEORIGIN blocks click-jacking from third-
 *     party domains. The admin panel uses a few iframes for invoice
 *     printing but they're same-origin.
 *
 *   - X-Content-Type-Options: nosniff prevents browsers from second-
 *     guessing our Content-Type headers, which is the standard
 *     defence against MIME-confusion XSS.
 *
 *   - Referrer-Policy: strict-origin-when-cross-origin sends the
 *     origin (but not the path) on cross-origin nav — enough for
 *     analytics to attribute visits without leaking page URLs.
 *
 *   - Permissions-Policy: blank-list of permissions the SaaS doesn't
 *     need (camera, mic, geolocation, payment, USB...). This makes a
 *     rogue script in our domain — or a third-party widget — unable
 *     to silently request access.
 *
 *   - Content-Security-Policy: scoped to scripts/styles we actually
 *     load. 'unsafe-inline' is unfortunately required by Vite's
 *     production bundle and Inertia's payload — tightening that
 *     further would require nonces, which would mean rewriting the
 *     Inertia + Vite integration. Listed below in order: script,
 *     style, img (incl. data: for embedded fonts), font, connect,
 *     frame-ancestors, base-uri, form-action.
 *
 * Header set ONLY on web requests — webhooks (Paymob, GitHub) and
 * the /healthz JSON endpoints don't need them and CSP can interfere
 * with legitimate POST bodies on the webhook endpoints.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip for webhook + healthcheck endpoints where these
        // headers serve no purpose and CSP can interfere.
        $path = $request->path();
        if (str_starts_with($path, 'webhooks/') || str_starts_with($path, 'healthz')) {
            return $response;
        }

        $headers = [
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => implode(', ', [
                'camera=()',
                'microphone=()',
                'geolocation=()',
                'payment=()',
                'usb=()',
                'magnetometer=()',
                'gyroscope=()',
                'accelerometer=()',
                'autoplay=()',
                'fullscreen=(self)',
            ]),
            // X-XSS-Protection is deprecated by all modern browsers
            // but setting it to '0' explicitly disables a buggy
            // legacy filter that can introduce XSS in old IE/Edge.
            'X-XSS-Protection' => '0',
        ];

        // CSP — only on text/html responses. JSON/XML/CSV would
        // refuse to render under a CSP header anyway, but setting
        // it would add noise to monitoring.
        $contentType = (string) $response->headers->get('Content-Type', '');
        if (str_contains($contentType, 'text/html')) {
            $nonce = (string) $request->attributes->get('csp_nonce', '');
            $headers['Content-Security-Policy'] = $this->buildCsp($nonce);
        }

        foreach ($headers as $key => $value) {
            if (!$response->headers->has($key)) {
                $response->headers->set($key, $value);
            }
        }

        return $response;
    }

    /**
     * Build the CSP value. Kept in its own method so future changes
     * (adding a new analytics provider, swapping the CDN) are a
     * single-source edit.
     */
    protected function buildCsp(string $nonce = ''): string
    {
        // Nonce gates the inline scripts WE control (SW registration,
        // JSON-LD, analytics bootstrap). 'unsafe-inline' stays for
        // Vite + Inertia auto-injected blobs we can't easily annotate
        // — but per CSP3, nonce takes precedence on browsers that
        // understand both, so our controlled scripts get hash-style
        // validation either way.
        $nonceSrc = $nonce !== '' ? "'nonce-{$nonce}' " : '';

        $directives = [
            "default-src 'self'",
            // Service worker registration + manifest are same-origin
            // by definition; the worker-src directive is required
            // explicitly because Chrome treats SW as a separate
            // resource class from script-src.
            "worker-src 'self'",
            "manifest-src 'self'",
            // Vite injects inline modulepreload, reCAPTCHA + Google
            // Tag Manager pull from www.google.com / www.gstatic.com.
            "script-src 'self' {$nonceSrc}'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://www.googletagmanager.com https://www.google-analytics.com",
            // Tailwind generates inline styles for arbitrary values.
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            // Cloudflare/Google fonts use data: URIs for some weights.
            "img-src 'self' data: https:",
            "font-src 'self' data: https://fonts.gstatic.com",
            // XHR/fetch — Inertia is same-origin; analytics + reCAPTCHA
            // hit Google domains. reCAPTCHA v3 specifically posts to
            // www.google.com/recaptcha/api2/* and pulls a token from
            // www.gstatic.com — both must be in connect-src or the
            // demo + contact forms silently fail to submit.
            "connect-src 'self' https://www.google.com https://www.gstatic.com https://www.google-analytics.com https://www.googletagmanager.com https://recaptcha.net",
            // reCAPTCHA iframe + Paymob redirect.
            "frame-src 'self' https://www.google.com https://accept.paymob.com",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self' https://accept.paymob.com",
            // Block plugins (Flash etc) entirely.
            "object-src 'none'",
        ];

        return implode('; ', $directives);
    }
}
