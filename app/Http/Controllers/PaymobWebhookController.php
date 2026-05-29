<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
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
     * We must verify the HMAC, find the Payment by the Paymob order id,
     * then atomically flip the invoice + subscription state.
     */
    public function handle(Request $request, PaymobService $paymob): JsonResponse
    {
        $payload = $request->all();
        $receivedHmac = (string) $request->query('hmac', '');
        $obj = $payload['obj'] ?? [];

        if (! $obj || ! $paymob->verifyHmac($obj, $receivedHmac)) {
            Log::warning('Paymob webhook HMAC mismatch', ['payload' => $payload]);

            return response()->json(['status' => 'invalid'], 400);
        }

        $orderId = (string) ($obj['order']['id'] ?? '');
        $txId = (string) ($obj['id'] ?? '');
        $success = (bool) ($obj['success'] ?? false);

        $payment = Payment::where('gateway_order_id', $orderId)->latest('id')->first();

        if (! $payment) {
            Log::warning('Paymob webhook: payment not found', ['order_id' => $orderId]);

            return response()->json(['status' => 'not_found'], 404);
        }

        // Idempotency guard — Paymob retries webhooks on any non-200
        // response, plus the same transaction can land twice via the
        // redirect callback + the server-to-server notification. If
        // we already stamped this exact transaction id as processed,
        // ack and exit instead of flipping the subscription twice.
        if ($payment->gateway_transaction_id === $txId && $payment->processed_at !== null) {
            Log::info('Paymob webhook: duplicate, already processed', [
                'order_id' => $orderId,
                'tx_id' => $txId,
                'payment_id' => $payment->id,
            ]);

            return response()->json(['status' => 'ok', 'duplicate' => true]);
        }

        DB::transaction(function () use ($payment, $obj, $txId, $success) {
            // Lock the payment row for the duration of the txn so two
            // simultaneous webhook deliveries from Paymob can't both
            // pass the idempotency check above and double-activate.
            $payment = Payment::where('id', $payment->id)->lockForUpdate()->first();

            // Re-check after lock in case a sibling worker just won
            // the race and stamped it.
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

                // Generate this sub's share code + credit any
                // referrer captured at signup. Inside the same DB
                // transaction as the activation so retries can't
                // partially apply.
                app(\App\Services\ReferralService::class)
                    ->onSubscriptionActivated($subscription->fresh(['demoRequest']));
            } elseif (! $success && $invoice->status === 'pending') {
                $invoice->update(['status' => 'failed']);
            }
        });

        return response()->json(['status' => 'ok']);
    }
}
