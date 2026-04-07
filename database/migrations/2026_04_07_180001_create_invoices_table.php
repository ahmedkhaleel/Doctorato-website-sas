<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('number')->unique(); // INV-2026-00001

            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('currency', 3)->default('EGP');

            $table->enum('status', ['draft', 'pending', 'paid', 'failed', 'refunded', 'cancelled'])
                ->default('pending');

            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->json('line_items')->nullable(); // [{name, qty, unit_price, total}, ...]
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
