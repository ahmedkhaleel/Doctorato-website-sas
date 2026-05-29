<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaymobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * Coverage for the Paymob webhook handler. This is the single highest-
 * stakes endpoint on the system — a wrong flip here means a customer
 * is charged but not activated (or worse: activated twice).
 *
 * The HMAC check is delegated to PaymobService, which we mock so the
 * test doesn't need real credentials. The idempotency guard and the
 * state transitions are the parts under test.
 */
class PaymobWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function fakeWebhook(Payment $payment, bool $success = true, ?string $txId = null): array
    {
        return [
            'type' => 'TRANSACTION',
            'obj' => [
                'id' => $txId ?? '999111',
                'success' => $success,
                'order' => ['id' => $payment->gateway_order_id],
                'source_data' => ['type' => 'card'],
                'data' => $success ? [] : ['message' => 'declined'],
            ],
        ];
    }

    protected function mockHmacAccept(): void
    {
        $this->mock(PaymobService::class, function (MockInterface $m) {
            $m->shouldReceive('verifyHmac')->andReturn(true);
        });
    }

    public function test_invalid_hmac_returns_400_and_nothing_changes(): void
    {
        $this->mock(PaymobService::class, function (MockInterface $m) {
            $m->shouldReceive('verifyHmac')->andReturn(false);
        });

        $payment = Payment::factory()->create(['gateway_order_id' => 'order-1']);

        $response = $this->postJson('/webhooks/paymob?hmac=bad', $this->fakeWebhook($payment));

        $response->assertStatus(400);
        $this->assertNotEquals('succeeded', $payment->fresh()->status);
    }

    public function test_unknown_order_id_returns_404(): void
    {
        $this->mockHmacAccept();

        $response = $this->postJson('/webhooks/paymob?hmac=anything', [
            'type' => 'TRANSACTION',
            'obj' => [
                'id' => 'tx-1',
                'success' => true,
                'order' => ['id' => 'order-does-not-exist'],
            ],
        ]);

        $response->assertStatus(404);
    }

    public function test_successful_payment_activates_subscription_and_marks_invoice_paid(): void
    {
        $this->mockHmacAccept();

        $sub = Subscription::factory()->create(['status' => 'pending', 'billing_cycle' => 'monthly']);
        $invoice = Invoice::factory()->create(['subscription_id' => $sub->id, 'status' => 'pending']);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'subscription_id' => $sub->id,
            'gateway_order_id' => 'order-success',
            'status' => 'pending',
        ]);

        $response = $this->postJson('/webhooks/paymob?hmac=ok', $this->fakeWebhook($payment, true, 'tx-success'));

        $response->assertOk();
        $payment->refresh();
        $invoice->refresh();
        $sub->refresh();

        $this->assertSame('succeeded', $payment->status);
        $this->assertSame('tx-success', $payment->gateway_transaction_id);
        $this->assertNotNull($payment->processed_at);
        $this->assertSame('paid', $invoice->status);
        $this->assertNotNull($invoice->paid_at);
        $this->assertSame('active', $sub->status);
    }

    public function test_failed_payment_marks_invoice_failed_without_activating_subscription(): void
    {
        $this->mockHmacAccept();

        $sub = Subscription::factory()->create(['status' => 'pending']);
        $invoice = Invoice::factory()->create(['subscription_id' => $sub->id, 'status' => 'pending']);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'subscription_id' => $sub->id,
            'gateway_order_id' => 'order-fail',
        ]);

        $response = $this->postJson('/webhooks/paymob?hmac=ok', $this->fakeWebhook($payment, false, 'tx-fail'));

        $response->assertOk();
        $this->assertSame('failed', $payment->fresh()->status);
        $this->assertSame('failed', $invoice->fresh()->status);
        $this->assertSame('pending', $sub->fresh()->status, 'Subscription must NOT activate on failure');
    }

    public function test_duplicate_webhook_is_acknowledged_but_does_not_reactivate(): void
    {
        // The single most important test in this file: Paymob retries
        // any non-200 response, and the same transaction can arrive
        // twice via the redirect callback + S2S notification. Without
        // the idempotency guard, the second hit would extend ends_at
        // by another month free of charge.
        $this->mockHmacAccept();

        $sub = Subscription::factory()->create(['status' => 'pending']);
        $invoice = Invoice::factory()->create(['subscription_id' => $sub->id, 'status' => 'pending']);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'subscription_id' => $sub->id,
            'gateway_order_id' => 'order-dup',
        ]);

        // First delivery
        $first = $this->postJson('/webhooks/paymob?hmac=ok', $this->fakeWebhook($payment, true, 'tx-dup'));
        $first->assertOk();
        $firstEndsAt = $sub->fresh()->ends_at;

        // Second delivery, same transaction id
        $second = $this->postJson('/webhooks/paymob?hmac=ok', $this->fakeWebhook($payment, true, 'tx-dup'));
        $second->assertOk();
        $second->assertJson(['duplicate' => true]);

        $secondEndsAt = $sub->fresh()->ends_at;
        $this->assertEquals($firstEndsAt, $secondEndsAt, 'ends_at must not move on a duplicate webhook');
    }
}
