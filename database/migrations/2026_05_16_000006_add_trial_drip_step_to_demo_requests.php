<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Track which step of the trial welcome drip the customer has
 * received. The existing TrialEndingSoonMail + TrialExpiredMail
 * fire on the back half of the trial; this new field powers the
 * FRONT half: welcome + feature tour + case study, so customers
 * stay engaged through the first 14 days instead of forgetting
 * they signed up.
 *
 *   step 0 = no welcome email sent yet
 *   step 1 = welcome (day 0)
 *   step 2 = feature tour (day 3)
 *   step 3 = case study (day 7)
 *
 * The "ending soon" reminder and "expired" notice are tracked
 * separately on their own columns and live on the same row.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('demo_requests', 'trial_drip_step')) {
            Schema::table('demo_requests', function (Blueprint $table) {
                $table->unsignedTinyInteger('trial_drip_step')->default(0)
                    ->after('trial_status');
                $table->timestamp('trial_drip_last_sent_at')->nullable()
                    ->after('trial_drip_step');
                // Composite index for the cron query: find trials where
                // step < 3 and trial started long enough ago to bump it.
                $table->index(['trial_drip_step', 'trial_started_at'], 'idx_demo_drip');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('demo_requests', 'trial_drip_step')) {
            Schema::table('demo_requests', function (Blueprint $table) {
                $table->dropIndex('idx_demo_drip');
                $table->dropColumn(['trial_drip_step', 'trial_drip_last_sent_at']);
            });
        }
    }
};
