<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One-time setup fee charged on top of the recurring subscription.
 * Covers real onboarding work: training, data migration, user setup,
 * template customization. Stored per (plan, country) so each market
 * has its own positioning, just like the subscription prices.
 *
 * Yearly subscribers get 50% off this fee (handled in PHP, not DB),
 * and launch-offer clinics get it waived entirely (handled at
 * checkout time, not stored here).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plan_prices', function (Blueprint $table) {
            $table->decimal('setup_fee', 12, 2)->default(0)->after('yearly_price');
        });
    }

    public function down(): void
    {
        Schema::table('plan_prices', function (Blueprint $table) {
            $table->dropColumn('setup_fee');
        });
    }
};
