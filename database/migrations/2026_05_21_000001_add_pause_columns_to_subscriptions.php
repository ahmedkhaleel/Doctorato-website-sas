<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Customer-initiated subscription pause.
 *
 * Why columns instead of a 'paused' enum value:
 *   - Altering the `status` ENUM on a populated table is a
 *     non-idempotent migration that locks the table for the rewrite
 *     — we documented that we avoid those changes (CHANGELOG, R7).
 *   - The pause is orthogonal to the state machine. A sub that pauses
 *     during active still has its ends_at; it just shouldn't renew
 *     while paused_at is set. Modeling it as a flag keeps the renewal
 *     query simple (`WHERE status='active' AND paused_at IS NULL`).
 *
 * Columns:
 *   - paused_at         = when the customer hit Pause
 *   - paused_until      = auto-resume date (null = manual resume only)
 *
 * Composite index for the auto-resume cron query.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('subscriptions', 'paused_at')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->timestamp('paused_at')->nullable()->after('cancelled_at');
                $table->timestamp('paused_until')->nullable()->after('paused_at');
                $table->index(['paused_at', 'paused_until'], 'idx_sub_pause_resume');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('subscriptions', 'paused_at')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->dropIndex('idx_sub_pause_resume');
                $table->dropColumn(['paused_at', 'paused_until']);
            });
        }
    }
};
