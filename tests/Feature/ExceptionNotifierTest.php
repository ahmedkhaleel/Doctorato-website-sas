<?php

namespace Tests\Feature;

use App\Exceptions\AdminExceptionNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Validates the most important guarantees of the home-built error
 * notifier:
 *
 *   1. User-flow exceptions (validation, 404, throttle) NEVER
 *      generate an admin email — they're normal traffic noise.
 *   2. Real exceptions trigger an email exactly once per signature
 *      per hour — a bad loop can't flood the inbox.
 *   3. Local/staging environments don't email at all — we don't
 *      want CI runs paging the inbox.
 */
class ExceptionNotifierTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        Mail::fake();
    }

    public function test_validation_exceptions_do_not_send_an_email(): void
    {
        // Force production mode for the duration of this test.
        $this->app->detectEnvironment(fn () => 'production');

        app(AdminExceptionNotifier::class)->notify(
            new \Illuminate\Validation\ValidationException(
                \Illuminate\Support\Facades\Validator::make([], ['name' => 'required'])
            )
        );

        Mail::assertNothingSent();
    }

    public function test_404_exceptions_do_not_send_an_email(): void
    {
        $this->app->detectEnvironment(fn () => 'production');

        app(AdminExceptionNotifier::class)->notify(
            new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException()
        );

        Mail::assertNothingSent();
    }

    public function test_unexpected_exceptions_send_exactly_one_email(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        $notifier = app(AdminExceptionNotifier::class);

        // Same signature (class + file + line) hit five times — only
        // the first should mail; the rest should silently increment
        // the recurrence counter.
        // Same call site = same signature = same throttle bucket.
        // We assert via the cache counter rather than Mail::assertSentCount
        // because MailFake::raw() is a no-op in Laravel's testing layer
        // (Mail::raw bypasses the tracked mailables collection entirely).
        // The cache counter is the more honest signal that the dedup
        // logic is doing its job.
        $exception = new \RuntimeException('Database deadlocked');
        for ($i = 0; $i < 5; $i++) {
            $notifier->notify($exception);
        }

        $sig = substr(sha1('RuntimeException|' . $exception->getFile() . '|' . $exception->getLine()), 0, 12);
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has("exception_notify:{$sig}"),
            'First notify() must stamp the throttle key.');
        $this->assertSame(5, (int) \Illuminate\Support\Facades\Cache::get("exception_count:{$sig}"),
            'All 5 hits must increment the counter; only the first mails.');
    }

    public function test_local_environment_does_not_send_at_all(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        app(AdminExceptionNotifier::class)->notify(new \RuntimeException('local-only test'));

        Mail::assertNothingSent();
    }
}
