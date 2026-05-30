<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\WebhookEvent;
use App\Services\PaymobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymobWebhookController extends Controller
{
    /**
     * Paymob → POST /webhooks/paymob?hmac=...
     * Body contains { type: "TRANSACTION", obj: { ... } }
     *
     * Flow:
     *   1. Record the inbound event in webhook_events BEFORE doing
     *      anything else — that way even a crash mid-processing
     *      leaves a row an admin can replay.
     *   2. Verify the HMAC. A mismatch is recorded with hmac_valid
     *      = false and the controller exits — invalid events are
     *      kept on disk for forensics but never replayed.
     *   3. Find the Payment by the Paymob order id.
     *   4. Atomically flip the invoice + subscription state.
     *   5. Update the webhook_events row with the outcome.
     *
     * Replay path: AdminWebhookController calls back into this
     * controller via processStoredEvent($id) which re-runs steps
     * 2-5 against the recorded payload, hitting the same
     * idempotency guards.
     */
    public function handle(Request $request, PaymobService $paymob): JsonResponse
    {
        // Use the JSON body only — NOT $request->all() — so query
        // string params (like ?hmac=...) don't leak into the stored
        // payload. The HMAC stays in $request->query() where it
        // belongs.
        $payload = $request->isJson()
            ? (array) $request->json()->all()
            : $request->except(array_keys($request->query()));
        $receivedHmac = (string) $request->query('hmac', '');
        $obj = $payload['obj'] ?? [];

        // Step 1: record FIRST (even if HMAC will fail). A failed
        // HMAC is data we want to keep — it might be a
        // misconfiguration on our side worth investigating.
        $event = WebhookEvent::create([
            'source' => 'paymob',
            'event_type' => (string) ($payload['type'] ?? 'unknown'),
            'gateway_id' => (string) ($obj['id'] ?? '') ?: null,
            'order_id' => (string) ($obj['order']['id'] ?? '') ?: null,
            'hmac_valid' => $paymob->verifyHmac($obj, $receivedHmac),
            'status' => WebhookEvent::STATUS_RECEIVED,
            'payload' => $payload,
            'ip' => $request->ip(),
            'received_at' => now(),
        ]);

        return $this->process($event, $paymob);
    }

    /**
     * Replay entry-point. Admin controller calls this with an
     * existing event id. We construct a fresh JsonResponse using
     * the stored payload, write the outcome back to the same row,
     * and the lineage stays linked via replayed_from_id (set by
     * the admin controller before calling).
     */
    public function processStoredEvent(WebhookEvent $event, PaymobService $paymob): JsonResponse
    {
        return $this->process($event, $paymob);
    }

    /**
     * Shared processor — runs against an already-recorded event row.
     * Updates the row's status field on every exit path so the
     * dashboard always reflects ground truth.
     */
    protected function process(WebhookEvent $event, PaymobService $paymob): JsonResponse
    {
        $payload = (array) $event->payload;
        $obj = $payload['obj'] ?? [];

        if (! $obj || ! $event->hmac_valid) {
            Log::warning('Paymob webhook HMAC mismatch', ['event_id' => $event->id]);
            $this->stamp($event, WebhookEvent::STATUS_FAILED, 400, ['status' => 'invalid'], 'hmac_mismatch');
            return response()->json(['status' => 'invalid'], 400);
        }

        $orderId = (string) ($obj['order']['id'] ?? '');
        $txId = (string) ($obj['id'] ?? '');
        $success = (bool) ($obj['success'] ?? false);

        $payment = Payment::where('gateway_order_id', $orderId)->latest('id')->first();

        if (! $payment) {
            Log::warning('Paymob webhook: payment not found', ['order_id' => $orderId, 'event_id' => $event->id]);
            $this->stamp($event, WebhookEvent::STATUS_FAILED, 404, ['status' => 'not_found'], 'payment_not_found');
            return response()->json(['status' => 'not_found'], 404);
        }

        // Idempotency guard.
        if ($payment->gateway_transaction_id === $txId && $payment->processed_at !== null) {
            Log::info('Paymob webhook: duplicate, already processed', [
                'order_id' => $orderId, 'tx_id' => $txId, 'payment_id' => $payment->id, 'event_id' => $event->id,
            ]);
            $this->stamp($event, WebhookEvent::STATUS_PROCESSED, 200, ['status' => 'ok', 'duplicate' => true]);
            return response()->json(['status' => 'ok', 'duplicate' => true]);
        }

        try {
            DB::transaction(function () use ($payment, $obj, $txId, $success) {
                $payment = Payment::where('id', $payment->id)->lockForUpdate()->first();

                if ($payment->gateway_transaction_id === $txId && $payment->processed_at !== null) {
                    return;
                }

                $invoice = $payment->invoice;
                $subscription = $invoice->subscription;

                $payment->update([
                    'gateway_transaction_id' => $txId,
                    'status' => $success ? 'succeeded' : 'failed',
                    'payment_method' => $obj['source_data']['type'] ?? null,
                    'failure_reason' => $success ? null : ($obj['data']['message'] ?? 'declined'),
                    'raw_response' => $obj,
                    'processed_at' => now(),
                ]);

                if ($success && $invoice->status !== 'paid') {
                    $invoice->update(['status' => 'paid', 'paid_at' => now()]);

                    $starts = now();
                    $ends = $subscription->billing_cycle === 'yearly'
                        ? $starts->copy()->addYear()
                        : $starts->copy()->addMonth();

                    $subscription->update([
                        'status' => 'active',
                        'starts_at' => $starts,
                        'ends_at' => $ends,
                    ]);

                    app(\App\Services\ReferralService::class)
                        ->onSubscriptionActivated($subscription->fresh(['demoRequest']));
                } elseif (! $success && $invoice->status === 'pending') {
                    $invoice->update(['status' => 'failed']);
                }
            });
        } catch (\Throwable $e) {
            // Crashed mid-processing. Leave the row replayable.
            $this->stamp($event, WebhookEvent::STATUS_FAILED, 500, ['status' => 'error'], $e->getMessage());
            throw $e;
        }

        $this->stamp($event, WebhookEvent::STATUS_PROCESSED, 200, ['status' => 'ok']);
        return response()->json(['status' => 'ok']);
    }

    /**
     * Write outcome back to the event row. Response body is
     * truncated at 4KB to keep the column small — we only ever
     * need the gist.
     */
    protected function stamp(WebhookEvent $event, string $status, int $code, array $body, ?string $error = null): void
    {
        $event->update([
            'status' => $status,
            'response_code' => $code,
            'response_body' => substr(json_encode($body) ?: '', 0, 4000),
            'error' => $error,
            'processed_at' => now(),
        ]);
    }
}
