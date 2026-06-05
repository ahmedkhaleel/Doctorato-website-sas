<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds location + online-presence columns to demo_requests so the
 * smarter intake form can capture where the clinic operates and how
 * to find them online. Existing `country` and `country_code` columns
 * are kept as-is; `governorate` / `city` / `address` are new and
 * nullable so legacy rows continue to work.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('demo_requests', 'governorate')) {
                $table->string('governorate', 64)->nullable()->after('country');
            }
            if (! Schema::hasColumn('demo_requests', 'city')) {
                $table->string('city', 64)->nullable()->after('governorate');
            }
            if (! Schema::hasColumn('demo_requests', 'address')) {
                $table->string('address', 500)->nullable()->after('city');
            }
            if (! Schema::hasColumn('demo_requests', 'website_url')) {
                // Stores the clinic website OR any social media URL
                // (Facebook page, Instagram handle URL, etc.) — single
                // free-form field so the visitor isn't forced to label
                // which platform they're sharing.
                $table->string('website_url', 500)->nullable()->after('address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            foreach (['governorate', 'city', 'address', 'website_url'] as $col) {
                if (Schema::hasColumn('demo_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
