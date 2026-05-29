<?php

namespace App\Console\Commands;

use App\Mail\TrialDripMail;
use App\Models\DemoRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Drives the 3-step trial welcome drip.
 *
 *   step 0 → 1 = welcome    (fires as soon as trial_started_at >= now)
 *   step 1 → 2 = feature tour (fires when trial is ≥ 3 days old)
 *   step 2 → 3 = case study   (fires when trial is ≥ 7 days old)
 *
 * Stop conditions — a trial is SKIPPED if any of these are true:
 *   - trial_status != 'active' (already expired/cancelled)
 *   - trial_drip_step already at 3 (done)
 *   - marketing_opt_in is false (respect the customer's preference)
 *   - email is empty (defensive — shouldn't happen but cheap to check)
 *
 * Idempotency: each step advances trial_drip_step + stamps
 * trial_drip_last_sent_at, so a duplicate run or a retried cron tick
 * won't double-send. The composite index idx_demo_drip on
 * (trial_drip_step, trial_started_at) keeps the query cheap as the
 * DemoRequest table grows.
 *
 * Scheduled daily at 10:00 — sends land in inboxes during work
 * hours regardless of customer timezone. Single send window also
 * means we never compete with the (more time-sensitive) "trial
 * ending soon" email that fires hourly.
 */
class SendTrialDrip extends Command
{
    protected $signature = 'trials:drip {--dry : Show what would send without sending}';
    protected $description = 'Send the next step of the trial welcome drip (welcome / tour / case study)';

    /** Step → minimum age of trial in days before this step fires. */
    protected const STEP_AGE_DAYS = [
        1 => 0, // welcome — immediately
        2 => 3, // feature tour — day 3
        3 => 7, // case study — day 7
    ];

    public function handle(): int
    {
        $dry = $this->option('dry');
        $sent = 0;
        $skipped = 0;

        // Process each step in REVERSE order (3, then 2, then 1).
        // A single run can only advance one step per trial — if we
        // walked forward, a 10-day-old step=0 trial would get its
        // row stamped step=1, then step=2, then step=3 in the same
        // tick, sending all three emails in one minute. Reverse
        // order means the step=2 query sees trials BEFORE the
        // step=1 query has bumped them, so the worst case is a 10-
        // day-old step=0 trial advancing to step=1 only.
        $steps = self::STEP_AGE_DAYS;
        krsort($steps);
        foreach ($steps as $nextStep => $minAgeDays) {
            $previousStep = $nextStep - 1;
            $cutoff = now()->subDays($minAgeDays);

            DemoRequest::query()
                ->where('trial_drip_step', $previousStep)
                ->where('trial_started_at', '<=', $cutoff)
                ->where('trial_status', 'active')
                ->where(function ($q) {
                    // marketing_opt_in column may be null on legacy
                    // rows that pre-date the column — treat null as
                    // opted-in (which is the default for new rows).
                    $q->whereNull('marketing_opt_in')->orWhere('marketing_opt_in', true);
                })
                ->whereNotNull('email')
                ->orderBy('id')
                ->chunkById(200, function ($trials) use ($nextStep, $dry, &$sent, &$skipped) {
                    foreach ($trials as $trial) {
                        if ($dry) {
                            $this->line("[dry] would send step {$nextStep} to {$trial->email} (trial #{$trial->id})");
                            $sent++;
                            continue;
                        }

                        try {
                            Mail::to($trial->email)->queue(new TrialDripMail($trial, $nextStep));
                            // Stamp BEFORE the queue worker actually
                            // sends — failure inside the worker is
                            // retried by ShouldQueue's tries=3; we
                            // care that we don't re-queue from this
                            // command on the next tick.
                            $trial->forceFill([
                                'trial_drip_step' => $nextStep,
                                'trial_drip_last_sent_at' => now(),
                            ])->save();
                            $sent++;
                        } catch (\Throwable $e) {
                            $skipped++;
                            $this->error("Drip step {$nextStep} failed for trial #{$trial->id}: " . $e->getMessage());
                        }
                    }
                });
        }

        $this->info("Trial drip: queued {$sent}, skipped {$skipped}.");
        return self::SUCCESS;
    }
}
