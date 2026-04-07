<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Faq, PricingPlan, Testimonial, Currency, ContactMessage, DemoRequest, NewsletterSubscriber, BlogPost, User, Subscription, Invoice, Payment};
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);
        $monthAgo = $now->copy()->subDays(30);
        $prevMonthStart = $now->copy()->subDays(60);

        // 7-day trend for demos & contacts
        $trend = collect(range(6, 0))->map(function ($daysAgo) use ($now) {
            $date = $now->copy()->subDays($daysAgo)->startOfDay();
            return [
                'label' => $date->format('M d'),
                'day' => $date->format('D'),
                'demos' => DemoRequest::whereDate('created_at', $date)->count(),
                'contacts' => ContactMessage::whereDate('created_at', $date)->count(),
            ];
        })->values();

        // Revenue / MRR
        $monthRevenue = (float) Invoice::where('status', 'paid')->where('paid_at', '>=', $monthAgo)->sum('total');
        $prevMonthRevenue = (float) Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$prevMonthStart, $monthAgo])->sum('total');
        $revenueDelta = $prevMonthRevenue > 0
            ? round((($monthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1)
            : null;
        $totalRevenue = (float) Invoice::where('status', 'paid')->sum('total');

        $activeMonthly = (float) Subscription::where('status', 'active')->where('billing_cycle', 'monthly')->sum('amount');
        $activeYearly = (float) Subscription::where('status', 'active')->where('billing_cycle', 'yearly')->sum('amount');
        $mrr = $activeMonthly + ($activeYearly / 12);

        // Subscription stats
        $activeSubs = Subscription::where('status', 'active')->count();
        $pendingSubs = Subscription::where('status', 'pending')->count();
        $newSubsThisMonth = Subscription::where('created_at', '>=', $monthAgo)->count();

        // Subscriptions expiring within 7 days
        $expiringSoon = Subscription::with('plan:id,name_ar,name_en')
            ->where('status', 'active')
            ->whereNotNull('ends_at')
            ->whereBetween('ends_at', [$now, $now->copy()->addDays(7)])
            ->orderBy('ends_at')
            ->take(5)
            ->get(['id', 'pricing_plan_id', 'reference', 'customer_name', 'amount', 'currency', 'ends_at', 'billing_cycle']);

        // Recent successful payments
        $recentPayments = Payment::with('subscription:id,customer_name,reference')
            ->where('status', 'succeeded')
            ->latest('processed_at')
            ->take(5)
            ->get(['id', 'subscription_id', 'amount', 'currency', 'payment_method', 'processed_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'faqs' => Faq::count(),
                'plans' => PricingPlan::count(),
                'testimonials' => Testimonial::count(),
                'currencies' => Currency::count(),
                'contacts' => ContactMessage::count(),
                'demos' => DemoRequest::count(),
                'subscribers' => NewsletterSubscriber::count(),
                'posts' => BlogPost::count(),
                'users' => User::count(),
                'unread_contacts' => ContactMessage::where('is_read', false)->count(),
                'new_demos' => DemoRequest::where('status', 'new')->count(),
                'demos_this_week' => DemoRequest::where('created_at', '>=', $weekAgo)->count(),
                'contacts_this_week' => ContactMessage::where('created_at', '>=', $weekAgo)->count(),

                // Revenue / Subscriptions
                'mrr' => round($mrr, 2),
                'month_revenue' => round($monthRevenue, 2),
                'revenue_delta' => $revenueDelta,
                'total_revenue' => round($totalRevenue, 2),
                'active_subscriptions' => $activeSubs,
                'pending_subscriptions' => $pendingSubs,
                'new_subs_this_month' => $newSubsThisMonth,
            ],
            'trend' => $trend,
            'recentDemos' => DemoRequest::latest()->take(5)->get(['id', 'clinic_name', 'full_name', 'status', 'created_at']),
            'recentContacts' => ContactMessage::latest()->take(5)->get(['id', 'name', 'subject', 'is_read', 'created_at']),
            'recentPayments' => $recentPayments,
            'expiringSoon' => $expiringSoon,
        ]);
    }
}
