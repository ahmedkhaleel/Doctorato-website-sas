<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Launch-pricing strategy fields.
 *
 * Adds the columns the 4-tier launch pricing model needs without
 * altering the existing monthly_price / yearly_price (those become
 * the "regular" anchor prices that get displayed with a strike-
 * through alongside the discounted launch price).
 *
 *   monthly_price_launch / yearly_price_launch
 *       - The actual prices customers pay during the launch
 *         offer. monthly_price (existing) is shown crossed out
 *         next to it as the "anchor".
 *
 *   setup_fee / setup_fee_launch
 *       - The implementation/onboarding fee. setup_fee_launch is
 *         what the customer pays. The yearly subscription gets an
 *         additional 50% off (computed at render time, not
 *         persisted, so an admin can adjust the rate in one place).
 *
 *   is_launch_offer_active + launch_offer_ends_at
 *       - Toggle the entire offer on/off + drives the countdown
 *         banner. When inactive, prices fall back to the regular
 *         monthly_price / yearly_price.
 *
 *   supports_installments + installment_count + installment_split
 *       - The 3-instalment annual payment split (40 / 30 / 30).
 *         installment_split holds the percentages so a future
 *         change (4-payment, 50/25/25, etc.) is a config edit.
 *
 *   included_specialties_count / included_specialties_pool
 *       - 'one' / 'three' / 'all'. The pool lists which specialties
 *         a customer may pick from when the tier doesn't include
 *         all of them (Starter / Growth).
 *
 *   max_doctors / max_staff / max_branches / storage_gb
 *       - Per-tier hard limits, surfaced in the comparison table.
 *
 *   support_response_hours
 *       - Numeric so the dashboard can sort by it.
 *
 * All columns added behind hasColumn guards so the migration can
 * re-run safely (Doctorato op rule R7).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('pricing_plans', 'monthly_price_launch')) {
                $table->decimal('monthly_price_launch', 10, 2)->nullable()->after('monthly_price');
            }
            if (!Schema::hasColumn('pricing_plans', 'yearly_price_launch')) {
                $table->decimal('yearly_price_launch', 10, 2)->nullable()->after('yearly_price');
            }
            if (!Schema::hasColumn('pricing_plans', 'setup_fee')) {
                $table->decimal('setup_fee', 10, 2)->default(0)->after('yearly_price_launch');
            }
            if (!Schema::hasColumn('pricing_plans', 'setup_fee_launch')) {
                $table->decimal('setup_fee_launch', 10, 2)->nullable()->after('setup_fee');
            }
            if (!Schema::hasColumn('pricing_plans', 'yearly_setup_discount_pct')) {
                $table->unsignedTinyInteger('yearly_setup_discount_pct')->default(50)->after('setup_fee_launch');
            }
            if (!Schema::hasColumn('pricing_plans', 'is_launch_offer_active')) {
                $table->boolean('is_launch_offer_active')->default(false)->after('yearly_setup_discount_pct');
            }
            if (!Schema::hasColumn('pricing_plans', 'launch_offer_ends_at')) {
                $table->timestamp('launch_offer_ends_at')->nullable()->after('is_launch_offer_active');
            }
            if (!Schema::hasColumn('pricing_plans', 'supports_installments')) {
                $table->boolean('supports_installments')->default(false)->after('launch_offer_ends_at');
            }
            if (!Schema::hasColumn('pricing_plans', 'installment_count')) {
                $table->unsignedTinyInteger('installment_count')->default(3)->after('supports_installments');
            }
            if (!Schema::hasColumn('pricing_plans', 'installment_split')) {
                $table->json('installment_split')->nullable()->after('installment_count');
            }
            if (!Schema::hasColumn('pricing_plans', 'included_specialties_count')) {
                $table->string('included_specialties_count', 16)->default('all')->after('installment_split');
            }
            if (!Schema::hasColumn('pricing_plans', 'included_specialties_pool')) {
                $table->json('included_specialties_pool')->nullable()->after('included_specialties_count');
            }
            if (!Schema::hasColumn('pricing_plans', 'max_doctors')) {
                $table->integer('max_doctors')->nullable()->after('max_patients');
            }
            if (!Schema::hasColumn('pricing_plans', 'max_staff')) {
                $table->integer('max_staff')->nullable()->after('max_doctors');
            }
            if (!Schema::hasColumn('pricing_plans', 'max_branches')) {
                $table->integer('max_branches')->nullable()->after('max_staff');
            }
            if (!Schema::hasColumn('pricing_plans', 'storage_gb')) {
                $table->integer('storage_gb')->nullable()->after('max_branches');
            }
            if (!Schema::hasColumn('pricing_plans', 'support_response_hours')) {
                $table->integer('support_response_hours')->nullable()->after('support_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pricing_plans', function (Blueprint $table) {
            $cols = [
                'monthly_price_launch', 'yearly_price_launch',
                'setup_fee', 'setup_fee_launch', 'yearly_setup_discount_pct',
                'is_launch_offer_active', 'launch_offer_ends_at',
                'supports_installments', 'installment_count', 'installment_split',
                'included_specialties_count', 'included_specialties_pool',
                'max_doctors', 'max_staff', 'max_branches', 'storage_gb',
                'support_response_hours',
            ];
            foreach ($cols as $c) {
                if (Schema::hasColumn('pricing_plans', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
