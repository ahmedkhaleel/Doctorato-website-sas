<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Walks paused subscriptions whose `paused_until` has elapsed and
 * clears the pause columns, returning them to the regular
 * active+renewing flow.
 *
 * Idempotent: a sub already cleared by a previous run is no longer
 * matched by the WHERE clause. Re-running is a no-op.
 *
 * Why not just check paused_until inline during renewal? Because
 * the renewal cron uses the composite index on (status, ends_at)
 * which doesn't include paused_at. A daily one-shot that flips
 * pause off is cheaper than every renewal query inspecting two
 * extra columns.
 */
class AutoResumePausedSubscriptions extends Command
{
    protected $signature = 'subs:auto-resume';
    protected $description = 'Clear pause columns on subscriptions whose paused_until has elapsed.';

    public function handle(): int
    {
        $count = 0;
        Subscription::query()
            ->whereNotNull('paused_at')
            ->whereNotNull('paused_until')
            ->where('paused_until', '<=', now())
            ->chunkById(200, function ($subs) use (&$count) {
                foreach ($subs as $sub) {
                    $sub->update(['paused_at' => null, 'paused_until' => null]);
                    Log::info('subs.auto_resumed', ['subscription_id' => $sub->id]);
                    $count++;
                }
            });

        $this->info("Auto-resumed {$count} subscription(s).");
        return self::SUCCESS;
    }
}
