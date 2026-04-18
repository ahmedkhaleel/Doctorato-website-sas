<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Per-country pricing for each pricing plan.
        // Admin sets explicit monthly/yearly prices per country — we don't
        // derive them from a currency conversion rate, because pricing in
        // each market has its own positioning logic (PPP, competition,
        // distribution costs) that a single FX rate can't capture.
        Schema::create('plan_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained()->cascadeOnDelete();

            $table->string('country_code', 2);      // ISO-3166-1 alpha-2, e.g. 'EG', 'SA'
            $table->string('country_name_ar');
            $table->string('country_name_en');
            $table->string('country_flag', 8)->nullable(); // emoji flag, e.g. '🇪🇬'

            $table->string('currency_code', 3);     // ISO 4217, e.g. 'EGP'
            $table->string('currency_symbol', 12);  // UI symbol: 'ج.م', 'ر.س', '$', '€'

            $table->decimal('monthly_price', 12, 2);
            $table->decimal('yearly_price', 12, 2);

            // Whether this country's prices are visible to the public. An
            // admin can draft new-market pricing without exposing it yet.
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // A plan can have at most one row per country.
            $table->unique(['pricing_plan_id', 'country_code']);
            $table->index('country_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_prices');
    }
};
