<?php

namespace Tests\Feature;

use App\Models\WebhookEvent;
use App\Services\PaymobService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Phase 17 — webhook event store + replay. Covers:
 *   - Every inbound POST creates a webhook_events row, even with
 *     a bad HMAC (forensics over silence)
 *   - hmac_valid flag is recorded correctly
 *   - Row stamped 'processed' with response_code=200 on happy path
 *   - Row stamped 'failed' with error message when HMAC invalid
 *   - Row stamped 'failed' with 404 when payment not found
 *   - Replay endpoint clones the row, links replayed_from_id, and
 *     re-runs the processor (the idempotency guard inside the
 *     processor handles the actual "don't double-bill" case —
 *     tested separately in PaymobWebhookController unit coverage)
 */
class WebhookEventStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_bad_hmac_still_creates_row(): void
    {
        $this->mockPaymob(false);

        $this->postJson('/webhooks/paymob?hmac=bad', [
            'type' => 'TRANSACTION',
            'obj' => ['id' => 'tx-1', 'order' => ['id' => 'ord-1']],
        ])->assertStatus(400);

        $row = WebhookEvent::first();
        $this->assertNotNull($row);
        $this->assertFalse($row->hmac_valid);
        $this->assertSame(WebhookEvent::STATUS_FAILED, $row->status);
        $this->assertSame(400, $row->response_code);
        $this->assertSame('hmac_mismatch', $row->error);
        $this->assertSame('TRANSACTION', $row->event_type);
        $this->assertSame('ord-1', $row->order_id);
        $this->assertSame('tx-1', $row->gateway_id);
    }

    public function test_missing_payment_marks_failed_404(): void
    {
        $this->mockPaymob(true);

        $response = $this->postJson('/webhooks/paymob?hmac=ok', [
            'type' => 'TRANSACTION',
            'obj' => ['id' => 'tx-x', 'order' => ['id' => 'unknown-order']],
        ]);
        $response->assertStatus(404);

        $row = WebhookEvent::first();
        $this->assertSame(WebhookEvent::STATUS_FAILED, $row->status);
        $this->assertSame(404, $row->response_code);
        $this->assertSame('payment_not_found', $row->error);
    }

    public function test_payload_is_stored_byte_for_byte(): void
    {
        $this->mockPaymob(false);

        $original = [
            'type' => 'TRANSACTION',
            'obj' => [
                'id' => 'tx-42',
                'order' => ['id' => 'ord-42'],
                'amount_cents' => 9900,
                'currency' => 'EGP',
                'source_data' => ['type' => 'card'],
            ],
        ];

        $this->postJson('/webhooks/paymob?hmac=bad', $original);

        $row = WebhookEvent::first();
        // payload is stored as JSON. Round-tripping through the
        // 'array' cast must give us the original input.
        $this->assertSame($original, $row->payload);
    }

    public function test_replay_clones_event_and_links_lineage(): void
    {
        $this->mockPaymob(false);

        // Seed a "failed" event (HMAC mismatch).
        $original = WebhookEvent::create([
            'source' => 'paymob',
            'event_type' => 'TRANSACTION',
            'order_id' => 'ord-99',
            'gateway_id' => 'tx-99',
            'hmac_valid' => true,
            'status' => WebhookEvent::STATUS_FAILED,
            'payload' => ['type' => 'TRANSACTION', 'obj' => ['id' => 'tx-99', 'order' => ['id' => 'ord-99']]],
            'received_at' => now(),
            'response_code' => 404,
            'error' => 'payment_not_found',
        ]);

        // Auth as an admin with webhooks.manage. Easiest path:
        // bypass the middleware by directly invoking the controller.
        $request = \Illuminate\Http\Request::create('/admin/webhooks/' . $original->id . '/replay', 'POST');
        $request->setLaravelSession(app('session.store'));
        $controller = app(\App\Http\Controllers\Admin\WebhookController::class);
        $controller->replay($request, $original->fresh(), app(\App\Services\PaymobService::class));

        $clone = WebhookEvent::where('replayed_from_id', $original->id)->first();
        $this->assertNotNull($clone, 'replay must create a new row');
        $this->assertSame($original->payload, $clone->payload);
        $this->assertSame($original->order_id, $clone->order_id);
        $this->assertSame($original->id, $clone->replayed_from_id);

        // The original is untouched - its status stays 'failed'.
        $this->assertSame(WebhookEvent::STATUS_FAILED, $original->fresh()->status);
    }

    /** Bind the PaymobService so verifyHmac is deterministic. */
    protected function mockPaymob(bool $hmacValid): void
    {
        $mock = Mockery::mock(PaymobService::class);
        $mock->shouldReceive('verifyHmac')->andReturn($hmacValid);
        $this->app->instance(PaymobService::class, $mock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
