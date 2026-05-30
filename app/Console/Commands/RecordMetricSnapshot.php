<?php

namespace App\Console\Commands;

use App\Models\MetricSnapshot;
use App\Services\SubscriptionMetricsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Daily one-shot that takes today's metrics snapshot and persists
 * it for trend reporting.
 *
 * Why a separate command instead of writing on every dashboard
 * load: the dashboard query is cached 15min (Phase 26), so reads
 * are cheap, but each hit doesn't represent a meaningful day-
 * level data point. A dedicated daily job gives us exactly one
 * row per calendar day — the right granularity for an MRR chart.
 *
 * Idempotent via the unique date column + updateOrCreate. Running
 * twice on the same day overwrites with the more recent snapshot,
 * which is what we want when --backfill catches a missed day.
 *
 * --backfill={N} runs the snapshot for the past N days using
 * the SAME live numbers — we don't have a time-travel query so
 * backfilled rows are approximations. Useful only when the table
 * is empty after a fresh install, NOT for repairing missed days.
 */
class RecordMetricSnapshot extends Command
{
    protected $signature = 'metrics:snapshot {--backfill=0 : Insert today + N prior days (approximation)}';
    protected $description = 'Capture the daily MRR/ARR/churn snapshot for trend reporting.';

    public function handle(SubscriptionMetricsService $metrics): int
    {
        $snap = $metrics->snapshot(forceFresh: true);
        $written = 0;

        $write = function ($date) use ($snap, &$written) {
            MetricSnapshot::updateOrCreate(
                ['snapshot_date' => $date],
                [
                    'mrr_sar' => $snap['mrr_sar'],
                    'arr_sar' => $snap['arr_sar'],
                    'active_subs' => $snap['active_subs'],
                    'paused_subs' => $snap['paused_subs'],
                    'past_due_subs' => $snap['past_due_subs'],
                    'arpu_sar' => $snap['arpu_sar'],
                    'churn_30d_pct' => $snap['churn_30d'],
                    'new_subs' => $snap['new_30d'],
                    'cancelled_subs' => $snap['cancelled_30d'],
                    'captured_at' => now(),
                ]
            );
            $written++;
        };

        $write(today());

        $backfillDays = (int) $this->option('backfill');
        if ($backfillDays > 0) {
            for ($i = 1; $i <= $backfillDays; $i++) {
                $write(today()->subDays($i));
            }
            Log::info('metrics.snapshot_backfilled', ['days' => $backfillDays]);
        }

        $this->info("Snapshot written for {$written} day(s).");
        return self::SUCCESS;
    }
}
