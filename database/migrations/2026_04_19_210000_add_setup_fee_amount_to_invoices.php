<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pull the setup-fee portion of an invoice into its own column so we
 * can GROUP BY and SUM() without reaching into the line_items JSON.
 * Makes the analytics dashboard (setup vs recurring vs add-on) a
 * simple column aggregation instead of a JSON walk.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('setup_fee_amount', 12, 2)->default(0)->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('setup_fee_amount');
        });
    }
};
