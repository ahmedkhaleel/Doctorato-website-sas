<?php

namespace App\Http\Middleware;

use App\Models\DemoRequest;
use App\Services\PortalAbuseDetector;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for /portal/* routes. The customer portal runs on a SEPARATE
 * session-based auth from the admin (intentionally — a customer
 * should never be able to inherit admin privileges from any session
 * accident). We park the DemoRequest id under 'portal.customer_id'
 * and validate it on every request.
 *
 * Behaviour:
 *   - No session id set → redirect to /portal (login form)
 *   - Session id refers to a deleted demo → clear + redirect
 *   - Otherwise inject the resolved DemoRequest as $request->customer
 *     so controllers don't have to look it up again
 */
class CustomerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->session()->get('portal.customer_id');
        if (!$id) {
            return redirect('/portal');
        }

        $customer = DemoRequest::find($id);
        if (!$customer) {
            $request->session()->forget('portal.customer_id');
            return redirect('/portal');
        }

        // Abuse signal: session-cookie hijack. The cache records the
        // first IP seen for this session id; if a later request on
        // the SAME session arrives from a different /16 we kill the
        // session and force re-auth. Belt-and-braces alongside the
        // SameSite=lax + Secure + HttpOnly cookie attrs.
        $detector = app(PortalAbuseDetector::class);
        if (!$detector->checkSession($request->session()->getId(), (int) $id, (string) $request->ip())) {
            $request->session()->forget(['portal.customer_id', 'portal.email']);
            $request->session()->invalidate();
            return redirect('/portal')->withErrors([
                'email' => 'تم إنهاء الجلسة لأسباب أمنية. سجّل دخولك من جديد.',
            ]);
        }

        // Abuse signal: enumeration. Once a single customer crosses
        // the threshold in the rolling window, return 429 — the
        // attacker's script breaks, and the cap auto-resets after
        // ENUMERATION_WINDOW_MIN. We DON'T log the customer out;
        // we just rate-limit them.
        if (!$detector->checkEnumeration((int) $id)) {
            abort(429, 'Too many requests. Try again in a few minutes.');
        }

        // Make the resolved customer available to downstream code
        // via $request->customer (or $request->attributes->get('customer'))
        $request->attributes->set('customer', $customer);
        return $next($request);
    }
}
