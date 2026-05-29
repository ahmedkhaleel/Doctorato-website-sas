<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marketing opt-in column on demo_requests.
 *
 * Defaults to true on existing rows because pre-portal users
 * already opted in by completing the demo request — they expected
 * follow-up emails. New rows default to true too; the portal UI
 * is where customers toggle it off.
 *
 * Transactional emails (login link, invoice receipts, dunning)
 * IGNORE this flag — they're contractual, not marketing. Only the
 * newsletter, product update, and re-engagement sends honor it.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('demo_requests', 'marketing_opt_in')) {
            Schema::table('demo_requests', function (Blueprint $table) {
                $table->boolean('marketing_opt_in')->default(true)->after('admin_reminder_seen');
                $table->timestamp('marketing_opted_out_at')->nullable()->after('marketing_opt_in');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('demo_requests', 'marketing_opt_in')) {
            Schema::table('demo_requests', function (Blueprint $table) {
                $table->dropColumn(['marketing_opt_in', 'marketing_opted_out_at']);
            });
        }
    }
};
