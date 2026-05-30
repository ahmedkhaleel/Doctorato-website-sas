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

        // Record-only abuse check — flag IP mismatches between
        // where the link was requested vs where it was clicked.
        // Doesn't block auth (high false-positive rate from
        // mobile→wifi handoffs) but surfaces to admin.
        app(\App\Services\PortalAbuseDetector::class)
            ->onTokenConsumed($row, $request->ip(), $request->userAgent());

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
                // Pause flags surfaced so the dashboard can show the
                // right CTA (Pause when running, Resume when paused).
                'is_paused' => $s->isPaused(),
                'paused_at' => $s->paused_at?->toIso8601String(),
                'paused_until' => $s->paused_until?->toIso8601String(),
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

        // Trial status — only relevant when the customer is still in
        // their free-trial window AND hasn't already converted to a
        // paid subscription. Once any subscription is 'active' the
        // trial card is hidden (subscription card takes over).
        $trial = null;
        $hasActiveSub = $subscriptions->contains(fn ($s) => $s['status'] === 'active');
        if (!$hasActiveSub && $customer->trial_status === 'active' && $customer->trial_ends_at?->isFuture()) {
            $totalDays = (int) ($customer->trial_started_at?->diffInDays($customer->trial_ends_at) ?: 14);
            $daysLeft = max(0, (int) now()->diffInDays($customer->trial_ends_at, false));
            $trial = [
                'status' => 'active',
                'starts_at' => $customer->trial_started_at?->toIso8601String(),
                'ends_at' => $customer->trial_ends_at->toIso8601String(),
                'days_left' => $daysLeft,
                'total_days' => $totalDays,
                'progress_pct' => $totalDays > 0 ? min(100, max(0, (int) round((($totalDays - $daysLeft) / $totalDays) * 100))) : 0,
                'drip_step' => (int) ($customer->trial_drip_step ?? 0),
                'subdomain' => $customer->subdomain ?? null,
            ];
        }

        return Inertia::render('Portal/Dashboard', [
            'customer' => [
                'name' => $customer->full_name,
                'clinic' => $customer->clinic_name,
                'email' => $customer->email,
            ],
            'trial' => $trial,
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
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        Log::info('portal.subscription_canceled', [
            'subscription_id' => $sub->id,
            'customer_id' => $customer->id,
            'ip' => $request->ip(),
        ]);

        return back()->with('portalMessage', 'تم إلغاء الاشتراك. ستحتفظ بالوصول حتى ' . $sub->ends_at?->format('Y-m-d') . '.');
    }

    /**
     * Reactivate a subscription the customer just canceled, as long
     * as the grace period (ends_at) hasn't expired yet. After
     * ends_at the renewal cron has stopped touching the row and a
     * fresh checkout is required.
     */
    public function resumeSubscription(Request $request, int $subscriptionId)
    {
        $customer = $request->attributes->get('customer');

        $sub = Subscription::where('id', $subscriptionId)
            ->where('demo_request_id', $customer->id)
            ->first();
        if (!$sub) {
            abort(404);
        }
        if ($sub->status !== 'cancelled') {
            return back()->withErrors(['subscription' => 'هذا الاشتراك ليس ملغى.']);
        }
        if ($sub->ends_at && $sub->ends_at->isPast()) {
            return back()->withErrors(['subscription' => 'انتهت فترة السماح. اشترك من جديد عبر صفحة الأسعار.']);
        }

        $sub->update(['status' => 'active', 'cancelled_at' => null]);

        Log::info('portal.subscription_resumed', [
            'subscription_id' => $sub->id,
            'customer_id' => $customer->id,
        ]);

        return back()->with('portalMessage', 'تم استئناف اشتراكك. سيستمر التجديد كالمعتاد.');
    }

    /**
     * Referral hub — shows the customer their share link, accumulated
     * credit, and a list of subs they've referred. Only meaningful
     * once they have at least one ACTIVE subscription (referral codes
     * are generated at activation, not at trial signup), so if they
     * don't, we render the page in a "complete your subscription to
     * unlock" empty state.
     */
    public function showReferrals(Request $request)
    {
        $customer = $request->attributes->get('customer');

        $sub = Subscription::where('demo_request_id', $customer->id)
            ->where('status', 'active')
            ->whereNotNull('referral_code')
            ->orderByDesc('starts_at')
            ->first();

        $referrals = [];
        $creditCents = 0;
        $currency = 'USD';
        $referralCode = null;
        $shareUrl = null;

        if ($sub) {
            $referralCode = $sub->referral_code;
            $creditCents = (int) ($sub->referral_credit_cents ?? 0);
            $currency = $sub->currency ?? 'USD';
            $shareUrl = url('/demo?ref=' . $referralCode);

            $referrals = Subscription::where('referred_by_subscription_id', $sub->id)
                ->with('demoRequest:id,clinic_name')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'clinic_name' => $r->demoRequest?->clinic_name ?? $r->customer_name ?? '—',
                    'status' => $r->status,
                    'activated_at' => $r->starts_at?->toIso8601String(),
                ])
                ->toArray();
        }

        return Inertia::render('Portal/Refer', [
            'hasActiveSubscription' => (bool) $sub,
            'referralCode' => $referralCode,
            'shareUrl' => $shareUrl,
            'creditCents' => $creditCents,
            'creditFormatted' => number_format($creditCents / 100, 2),
            'currency' => $currency,
            'rewardBps' => \App\Services\ReferralService::REWARD_BPS,
            'referrals' => $referrals,
        ]);
    }

    /**
     * Pause an active subscription. The customer specifies a number
     * of days (max 90) and the sub stops auto-renewing for that
     * window. paused_at is the "we know about this" stamp; paused_
     * until is the auto-resume target — when the daily resume cron
     * sees `paused_until <= now`, it clears both columns.
     *
     * Why a max of 90 days: longer pauses are effectively churn and
     * should go through cancel-then-resubscribe for tax/refund
     * cleanness.
     */
    public function pauseSubscription(Request $request, int $subscriptionId)
    {
        $customer = $request->attributes->get('customer');

        $data = $request->validate([
            'days' => 'required|integer|min:7|max:90',
        ]);

        $sub = Subscription::where('id', $subscriptionId)
            ->where('demo_request_id', $customer->id)
            ->first();
        if (!$sub) {
            abort(404);
        }
        if ($sub->status !== 'active') {
            return back()->withErrors(['subscription' => 'الاشتراك يجب أن يكون نشطاً ليتم إيقافه مؤقتاً.']);
        }
        if ($sub->isPaused()) {
            return back()->withErrors(['subscription' => 'الاشتراك متوقف مؤقتاً بالفعل.']);
        }

        $sub->update([
            'paused_at' => now(),
            'paused_until' => now()->addDays((int) $data['days']),
        ]);

        Log::info('portal.subscription_paused', [
            'subscription_id' => $sub->id,
            'customer_id' => $customer->id,
            'days' => $data['days'],
        ]);

        return back()->with('portalMessage',
            "تم إيقاف الاشتراك مؤقتاً حتى {$sub->paused_until->format('Y-m-d')}. سيُستأنف تلقائياً.");
    }

    /**
     * Manually resume a paused sub before paused_until is reached.
     * Clears both pause columns. The renewal cron picks up where it
     * left off — no penalty for resuming early.
     */
    public function unpauseSubscription(Request $request, int $subscriptionId)
    {
        $customer = $request->attributes->get('customer');

        $sub = Subscription::where('id', $subscriptionId)
            ->where('demo_request_id', $customer->id)
            ->first();
        if (!$sub) {
            abort(404);
        }
        if (!$sub->isPaused()) {
            return back()->withErrors(['subscription' => 'الاشتراك ليس متوقفاً.']);
        }

        $sub->update(['paused_at' => null, 'paused_until' => null]);

        Log::info('portal.subscription_unpaused', [
            'subscription_id' => $sub->id,
            'customer_id' => $customer->id,
        ]);

        return back()->with('portalMessage', 'تم استئناف اشتراكك.');
    }

    /**
     * Profile page — bare-bones edit form for the customer's own
     * details. Email is read-only because it's the magic-link
     * identifier; changing it would lock the customer out.
     */
    public function showProfile(Request $request)
    {
        $customer = $request->attributes->get('customer');
        return Inertia::render('Portal/Profile', [
            'customer' => [
                'full_name' => $customer->full_name,
                'clinic_name' => $customer->clinic_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'country' => $customer->country,
                'specialty' => $customer->specialty,
                'marketing_opt_in' => (bool) ($customer->marketing_opt_in ?? true),
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $customer = $request->attributes->get('customer');

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'clinic_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'specialty' => 'nullable|string|max:100',
        ]);

        $customer->update($data);

        Log::info('portal.profile_updated', [
            'customer_id' => $customer->id,
            'fields' => array_keys($data),
        ]);

        return back()->with('portalMessage', 'تم تحديث بياناتك.');
    }

    /**
     * Marketing email preferences. Compliance touchpoint — once a
     * customer opts out we stamp the timestamp so the newsletter /
     * re-engagement sends can filter on it. Transactional emails
     * (login link, invoice receipt, dunning) ignore this flag.
     */
    public function updatePreferences(Request $request)
    {
        $customer = $request->attributes->get('customer');

        $optIn = (bool) $request->boolean('marketing_opt_in');
        $patch = ['marketing_opt_in' => $optIn];
        $previously = (bool) ($customer->marketing_opt_in ?? true);
        if (!$optIn && $previously) {
            // First time toggling off — capture when, so an audit
            // can verify we stopped emailing on the right date.
            $patch['marketing_opted_out_at'] = now();
        }

        $customer->update($patch);

        Log::info('portal.preferences_updated', [
            'customer_id' => $customer->id,
            'marketing_opt_in' => $optIn,
        ]);

        return back()->with('portalMessage', $optIn
            ? 'تم تفعيل التحديثات البريدية.'
            : 'تم إيقاف التحديثات البريدية. ستستمر الفواتير وإشعارات الاشتراك في الوصول إليك.');
    }
}
