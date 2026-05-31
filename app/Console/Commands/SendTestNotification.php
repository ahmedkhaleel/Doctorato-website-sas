<?php

namespace App\Console\Commands;

use App\Mail\ContactAdminNotification;
use App\Mail\DemoAdminNotification;
use App\Models\ContactMessage;
use App\Models\DemoRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * One-shot diagnostic for the admin notification pipeline.
 *
 *   php artisan notify:test demo         → sends fake demo to demo@doctorato.com
 *   php artisan notify:test contact      → sends fake contact to info@doctorato.com
 *   php artisan notify:test demo --to=x@y.com   → override the target address
 *
 * Used to validate SMTP config without filling out the public form.
 * Logs full Mail driver output + the resolved 'to' address so you can
 * tell whether the issue is mail config or address routing.
 */
class SendTestNotification extends Command
{
    protected $signature = 'notify:test {type=demo : "demo" or "contact"} {--to= : Override the destination address}';
    protected $description = 'Send a test admin notification email so SMTP delivery can be verified.';

    public function handle(): int
    {
        $type = $this->argument('type');
        $override = $this->option('to');

        if (!in_array($type, ['demo', 'contact'], true)) {
            $this->error("Type must be 'demo' or 'contact'. Got: {$type}");
            return self::FAILURE;
        }

        $this->line('');
        $this->info("Mail driver: " . config('mail.default'));
        $this->info("From address: " . config('mail.from.address'));

        if ($type === 'demo') {
            $to = $override ?: config('notifications.demo_email', 'demo@doctorato.com');
            $this->info("Sending DEMO test → {$to}");

            // Build an in-memory model without saving it — so we don't pollute the DB.
            $fake = new DemoRequest([
                'clinic_name' => 'Test Clinic — notify:test',
                'full_name' => 'Diagnostic User',
                'email' => 'diagnostic@example.com',
                'phone' => '1000000000',
                'country_code' => '+20',
                'facility_type' => 'clinic',
                'doctors_count' => '2-5',
                'specialty' => 'general',
                'interested_modules' => ['emr', 'billing'],
                'referral_source' => 'diagnostic',
            ]);
            $fake->id = 0;
            $fake->created_at = now();

            try {
                Mail::to($to)->send(new DemoAdminNotification($fake));
                $this->info("✓ Sent demo notification to {$to}");
                return self::SUCCESS;
            } catch (\Throwable $e) {
                $this->error("✗ Send failed: " . $e->getMessage());
                return self::FAILURE;
            }
        }

        // contact
        $to = $override ?: config('notifications.contact_email', 'info@doctorato.com');
        $this->info("Sending CONTACT test → {$to}");

        $fake = new ContactMessage([
            'name' => 'Diagnostic User',
            'email' => 'diagnostic@example.com',
            'phone' => '1000000000',
            'subject' => 'Test from notify:test',
            'message' => 'This is a diagnostic message sent by the notify:test artisan command.',
        ]);
        $fake->id = 0;
        $fake->created_at = now();

        try {
            Mail::to($to)->send(new ContactAdminNotification($fake));
            $this->info("✓ Sent contact notification to {$to}");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("✗ Send failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
