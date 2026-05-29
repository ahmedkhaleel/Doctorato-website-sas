<?php

namespace Tests\Feature;

use App\Logging\PiiScrubbingProcessor;
use App\Models\DemoRequest;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Services\GdprService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Monolog\Level;
use Monolog\LogRecord;
use Tests\TestCase;

/**
 * Phase 14 — GDPR rights + PII log scrubbing. Covers:
 *   - Email redaction keeps first char + domain
 *   - Phone redaction keeps last 4 digits
 *   - Credit-card pattern replaced wholesale
 *   - Sensitive keys (password, secret, token) blanked
 *   - Timestamps and IDs NOT mistaken for phone numbers
 *   - GdprService::export collates all subject data
 *   - GdprService::erase overwrites PII but keeps row count
 *   - Erase wipes login_attempts for that email
 *   - Erase creates a tombstone activity_log row
 */
class GdprAndScrubberTest extends TestCase
{
    use RefreshDatabase;

    // -------- PII Scrubber --------

    public function test_scrubber_redacts_email(): void
    {
        $rec = $this->record('Sent magic link to alice.smith@example.com', []);
        $out = (new PiiScrubbingProcessor())($rec);
        $this->assertStringContainsString('a***@example.com', $out->message);
        $this->assertStringNotContainsString('alice.smith', $out->message);
    }

    public function test_scrubber_keeps_phone_last_four(): void
    {
        $rec = $this->record('Confirmed phone +20 100 555 1234', []);
        $out = (new PiiScrubbingProcessor())($rec);
        $this->assertStringContainsString('***1234', $out->message);
        $this->assertStringNotContainsString('555', $out->message);
    }

    public function test_scrubber_blanks_card_numbers(): void
    {
        $rec = $this->record('Saw card 4111 1111 1111 1111 in payload', []);
        $out = (new PiiScrubbingProcessor())($rec);
        $this->assertStringContainsString('[CARD]', $out->message);
        $this->assertStringNotContainsString('4111', $out->message);
    }

    public function test_scrubber_redacts_sensitive_keys(): void
    {
        $rec = $this->record('login attempt', [
            'email' => 'bob@example.com',
            'password' => 'hunter2',
            'recaptcha_token' => 'abc123',
            'nested' => ['secret' => 'shh'],
        ]);
        $out = (new PiiScrubbingProcessor())($rec);
        $this->assertSame('[REDACTED]', $out->context['password']);
        $this->assertSame('[REDACTED]', $out->context['recaptcha_token']);
        $this->assertSame('[REDACTED]', $out->context['nested']['secret']);
        $this->assertStringContainsString('b***@example.com', $out->context['email']);
    }

    public function test_scrubber_preserves_iso_timestamps(): void
    {
        $rec = $this->record('Event at 2026-05-30T14:23:11+0300 from user', []);
        $out = (new PiiScrubbingProcessor())($rec);
        // The phone regex must NOT swallow ISO timestamps.
        $this->assertStringContainsString('2026-05-30T14:23:11', $out->message);
    }

    // -------- GDPR Service --------

    public function test_export_returns_no_data_for_unknown_email(): void
    {
        $report = app(GdprService::class)->export('ghost@nowhere.com');
        $this->assertSame('no_data', $report['status']);
    }

    public function test_export_returns_subject_data(): void
    {
        $demo = DemoRequest::create([
            'full_name' => 'Real Customer',
            'email' => 'real@x.com',
            'phone' => '+201001234567',
            'clinic_name' => 'X',
            'specialty' => 'general',
            'country' => 'EG',
        ]);

        $report = app(GdprService::class)->export('real@x.com');
        $this->assertSame('ok', $report['status']);
        $this->assertSame('real@x.com', $report['subject']['demo_request']['email']);
        $this->assertSame('Real Customer', $report['subject']['demo_request']['full_name']);
    }

    public function test_erase_overwrites_pii_keeps_row(): void
    {
        $demo = DemoRequest::create([
            'full_name' => 'Will Disappear',
            'email' => 'erase@x.com',
            'phone' => '+201001234567',
            'clinic_name' => 'Clinic',
            'specialty' => 'general',
            'country' => 'EG',
            'notes' => 'sensitive note',
        ]);

        // Pre-seed a login_attempts row for the same email.
        DB::table('login_attempts')->insert([
            'email_hashed' => hash('sha256', 'erase@x.com'),
            'ip' => '1.1.1.1',
            'success' => false,
            'reason' => 'bad_password',
            'attempted_at' => now(),
        ]);

        $result = app(GdprService::class)->erase('erase@x.com', 'sar_request');
        $this->assertSame('ok', $result['status']);

        $row = DemoRequest::find($demo->id);
        $this->assertNotNull($row, 'demo_requests row must NOT be deleted (FKs).');
        $this->assertSame(GdprService::ERASED_NAME, $row->full_name);
        $this->assertStringStartsWith(GdprService::ERASED_EMAIL_PREFIX, $row->email);
        $this->assertStringEndsWith(GdprService::ERASED_EMAIL_DOMAIN, $row->email);
        // Phone column is NOT NULL in the schema, so erase overwrites
        // with the sentinel rather than nulling.
        $this->assertSame(GdprService::ERASED_NAME, $row->phone);
        $this->assertNull($row->notes);

        // Login attempts dropped.
        $this->assertSame(0, DB::table('login_attempts')
            ->where('email_hashed', hash('sha256', 'erase@x.com'))->count());

        // Tombstone activity log row created.
        $this->assertSame(1, DB::table('activity_logs')
            ->where('action', 'gdpr_delete')
            ->where('subject_id', $demo->id)
            ->count());
    }

    public function test_erase_scrubs_activity_log_descriptions(): void
    {
        $demo = DemoRequest::create([
            'full_name' => 'X',
            'email' => 'audit@x.com',
            'phone' => '+201001112222',
            'clinic_name' => 'X',
            'specialty' => 'general',
            'country' => 'EG',
        ]);

        DB::table('activity_logs')->insert([
            'action' => 'created',
            'description' => 'Saved demo from audit@x.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app(GdprService::class)->erase('audit@x.com');

        $row = DB::table('activity_logs')
            ->where('action', 'created')
            ->first();
        $this->assertStringNotContainsString('audit@x.com', $row->description);
        $this->assertStringContainsString('a***@x.com', $row->description);
    }

    protected function record(string $message, array $context): LogRecord
    {
        return new LogRecord(
            datetime: new \DateTimeImmutable(),
            channel: 'test',
            level: Level::Info,
            message: $message,
            context: $context,
        );
    }
}
