<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Models\MetricSnapshot;
use App\Models\PricingPlan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Phase 29 — daily metric snapshots. Covers:
 *   - metrics:snapshot writes exactly one row per call
 *   - Two runs on the same day update-or-replace (idempotent)
 *   - Snapshot values match the live service output
 *   - --backfill=N inserts N+1 rows (today + N prior)
 *   - Snapshot writes captured_at + snapshot_date correctly
 */
class MetricSnapshotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        DB::table('currencies')->insert([
            'code' => 'SAR', 'name_ar' => 'ريال', 'name_en' => 'SAR',
            'symbol' => 'ر.س', 'symbol_position' => 'after',
            'decimal_places' => 2, 'rate_to_sar' => 1, 'is_active' => 1,
        ]);
    }

    protected function makeActiveSub(float $amount = 100): Subscription
    {
        $plan = PricingPlan::firstOrCreate(['slug' => 'standard'], [
            'name_ar' => 'قياسي', 'name_en' => 'Standard',
            'description_ar' => '-', 'description_en' => '-',
            'features_ar' => '[]', 'features_en' => '[]',
            'modules_included' => '[]', 'support_level' => 'standard',
            'monthly_price' => 100, 'yearly_price' => 1000,
            'currency' => 'USD', 'is_active' => true,
        ]);

        $demo = DemoRequest::create([
            'full_name' => 'X', 'email' => 's+' . uniqid() . '@x.com',
            'phone' => '+1', 'clinic_name' => 'X',
            'specialty' => 'general', 'country' => 'EG',
        ]);
        return Subscription::create([
            'pricing_plan_id' => $plan->id, 'demo_request_id' => $demo->id,
            'customer_name' => 'X', 'customer_email' => $demo->email, 'customer_phone' => '+1',
            'clinic_name' => 'X', 'country' => 'EG',
            'billing_cycle' => 'monthly', 'amount' => $amount, 'currency' => 'SAR',
            'status' => 'active', 'starts_at' => now()->subDays(60), 'ends_at' => now()->addMonth(),
        ]);
    }

    public function test_snapshot_writes_one_row(): void
    {
        $this->makeActiveSub(100);
        $this->artisan('metrics:snapshot')->assertExitCode(0);
        $this->assertSame(1, MetricSnapshot::count());
    }

    public function test_snapshot_records_correct_mrr(): void
    {
        $this->makeActiveSub(150);
        $this->makeActiveSub(200);
        $this->artisan('metrics:snapshot')->assertExitCode(0);

        $row = MetricSnapshot::first();
        $this->assertEqualsWithDelta(350.0, (float) $row->mrr_sar, 0.01);
        $this->assertSame(2, (int) $row->active_subs);
    }

    public function test_second_run_same_day_is_idempotent(): void
    {
        $this->makeActiveSub(100);
        $this->artisan('metrics:snapshot')->assertExitCode(0);
        $this->artisan('metrics:snapshot')->assertExitCode(0);

        $this->assertSame(1, MetricSnapshot::count(),
            'Two runs on the same date must overwrite, not duplicate.');
    }

    public function test_backfill_inserts_extra_days(): void
    {
        $this->makeActiveSub(100);
        $this->artisan('metrics:snapshot --backfill=7')->assertExitCode(0);

        $this->assertSame(8, MetricSnapshot::count(), 'today + 7 prior = 8 rows');

        // Dates should be distinct and consecutive from today backwards.
        $dates = MetricSnapshot::orderByDesc('snapshot_date')->pluck('snapshot_date')
            ->map(fn ($d) => $d->toDateString())->toArray();
        $this->assertSame(today()->toDateString(), $dates[0]);
        $this->assertSame(today()->subDays(7)->toDateString(), $dates[7]);
    }

    public function test_snapshot_captures_today_date_and_now_timestamp(): void
    {
        $this->makeActiveSub(100);
        $this->artisan('metrics:snapshot')->assertExitCode(0);

        $row = MetricSnapshot::first();
        $this->assertSame(today()->toDateString(), $row->snapshot_date->toDateString());
        $this->assertNotNull($row->captured_at);
    }
}
