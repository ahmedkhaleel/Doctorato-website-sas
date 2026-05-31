<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            if (! Schema::hasColumn('pricing_plans', 'included_doctors')) {
                $table->unsignedInteger('included_doctors')->default(1)->after('max_doctors');
            }
            if (! Schema::hasColumn('pricing_plans', 'per_extra_doctor_price')) {
                $table->decimal('per_extra_doctor_price', 10, 2)->nullable()->after('included_doctors');
            }
            if (! Schema::hasColumn('pricing_plans', 'is_contact_sales')) {
                $table->boolean('is_contact_sales')->default(false)->after('per_extra_doctor_price');
            }
            if (! Schema::hasColumn('pricing_plans', 'trial_days')) {
                $table->unsignedInteger('trial_days')->default(30)->after('is_contact_sales');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            foreach (['included_doctors', 'per_extra_doctor_price', 'is_contact_sales', 'trial_days'] as $col) {
                if (Schema::hasColumn('pricing_plans', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
