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

        DB::transaction(function () use ($payment, $obj, $txId, $success) {
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
            } elseif (! $success && $invoice->status === 'pending') {
                $invoice->update(['status' => 'failed']);
            }
        });

        return response()->json(['status' => 'ok']);
    }
}
