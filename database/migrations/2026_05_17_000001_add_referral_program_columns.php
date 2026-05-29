<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Referral program — two-sided.
 *
 * On demo_requests:
 *   - referred_by_code = the code captured from ?ref= when the lead
 *     signed up. We store it on the lead, not the subscription,
 *     because the link is clicked BEFORE the subscription exists.
 *
 * On subscriptions:
 *   - referral_code = this customer's own share code (auto-generated
 *     on activation). Format: DOC- + 8 chars upper-case alphanum.
 *   - referred_by_subscription_id = resolved foreign key once we
 *     map the demo_request's referred_by_code → an existing sub.
 *   - referral_credit_cents = accumulated credit owed to this sub
 *     (when one of THEIR referrals activates). Cents, not decimal,
 *     to keep arithmetic exact and currency-agnostic.
 *
 * All columns are idempotent (Schema::hasColumn checks) so this can
 * be safely re-run on the production host.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('demo_requests', 'referred_by_code')) {
                $table->string('referred_by_code', 32)->nullable()->after('referral_source');
                $table->index('referred_by_code', 'idx_demo_referred_by');
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'referral_code')) {
                $table->string('referral_code', 32)->nullable()->after('reference');
                $table->unique('referral_code', 'uniq_sub_referral_code');
            }
            if (!Schema::hasColumn('subscriptions', 'referred_by_subscription_id')) {
                $table->unsignedBigInteger('referred_by_subscription_id')->nullable()
                    ->after('referral_code');
                $table->index('referred_by_subscription_id', 'idx_sub_referred_by');
            }
            if (!Schema::hasColumn('subscriptions', 'referral_credit_cents')) {
                $table->unsignedBigInteger('referral_credit_cents')->default(0)
                    ->after('referred_by_subscription_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            if (Schema::hasColumn('demo_requests', 'referred_by_code')) {
                $table->dropIndex('idx_demo_referred_by');
                $table->dropColumn('referred_by_code');
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('subscriptions', 'referral_code')) {
                $table->dropUnique('uniq_sub_referral_code');
                $table->dropColumn('referral_code');
            }
            if (Schema::hasColumn('subscriptions', 'referred_by_subscription_id')) {
                $table->dropIndex('idx_sub_referred_by');
                $table->dropColumn('referred_by_subscription_id');
            }
            if (Schema::hasColumn('subscriptions', 'referral_credit_cents')) {
                $table->dropColumn('referral_credit_cents');
            }
        });
    }
};
