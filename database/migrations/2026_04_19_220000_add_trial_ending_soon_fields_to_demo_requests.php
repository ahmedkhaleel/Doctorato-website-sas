<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Trial ending soon" reminders (fired 3 days before the trial expires).
 * Kept as a separate flag from trial_expiry_notified so the same trial
 * can get BOTH emails — the heads-up and the final notification —
 * without the later call suppressing the earlier state.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->boolean('trial_ending_soon_notified')->default(false)->after('trial_expiry_notified');
            $table->timestamp('trial_ending_soon_notified_at')->nullable()->after('trial_ending_soon_notified');
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->dropColumn(['trial_ending_soon_notified', 'trial_ending_soon_notified_at']);
        });
    }
};
