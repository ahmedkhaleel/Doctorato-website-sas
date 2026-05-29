<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * `php artisan queue:health`
 *
 * Single-shot diagnostic for the database-driven queue. Surfaces the
 * three failure modes that production cron-driven setups silently
 * accumulate:
 *
 *   1. Backlog       — pending jobs older than threshold (worker is
 *                       slow, dead, or not running at all).
 *   2. Stuck jobs    — reserved_at set but not finished within
 *                       threshold (worker crashed mid-job).
 *   3. Failures      — failed_jobs table growing (retries exhausted,
 *                       admin needs to investigate the payload).
 *
 * Designed to be cron-callable: exits non-zero on warnings so a
 * monitoring wrapper (HealthChecks.io, Uptime Robot, custom email)
 * can fire on failure.
 */
class QueueHealthCheck extends Command
{
    protected $signature = 'queue:health
        {--stale-minutes=5 : Treat jobs pending longer than this as backlog}
        {--reserved-minutes=10 : Treat reserved-but-not-finished jobs as stuck}
        {--max-failures=10 : Warning threshold for failed_jobs}';

    protected $description = 'Reports the health of the database queue (backlog, stuck jobs, failures).';

    public function handle(): int
    {
        $stale = (int) $this->option('stale-minutes');
        $reserved = (int) $this->option('reserved-minutes');
        $maxFailures = (int) $this->option('max-failures');

        $now = Carbon::now();
        $staleThreshold = $now->copy()->subMinutes($stale)->timestamp;
        $reservedThreshold = $now->copy()->subMinutes($reserved)->timestamp;

        $pending = DB::table('jobs')->count();
        $backlog = DB::table('jobs')
            ->whereNull('reserved_at')
            ->where('created_at', '<', $staleThreshold)
            ->count();
        $stuck = DB::table('jobs')
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '<', $reservedThreshold)
            ->count();
        $failures = DB::table('failed_jobs')->count();

        $this->table(
            ['Metric', 'Value', 'Threshold', 'Status'],
            [
                ['Pending jobs (total)',  $pending,  '—',          $pending < 100 ? '✓' : '⚠'],
                ['Backlog (> '.$stale.'m)',  $backlog,  '0',         $backlog === 0 ? '✓' : '⚠'],
                ['Stuck (> '.$reserved.'m)', $stuck,    '0',         $stuck === 0 ? '✓' : '⚠'],
                ['Failed jobs (total)',  $failures, "≤ {$maxFailures}", $failures <= $maxFailures ? '✓' : '⚠'],
            ]
        );

        // Exit non-zero on any warning so monitoring can pick it up.
        $unhealthy = $backlog > 0 || $stuck > 0 || $failures > $maxFailures;
        if ($unhealthy) {
            $this->error('Queue is unhealthy. Check the worker cron and recent failed jobs.');
            return self::FAILURE;
        }

        $this->info('Queue is healthy.');
        return self::SUCCESS;
    }
}
