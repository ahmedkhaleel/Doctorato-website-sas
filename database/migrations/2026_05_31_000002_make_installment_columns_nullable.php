<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make legacy installment columns nullable now that supports_installments
        // is always false post-reset. Both columns originally required values in
        // the seeder; post-reset they're not set at all.
        if (Schema::hasColumn('pricing_plans', 'installment_count')) {
            DB::statement('ALTER TABLE `pricing_plans` MODIFY `installment_count` INT UNSIGNED NULL');
        }
        if (Schema::hasColumn('pricing_plans', 'installment_split')) {
            DB::statement('ALTER TABLE `pricing_plans` MODIFY `installment_split` JSON NULL');
        }
        if (Schema::hasColumn('pricing_plans', 'monthly_price_launch')) {
            DB::statement('ALTER TABLE `pricing_plans` MODIFY `monthly_price_launch` DECIMAL(10,2) NULL');
        }
        if (Schema::hasColumn('pricing_plans', 'yearly_price_launch')) {
            DB::statement('ALTER TABLE `pricing_plans` MODIFY `yearly_price_launch` DECIMAL(10,2) NULL');
        }
        if (Schema::hasColumn('pricing_plans', 'setup_fee_launch')) {
            DB::statement('ALTER TABLE `pricing_plans` MODIFY `setup_fee_launch` DECIMAL(10,2) NULL');
        }
        if (Schema::hasColumn('pricing_plans', 'launch_offer_ends_at')) {
            DB::statement('ALTER TABLE `pricing_plans` MODIFY `launch_offer_ends_at` TIMESTAMP NULL');
        }
    }

    public function down(): void
    {
        // No-op — irreversible relaxation. Re-adding NOT NULL would require
        // backfilling defaults and could break the new plan rows.
    }
};
