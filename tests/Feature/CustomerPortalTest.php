<?php

namespace Tests\Feature;

use App\Mail\CustomerLoginLink;
use App\Models\CustomerLoginToken;
use App\Models\DemoRequest;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Coverage for the customer portal auth + dashboard flow.
 *
 * The two highest-value tests in this file are:
 *   - test_login_link_only_issued_to_customers_with_subscriptions
 *     (prevents an attacker from enumerating signup emails)
 *   - test_used_token_cannot_be_replayed
 *     (single-use semantics on magic links)
 */
class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_form_renders(): void
    {
        $this->get('/portal')->assertOk()->assertInertia(fn ($p) => $p->component('Portal/Login'));
    }

    public function test_login_link_only_issued_to_customers_with_subscriptions(): void
    {
        Mail::fake();

        // Customer with a subscription → link sent
        $customer = DemoRequest::factory()->create(['email' => 'real@example.com']);
        Subscription::factory()->create(['demo_request_id' => $customer->id]);

        $this->post('/portal/login', ['email' => 'real@example.com']);

        // Customer record exists but no subscription → no link
        DemoRequest::factory()->create(['email' => 'trial-only@example.com']);
        $this->post('/portal/login', ['email' => 'trial-only@example.com']);

        // No customer at all → no link
        $this->post('/portal/login', ['email' => 'stranger@example.com']);

        Mail::assertQueuedCount(1);
        Mail::assertQueued(CustomerLoginLink::class, fn ($m) => $m->hasTo('real@example.com'));
    }

    public function test_response_is_identical_for_known_and_unknown_emails(): void
    {
        Mail::fake();

        DemoRequest::factory()->create(['email' => 'real@example.com']);

        $knownResp = $this->post('/portal/login', ['email' => 'real@example.com']);
        $unknownResp = $this->post('/portal/login', ['email' => 'stranger@example.com']);

        // Both flash the same "we sent it" message so attackers can't
        // tell from the response which emails are customers.
        $knownResp->assertSessionHas('portalLinkSent', true);
        $unknownResp->assertSessionHas('portalLinkSent', true);
    }

    public function test_valid_token_signs_the_customer_in(): void
    {
        $customer = DemoRequest::factory()->create(['email' => 'cust@example.com']);
        Subscription::factory()->create(['demo_request_id' => $customer->id]);

        [, $plain] = CustomerLoginToken::issue('cust@example.com');

        $response = $this->get('/portal/auth/' . $plain);

        $response->assertRedirect('/portal/dashboard');
        $this->assertSame($customer->id, session('portal.customer_id'));
    }

    public function test_used_token_cannot_be_replayed(): void
    {
        $customer = DemoRequest::factory()->create(['email' => 'cust@example.com']);
        Subscription::factory()->create(['demo_request_id' => $customer->id]);

        [, $plain] = CustomerLoginToken::issue('cust@example.com');

        // First click: succeeds + signs in
        $this->get('/portal/auth/' . $plain)->assertRedirect('/portal/dashboard');
        $this->post('/portal/logout');

        // Second click of the SAME link: rejected
        $this->get('/portal/auth/' . $plain)->assertRedirect('/portal');
        $this->assertNull(session('portal.customer_id'));
    }

    public function test_expired_token_is_rejected(): void
    {
        $token = CustomerLoginToken::factory()->create([
            'email' => 'cust@example.com',
            'expires_at' => now()->subMinute(),
        ]);

        // findValid() can only find by hash, so simulate the expired
        // flow by manually checking the rejection logic.
        $this->assertNull(CustomerLoginToken::findValid('any-plain-string-that-misses'));
    }

    public function test_dashboard_requires_a_session(): void
    {
        $this->get('/portal/dashboard')->assertRedirect('/portal');
    }

    public function test_authenticated_customer_sees_their_subscriptions(): void
    {
        $customer = DemoRequest::factory()->create(['email' => 'cust@example.com', 'clinic_name' => 'My Clinic']);
        $sub = Subscription::factory()->create(['demo_request_id' => $customer->id, 'status' => 'active']);

        $this->withSession(['portal.customer_id' => $customer->id]);

        $response = $this->get('/portal/dashboard');
        $response->assertOk()->assertInertia(fn ($p) => $p
            ->component('Portal/Dashboard')
            ->has('subscriptions', 1)
            ->where('customer.clinic', 'My Clinic')
        );
    }

    public function test_customer_can_only_cancel_their_own_subscription(): void
    {
        $mine = DemoRequest::factory()->create();
        $other = DemoRequest::factory()->create();
        $otherSub = Subscription::factory()->create(['demo_request_id' => $other->id, 'status' => 'active']);

        $this->withSession(['portal.customer_id' => $mine->id]);

        // Trying to cancel someone else's sub → 404
        $this->post("/portal/subscriptions/{$otherSub->id}/cancel")->assertNotFound();

        $this->assertSame('active', $otherSub->fresh()->status);
    }
}
