<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingSoonMail;
use App\Mail\TrialExpiredMail;
use App\Models\DemoRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Runs every hour. Does two passes:
 *   1. Trials expiring in ≤3 days that haven't had the heads-up email
 *      → fire TrialEndingSoonMail once, flip trial_ending_soon_notified.
 *   2. Trials past their end date that haven't had the expiry email
 *      → fire TrialExpiredMail, mark as expired.
 *
 * Both passes are idempotent (guarded by *_notified flags) so retries
 * or overlapping schedule runs don't spam the customer.
 */
class CheckExpiringTrials extends Command
{
    protected $signature = 'trials:check';

    protected $description = 'Send "ending soon" reminders and expiry notifications for trials';

    public function handle(): int
    {
        $soonCount = $this->sendEndingSoonReminders();
        $expiredCount = $this->sendExpiryNotifications();

        $this->info("Processed {$soonCount} ending-soon reminder(s) and {$expiredCount} expiry notification(s).");
        return self::SUCCESS;
    }

    protected function sendEndingSoonReminders(): int
    {
        $count = 0;
        foreach (DemoRequest::needsEndingSoonNotification()->get() as $trial) {
            try {
                if ($trial->email) {
                    Mail::to($trial->email)->send(new TrialEndingSoonMail($trial));
                }
                $trial->update([
                    'trial_ending_soon_notified' => true,
                    'trial_ending_soon_notified_at' => now(),
                ]);
                $count++;
            } catch (\Throwable $e) {
                $this->error("Ending-soon mail failed for trial #{$trial->id}: " . $e->getMessage());
            }
        }
        return $count;
    }

    protected function sendExpiryNotifications(): int
    {
        $count = 0;
        foreach (DemoRequest::needsExpiryNotification()->get() as $trial) {
            try {
                if ($trial->email) {
                    Mail::to($trial->email)->send(new TrialExpiredMail($trial));
                }
                $adminEmail = config('mail.from.address');
                if ($adminEmail && $adminEmail !== 'hello@example.com') {
                    Mail::to($adminEmail)->send(new TrialExpiredMail($trial, true));
                }
                $trial->update([
                    'trial_status' => 'expired',
                    'trial_expiry_notified' => true,
                    'trial_expiry_notified_at' => now(),
                    'admin_reminder_seen' => false,
                ]);
                $count++;
            } catch (\Throwable $e) {
                $this->error("Expiry mail failed for trial #{$trial->id}: " . $e->getMessage());
            }
        }
        return $count;
    }
}
