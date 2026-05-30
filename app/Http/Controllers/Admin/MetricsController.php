<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        return Inertia::render('Admin/Metrics', [
            'snapshot' => $metrics->snapshot(),
        ]);
    }

    public function refresh(Request $request, SubscriptionMetricsService $metrics)
    {
        $metrics->snapshot(forceFresh: true);
        return back()->with('success', 'تم تحديث الإحصائيات.');
    }
}
