<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Strip PII from a production DB dump so it can be safely used in
 * staging, dev, or for engineering interview tests.
 *
 * Use case:
 *   1. mysqldump production → restore into a staging schema.
 *   2. php artisan db:anonymise --force
 *   3. The schema now has all the relational shape + row counts
 *      a developer needs, but every email/phone/name/note is fake.
 *
 * What gets anonymised (and why):
 *   demo_requests     full_name, email, phone, notes, admin_notes,
 *                     subdomain, referral_source, referred_by_code
 *   subscriptions     customer_name, customer_email, customer_phone
 *   activity_logs     description (in case it embeds a real email)
 *   email_logs        hashed_recipient is already hashed; we
 *                     blank recipient_display + nuke the table —
 *                     the message-id correlation is meaningless
 *                     against a fresh anonymised dataset
 *   webhook_events    payload field — Paymob payloads include
 *                     billing_data with the customer name + email
 *                     in the body
 *   login_attempts    truncated entirely (the hashes are reversible
 *                     against a known email list)
 *   customer_login_tokens  truncated (any in-flight magic link
 *                          would auth a real customer otherwise)
 *
 * What we DON'T touch:
 *   - Pricing plans, currencies, FAQ, blog content, testimonials,
 *     case studies, settings, users (admin accounts have separate
 *     credential management — anonymising would lock everyone out).
 *
 * Hard refusal:
 *   - Refuses to run unless APP_ENV != 'production'. The --force
 *     flag still respects this. Production data should NEVER be
 *     anonymised in place; this command is for downstream copies.
 *   - Also refuses if APP_URL contains 'doctorato.com' (defence in
 *     depth — if a misconfigured staging box has the prod URL we
 *     won't shred it).
 */
class AnonymiseDatabase extends Command
{
    protected $signature = 'db:anonymise
        {--force : Skip the interactive confirmation prompt}
        {--dry : Show row counts without writing}';

    protected $description = 'Replace PII columns with synthetic data so the schema can be safely copied to staging or dev.';

    /** Deterministic fake-name helpers — no faker dep needed. */
    protected const FIRST_NAMES = ['Sarah', 'Omar', 'Layla', 'Karim', 'Mona', 'Tariq', 'Yasmin', 'Hassan', 'Noor', 'Adam'];
    protected const LAST_NAMES = ['Mahmoud', 'Saleh', 'Hadi', 'Khalil', 'Nasr', 'Said', 'Habib', 'Aziz', 'Farouk', 'Mansour'];
    protected const CLINIC_PREFIXES = ['Care', 'Health', 'Plus', 'Smile', 'Bright', 'Modern', 'Royal', 'Family'];

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Refuses to run in production environment. Run against a downstream copy.');
            return self::FAILURE;
        }

        $appUrl = (string) config('app.url');
        if (str_contains($appUrl, 'doctorato.com')) {
            $this->error("APP_URL contains 'doctorato.com' — refusing to anonymise. Override APP_URL first if you're sure.");
            return self::FAILURE;
        }

        $dry = $this->option('dry');
        if (!$dry && !$this->option('force')) {
            if (!$this->confirm('This OVERWRITES email/phone/name columns across the DB. Continue?')) {
                $this->warn('Cancelled.');
                return self::FAILURE;
            }
        }

        $this->info('Anonymising — env=' . app()->environment() . ', app_url=' . $appUrl);

        $this->processDemoRequests($dry);
        $this->processSubscriptions($dry);
        $this->processActivityLogs($dry);
        $this->processEmailLogs($dry);
        $this->processWebhookEvents($dry);
        $this->truncateLoginAttempts($dry);
        $this->truncateLoginTokens($dry);

        $this->info($dry ? 'Dry run complete — nothing written.' : 'Anonymisation complete.');
        return self::SUCCESS;
    }

    protected function processDemoRequests(bool $dry): void
    {
        if (!Schema::hasTable('demo_requests')) return;

        $count = DB::table('demo_requests')->count();
        $this->line("  demo_requests: {$count} row(s)");
        if ($dry || $count === 0) return;

        // chunkById to keep memory flat on large tables. Mutate the
        // row, push back, move on.
        DB::table('demo_requests')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $first = self::FIRST_NAMES[$row->id % count(self::FIRST_NAMES)];
                $last = self::LAST_NAMES[($row->id * 7) % count(self::LAST_NAMES)];
                $clinicPrefix = self::CLINIC_PREFIXES[($row->id * 3) % count(self::CLINIC_PREFIXES)];

                DB::table('demo_requests')->where('id', $row->id)->update([
                    'full_name' => "{$first} {$last}",
                    'email' => "demo+{$row->id}@anonymised.invalid",
                    'phone' => '+1000000' . str_pad((string) $row->id, 4, '0', STR_PAD_LEFT),
                    'clinic_name' => "{$clinicPrefix} Clinic #{$row->id}",
                    'notes' => null,
                    'admin_notes' => null,
                    'subdomain' => null,
                    'referral_source' => null,
                    'referred_by_code' => null,
                ]);
            }
        });
    }

    protected function processSubscriptions(bool $dry): void
    {
        if (!Schema::hasTable('subscriptions')) return;

        $count = DB::table('subscriptions')->count();
        $this->line("  subscriptions: {$count} row(s)");
        if ($dry || $count === 0) return;

        DB::table('subscriptions')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $first = self::FIRST_NAMES[$row->id % count(self::FIRST_NAMES)];
                $last = self::LAST_NAMES[($row->id * 7) % count(self::LAST_NAMES)];

                DB::table('subscriptions')->where('id', $row->id)->update([
                    'customer_name' => "{$first} {$last}",
                    'customer_email' => "sub+{$row->id}@anonymised.invalid",
                    'customer_phone' => '+1000001' . str_pad((string) $row->id, 4, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    /**
     * activity_logs.description routinely embeds real customer
     * emails — e.g. 'Saved demo from jane@x.com'. Run our existing
     * PII scrubber across the column so the wording stays
     * meaningful but the addresses get redacted.
     */
    protected function processActivityLogs(bool $dry): void
    {
        if (!Schema::hasTable('activity_logs')) return;

        $count = DB::table('activity_logs')->count();
        $this->line("  activity_logs: {$count} row(s) (description scrubbed)");
        if ($dry || $count === 0) return;

        $scrubber = new \App\Logging\PiiScrubbingProcessor();
        DB::table('activity_logs')->orderBy('id')->chunkById(500, function ($rows) use ($scrubber) {
            foreach ($rows as $row) {
                if (!$row->description) continue;
                $clean = $scrubber->__invoke(new \Monolog\LogRecord(
                    datetime: new \DateTimeImmutable(),
                    channel: 'anon', level: \Monolog\Level::Info,
                    message: $row->description, context: [],
                ))->message;
                if ($clean !== $row->description) {
                    DB::table('activity_logs')->where('id', $row->id)->update(['description' => $clean]);
                }
            }
        });
    }

    protected function processEmailLogs(bool $dry): void
    {
        if (!Schema::hasTable('email_logs')) return;
        $count = DB::table('email_logs')->count();
        $this->line("  email_logs: {$count} row(s) (truncated)");
        if ($dry || $count === 0) return;
        DB::table('email_logs')->truncate();
    }

    protected function processWebhookEvents(bool $dry): void
    {
        if (!Schema::hasTable('webhook_events')) return;
        $count = DB::table('webhook_events')->count();
        $this->line("  webhook_events: {$count} row(s) (payload redacted)");
        if ($dry || $count === 0) return;

        DB::table('webhook_events')->orderBy('id')->chunkById(200, function ($rows) {
            foreach ($rows as $row) {
                DB::table('webhook_events')->where('id', $row->id)->update([
                    'payload' => json_encode(['anonymised' => true, 'original_event_id' => $row->id]),
                    'response_body' => null,
                ]);
            }
        });
    }

    protected function truncateLoginAttempts(bool $dry): void
    {
        if (!Schema::hasTable('login_attempts')) return;
        $count = DB::table('login_attempts')->count();
        $this->line("  login_attempts: {$count} row(s) (truncated)");
        if ($dry || $count === 0) return;
        DB::table('login_attempts')->truncate();
    }

    protected function truncateLoginTokens(bool $dry): void
    {
        if (!Schema::hasTable('customer_login_tokens')) return;
        $count = DB::table('customer_login_tokens')->count();
        $this->line("  customer_login_tokens: {$count} row(s) (truncated)");
        if ($dry || $count === 0) return;
        DB::table('customer_login_tokens')->truncate();
    }
}
