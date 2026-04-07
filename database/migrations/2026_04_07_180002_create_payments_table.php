<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();

            $table->string('gateway')->default('paymob');
            $table->string('gateway_order_id')->nullable()->index();       // Paymob order id
            $table->string('gateway_transaction_id')->nullable()->index(); // Paymob transaction id
            $table->string('payment_method')->nullable();                  // card, wallet, kiosk, etc.

            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('EGP');

            $table->enum('status', [
                'initiated',
                'pending',
                'succeeded',
                'failed',
                'refunded',
            ])->default('initiated');

            $table->string('failure_reason')->nullable();
            $table->json('raw_response')->nullable(); // full gateway payload for debugging
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
