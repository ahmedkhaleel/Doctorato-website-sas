<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Launch pricing for add-ons. Mirrors the pattern from
 * Phase A's pricing_plans migration so the public site can render
 * a strikethrough anchor + the launch price + a "🔥 launch" badge
 * for each add-on, consistent with the plan cards.
 *
 *   price_egp_launch     = the launch (discounted) price
 *   is_launch_active     = independent toggle per add-on, so admin
 *                          can wind specific add-ons off the offer
 *                          without touching the rest
 *   included_in_plans    = JSON array of plan slugs where this
 *                          add-on is bundled FREE. The add-on card
 *                          shows "مجاناً مع Pro" in those tiers.
 *
 * All columns guarded by hasColumn checks (Doctorato op rule R7).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('add_ons', function (Blueprint $table) {
            if (!Schema::hasColumn('add_ons', 'price_egp_launch')) {
                $table->decimal('price_egp_launch', 12, 2)->nullable()->after('price_egp');
            }
            if (!Schema::hasColumn('add_ons', 'is_launch_active')) {
                $table->boolean('is_launch_active')->default(false)->after('price_egp_launch');
            }
            if (!Schema::hasColumn('add_ons', 'included_in_plans')) {
                $table->json('included_in_plans')->nullable()->after('is_launch_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('add_ons', function (Blueprint $table) {
            foreach (['price_egp_launch', 'is_launch_active', 'included_in_plans'] as $c) {
                if (Schema::hasColumn('add_ons', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
