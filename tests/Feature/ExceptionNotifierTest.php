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
        for ($i = 0; $i < 5; $i++) {
            $notifier->notify(new \RuntimeException('Database deadlocked'));
        }

        Mail::assertSentCount(1);
    }

    public function test_local_environment_does_not_send_at_all(): void
    {
        $this->app->detectEnvironment(fn () => 'local');

        app(AdminExceptionNotifier::class)->notify(new \RuntimeException('local-only test'));

        Mail::assertNothingSent();
    }
}
