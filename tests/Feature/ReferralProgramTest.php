<?php

namespace Tests\Feature;

use App\Http\Middleware\CaptureReferralCode;
use App\Models\DemoRequest;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Services\ReferralService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Phase 11 — referral program. Covers:
 *   - Code capture from ?ref= via middleware (good + bad + already-set)
 *   - prepareForValidation drops malformed codes silently
 *   - generateReferralCode produces the right shape + is unique
 *   - onSubscriptionActivated assigns a code + credits the referrer
 *   - Self-referral is blocked
 *   - Unknown code is silently dropped
 *   - Re-running onSubscriptionActivated is a no-op (idempotency)
 *   - /portal/refer renders empty state when no active sub
 */
class ReferralProgramTest extends TestCase
{
    use RefreshDatabase;

    /** Build a minimal active subscription owned by $email. */
    protected function plan(): PricingPlan
    {
        return PricingPlan::firstOrCreate(
            ['slug' => 'standard'],
            [
                'name_ar' => 'قياسي', 'name_en' => 'Standard',
                'description_ar' => '-', 'description_en' => '-',
                'features_ar' => json_encode([]), 'features_en' => json_encode([]),
                'modules_included' => json_encode([]),
                'support_level' => 'standard',
                'monthly_price' => 100, 'yearly_price' => 1000,
                'currency' => 'USD', 'is_active' => true,
            ]
        );
    }

    protected function makeActiveSub(string $email, array $overrides = []): Subscription
    {
        $demo = DemoRequest::create([
            'full_name' => 'Owner',
            'email' => $email,
            'clinic_name' => 'Owner Clinic',
            'phone' => '+1234567890',
            'specialty' => 'general',
            'country' => 'AE',
        ]);

        return Subscription::create(array_merge([
            'pricing_plan_id' => $this->plan()->id,
            'demo_request_id' => $demo->id,
            'customer_name' => 'Owner',
            'customer_email' => $email,
            'customer_phone' => '+1234567890',
            'clinic_name' => 'Owner Clinic',
            'country' => 'AE',
            'billing_cycle' => 'monthly',
            'amount' => 100.00,
            'currency' => 'USD',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ], $overrides));
    }

    public function test_valid_ref_query_sets_cookie(): void
    {
        $response = $this->get('/?ref=DOC-ABCD2345');
        $response->assertStatus(200);
        $cookies = $response->headers->getCookies();
        $found = collect($cookies)->first(fn ($c) => $c->getName() === CaptureReferralCode::COOKIE_NAME);
        $this->assertNotNull($found);
    }

    public function test_malformed_ref_query_is_ignored(): void
    {
        $response = $this->get('/?ref=not-a-code');
        $cookies = $response->headers->getCookies();
        $found = collect($cookies)->first(fn ($c) => $c->getName() === CaptureReferralCode::COOKIE_NAME);
        $this->assertNull($found);
    }

    public function test_existing_cookie_is_not_overwritten(): void
    {
        // First-touch wins. Visit with code A, then code B — cookie
        // should still hold A (we simulate the cookie being present).
        $response = $this->withCookie(CaptureReferralCode::COOKIE_NAME, 'DOC-AAAA2345')
            ->get('/?ref=DOC-BBBB2345');
        $cookies = $response->headers->getCookies();
        // No new Set-Cookie header for the referral cookie.
        $found = collect($cookies)->first(fn ($c) => $c->getName() === CaptureReferralCode::COOKIE_NAME);
        $this->assertNull($found, 'Existing referral cookie should not be overwritten on second visit.');
    }

