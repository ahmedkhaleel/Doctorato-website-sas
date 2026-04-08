<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('add_ons', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();

            $table->decimal('price_egp', 12, 2);
            $table->enum('period', ['monthly', 'yearly', 'one_time'])->default('monthly');

            $table->string('icon')->nullable(); // name of SVG icon ('user', 'sms', 'whatsapp', etc.)
            $table->string('badge_ar')->nullable(); // e.g. "الأكثر طلباً"
            $table->string('badge_en')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('display_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('add_ons');
    }
};
