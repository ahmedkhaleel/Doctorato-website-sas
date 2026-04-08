<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();

            // Discount: percentage (0-100) or fixed amount in EGP
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 12, 2);

            // Constraints
            $table->decimal('min_amount', 12, 2)->nullable(); // minimum subscription amount to qualify
            $table->unsignedInteger('max_uses')->nullable();  // null = unlimited
            $table->unsignedInteger('used_count')->default(0);
            $table->unsignedInteger('max_uses_per_customer')->nullable();

            // Scope: restrict to specific plans (JSON array of plan IDs) or null = all plans
            $table->json('plan_ids')->nullable();

            // Validity window
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
