<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        $subscriptions = Subscription::query()
            ->with(['plan:id,name_ar,name_en,slug', 'latestInvoice:id,subscription_id,number,total,status'])
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

        $stats = [
            'total' => Subscription::count(),
            'active' => Subscription::where('status', 'active')->count(),
            'pending' => Subscription::where('status', 'pending')->count(),
            'past_due' => Subscription::where('status', 'past_due')->count(),
            'mrr' => Subscription::where('status', 'active')
                ->where('billing_cycle', 'monthly')
                ->sum('amount')
                + (Subscription::where('status', 'active')
                    ->where('billing_cycle', 'yearly')
                    ->sum('amount') / 12),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total'),
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
        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return back()->with('success', 'تم إلغاء الاشتراك');
    }
}
