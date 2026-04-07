<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('demo_request_id')->nullable()->constrained()->nullOnDelete();

            // Customer snapshot
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->string('clinic_name')->nullable();
            $table->string('country', 2)->default('EG');
            $table->string('city')->nullable();

            // Billing
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->decimal('amount', 12, 2); // in EGP
            $table->string('currency', 3)->default('EGP');

            // State machine
            $table->enum('status', [
                'pending',    // awaiting first payment
                'active',     // paid & running
                'past_due',   // renewal failed
                'cancelled',
                'expired',
            ])->default('pending');

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->string('reference')->unique(); // public-facing code (e.g. SUB-XXXX)
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'ends_at']);
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
