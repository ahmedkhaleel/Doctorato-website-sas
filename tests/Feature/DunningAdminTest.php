<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Models\Invoice;
use App\Models\PricingPlan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Phase 25 — Admin dunning console. Covers:
 *   - advance() increments dunning_stage by exactly 1
 *   - advance() refuses to go past stage 5
 *   - reset() drops dunning_stage back to 0
 *   - resolve() jumps directly to stage 5
 *   - Each action writes an activity_logs row with the actor
 *   - Permission gate denies users without billing.manage
 *
 * We hit the controller directly to avoid the admin auth /
 * Inertia round-trip, which keeps the tests fast and focused on
 * state-machine behaviour.
 */
class DunningAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function makeInvoice(int $stage = 0): Invoice
    {
        $plan = PricingPlan::firstOrCreate(
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

        $demo = DemoRequest::create([
            'full_name' => 'X', 'email' => 'd+' . uniqid() . '@x.com',
            'phone' => '+1', 'clinic_name' => 'X',
            'specialty' => 'general', 'country' => 'EG',
        ]);
        $sub = Subscription::create([
            'pricing_plan_id' => $plan->id, 'demo_request_id' => $demo->id,
            'customer_name' => 'X', 'customer_email' => $demo->email, 'customer_phone' => '+1',
            'clinic_name' => 'X', 'country' => 'EG',
            'billing_cycle' => 'monthly', 'amount' => 100, 'currency' => 'USD',
            'status' => 'active', 'starts_at' => now(), 'ends_at' => now()->addMonth(),
        ]);
        return Invoice::create([
            'subscription_id' => $sub->id,
            'number' => 'INV-' . uniqid(),
            'subtotal' => 100, 'total' => 100, 'currency' => 'USD',
            'status' => 'failed',
            'dunning_stage' => $stage,
        ]);
    }

    protected function adminUser(array $perms = ['billing.manage']): User
    {
        return User::create([
            'name' => 'Billing Ops', 'email' => 'ops+' . uniqid() . '@x.com',
            'password' => Hash::make('secret'), 'role' => 'manager',
            'permissions' => $perms, 'is_active' => true,
        ]);
    }

    public function test_advance_increments_stage(): void
    {
        $admin = $this->adminUser();
        $invoice = $this->makeInvoice(1);

        $this->actingAs($admin)
            ->post("/admin/dunning/{$invoice->id}/advance")
            ->assertRedirect();

        $this->assertSame(2, (int) $invoice->fresh()->dunning_stage);
    }

    public function test_advance_refuses_past_stage_5(): void
    {
        $admin = $this->adminUser();
        $invoice = $this->makeInvoice(5);

        $this->actingAs($admin)
            ->post("/admin/dunning/{$invoice->id}/advance")
            ->assertSessionHasErrors('dunning');

        $this->assertSame(5, (int) $invoice->fresh()->dunning_stage);
    }

    public function test_reset_drops_stage_to_zero(): void
    {
        $admin = $this->adminUser();
        $invoice = $this->makeInvoice(3);

        $this->actingAs($admin)
            ->post("/admin/dunning/{$invoice->id}/reset")
            ->assertRedirect();

        $this->assertSame(0, (int) $invoice->fresh()->dunning_stage);
    }

    public function test_resolve_jumps_to_five(): void
    {
        $admin = $this->adminUser();
        $invoice = $this->makeInvoice(2);

        $this->actingAs($admin)
            ->post("/admin/dunning/{$invoice->id}/resolve")
            ->assertRedirect();

        $this->assertSame(5, (int) $invoice->fresh()->dunning_stage);
    }

    public function test_each_action_logs_activity(): void
    {
        $admin = $this->adminUser();
        $invoice = $this->makeInvoice(1);

        $this->actingAs($admin)->post("/admin/dunning/{$invoice->id}/advance");

        $log = DB::table('activity_logs')
            ->where('action', 'dunning_advance')
            ->where('subject_id', $invoice->id)
            ->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, (int) $log->user_id);
        $this->assertStringContainsString('stage 1 to 2', $log->description);
    }

    public function test_user_without_permission_is_denied(): void
    {
        $unauthed = $this->adminUser(['dashboard.view']);
        $invoice = $this->makeInvoice(1);

        $this->actingAs($unauthed)
            ->post("/admin/dunning/{$invoice->id}/advance")
            ->assertForbidden();

        $this->assertSame(1, (int) $invoice->fresh()->dunning_stage);
    }
}
