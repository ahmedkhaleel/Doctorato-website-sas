<?php

namespace Tests\Feature;

use App\Listeners\LogEmailFailure;
use App\Models\EmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\JobFailed;
use Tests\TestCase;

/**
 * Phase 24 — JobFailed bridge to EmailLog. Covers:
 *   - A SendQueuedMailable failure flips an existing 'sending' row
 *     to 'failed' with the exception message
 *   - Same but for a 'queued' precursor row
 *   - No precursor row → a fresh 'failed' tombstone is created
 *   - Long error messages are truncated to 1KB
 *   - Non-mail job failures are silently ignored (no row touched)
 *   - Mailable class FQCN is extracted from the payload
 *
 * We construct fake JobFailed events by stubbing the Job interface
 * with a minimal anonymous class instead of dispatching real queue
 * jobs — much faster and avoids needing a real failed_jobs row.
 */
class EmailFailureBridgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_flips_sending_row_to_failed(): void
    {
        EmailLog::create([
            'mailable_class' => 'App\\Mail\\TrialDripMail',
            'subject' => 'Hi',
            'hashed_recipient' => EmailLog::hashEmail('alice@x.com'),
            'recipient_display' => 'a***@x.com',
            'status' => EmailLog::STATUS_SENDING,
            'queued_at' => now()->subSeconds(5),
        ]);

        $event = $this->failedJobEvent('alice@x.com', 'SMTP refused: 550 spam');
        (new LogEmailFailure())->handle($event);

        $row = EmailLog::first();
        $this->assertSame(EmailLog::STATUS_FAILED, $row->status);
        $this->assertStringContainsString('SMTP refused', $row->error);
        $this->assertNotNull($row->failed_at);
    }

    public function test_flips_queued_row_to_failed(): void
    {
        EmailLog::create([
            'mailable_class' => 'App\\Mail\\TrialDripMail',
            'subject' => 'Hi',
            'hashed_recipient' => EmailLog::hashEmail('bob@x.com'),
            'recipient_display' => 'b***@x.com',
            'status' => EmailLog::STATUS_QUEUED,
            'queued_at' => now()->subSeconds(5),
        ]);

        $event = $this->failedJobEvent('bob@x.com', 'Connection refused');
        (new LogEmailFailure())->handle($event);

        $row = EmailLog::first();
        $this->assertSame(EmailLog::STATUS_FAILED, $row->status);
    }

    public function test_creates_tombstone_when_no_precursor(): void
    {
        // No EmailLog row exists yet — the worker logged the failure
        // before MessageSending fired. We still want a paper trail.
        $event = $this->failedJobEvent('charlie@x.com', 'Mailer DSN missing');
        (new LogEmailFailure())->handle($event);

        $row = EmailLog::first();
        $this->assertNotNull($row);
        $this->assertSame(EmailLog::STATUS_FAILED, $row->status);
        $this->assertSame(EmailLog::hashEmail('charlie@x.com'), $row->hashed_recipient);
        $this->assertSame('c***@x.com', $row->recipient_display);
    }

    public function test_long_error_message_is_truncated(): void
    {
        $longError = str_repeat('error-trace-line ', 200); // ~3.4KB

        $event = $this->failedJobEvent('eve@x.com', $longError);
        (new LogEmailFailure())->handle($event);

        $row = EmailLog::first();
        $this->assertLessThanOrEqual(1000, mb_strlen($row->error));
    }

    public function test_non_mail_job_is_ignored(): void
    {
        $event = $this->failedJobEvent(
            'dave@x.com', 'whatever',
            commandName: 'App\\Jobs\\SomeOtherJob',
        );
        (new LogEmailFailure())->handle($event);

        $this->assertSame(0, EmailLog::count(), 'Non-mail jobs must NOT touch email_logs');
    }

    public function test_extracts_mailable_class_from_payload(): void
    {
        $event = $this->failedJobEvent('frank@x.com', 'fail', mailableClass: 'App\\Mail\\CustomerLoginLink');
        (new LogEmailFailure())->handle($event);

        $row = EmailLog::first();
        $this->assertSame('App\\Mail\\CustomerLoginLink', $row->mailable_class);
    }

    // ---- helpers ----

    /**
     * Build a fake JobFailed event with a payload shaped like
     * Laravel's SendQueuedMailable serialisation. We embed the
     * recipient + mailable class in the `command` string so the
     * listener's regex extraction has something to match.
     */
    protected function failedJobEvent(
        string $to,
        string $errorMessage,
        string $commandName = \Illuminate\Mail\SendQueuedMailable::class,
        string $mailableClass = 'App\\Mail\\TrialDripMail',
    ): JobFailed {
        $command = 'O:35:"' . $mailableClass . '":1:{s:2:"to";a:1:{i:0;a:1:{s:7:"address";s:'
            . strlen($to) . ':"' . $to . '";}}}';

        $job = new class($commandName, $command) implements \Illuminate\Contracts\Queue\Job {
            public function __construct(public string $commandName, public string $command) {}
            public function payload(): array {
                return ['data' => ['commandName' => $this->commandName, 'command' => $this->command]];
            }
            public function getName(): string { return $this->commandName; }
            // Stubs to satisfy interface.
            public function uuid() { return 'fake-uuid'; }
            public function fire() {}
            public function release($delay = 0) {}
            public function isReleased() { return false; }
            public function delete() {}
            public function isDeleted() { return false; }
            public function isDeletedOrReleased() { return false; }
            public function attempts() { return 1; }
            public function hasFailed() { return true; }
            public function markAsFailed() {}
            public function fail($e = null) {}
            public function maxTries() { return null; }
            public function maxExceptions() { return null; }
            public function backoff() { return null; }
            public function retryUntil() { return null; }
            public function timeout() { return null; }
            public function getConnectionName(): string { return 'database'; }
            public function getQueue(): string { return 'default'; }
            public function getRawBody(): string { return ''; }
            public function getJobId(): string { return '1'; }
            public function resolveName(): string { return $this->commandName; }
            public function resolveQueuedJobClass(): string { return $this->commandName; }
        };

        return new JobFailed('database', $job, new \RuntimeException($errorMessage));
    }
}