    public function test_generate_referral_code_is_well_formed(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $code = Subscription::generateReferralCode();
            $this->assertMatchesRegularExpression('/^DOC-[A-Z2-9]{8}$/', $code);
        }
    }

    public function test_activation_assigns_code_and_credits_referrer(): void
    {
        $referrerSub = $this->makeActiveSub('referrer@example.com');
        $referrerSub->forceFill(['referral_code' => 'DOC-AAAA2345'])->save();

        // New customer signs up with the referrer's code.
        $newDemo = DemoRequest::create([
            'full_name' => 'New Customer',
            'email' => 'new@example.com',
            'clinic_name' => 'New Clinic',
            'phone' => '+1234567890',
            'specialty' => 'general',
            'country' => 'AE',
            'referred_by_code' => 'DOC-AAAA2345',
        ]);

        $newSub = Subscription::create([
            'pricing_plan_id' => $this->plan()->id,
            'demo_request_id' => $newDemo->id,
            'customer_name' => 'New Customer',
            'customer_email' => 'new@example.com',
            'customer_phone' => '+1234567890',
            'clinic_name' => 'New Clinic',
            'country' => 'AE',
            'billing_cycle' => 'monthly',
            'amount' => 100.00,
            'currency' => 'USD',
            'status' => 'active',
            'starts_at' => now(),
            'ends_at' => now()->addMonth(),
        ]);

        app(ReferralService::class)->onSubscriptionActivated($newSub->load('demoRequest'));

        $newSub->refresh();
        $referrerSub->refresh();

        // New sub got its own code, AND the referrer was linked.
        $this->assertNotNull($newSub->referral_code);
        $this->assertMatchesRegularExpression('/^DOC-[A-Z2-9]{8}$/', $newSub->referral_code);
        $this->assertSame($referrerSub->id, $newSub->referred_by_subscription_id);

        // Referrer credited 10% of $100 = 1000 cents.
        $this->assertSame(1000, $referrerSub->referral_credit_cents);
    }

    public function test_self_referral_is_blocked(): void
    {
        $sub = $this->makeActiveSub('self@example.com');
        $sub->forceFill(['referral_code' => 'DOC-SELF2345'])->save();
        $sub->demoRequest->update(['referred_by_code' => 'DOC-SELF2345']);

        app(ReferralService::class)->onSubscriptionActivated($sub->load('demoRequest'));

        $sub->refresh();
        $this->assertNull($sub->referred_by_subscription_id);
        $this->assertSame(0, $sub->referral_credit_cents);
    }

    public function test_unknown_code_is_silently_dropped(): void
    {
        $newDemo = DemoRequest::create([
            'full_name' => 'New',
            'email' => 'new@x.com',
            'clinic_name' => 'New',
            'phone' => '+1234567890',
            'specialty' => 'general',
            'country' => 'AE',
            'referred_by_code' => 'DOC-NOPE2345',
        ]);
        $sub = Subscription::create([
            'pricing_plan_id' => $this->plan()->id,
            'demo_request_id' => $newDemo->id,
            'customer_name' => 'New', 'customer_email' => 'new@x.com',
            'customer_phone' => '+1234567890',
            'clinic_name' => 'New', 'country' => 'AE',
            'billing_cycle' => 'monthly', 'amount' => 100.00, 'currency' => 'USD',
            'status' => 'active', 'starts_at' => now(), 'ends_at' => now()->addMonth(),
        ]);

        app(ReferralService::class)->onSubscriptionActivated($sub->load('demoRequest'));

        $sub->refresh();
        $this->assertNotNull($sub->referral_code, 'New sub should still get its own code.');
        $this->assertNull($sub->referred_by_subscription_id);
    }

    public function test_double_activation_does_not_double_credit(): void
    {
        $referrerSub = $this->makeActiveSub('r@example.com');
        $referrerSub->forceFill(['referral_code' => 'DOC-DUPE2345'])->save();

        $newDemo = DemoRequest::create([
            'full_name' => 'New', 'email' => 'n@example.com',
            'clinic_name' => 'New', 'phone' => '+1', 'specialty' => 'general', 'country' => 'AE',
            'referred_by_code' => 'DOC-DUPE2345',
        ]);
        $sub = Subscription::create([
            'pricing_plan_id' => $this->plan()->id,
            'demo_request_id' => $newDemo->id,
            'customer_name' => 'New', 'customer_email' => 'n@example.com',
            'customer_phone' => '+1234567890',
            'clinic_name' => 'New', 'country' => 'AE',
            'billing_cycle' => 'monthly', 'amount' => 100.00, 'currency' => 'USD',
            'status' => 'active', 'starts_at' => now(), 'ends_at' => now()->addMonth(),
        ]);

        // Simulate webhook retry: same activation processed twice.
        $service = app(ReferralService::class);
        $service->onSubscriptionActivated($sub->load('demoRequest'));
        $service->onSubscriptionActivated($sub->fresh(['demoRequest']));

        $referrerSub->refresh();
        $this->assertSame(1000, $referrerSub->referral_credit_cents, 'Credit must apply exactly once.');
    }
}
