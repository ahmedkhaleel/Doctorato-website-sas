<?php

namespace App\Console\Commands;

use App\Mail\TrialExpiredMail;
use App\Models\DemoRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckExpiringTrials extends Command
{
    protected $signature = 'trials:check';

    protected $description = 'Check expiring trials, mark them as expired, and notify users + admin';

    public function handle(): int
    {
        $expired = DemoRequest::needsExpiryNotification()->get();

        $count = 0;
        foreach ($expired as $demo) {
            try {
                // Send notification email to client
                if ($demo->email) {
                    Mail::to($demo->email)->send(new TrialExpiredMail($demo));
                }

                // Notify admin
                $adminEmail = config('mail.from.address');
                if ($adminEmail && $adminEmail !== 'hello@example.com') {
                    Mail::to($adminEmail)->send(new TrialExpiredMail($demo, true));
                }

                $demo->update([
                    'trial_status' => 'expired',
                    'trial_expiry_notified' => true,
                    'trial_expiry_notified_at' => now(),
                    'admin_reminder_seen' => false,
                ]);
                $count++;
            } catch (\Throwable $e) {
                $this->error("Failed for demo #{$demo->id}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$count} expired trial(s).");
        return self::SUCCESS;
    }
}
