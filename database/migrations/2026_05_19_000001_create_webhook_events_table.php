<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Webhook event store.
 *
 * Every inbound POST to /webhooks/paymob is recorded BEFORE the
 * controller processes it. Two reasons:
 *
 *   1. Forensics. When a customer says "I paid but my sub never
 *      activated", we can answer "Paymob sent us this exact payload
 *      at this exact time, and here's what our processor did with
 *      it". No more guessing at log lines or asking Paymob to
 *      re-fetch.
 *
 *   2. Replay. If the processor crashed mid-handle (e.g. SMTP timed
 *      out trying to send the receipt email), the row stays in
 *      status=received. An admin can hit "Replay" in the dashboard
 *      and the controller re-runs against the stored payload,
 *      reaching the same idempotency guard that prevents
 *      double-billing.
 *
 * Why we don't rely on raw Laravel logs:
 *   - Logs rotate (daily channel keeps 14 days).
 *   - The PII scrubber rewrites email/phone in log lines, which is
 *     correct for the log channel but wrong for forensics — we
 *     need the original payload byte-for-byte.
 *   - Searching across 14 days of laravel.log for "order_id=ABC123"
 *     is slow and brittle compared to a DB index.
 *
 * Schema:
 *   - source           = "paymob" (room to add Stripe / etc later)
 *   - event_type       = payload['type'] or 'unknown'
 *   - gateway_id       = payload['obj']['id'] (transaction id) when present
 *   - order_id         = payload['obj']['order']['id'] when present
 *   - hmac_valid       = whether verifyHmac() passed at receive time
 *   - status           = received | processed | failed | replayed
 *   - payload (json)   = original POST body
 *   - response_code    = HTTP status the controller returned
 *   - response_body    = controller's JSON response (truncated 4KB)
 *   - error            = exception message if status=failed
 *   - replayed_from_id = nullable FK; lineage when an admin replays
 *
 * Indexes are tuned for the two query patterns:
 *   - admin dashboard list by recency  → (status, received_at DESC)
 *   - find-by-order-id during forensics → (order_id)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('webhook_events')) return;

        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('source', 32)->default('paymob');
            $table->string('event_type', 64)->nullable();
            $table->string('gateway_id', 100)->nullable();
            $table->string('order_id', 100)->nullable();
            $table->boolean('hmac_valid')->default(false);
            $table->string('status', 16)->default('received');
            $table->json('payload');
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->text('error')->nullable();
            $table->unsignedBigInteger('replayed_from_id')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();

            $table->index(['status', 'received_at'], 'idx_wh_status_recent');
            $table->index('order_id', 'idx_wh_order');
            $table->index('gateway_id', 'idx_wh_gateway');
            $table->index('replayed_from_id', 'idx_wh_replayed_from');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
