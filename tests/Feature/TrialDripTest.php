<?php

namespace Tests\Feature;

use App\Mail\TrialDripMail;
use App\Models\DemoRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Covers the trial:drip artisan command end-to-end:
 *   - Sends welcome on day 0 and stamps step=1
 *   - Sends tour on day 3 and stamps step=2
 *   - Sends case study on day 7 and stamps step=3
 *   - Doesn't re-send an already-sent step (idempotency)
 *   - Skips opted-out trials
 *   - Skips expired/cancelled trials
 *   - --dry doesn't queue anything or mutate the DB
 *   - Each run advances at most ONE step per trial (so a brand-new
 *     trial doesn't get all 3 emails on day 0 if cron was paused)
 */
class TrialDripTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    /** Helper: build a trial at an arbitrary age in days. */
    protected function makeTrial(int $ageDays, array $overrides = []): DemoRequest
    {
        return DemoRequest::create(array_merge([
            'full_name' => 'Test Doctor',
            'email' => 'test+' . uniqid() . '@example.com',
            'clinic_name' => 'Test Clinic',
            'phone' => '+1234567890',
            'specialty' => 'general',
            'country' => 'AE',
            'trial_started_at' => Carbon::now()->subDays($ageDays),
            'trial_ends_at' => Carbon::now()->subDays($ageDays)->addDays(14),
            'trial_status' => 'active',
            'trial_drip_step' => 0,
            'marketing_opt_in' => true,
        ], $overrides));
    }

    public function test_brand_new_trial_gets_welcome_only(): void
    {
        $trial = $this->makeTrial(0);

        $this->artisan('trials:drip')->assertExitCode(0);

        Mail::assertQueued(TrialDripMail::class, fn ($m) => $m->trial->id === $trial->id && $m->step === 1);
        Mail::assertQueuedCount(1);
        $this->assertSame(1, $trial->fresh()->trial_drip_step);
        $this->assertNotNull($trial->fresh()->trial_drip_last_sent_at);
    }

    public function test_three_day_old_trial_advances_to_tour(): void
    {
        $trial = $this->makeTrial(3, ['trial_drip_step' => 1]);

        $this->artisan('trials:drip')->assertExitCode(0);

        Mail::assertQueued(TrialDripMail::class, fn ($m) => $m->step === 2);
        $this->assertSame(2, $trial->fresh()->trial_drip_step);
    }

    public function test_seven_day_old_trial_advances_to_case_study(): void
    {
        $trial = $this->makeTrial(7, ['trial_drip_step' => 2]);

        $this->artisan('trials:drip')->assertExitCode(0);

        Mail::assertQueued(TrialDripMail::class, fn ($m) => $m->step === 3);
        $this->assertSame(3, $trial->fresh()->trial_drip_step);
    }

    public function test_completed_drip_is_not_resent(): void
    {
        $trial = $this->makeTrial(10, ['trial_drip_step' => 3]);

        $this->artisan('trials:drip')->assertExitCode(0);

        Mail::assertNothingQueued();
        $this->assertSame(3, $trial->fresh()->trial_drip_step);
    }

    public function test_opted_out_trial_is_skipped(): void
    {
        $trial = $this->makeTrial(0, ['marketing_opt_in' => false]);

        $this->artisan('trials:drip')->assertExitCode(0);

        Mail::assertNothingQueued();
        $this->assertSame(0, $trial->fresh()->trial_drip_step);
    }

    public function test_expired_trial_is_skipped(): void
    {
        $trial = $this->makeTrial(0, ['trial_status' => 'expired']);

        $this->artisan('trials:drip')->assertExitCode(0);

        Mail::assertNothingQueued();
    }

    public function test_single_run_advances_at_most_one_step(): void
    {
        // 10 days old + step still 0 (cron was down). Expect step 1
        // only — NOT a flood of all 3 emails at once.
        $trial = $this->makeTrial(10, ['trial_drip_step' => 0]);

        $this->artisan('trials:drip')->assertExitCode(0);

        Mail::assertQueuedCount(1);
        Mail::assertQueued(TrialDripMail::class, fn ($m) => $m->step === 1);
        $this->assertSame(1, $trial->fresh()->trial_drip_step);
    }

    public function test_dry_run_doesnt_queue_or_mutate(): void
    {
        $trial = $this->makeTrial(0);

        $this->artisan('trials:drip --dry')->assertExitCode(0);

        Mail::assertNothingQueued();
        $this->assertSame(0, $trial->fresh()->trial_drip_step);
        $this->assertNull($trial->fresh()->trial_drip_last_sent_at);
    }

    public function test_invalid_step_throws(): void
    {
        $trial = $this->makeTrial(0);

        $this->expectException(\InvalidArgumentException::class);
        new TrialDripMail($trial, 99);
    }
}
