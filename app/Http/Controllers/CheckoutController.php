<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\PricingPlan;
use App\Models\Subscription;
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
    public function show(Request $request, string $planSlug): Response|RedirectResponse
    {
        $plan = PricingPlan::where('slug', $planSlug)->active()->first();

        if (! $plan || $plan->is_custom) {
            return redirect()->route('contact');
        }

        $cycle = $request->query('cycle') === 'yearly' ? 'yearly' : 'monthly';

        return Inertia::render('Checkout', [
            'plan' => [
                'id' => $plan->id,
                'slug' => $plan->slug,
                'name_ar' => $plan->name_ar,
                'name_en' => $plan->name_en,
                'monthly_price' => (float) $plan->monthly_price,
                'yearly_price' => (float) $plan->yearly_price,
                'currency' => $plan->currency,
            ],
            'cycle' => $cycle,
        ]);
    }

    /** AJAX endpoint: validate a coupon against a plan+cycle and return the discount. */
    public function validateCoupon(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:40'],
            'plan_id' => ['required', 'integer', 'exists:pricing_plans,id'],
            'cycle' => ['required', 'in:monthly,yearly'],
        ]);

        $coupon = Coupon::where('code', strtoupper($data['code']))->first();
        $plan = PricingPlan::find($data['plan_id']);

        if (!$coupon || !$plan) {
            return response()->json(['valid' => false, 'message' => 'كود غير صحيح'], 404);
        }

        $amount = $data['cycle'] === 'yearly' ? (float) $plan->yearly_price : (float) $plan->monthly_price;

        if (!$coupon->isValid($amount, $plan->id)) {
            return response()->json(['valid' => false, 'message' => 'الكوبون غير صالح أو منتهي'], 422);
        }

        $discount = $coupon->computeDiscount($amount);

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'discount' => $discount,
            'total' => round($amount - $discount, 2),
            'label' => $coupon->type === 'percentage'
                ? ((int) $coupon->value) . '% خصم'
                : ((int) $coupon->value) . ' ج.م خصم',
        ]);
    }

    /** Create the subscription + invoice and redirect to Paymob iframe. */
    public function start(Request $request, PaymobService $paymob): RedirectResponse
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

        $plan = PricingPlan::findOrFail($data['plan_id']);
        abort_if($plan->is_custom, 404);

        $subtotal = $data['cycle'] === 'yearly'
            ? (float) $plan->yearly_price
            : (float) $plan->monthly_price;

        // Apply coupon if present and still valid at submit time
        $discount = 0;
        $coupon = null;
        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();
            if ($coupon && $coupon->isValid($subtotal, $plan->id)) {
                $discount = $coupon->computeDiscount($subtotal);
            }
        }
        $total = max(0, $subtotal - $discount);

        [$subscription, $invoice] = DB::transaction(function () use ($data, $plan, $subtotal, $discount, $total, $coupon) {
            $sub = Subscription::create([
                'pricing_plan_id' => $plan->id,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'clinic_name' => $data['clinic_name'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => 'EG',
                'billing_cycle' => $data['cycle'],
                'amount' => $total,
                'coupon_code' => $coupon?->code,
                'discount_amount' => $discount,
                'currency' => $plan->currency ?: 'EGP',
                'status' => 'pending',
            ]);

            $inv = Invoice::create([
                'subscription_id' => $sub->id,
                'subtotal' => $subtotal,
                'tax' => 0,
                'discount' => $discount,
                'total' => $total,
                'currency' => $sub->currency,
                'status' => 'pending',
                'due_at' => now()->addDays(3),
                'line_items' => [[
                    'name' => ($plan->name_ar ?: $plan->name_en) . ' — ' . $data['cycle'],
                    'qty' => 1,
                    'unit_price' => $subtotal,
                    'total' => $subtotal,
                ]],
                'metadata' => $coupon ? ['coupon' => $coupon->code] : null,
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
