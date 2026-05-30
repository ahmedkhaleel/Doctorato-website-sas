<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Services\SubscriptionMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 26 — subscription metrics service. Covers:
 *   - MRR computes monthly + 1/12 yearly per sub, summed
 *   - MRR converts currencies via rate_to_sar
 *   - ARR = MRR × 12
 *   - Active count excludes paused subs (Phase 20 contract)
 *   - Active count excludes cancelled subs
 *   - ARPU = MRR / active count
 *   - by_cycle splits monthly vs yearly correctly
 *   - 0 active subs → 0 ARPU (no div-by-zero)
 *   - Snapshot cached; forceFresh recomputes
 *   - recent_cancellations returns up to 10, ordered desc
 */
class SubscriptionMetricsTest extends TestCase
{
    use RefreshDatabase;

    protected function plan(): PricingPlan
    {
        return PricingPlan::firstOrCreate(['slug' => 'standard'], [
            'name_ar' => 'قياسي', 'name_en' => 'Standard',
            'description_ar' => '-', 'description_en' => '-',
            'features_ar' => '[]', 'features_en' => '[]',
            'modules_included' => '[]', 'support_level' => 'standard',
            'monthly_price' => 100, 'yearly_price' => 1000,
            'currency' => 'USD', 'is_active' => true,
        ]);
    }

    protected function makeSub(array $overrides = []): Subscription
    {
        $demo = DemoRequest::create([
            'full_name' => 'X', 'email' => 'm+' . uniqid() . '@x.com',
            'phone' => '+1', 'clinic_name' => 'M',
            'specialty' => 'general', 'country' => 'EG',
        ]);
        return Subscription::create(array_merge([
            'pricing_plan_id' => $this->plan()->id,
            'demo_request_id' => $demo->id,
            'customer_name' => 'X', 'customer_email' => $demo->email, 'customer_phone' => '+1',
            'clinic_name' => 'X', 'country' => 'EG',
            'billing_cycle' => 'monthly', 'amount' => 100, 'currency' => 'SAR',
            'status' => 'active', 'starts_at' => now()->subDays(60), 'ends_at' => now()->addMonth(),
        ], $overrides));
    }

    protected function seedSarCurrency(): void
    {
        DB::table('currencies')->insert([
            'code' => 'SAR', 'name_ar' => 'ريال', 'name_en' => 'SAR',
            'symbol' => 'ر.س', 'symbol_position' => 'after',
            'decimal_places' => 2, 'rate_to_sar' => 1, 'is_active' => 1,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_mrr_sums_active_subs(): void
    {
        $this->seedSarCurrency();
        $this->makeSub(['amount' => 100, 'billing_cycle' => 'monthly']);
        $this->makeSub(['amount' => 1200, 'billing_cycle' => 'yearly']);

        $snap = app(SubscriptionMetricsService::class)->snapshot();
        // 100 monthly + 1200/12 = 100 + 100 = 200
        $this->assertEqualsWithDelta(200.0, $snap['mrr_sar'], 0.01);
        $this->assertEqualsWithDelta(2400.0, $snap['arr_sar'], 0.01);
    }

    public function test_active_count_excludes_paused(): void
    {
        $this->seedSarCurrency();
        $this->makeSub();
        $this->makeSub(['paused_at' => now()]);

        $snap = app(SubscriptionMetricsService::class)->snapshot();
        $this->assertSame(1, $snap['active_subs']);
        $this->assertSame(1, $snap['paused_subs']);
    }

    public function test_active_count_excludes_cancelled(): void
    {
        $this->seedSarCurrency();
        $this->makeSub();
        $this->makeSub(['status' => 'cancelled', 'cancelled_at' => now()->subDays(2)]);

        $snap = app(SubscriptionMetricsService::class)->snapshot();
        $this->assertSame(1, $snap['active_subs']);
    }

    public function test_arpu_divides_by_active_count(): void
    {
        $this->seedSarCurrency();
        $this->makeSub(['amount' => 100]);
        $this->makeSub(['amount' => 300]);

        $snap = app(SubscriptionMetricsService::class)->snapshot();
        // MRR 400 / 2 active = 200
        $this->assertEqualsWithDelta(200.0, $snap['arpu_sar'], 0.01);
    }

    public function test_no_active_subs_yields_zero_arpu(): void
    {
        $this->seedSarCurrency();
        $snap = app(SubscriptionMetricsService::class)->snapshot();
        $this->assertSame(0, (int) $snap['active_subs']);
        $this->assertSame(0.0, (float) $snap['arpu_sar']);
        $this->assertSame(0.0, (float) $snap['mrr_sar']);
    }

    public function test_by_cycle_split(): void
    {
        $this->seedSarCurrency();
        $this->makeSub(['billing_cycle' => 'monthly']);
        $this->makeSub(['billing_cycle' => 'monthly']);
        $this->makeSub(['billing_cycle' => 'yearly']);

        $snap = app(SubscriptionMetricsService::class)->snapshot();
        $this->assertSame(2, $snap['by_cycle']['monthly']);
        $this->assertSame(1, $snap['by_cycle']['yearly']);
    }

    public function test_recent_cancellations_returns_up_to_ten_desc(): void
    {
        $this->seedSarCurrency();
        for ($i = 0; $i < 12; $i++) {
            $this->makeSub([
                'status' => 'cancelled',
                'cancelled_at' => now()->subDays($i),
            ]);
        }

        $snap = app(SubscriptionMetricsService::class)->snapshot();
        $this->assertCount(10, $snap['recent_cancellations']);
        // First row is the most recent.
        $this->assertEqualsWithDelta(now()->timestamp,
            \Carbon\Carbon::parse($snap['recent_cancellations'][0]['cancelled_at'])->timestamp,
            5);
    }

    public function test_snapshot_is_cached_and_force_fresh_recomputes(): void
    {
        $this->seedSarCurrency();
        $this->makeSub(['amount' => 100]);

        $service = app(SubscriptionMetricsService::class);
        $first = $service->snapshot();
        $this->assertEqualsWithDelta(100.0, $first['mrr_sar'], 0.01);

        // Add another sub. Without forceFresh, the cached snapshot
        // still reads 100.
        $this->makeSub(['amount' => 200]);
        $cached = $service->snapshot();
        $this->assertEqualsWithDelta(100.0, $cached['mrr_sar'], 0.01);

        // Force a fresh compute — now we see 300.
        $fresh = $service->snapshot(forceFresh: true);
        $this->assertEqualsWithDelta(300.0, $fresh['mrr_sar'], 0.01);
    }
}
