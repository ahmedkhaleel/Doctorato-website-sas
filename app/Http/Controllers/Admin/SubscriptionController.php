<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        // Eager-load relationships WITHOUT column restriction on latestInvoice.
        // Constrained eager loading via 'latestInvoice:id,subscription_id,...'
        // can fail under some MySQL configurations when latestOfMany() uses
        // a window function — and the saving from selecting fewer columns is
        // not worth the production 500. Plain Eloquent is reliable.
        $subscriptions = Subscription::query()
            ->with(['plan:id,name_ar,name_en,slug', 'latestInvoice'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('customer_name', 'like', "%{$term}%")
                        ->orWhere('customer_email', 'like', "%{$term}%")
                        ->orWhere('reference', 'like', "%{$term}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Stats: cast every aggregate to float and coalesce nulls. A single
        // null in any sum() result used to bubble through the addition and
        // make MRR null, which crashed Vue's fmtMoney() on the page.
        $monthlyAmount = (float) (Subscription::where('status', 'active')
            ->where('billing_cycle', 'monthly')
            ->sum('amount') ?? 0);
        $yearlyAmount = (float) (Subscription::where('status', 'active')
            ->where('billing_cycle', 'yearly')
            ->sum('amount') ?? 0);

        $stats = [
            'total' => (int) Subscription::count(),
            'active' => (int) Subscription::where('status', 'active')->count(),
            'pending' => (int) Subscription::where('status', 'pending')->count(),
            'past_due' => (int) Subscription::where('status', 'past_due')->count(),
            'mrr' => round($monthlyAmount + ($yearlyAmount / 12), 2),
            'total_revenue' => (float) (Invoice::where('status', 'paid')->sum('total') ?? 0),
        ];

        return Inertia::render('Admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'q' => $request->query('q'),
            ],
        ]);
    }

    public function show(Subscription $subscription)
    {
        $subscription->load([
            'plan',
            'invoices' => fn ($q) => $q->latest(),
            'payments' => fn ($q) => $q->latest(),
        ]);

        return Inertia::render('Admin/SubscriptionDetails', [
            'subscription' => $subscription,
        ]);
    }

    public function cancel(Subscription $subscription)
    {
        // Wrap in a transaction so the status flip + pending-invoice void
        // + activity log land together. Previously an activity-log write
        // failure would leave a cancelled subscription with no audit
        // trail.
        \Illuminate\Support\Facades\DB::transaction(function () use ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Void any pending invoice so we don't keep chasing payment
            // for a cancelled subscription.
            $subscription->invoices()
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            ActivityLog::record(
                'cancelled',
                $subscription,
                "ألغى اشتراك {$subscription->customer_name} ({$subscription->reference})"
            );
        });

        return back()->with('success', 'تم إلغاء الاشتراك');
    }

    /**
     * Refund a specific payment on a subscription.
     * In test mode (or when Paymob isn't configured) it marks locally.
     * In production it calls PaymobService::refund() and updates state.
     */
    public function refundPayment(Payment $payment, \App\Services\PaymobService $paymob)
    {
        abort_if($payment->status !== 'succeeded', 422, 'يمكن استرداد الدفعات الناجحة فقط');

        $subscription = $payment->subscription;
        $invoice = $payment->invoice;

        try {
            if (config('paymob.test_mode') || !$paymob->isConfigured() || !$payment->gateway_transaction_id) {
                // Local refund simulation
                $response = ['test_mode' => true, 'refunded_at' => now()->toIso8601String()];
            } else {
                $response = $paymob->refund($payment->gateway_transaction_id, (float) $payment->amount);
            }
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['refund' => 'فشل الاسترداد: ' . $e->getMessage()]);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $invoice, $subscription, $response) {
            $payment->update([
                'status' => 'refunded',
                'raw_response' => array_merge($payment->raw_response ?? [], ['refund' => $response]),
                'processed_at' => now(),
            ]);

            if ($invoice) {
                $invoice->update(['status' => 'refunded']);
            }

            // If this was the sub's only successful payment, move it to cancelled
            $hasOtherSuccess = $subscription?->payments()
                ->where('id', '!=', $payment->id)
                ->where('status', 'succeeded')
                ->exists();

            if ($subscription && !$hasOtherSuccess) {
                $subscription->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            }
        });

        ActivityLog::record(
            'refunded',
            $subscription,
            "استردّ مبلغ {$payment->amount} {$payment->currency} للعميل {$subscription->customer_name}"
        );

        return back()->with('success', 'تم استرداد المبلغ');
    }
}
