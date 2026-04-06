<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('symbol', 10);
            $table->enum('symbol_position', ['before', 'after'])->default('after');
            $table->tinyInteger('decimal_places')->default(2);
            $table->decimal('rate_to_sar', 12, 6)->default(1);
            $table->string('country_code', 2)->nullable();
            $table->string('flag_emoji', 10)->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
