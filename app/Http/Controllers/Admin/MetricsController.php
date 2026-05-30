<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetricSnapshot;
use App\Services\SubscriptionMetricsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Owner-level KPI dashboard.
 *
 *   GET  /admin/metrics              cached snapshot (15min TTL)
 *   POST /admin/metrics/refresh      bust the cache + recompute
 *
 * Permission: metrics.view — separate from billing.manage because
 * a part-time finance ops shouldn't necessarily see headline MRR.
 */
class MetricsController extends Controller
{
    public function index(Request $request, SubscriptionMetricsService $metrics)
    {
        // Last 90 days of snapshots, oldest-first so the chart
        // renders left-to-right naturally. We cap at 90 because
        // the sparkline is decorative — a longer history makes
        // each daily change too small to see.
        $trend = MetricSnapshot::query()
            ->where('snapshot_date', '>=', now()->subDays(90)->toDateString())
            ->orderBy('snapshot_date')
            ->get(['snapshot_date', 'mrr_sar', 'active_subs', 'churn_30d_pct'])
            ->map(fn ($r) => [
                'date' => $r->snapshot_date->toDateString(),
                'mrr' => (float) $r->mrr_sar,
                'active' => (int) $r->active_subs,
                'churn' => (float) $r->churn_30d_pct,
            ])->values();

        return Inertia::render('Admin/Metrics', [
            'snapshot' => $metrics->snapshot(),
            'trend' => $trend,
        ]);
    }

    public function refresh(Request $request, SubscriptionMetricsService $metrics)
    {
        $metrics->snapshot(forceFresh: true);
        return back()->with('success', 'تم تحديث الإحصائيات.');
    }
}
