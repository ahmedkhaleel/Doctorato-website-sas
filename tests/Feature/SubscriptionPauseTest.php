<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Models\PricingPlan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 20 — customer-initiated subscription pause. Covers:
 *   - isPaused() reads paused_at correctly
 *   - Pause command sets both pause cols
 *   - Pause refuses non-active subs
 *   - Pause refuses an already-paused sub
 *   - Unpause clears both cols
 *   - Unpause refuses a non-paused sub
 *   - subs:auto-resume clears subs whose paused_until <= now
 *   - subs:auto-resume leaves subs whose paused_until > now alone
 *   - Days validation: 7 minimum, 90 maximum
 */
class SubscriptionPauseTest extends TestCase
{
    use RefreshDatabase;

    protected function plan(): PricingPlan
    {
        return PricingPlan::firstOrCreate(
            ['slug' => 'standard'],
            [
                'name_ar' => 'قياسي', 'name_en' => 'Standard',
                'description_ar' => '-', 'description_en' => '-',
                'features_ar' => '[]', 'features_en' => '[]',
                'modules_included' => '[]', 'support_level' => 'standard',
                'monthly_price' => 100, 'yearly_price' => 1000,
                'currency' => 'USD', 'is_active' => true,
            ]
        );
    }

    protected function makeSubAndAuth(array $overrides = []): array
    {
        $demo = DemoRequest::create([
            'full_name' => 'Pause Test', 'email' => 'pause+' . uniqid() . '@x.com',
            'phone' => '+1234567890', 'clinic_name' => 'X',
            'specialty' => 'general', 'country' => 'EG',
        ]);
        $sub = Subscription::create(array_merge([
            'pricing_plan_id' => $this->plan()->id,
            'demo_request_id' => $demo->id,
            'customer_name' => 'X', 'customer_email' => $demo->email, 'customer_phone' => '+1',
            'clinic_name' => 'X', 'country' => 'EG',
            'billing_cycle' => 'monthly', 'amount' => 100, 'currency' => 'USD',
            'status' => 'active', 'starts_at' => now(), 'ends_at' => now()->addMonth(),
        ], $overrides));

        $this->withSession([
            'portal.customer_id' => $demo->id,
            'portal.email' => $demo->email,
        ]);

        return [$demo, $sub];
    }

    // -------- model helpers --------

    public function test_is_paused_reads_paused_at(): void
    {
        [, $sub] = $this->makeSubAndAuth();
        $this->assertFalse($sub->isPaused());
        $sub->paused_at = now();
        $this->assertTrue($sub->isPaused());
    }

    // -------- pause endpoint --------

    public function test_pause_sets_both_columns(): void
    {
        [, $sub] = $this->makeSubAndAuth();
        $this->post("/portal/subscriptions/{$sub->id}/pause", ['days' => 14])
            ->assertRedirect();

        $fresh = $sub->fresh();
        $this->assertNotNull($fresh->paused_at);
        $this->assertNotNull($fresh->paused_until);
        $this->assertEqualsWithDelta(now()->addDays(14)->timestamp, $fresh->paused_until->timestamp, 5);
    }

    public function test_pause_refuses_non_active_subscription(): void
    {
        [, $sub] = $this->makeSubAndAuth(['status' => 'cancelled']);
        $this->post("/portal/subscriptions/{$sub->id}/pause", ['days' => 14])
            ->assertSessionHasErrors('subscription');
        $this->assertNull($sub->fresh()->paused_at);
    }

    public function test_pause_refuses_already_paused(): void
    {
        [, $sub] = $this->makeSubAndAuth(['paused_at' => now(), 'paused_until' => now()->addDays(7)]);
        $this->post("/portal/subscriptions/{$sub->id}/pause", ['days' => 14])
            ->assertSessionHasErrors('subscription');
    }

    public function test_pause_validates_days_range(): void
    {
        [, $sub] = $this->makeSubAndAuth();
        $this->post("/portal/subscriptions/{$sub->id}/pause", ['days' => 3])
            ->assertSessionHasErrors('days');
        $this->post("/portal/subscriptions/{$sub->id}/pause", ['days' => 100])
            ->assertSessionHasErrors('days');
    }

    // -------- unpause endpoint --------

    public function test_unpause_clears_both_columns(): void
    {
        [, $sub] = $this->makeSubAndAuth(['paused_at' => now(), 'paused_until' => now()->addDays(7)]);
        $this->post("/portal/subscriptions/{$sub->id}/unpause")
            ->assertRedirect();
        $fresh = $sub->fresh();
        $this->assertNull($fresh->paused_at);
        $this->assertNull($fresh->paused_until);
    }

    public function test_unpause_refuses_non_paused(): void
    {
        [, $sub] = $this->makeSubAndAuth();
        $this->post("/portal/subscriptions/{$sub->id}/unpause")
            ->assertSessionHasErrors('subscription');
    }

    // -------- auto-resume cron --------

    public function test_auto_resume_clears_expired_pauses(): void
    {
        [, $sub] = $this->makeSubAndAuth([
            'paused_at' => now()->subDays(8),
            'paused_until' => now()->subDay(),
        ]);

        $this->artisan('subs:auto-resume')->assertExitCode(0);

        $fresh = $sub->fresh();
        $this->assertNull($fresh->paused_at);
        $this->assertNull($fresh->paused_until);
    }

    public function test_auto_resume_leaves_future_pauses_alone(): void
    {
        [, $sub] = $this->makeSubAndAuth([
            'paused_at' => now()->subDay(),
            'paused_until' => now()->addDays(7),
        ]);

        $this->artisan('subs:auto-resume')->assertExitCode(0);

        $fresh = $sub->fresh();
        $this->assertNotNull($fresh->paused_at);
        $this->assertNotNull($fresh->paused_until);
    }
}
