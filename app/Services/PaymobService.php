<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin wrapper around Paymob's Accept API (Egypt).
 *
 * Flow:
 *  1) auth()                     → returns auth token
 *  2) createOrder()              → registers the order, returns Paymob order id
 *  3) createPaymentKey()         → returns payment_token for the iframe
 *  4) user pays through iframe   → Paymob POSTs a webhook (HMAC-signed) to /webhooks/paymob
 *  5) verifyHmac()               → validate webhook authenticity
 */
class PaymobService
{
    public function __construct(
        protected ?string $apiKey = null,
        protected ?string $integrationId = null,
        protected ?string $iframeId = null,
        protected ?string $hmacSecret = null,
        protected ?string $baseUrl = null,
    ) {
        $this->apiKey = $apiKey ?? config('paymob.api_key');
        $this->integrationId = $integrationId ?? config('paymob.integration_id');
        $this->iframeId = $iframeId ?? config('paymob.iframe_id');
        $this->hmacSecret = $hmacSecret ?? config('paymob.hmac_secret');
        $this->baseUrl = $baseUrl ?? config('paymob.base_url');
    }

    public function isConfigured(): bool
    {
        return $this->apiKey && $this->integrationId && $this->iframeId && $this->hmacSecret;
    }

    /** Step 1: Authenticate and get a short-lived auth token. */
    public function auth(): string
    {
        $res = Http::acceptJson()
            ->post("{$this->baseUrl}/auth/tokens", ['api_key' => $this->apiKey])
            ->throw();

        return $res->json('token');
    }

    /**
     * Step 2: Register an order in Paymob.
     * Amount MUST be in piasters (cents) — multiply EGP by 100.
     */
    public function createOrder(string $authToken, Invoice $invoice): array
    {
        $amountCents = (int) round(((float) $invoice->total) * 100);

        $res = Http::acceptJson()
            ->post("{$this->baseUrl}/ecommerce/orders", [
                'auth_token' => $authToken,
                'delivery_needed' => false,
                'amount_cents' => $amountCents,
                'currency' => $invoice->currency,
                'merchant_order_id' => $invoice->number,
                'items' => array_map(fn ($item) => [
                    'name' => $item['name'] ?? 'Subscription',
                    'amount_cents' => (int) round((float) ($item['total'] ?? $invoice->total) * 100),
                    'quantity' => $item['qty'] ?? 1,
                ], $invoice->line_items ?? [[
                    'name' => 'Subscription',
                    'total' => $invoice->total,
                    'qty' => 1,
                ]]),
            ])
            ->throw();

        return $res->json();
    }

    /** Step 3: Generate a payment key that the iframe uses. */
    public function createPaymentKey(string $authToken, array $order, Subscription $subscription, Invoice $invoice): string
    {
        $amountCents = (int) round(((float) $invoice->total) * 100);

        $res = Http::acceptJson()
            ->post("{$this->baseUrl}/acceptance/payment_keys", [
                'auth_token' => $authToken,
                'amount_cents' => $amountCents,
                'expiration' => 3600,
                'order_id' => $order['id'],
                'billing_data' => $this->billingData($subscription),
                'currency' => $invoice->currency,
                'integration_id' => (int) $this->integrationId,
                'lock_order_when_paid' => true,
            ])
            ->throw();

        return $res->json('token');
    }

    /** Convenience: full flow, returns the iframe URL to redirect the user. */
    public function getIframeUrlFor(Subscription $subscription, Invoice $invoice): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Paymob is not configured. Set PAYMOB_* environment variables.');
        }

        try {
            $token = $this->auth();
            $order = $this->createOrder($token, $invoice);
            $paymentKey = $this->createPaymentKey($token, $order, $subscription, $invoice);
        } catch (RequestException $e) {
            Log::error('Paymob request failed', [
                'message' => $e->getMessage(),
                'response' => $e->response?->body(),
            ]);
            throw new RuntimeException('Paymob request failed: ' . $e->getMessage(), previous: $e);
        }

        // Persist the Paymob order id on an initiated payment record for reconciliation
        Payment::create([
            'invoice_id' => $invoice->id,
            'subscription_id' => $subscription->id,
            'gateway' => 'paymob',
            'gateway_order_id' => (string) ($order['id'] ?? ''),
            'amount' => $invoice->total,
            'currency' => $invoice->currency,
            'status' => 'initiated',
            'raw_response' => ['order' => $order],
        ]);

        return "https://accept.paymob.com/api/acceptance/iframes/{$this->iframeId}?payment_token={$paymentKey}";
    }

    /**
     * Step 5: Verify Paymob transaction webhook HMAC.
     * Paymob concatenates a fixed set of fields in a documented order, then
     * computes SHA-512 HMAC with the merchant's HMAC secret.
     *
     * @see https://docs.paymob.com/docs/hmac-calculation
     */
    public function verifyHmac(array $obj, string $receivedHmac): bool
    {
        $fields = [
            $obj['amount_cents'] ?? '',
            $obj['created_at'] ?? '',
            $obj['currency'] ?? '',
            $obj['error_occured'] ?? '',
            $obj['has_parent_transaction'] ?? '',
            $obj['id'] ?? '',
            $obj['integration_id'] ?? '',
            $obj['is_3d_secure'] ?? '',
            $obj['is_auth'] ?? '',
            $obj['is_capture'] ?? '',
            $obj['is_refunded'] ?? '',
            $obj['is_standalone_payment'] ?? '',
            $obj['is_voided'] ?? '',
            $obj['order']['id'] ?? '',
            $obj['owner'] ?? '',
            $obj['pending'] ?? '',
            $obj['source_data']['pan'] ?? '',
            $obj['source_data']['sub_type'] ?? '',
            $obj['source_data']['type'] ?? '',
            $obj['success'] ?? '',
        ];

        $concatenated = implode('', array_map(fn ($v) => is_bool($v) ? ($v ? 'true' : 'false') : (string) $v, $fields));
        $computed = hash_hmac('sha512', $concatenated, $this->hmacSecret);

        return hash_equals($computed, (string) $receivedHmac);
    }

    protected function billingData(Subscription $sub): array
    {
        [$first, $last] = $this->splitName($sub->customer_name);

        return [
            'apartment' => 'NA',
            'email' => $sub->customer_email,
            'floor' => 'NA',
            'first_name' => $first,
            'street' => 'NA',
            'building' => 'NA',
            'phone_number' => $sub->customer_phone,
            'shipping_method' => 'NA',
            'postal_code' => 'NA',
            'city' => $sub->city ?: 'Cairo',
            'country' => $sub->country ?: 'EG',
            'last_name' => $last,
            'state' => 'NA',
        ];
    }

    protected function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name));
        $first = array_shift($parts) ?: 'NA';
        $last = implode(' ', $parts) ?: 'NA';

        return [$first, $last];
    }
}
