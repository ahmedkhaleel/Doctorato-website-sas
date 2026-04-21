<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Services\CountryDetector;
use App\Services\PaymobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    /** Show the checkout form for a given plan + billing cycle. */
    public function show(Request $request, string $planSlug, CountryDetector $detector): Response|RedirectResponse
    {
        $plan = PricingPlan::with(['prices' => fn ($q) => $q->where('is_active', true)])
            ->where('slug', $planSlug)
            ->active()
            ->first();

        if (! $plan || $plan->is_custom) {
            return redirect()->route('contact');
        }

        $cycle = $request->query('cycle') === 'yearly' ? 'yearly' : 'monthly';
        $country = $detector->resolve($request);
        $price = $plan->priceFor($country);

        return Inertia::render('Checkout', [
            'plan' => [
                'id' => $plan->id,
                'slug' => $plan->slug,
                'name_ar' => $plan->name_ar,
                'name_en' => $plan->name_en,
                'monthly_price' => $price['monthly'],
                'yearly_price' => $price['yearly'],
                'setup_fee' => $price['setup_fee'],
                'setup_fee_yearly' => $price['setup_fee_yearly'],
                'currency' => $price['currency'],
                'currency_symbol' => $price['currency_symbol'],
                'country_code' => $price['country_code'],
                'country_flag' => $price['country_flag'],
            ],
            'cycle' => $cycle,
        ]);
    }

    /** AJAX endpoint: validate a coupon against a plan+cycle and return the discount. */
    public function validateCoupon(Request $request, CountryDetector $detector): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:40'],
            'plan_id' => ['required', 'integer', 'exists:pricing_plans,id'],
            'cycle' => ['required', 'in:monthly,yearly'],
        ]);

        $coupon = Coupon::where('code', strtoupper($data['code']))->first();
        $plan = PricingPlan::with('prices')->find($data['plan_id']);

        if (!$coupon || !$plan) {
            return response()->json(['valid' => false, 'message' => 'كود غير صحيح'], 404);
        }

        $country = $detector->resolve($request);
        $price = $plan->priceFor($country);
        // Coupon applies to the recurring subscription portion only — we
        // don't discount the setup fee. That's intentional: the launch
        // offer (50% yearly / full monthly) already handles onboarding.
        $subscriptionAmount = $data['cycle'] === 'yearly' ? $price['yearly'] : $price['monthly'];

        if (!$coupon->isValid($subscriptionAmount, $plan->id)) {
            return response()->json(['valid' => false, 'message' => 'الكوبون غير صالح أو منتهي'], 422);
        }

        $discount = $coupon->computeDiscount($subscriptionAmount);

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'discount' => $discount,
            'total' => round($subscriptionAmount - $discount, 2), // subscription only — setup fee added client-side
            'label' => $coupon->type === 'percentage'
                ? ((int) $coupon->value) . '% خصم'
                : ((int) $coupon->value) . ' ' . ($price['currency_symbol'] ?: $price['currency']) . ' خصم',
        ]);
    }

    /** Create the subscription + invoice and redirect to Paymob iframe. */
    public function start(Request $request, PaymobService $paymob, CountryDetector $detector): RedirectResponse
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:pricing_plans,id'],
            'cycle' => ['required', 'in:monthly,yearly'],
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'max:30'],
            'clinic_name' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:80'],
            'coupon_code' => ['nullable', 'string', 'max:40'],
        ]);

        $plan = PricingPlan::with('prices')->findOrFail($data['plan_id']);
        abort_if($plan->is_custom, 404);

        $country = $detector->resolve($request);
        $price = $plan->priceFor($country);

        $subscriptionAmount = $data['cycle'] === 'yearly' ? $price['yearly'] : $price['monthly'];
        // Yearly gets 50% off the one-time setup fee (pre-computed in
        // PricingPlan::priceFor as setup_fee_yearly).
        $setupFee = $data['cycle'] === 'yearly' ? $price['setup_fee_yearly'] : $price['setup_fee'];

        // Apply coupon to the subscription portion only.
        $discount = 0;
        $coupon = null;
        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();
            if ($coupon && $coupon->isValid($subscriptionAmount, $plan->id)) {
                $discount = $coupon->computeDiscount($subscriptionAmount);
            }
        }

        $subtotal = round($subscriptionAmount + $setupFee, 2);
        $total = max(0, round($subscriptionAmount - $discount + $setupFee, 2));

        [$subscription, $invoice] = DB::transaction(function () use ($data, $plan, $price, $subscriptionAmount, $setupFee, $subtotal, $discount, $total, $coupon, $country) {
            $sub = Subscription::create([
                'pricing_plan_id' => $plan->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'clinic_name' => $data['clinic_name'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $country,
                'billing_cycle' => $data['cycle'],
                'amount' => $total,
                'coupon_code' => $coupon?->code,
                'discount_amount' => $discount,
                'currency' => $price['currency'] ?: 'EGP',
                'status' => 'pending',
            ]);

            $lineItems = [[
                'name' => ($plan->name_ar ?: $plan->name_en) . ' — ' . ($data['cycle'] === 'yearly' ? 'سنوي' : 'شهري'),
                'qty' => 1,
                'unit_price' => $subscriptionAmount,
                'total' => $subscriptionAmount,
            ]];
            if ($setupFee > 0) {
                $lineItems[] = [
                    'name' => 'رسوم الإعداد (لمرة واحدة)' . ($data['cycle'] === 'yearly' ? ' — خصم 50% سنوي' : ''),
                    'qty' => 1,
                    'unit_price' => $setupFee,
                    'total' => $setupFee,
                ];
            }

            $inv = Invoice::create([
                'subscription_id' => $sub->id,
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => $discount,
                'total' => $total,
                'currency' => $sub->currency,
                'status' => 'pending',
                'due_at' => now()->addDays(3),
                'line_items' => $lineItems,
                'metadata' => array_filter([
                    'coupon' => $coupon?->code,
                    'setup_fee' => $setupFee ?: null,
                    'country' => $country,
                ]),
            ]);

            if ($coupon) {
                $coupon->markUsed();
            }

            return [$sub, $inv];
        });

        // Test mode: skip Paymob and mark invoice paid immediately (useful for local dev)
        if (config('paymob.test_mode')) {
            $this->markInvoicePaid($invoice, ['gateway' => 'paymob-test', 'status' => 'succeeded']);

            return redirect()->route('checkout.success', ['reference' => $subscription->reference]);
        }

        try {
            $iframeUrl = $paymob->getIframeUrlFor($subscription, $invoice);
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['payment' => __('لا يمكن بدء الدفع الآن. برجاء المحاولة لاحقاً أو التواصل معنا.')]);
        }

        return redirect()->away($iframeUrl);
    }

    public function success(Request $request): Response
    {
        $sub = Subscription::with('latestInvoice')
            ->where('reference', $request->query('reference'))
            ->first();

        return Inertia::render('CheckoutSuccess', [
            'subscription' => $sub ? [
                'reference' => $sub->reference,
                'status' => $sub->status,
                'amount' => (float) $sub->amount,
                'currency' => $sub->currency,
                'invoice_number' => $sub->latestInvoice?->number,
            ] : null,
        ]);
    }

    public function failed(Request $request): Response
    {
        return Inertia::render('CheckoutFailed', [
            'reference' => $request->query('reference'),
        ]);
    }

    protected function markInvoicePaid(Invoice $invoice, array $paymentAttrs = []): void
    {
        DB::transaction(function () use ($invoice, $paymentAttrs) {
            $invoice->update(['status' => 'paid', 'paid_at' => now()]);

            $sub = $invoice->subscription;
            $starts = now();
            $ends = $sub->billing_cycle === 'yearly' ? $starts->copy()->addYear() : $starts->copy()->addMonth();

            $sub->update([
                'status' => 'active',
                'starts_at' => $starts,
                'ends_at' => $ends,
            ]);

            $invoice->payments()->create(array_merge([
                'subscription_id' => $sub->id,
                'gateway' => 'paymob',
                'amount' => $invoice->total,
                'currency' => $invoice->currency,
                'status' => 'succeeded',
                'processed_at' => now(),
            ], $paymentAttrs));
        });
    }
}
