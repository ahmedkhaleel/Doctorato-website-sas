<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\DemoRequest;
use App\Models\Invoice;
use App\Models\NewsletterSubscriber;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(): Response
    {
        $now = now();
        $thirtyDaysAgo = $now->copy()->subDays(30);
        $sixtyDaysAgo = $now->copy()->subDays(60);

        // Funnel
        $demos = DemoRequest::where('created_at', '>=', $thirtyDaysAgo)->count();
        $contacts = ContactMessage::where('created_at', '>=', $thirtyDaysAgo)->count();
        $newSubscriptions = Subscription::where('created_at', '>=', $thirtyDaysAgo)->count();
        $paidSubscriptions = Subscription::where('status', 'active')
            ->where('created_at', '>=', $thirtyDaysAgo)->count();
        $newsletterCount = NewsletterSubscriber::count();

        // Revenue
        $monthRevenue = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $thirtyDaysAgo)->sum('total');
        $prevMonthRevenue = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$sixtyDaysAgo, $thirtyDaysAgo])->sum('total');
        $revenueDelta = $prevMonthRevenue > 0
            ? round((($monthRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1)
            : null;

        // MRR
        $activeMonthly = Subscription::where('status', 'active')->where('billing_cycle', 'monthly')->sum('amount');
        $activeYearly = Subscription::where('status', 'active')->where('billing_cycle', 'yearly')->sum('amount');
        $mrr = $activeMonthly + ($activeYearly / 12);

        // Conversion rate (paid / demos requested in last 30d)
        $conversionRate = $demos > 0 ? round(($paidSubscriptions / $demos) * 100, 1) : 0;

        // Daily series — last 30 days
        $dailySeries = $this->dailySeries($thirtyDaysAgo, $now);

        // Plan distribution
        $planDistribution = Subscription::query()
            ->where('status', 'active')
            ->join('pricing_plans', 'pricing_plans.id', '=', 'subscriptions.pricing_plan_id')
            ->select('pricing_plans.name_ar as plan', DB::raw('count(*) as count'))
            ->groupBy('pricing_plans.id', 'pricing_plans.name_ar')
            ->get();

        // Revenue split — subscription vs setup fee for last 30 days.
        // `setup_fee_amount` is stored on invoices (see migration
        // 2026_04_19_210000) so we don't need to walk the line_items JSON.
        $paidThisMonth = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', $thirtyDaysAgo)
            ->get(['total', 'discount', 'setup_fee_amount']);
        $setupRevenue = (float) $paidThisMonth->sum('setup_fee_amount');
        $subscriptionRevenue = max(0, (float) $paidThisMonth->sum('total') - $setupRevenue);
        $revenueSplit = [
            'subscription' => round($subscriptionRevenue, 2),
            'setup_fee' => round($setupRevenue, 2),
            'total' => round($subscriptionRevenue + $setupRevenue, 2),
            'setup_fee_percent' => $subscriptionRevenue + $setupRevenue > 0
                ? round(($setupRevenue / ($subscriptionRevenue + $setupRevenue)) * 100, 1)
                : 0,
        ];

        // Revenue by country — top 8 markets last 30d. Keeps the chart
        // legible and matches the countries we actively sell into.
        $countryRevenue = Invoice::where('invoices.status', 'paid')
            ->where('invoices.paid_at', '>=', $thirtyDaysAgo)
            ->join('subscriptions', 'subscriptions.id', '=', 'invoices.subscription_id')
            ->select('subscriptions.country', DB::raw('SUM(invoices.total) as revenue'), DB::raw('COUNT(*) as invoices'))
            ->groupBy('subscriptions.country')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'country' => $r->country ?: 'EG',
                'revenue' => round((float) $r->revenue, 2),
                'invoices' => (int) $r->invoices,
            ]);

        // Trial funnel — demo calls vs instant self-serve trials last 30d.
        $demoCallSignups = DemoRequest::where('created_at', '>=', $thirtyDaysAgo)
            ->where(fn ($q) => $q->where('is_instant_trial', false)->orWhereNull('is_instant_trial'))
            ->count();
        $instantTrials = DemoRequest::where('created_at', '>=', $thirtyDaysAgo)
            ->where('is_instant_trial', true)
            ->count();

        return Inertia::render('Admin/Analytics', [
            'kpis' => [
                'demos' => $demos,
                'contacts' => $contacts,
                'new_subscriptions' => $newSubscriptions,
                'paid_subscriptions' => $paidSubscriptions,
                'newsletter_subscribers' => $newsletterCount,
                'month_revenue' => round($monthRevenue, 2),
                'revenue_delta' => $revenueDelta,
                'mrr' => round($mrr, 2),
                'conversion_rate' => $conversionRate,
                'demo_call_signups' => $demoCallSignups,
                'instant_trials' => $instantTrials,
            ],
            'daily_series' => $dailySeries,
            'plan_distribution' => $planDistribution,
            'revenue_split' => $revenueSplit,
            'country_revenue' => $countryRevenue,
        ]);
    }

    /** Daily counts for demos / subscriptions / revenue over a period. */
    protected function dailySeries(Carbon $from, Carbon $to): array
    {
        $days = [];
        $cursor = $from->copy()->startOfDay();
        $endDay = $to->copy()->startOfDay();

        while ($cursor <= $endDay) {
            $days[$cursor->format('Y-m-d')] = [
                'date' => $cursor->format('Y-m-d'),
                'demos' => 0,
                'subscriptions' => 0,
                'revenue' => 0,
            ];
            $cursor->addDay();
        }

        DemoRequest::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $from)
            ->groupBy('date')
            ->get()
            ->each(function ($r) use (&$days) {
                if (isset($days[$r->date])) $days[$r->date]['demos'] = (int) $r->count;
            });

        Subscription::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $from)
            ->groupBy('date')
            ->get()
            ->each(function ($r) use (&$days) {
                if (isset($days[$r->date])) $days[$r->date]['subscriptions'] = (int) $r->count;
            });

        Invoice::select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(total) as total'))
            ->where('status', 'paid')
            ->where('paid_at', '>=', $from)
            ->groupBy('date')
            ->get()
            ->each(function ($r) use (&$days) {
                if (isset($days[$r->date])) $days[$r->date]['revenue'] = round((float) $r->total, 2);
            });

        return array_values($days);
    }
}
