<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Coverage for the profile + preferences + subscription resume
 * surfaces added in the portal completion pass.
 */
class PortalProfileAndPreferencesTest extends TestCase
{
    use RefreshDatabase;

    protected function signedInCustomer(): DemoRequest
    {
        $c = DemoRequest::factory()->create([
            'full_name' => 'Original Name',
            'clinic_name' => 'Original Clinic',
            'marketing_opt_in' => true,
        ]);
        $this->withSession(['portal.customer_id' => $c->id]);
        return $c;
    }

    public function test_profile_page_renders(): void
    {
        $this->signedInCustomer();
        $response = $this->get('/portal/profile');
        $response->assertOk()->assertInertia(fn ($p) => $p->component('Portal/Profile'));
    }

    public function test_customer_can_update_their_profile(): void
    {
        $customer = $this->signedInCustomer();

        $response = $this->put('/portal/profile', [
            'full_name' => 'New Name',
            'clinic_name' => 'New Clinic',
            'phone' => '+971501234567',
            'country' => 'United Arab Emirates',
            'specialty' => 'Dental',
        ]);

        $response->assertRedirect();
        $this->assertSame('New Name', $customer->fresh()->full_name);
        $this->assertSame('New Clinic', $customer->fresh()->clinic_name);
    }

    public function test_profile_rejects_missing_required_fields(): void
    {
        $this->signedInCustomer();

        $response = $this->put('/portal/profile', [
            'full_name' => '',
            'clinic_name' => '',
        ]);

        $response->assertSessionHasErrors(['full_name', 'clinic_name']);
    }

    public function test_email_cannot_be_changed_through_profile_update(): void
    {
        // Email is the magic-link identifier — changing it would
        // lock the customer out. The controller doesn't include
        // email in its validation rules so unknown keys are ignored.
        $customer = $this->signedInCustomer();
        $originalEmail = $customer->email;

        $this->put('/portal/profile', [
            'full_name' => 'New Name',
            'clinic_name' => 'New Clinic',
            'email' => 'newaddr@example.com',
        ]);

        $this->assertSame($originalEmail, $customer->fresh()->email);
    }

    public function test_opt_out_stamps_the_timestamp(): void
    {
        $customer = $this->signedInCustomer();
        $this->assertNull($customer->marketing_opted_out_at);

        $this->put('/portal/preferences', ['marketing_opt_in' => false]);

        $customer->refresh();
        $this->assertFalse((bool) $customer->marketing_opt_in);
        $this->assertNotNull($customer->marketing_opted_out_at);
    }

    public function test_opting_back_in_does_not_clear_the_audit_timestamp(): void
    {
        // We KEEP the opted_out_at timestamp even after they re-opt in,
        // so the audit trail shows there was a period of opt-out.
        $customer = $this->signedInCustomer();
        $this->put('/portal/preferences', ['marketing_opt_in' => false]);
        $stampedAt = $customer->fresh()->marketing_opted_out_at;
        $this->assertNotNull($stampedAt);

        $this->put('/portal/preferences', ['marketing_opt_in' => true]);
        $customer->refresh();
        $this->assertTrue((bool) $customer->marketing_opt_in);
        $this->assertNotNull($customer->marketing_opted_out_at, 'opted_out_at must persist as audit history');
    }

    public function test_canceled_subscription_can_be_resumed_within_grace_period(): void
    {
        $customer = $this->signedInCustomer();
        $sub = Subscription::factory()->create([
            'demo_request_id' => $customer->id,
            'status' => 'cancelled',
            'cancelled_at' => now()->subDay(),
            'ends_at' => now()->addDays(10),
        ]);

        $this->post("/portal/subscriptions/{$sub->id}/resume");

        $sub->refresh();
        $this->assertSame('active', $sub->status);
        $this->assertNull($sub->cancelled_at);
    }

    public function test_resume_is_rejected_after_grace_period(): void
    {
        $customer = $this->signedInCustomer();
        $sub = Subscription::factory()->create([
            'demo_request_id' => $customer->id,
            'status' => 'cancelled',
            'cancelled_at' => now()->subMonth(),
            'ends_at' => now()->subDay(),  // already past
        ]);

        $response = $this->post("/portal/subscriptions/{$sub->id}/resume");

        $response->assertSessionHasErrors(['subscription']);
        $this->assertSame('cancelled', $sub->fresh()->status);
    }

    public function test_resume_only_works_on_canceled_subs(): void
    {
        $customer = $this->signedInCustomer();
        $sub = Subscription::factory()->create([
            'demo_request_id' => $customer->id,
            'status' => 'active',
        ]);

        $response = $this->post("/portal/subscriptions/{$sub->id}/resume");
        $response->assertSessionHasErrors(['subscription']);
    }

    public function test_resume_404s_when_not_the_owner(): void
    {
        $this->signedInCustomer();
        $other = DemoRequest::factory()->create();
        $otherSub = Subscription::factory()->create([
            'demo_request_id' => $other->id,
            'status' => 'cancelled',
            'ends_at' => now()->addDays(10),
        ]);

        $this->post("/portal/subscriptions/{$otherSub->id}/resume")->assertNotFound();
    }
}
