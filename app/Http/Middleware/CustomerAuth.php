<?php

namespace App\Http\Middleware;

use App\Models\DemoRequest;
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

        // Make the resolved customer available to downstream code
        // via $request->customer (or $request->attributes->get('customer'))
        $request->attributes->set('customer', $customer);
        return $next($request);
    }
}
