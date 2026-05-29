<?php

namespace App\Http\Controllers;

use App\Mail\CustomerLoginLink;
use App\Models\CustomerLoginToken;
use App\Models\DemoRequest;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

/**
 * Customer-facing self-service portal.
 *
 * Auth model: magic link.
 *   1. Customer enters their email on /portal.
 *   2. If the email matches a DemoRequest row with at least one
 *      subscription, we mail a one-time link.
 *   3. Click → /portal/auth/{token} → consume token, set session,
 *      redirect to /portal/dashboard.
 *
 * Important: we DON'T reveal whether the email belongs to a real
 * customer in the response. The success message is identical for
 * unknown emails, so an attacker can't enumerate the customer list.
 * Real customers get the email; unknowns get nothing.
 */
class CustomerPortalController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Portal/Login');
    }

    public function sendLoginLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = strtolower($request->input('email'));

        // Only issue if this email is associated with a paying customer.
        // The "is associated" check happens silently — the same flash
        // message is returned either way so enumeration fails.
        $customer = DemoRequest::where('email', $email)->first();
        if ($customer) {
            $hasSubscription = Subscription::whereHas('demoRequest', fn ($q) => $q->where('email', $email))->exists();

            if ($hasSubscription) {
                [$token, $plain] = CustomerLoginToken::issue($email, $request->ip());
                $link = url('/portal/auth/' . $plain);
                try {
                    Mail::to($email)->queue(new CustomerLoginLink($email, $link, CustomerLoginToken::LIFETIME_MINUTES));
                } catch (\Throwable $e) {
                    Log::warning('Portal login link send failed', [
                        'email' => $email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return back()->with('portalLinkSent', true);
    }

    public function authenticate(Request $request, string $token)
    {
        $row = CustomerLoginToken::findValid($token);
        if (!$row) {
            return redirect('/portal')->withErrors(['email' => 'الرابط منتهي الصلاحية أو غير صحيح. اطلب رابط جديد.']);
        }

        $row->consume();

        $customer = DemoRequest::where('email', $row->email)->first();
        if (!$customer) {
            return redirect('/portal')->withErrors(['email' => 'لم نتمكن من العثور على حسابك.']);
        }

        $request->session()->regenerate();
        $request->session()->put('portal.customer_id', $customer->id);
        $request->session()->put('portal.email', $customer->email);

        return redirect('/portal/dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['portal.customer_id', 'portal.email']);
        $request->session()->regenerate();
        return redirect('/portal');
    }

    public function dashboard(Request $request)
    {
        $customer = $request->attributes->get('customer');

        // Load every subscription this customer has, with latest
        // invoice + plan eager-loaded so the dashboard renders in
        // one query each instead of N.
        $subscriptions = Subscription::where('demo_request_id', $customer->id)
            ->with(['plan', 'latestInvoice'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'plan_name' => $s->plan?->name_en ?? '—',
                'status' => $s->status,
                'billing_cycle' => $s->billing_cycle,
                'starts_at' => $s->starts_at?->toIso8601String(),
                'ends_at' => $s->ends_at?->toIso8601String(),
                'next_billing_date' => $s->next_billing_date?->toIso8601String() ?? null,
            ]);

        $invoices = Invoice::whereIn('subscription_id', $subscriptions->pluck('id'))
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(fn ($i) => [
                'id' => $i->id,
                'number' => $i->number,
                'total' => (float) $i->total,
                'currency' => $i->currency,
                'status' => $i->status,
                'created_at' => $i->created_at->toIso8601String(),
                'paid_at' => $i->paid_at?->toIso8601String(),
            ]);

        return Inertia::render('Portal/Dashboard', [
            'customer' => [
                'name' => $customer->full_name,
                'clinic' => $customer->clinic_name,
                'email' => $customer->email,
            ],
            'subscriptions' => $subscriptions,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Print-ready HTML invoice. The browser handles the actual PDF
     * export via Cmd+P / Ctrl+P, which avoids bundling a PHP PDF
     * library (mpdf/dompdf both pull in dozens of transitive deps).
     *
     * The view uses an @media print stylesheet that hides the nav
     * + the "Download" button, leaving only the invoice. Users hit
     * "Save as PDF" in the print dialog and get a clean document.
     *
     * Strict ownership check: 404 if the invoice doesn't belong to
     * the signed-in customer. A subscription mismatch is silently
     * indistinguishable from "this id doesn't exist".
     */
    public function showInvoice(Request $request, int $invoiceId)
    {
        $customer = $request->attributes->get('customer');

        $invoice = Invoice::with(['subscription.plan', 'payments'])
            ->where('id', $invoiceId)
            ->whereHas('subscription', fn ($q) => $q->where('demo_request_id', $customer->id))
            ->first();

        if (!$invoice) {
            abort(404);
        }

        return view('portal.invoice', [
            'invoice' => $invoice,
            'customer' => $customer,
            'subscription' => $invoice->subscription,
            'autoPrint' => (bool) $request->query('print'),
        ]);
    }

    public function cancelSubscription(Request $request, int $subscriptionId)
    {
        $customer = $request->attributes->get('customer');

        // Verify ownership BEFORE any state change.
        $sub = Subscription::where('id', $subscriptionId)
            ->where('demo_request_id', $customer->id)
            ->first();
        if (!$sub) {
            abort(404);
        }
        if (!in_array($sub->status, ['active', 'past_due'], true)) {
            return back()->withErrors(['subscription' => 'هذا الاشتراك ليس نشطاً، لا يمكن إلغاؤه.']);
        }

        // Soft cancel: subscription stays usable until ends_at, then
        // expires naturally. Avoids the surprise of paying for a
        // month and losing access mid-cycle.
        $sub->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        Log::info('portal.subscription_canceled', [
            'subscription_id' => $sub->id,
            'customer_id' => $customer->id,
            'ip' => $request->ip(),
        ]);

        return back()->with('portalMessage', 'تم إلغاء الاشتراك. ستحتفظ بالوصول حتى ' . $sub->ends_at?->format('Y-m-d') . '.');
    }
}
