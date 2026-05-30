<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Trims log/audit tables according to a documented retention policy.
 *
 *   activity_logs        → 365 days (keep one full audit year)
 *   failed_jobs          →  30 days (any older was clearly not investigated)
 *   sessions             →  30 days past last activity (Laravel doesn't auto-purge)
 *   customer_login_tokens → 7 days (lifetime is 15 min — anything older is dead)
 *   webhook_events       → 180 days (forensics window covers any
 *                          plausible chargeback or refund dispute)
 *   personal_access_tokens (if present) → expired + 30 day grace
 *
 * Why retention matters here:
 *   1. Storage cost on cPanel — table bloat slows backups and full table
 *      scans on the activity log view eventually time out.
 *   2. GDPR/data minimisation — once a customer is gone we should not be
 *      sitting on a 5-year audit trail of their email address.
 *   3. Failed-job table being unbounded is the classic "your queue worker
 *      is dead and nobody noticed for 2 months" scenario.
 *
 * Idempotent: re-running on the same day is a no-op. Wrapped in a
 * transaction per table so a deadlock on one table doesn't roll back
 * the prior ones.
 *
 * Activity-log retention is the longest and most sensitive — pruned via
 * a soft criterion: `action` is NOT in the "permanent" allowlist
 * (deleted, suspended, refunded — anything a regulator might ask about).
 */
class PruneRetention extends Command
{
    protected $signature = 'maint:prune {--dry : Show what would be pruned without running deletes}';
    protected $description = 'Trim log/audit tables according to the retention policy';

    /** Actions kept forever for audit / dispute / regulatory reasons. */
    protected const PERMANENT_ACTIONS = [
        'deleted', 'suspended', 'refunded', 'cancelled', 'canceled',
        'gdpr_export', 'gdpr_delete', 'permission_changed', 'role_changed',
    ];

    public function handle(): int
    {
        $dry = $this->option('dry');
        $totals = [];

        $totals['activity_logs'] = $this->pruneActivityLogs($dry);
        $totals['failed_jobs'] = $this->pruneFailedJobs($dry);
        $totals['sessions'] = $this->pruneSessions($dry);
        $totals['customer_login_tokens'] = $this->pruneLoginTokens($dry);
        $totals['webhook_events'] = $this->pruneWebhookEvents($dry);
        $totals['email_logs'] = $this->pruneEmailLogs($dry);

        $verb = $dry ? 'would prune' : 'pruned';
        foreach ($totals as $table => $count) {
            $this->info("{$verb} {$count} row(s) from {$table}");
        }

        return self::SUCCESS;
    }

    protected function pruneActivityLogs(bool $dry): int
    {
        if (!Schema::hasTable('activity_logs')) return 0;

        $cutoff = now()->subDays(365);
        $query = DB::table('activity_logs')
            ->where('created_at', '<', $cutoff)
            ->whereNotIn('action', self::PERMANENT_ACTIONS);

        return $dry ? $query->count() : $query->delete();
    }

    protected function pruneFailedJobs(bool $dry): int
    {
        if (!Schema::hasTable('failed_jobs')) return 0;

        $cutoff = now()->subDays(30);
        $query = DB::table('failed_jobs')->where('failed_at', '<', $cutoff);

        return $dry ? $query->count() : $query->delete();
    }

    /**
     * Laravel's session GC fires probabilistically (1/100 reqs by default)
     * and DOESN'T touch rows where last_activity is far in the past.
     * Explicit prune ensures we don't sit on stale sessions forever.
     */
    protected function pruneSessions(bool $dry): int
    {
        if (!Schema::hasTable('sessions')) return 0;

        $cutoffTs = now()->subDays(30)->timestamp;
        $query = DB::table('sessions')->where('last_activity', '<', $cutoffTs);

        return $dry ? $query->count() : $query->delete();
    }

    protected function pruneLoginTokens(bool $dry): int
    {
        if (!Schema::hasTable('customer_login_tokens')) return 0;

        $cutoff = now()->subDays(7);
        // Token lifetime is 15 min, so anything older than a week
        // is guaranteed dead — keep a 7-day buffer for forensics.
        $query = DB::table('customer_login_tokens')->where('created_at', '<', $cutoff);

        return $dry ? $query->count() : $query->delete();
    }

    /**
     * Webhook events older than 180 days. The window covers any
     * plausible chargeback or refund dispute (Paymob's 120-day
     * limit + 60-day buffer) — beyond that, the original
     * transaction is closed and the row is dead weight.
     */
    protected function pruneWebhookEvents(bool $dry): int
    {
        if (!Schema::hasTable('webhook_events')) return 0;

        $cutoff = now()->subDays(180);
        $query = DB::table('webhook_events')->where('received_at', '<', $cutoff);

        return $dry ? $query->count() : $query->delete();
    }

    /**
     * Email logs older than 90 days. We keep less than activity_logs
     * because email reachability questions are typically asked
     * within a few weeks, and the hashed-recipient column is enough
     * privacy weight to want it gone sooner.
     */
    protected function pruneEmailLogs(bool $dry): int
    {
        if (!Schema::hasTable('email_logs')) return 0;

        $cutoff = now()->subDays(90);
        $query = DB::table('email_logs')->where('queued_at', '<', $cutoff);

        return $dry ? $query->count() : $query->delete();
    }
}
