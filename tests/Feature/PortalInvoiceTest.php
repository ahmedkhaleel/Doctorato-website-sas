<?php

namespace Tests\Feature;

use App\Models\DemoRequest;
use App\Models\Invoice;
use App\Models\PricingPlan;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Coverage for the per-invoice view route — the surface customers
 * hit when they click "View" or "PDF" on the dashboard.
 *
 * The ownership check is the single most important test in this
 * file. Without it, any signed-in customer could iterate through
 * invoice IDs and read someone else's billing history.
 */
class PortalInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_open_their_own_invoice(): void
    {
        $customer = DemoRequest::factory()->create();
        $plan = PricingPlan::factory()->create(['name_en' => 'Pro']);
        $sub = Subscription::factory()->create(['demo_request_id' => $customer->id, 'plan_id' => $plan->id]);
        $invoice = Invoice::factory()->create([
            'subscription_id' => $sub->id,
            'number' => 'INV-2026-0001',
            'total' => 199.00,
            'currency' => 'USD',
            'status' => 'paid',
        ]);

        $this->withSession(['portal.customer_id' => $customer->id]);

        $response = $this->get("/portal/invoices/{$invoice->id}");

        $response->assertOk();
        $response->assertSee('INV-2026-0001');
        $response->assertSee('Pro');
    }

    public function test_404_when_invoice_belongs_to_someone_else(): void
    {
        // The high-stakes test: a signed-in customer trying to view
        // an invoice that belongs to a different customer must hit
        // a hard 404 (not 403, not the dashboard) so they can't
        // tell whether the invoice exists.
        $me = DemoRequest::factory()->create();
        $other = DemoRequest::factory()->create();
        $otherSub = Subscription::factory()->create(['demo_request_id' => $other->id]);
        $otherInvoice = Invoice::factory()->create(['subscription_id' => $otherSub->id]);

        $this->withSession(['portal.customer_id' => $me->id]);

        $this->get("/portal/invoices/{$otherInvoice->id}")->assertNotFound();
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $customer = DemoRequest::factory()->create();
        $sub = Subscription::factory()->create(['demo_request_id' => $customer->id]);
        $invoice = Invoice::factory()->create(['subscription_id' => $sub->id]);

        // No portal.customer_id session key set
        $this->get("/portal/invoices/{$invoice->id}")->assertRedirect('/portal');
    }

    public function test_print_query_param_triggers_auto_print_script(): void
    {
        $customer = DemoRequest::factory()->create();
        $sub = Subscription::factory()->create(['demo_request_id' => $customer->id]);
        $invoice = Invoice::factory()->create(['subscription_id' => $sub->id]);

        $this->withSession(['portal.customer_id' => $customer->id]);

        $response = $this->get("/portal/invoices/{$invoice->id}?print=1");
        $response->assertOk();
        // The auto-print JS only renders when the query param is present.
        $response->assertSee('window.print()', escape: false);

        $normal = $this->get("/portal/invoices/{$invoice->id}");
        $this->assertStringNotContainsString('setTimeout(() => window.print()', $normal->getContent());
    }
}
