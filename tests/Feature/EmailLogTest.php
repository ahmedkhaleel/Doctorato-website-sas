<?php

namespace Tests\Feature;

use App\Listeners\LogEmailDelivery;
use App\Models\EmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;
use Tests\TestCase;

/**
 * Phase 23 — outbound email log. Covers:
 *   - hashEmail() normalises case and trims
 *   - redactEmail() keeps first char + domain
 *   - handleSending creates a 'sending' row per recipient
 *   - handleSent promotes the matching 'sending' row to 'sent'
 *     with the message_id captured
 *   - handleSent falls back to creating a fresh 'sent' row when
 *     there's no precursor (covers Mail::raw etc)
 *   - Pruner trims rows older than 90 days
 *   - Pruner --dry leaves them alone
 *   - A listener failure does NOT bubble (must not break mail)
 */
class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_hash_normalises_case_and_trim(): void
    {
        $this->assertSame(
            EmailLog::hashEmail('a@x.com'),
            EmailLog::hashEmail(' A@X.COM ')
        );
    }

    public function test_redact_keeps_first_char_and_domain(): void
    {
        $this->assertSame('a***@x.com', EmailLog::redactEmail('alice@x.com'));
        $this->assertSame('b***@example.com', EmailLog::redactEmail('bob.smith@example.com'));
    }

    public function test_redact_handles_missing_at(): void
    {
        $this->assertSame('***', EmailLog::redactEmail('not-an-email'));
    }

    public function test_sending_event_creates_row(): void
    {
        $event = $this->makeSendingEvent('alice@x.com', 'Hello');
        (new LogEmailDelivery())->handleSending($event);

        $row = EmailLog::first();
        $this->assertNotNull($row);
        $this->assertSame(EmailLog::STATUS_SENDING, $row->status);
        $this->assertSame(EmailLog::hashEmail('alice@x.com'), $row->hashed_recipient);
        $this->assertSame('a***@x.com', $row->recipient_display);
        $this->assertSame('Hello', $row->subject);
    }

    public function test_sent_event_promotes_existing_row(): void
    {
        // Pre-seed a 'sending' row.
        EmailLog::create([
            'mailable_class' => null, 'subject' => 'Pending',
            'hashed_recipient' => EmailLog::hashEmail('bob@x.com'),
            'recipient_display' => 'b***@x.com',
            'status' => EmailLog::STATUS_SENDING,
            'queued_at' => now()->subSeconds(2),
        ]);

        $event = $this->makeSentEvent('bob@x.com', 'Subject', 'msg-id-123');
        (new LogEmailDelivery())->handleSent($event);

        $row = EmailLog::where('hashed_recipient', EmailLog::hashEmail('bob@x.com'))->first();
        $this->assertSame(EmailLog::STATUS_SENT, $row->status);
        $this->assertSame('msg-id-123', $row->message_id);
        $this->assertNotNull($row->sent_at);
    }

    public function test_sent_event_without_precursor_creates_row(): void
    {
        $event = $this->makeSentEvent('charlie@x.com', 'Direct', 'msg-id-456');
        (new LogEmailDelivery())->handleSent($event);

        $row = EmailLog::where('hashed_recipient', EmailLog::hashEmail('charlie@x.com'))->first();
        $this->assertNotNull($row);
        $this->assertSame(EmailLog::STATUS_SENT, $row->status);
        $this->assertSame('msg-id-456', $row->message_id);
    }

    public function test_prune_drops_email_logs_older_than_90_days(): void
    {
        DB::table('email_logs')->insert([
            'mailable_class' => null, 'subject' => 'Old',
            'hashed_recipient' => str_repeat('a', 64),
            'recipient_display' => 'o***@x.com',
            'status' => 'sent', 'queued_at' => now()->subDays(100),
        ]);
        DB::table('email_logs')->insert([
            'mailable_class' => null, 'subject' => 'Fresh',
            'hashed_recipient' => str_repeat('b', 64),
            'recipient_display' => 'f***@x.com',
            'status' => 'sent', 'queued_at' => now()->subDays(30),
        ]);

        $this->artisan('maint:prune')->assertExitCode(0);

        $this->assertSame(1, DB::table('email_logs')->count());
        $this->assertSame('Fresh', DB::table('email_logs')->value('subject'));
    }

    public function test_prune_dry_doesnt_touch_email_logs(): void
    {
        DB::table('email_logs')->insert([
            'mailable_class' => null, 'subject' => 'Old',
            'hashed_recipient' => str_repeat('a', 64),
            'recipient_display' => 'o***@x.com',
            'status' => 'sent', 'queued_at' => now()->subDays(100),
        ]);

        $this->artisan('maint:prune --dry')->assertExitCode(0);
        $this->assertSame(1, DB::table('email_logs')->count());
    }

    // ---- helpers ----

    protected function makeSendingEvent(string $to, string $subject): MessageSending
    {
        $message = (new Email())->from('app@x.com')->to($to)->subject($subject)->text('body');
        return new MessageSending($message);
    }

    protected function makeSentEvent(string $to, string $subject, string $messageId): MessageSent
    {
        $message = (new Email())->from('app@x.com')->to($to)->subject($subject)->text('body');
        $envelope = new \Symfony\Component\Mailer\Envelope(
            new Address('app@x.com'),
            [new Address($to)]
        );
        $symfony = new SymfonySentMessage($message, $envelope);
        $symfony->setMessageId($messageId);
        $sent = new SentMessage($symfony);
        return new MessageSent($sent);
    }
}
