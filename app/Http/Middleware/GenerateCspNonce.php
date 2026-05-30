<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Generates a per-request CSP nonce, shares it with the view layer,
 * and stashes it on the request so SecurityHeaders can inject it
 * into the Content-Security-Policy header.
 *
 * Why we use a nonce alongside 'unsafe-inline':
 *   - Inline scripts WE control (SW registration, the JSON-LD block,
 *     analytics bootstrap) get a nonce attribute and become trusted.
 *   - Vite's auto-injected modulepreload + Inertia's data-page JSON
 *     blob can NOT easily accept a server-set nonce without
 *     non-trivial Vite plugin work. Those still rely on
 *     'unsafe-inline'.
 *   - Per the CSP3 spec, a nonce takes precedence over
 *     'unsafe-inline' for browsers that understand both. So the
 *     nonce-equipped scripts are validated by hash even when
 *     'unsafe-inline' is present, which is at least defence-in-
 *     depth: a script-injection that tries to add an arbitrary
 *     inline block without our nonce is rejected on nonce-aware
 *     browsers (Chrome, Edge, Safari 15.4+, Firefox).
 *
 * We use 16 bytes from CSPRNG → base64 → 22 chars. Strong enough
 * that an attacker cant guess it within the page lifetime.
 */
class GenerateCspNonce
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');

        // Share with blade so {{ $cspNonce }} or @cspNonce works.
        View::share('cspNonce', $nonce);
        // Also stash on the request for SecurityHeaders to read.
        $request->attributes->set('csp_nonce', $nonce);

        return $next($request);
    }
}
