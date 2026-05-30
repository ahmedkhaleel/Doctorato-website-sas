<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Track WHERE the magic link was consumed, not just where it was
 * requested. The two values diverging is the classic "attacker
 * intercepted my email" signal:
 *   - Issued from IP A (legitimate request from customer)
 *   - Consumed from IP B  (attacker who got the email)
 *
 * PortalAbuseDetector reads this column to flag the mismatch.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('customer_login_tokens', 'consumed_ip')) {
            Schema::table('customer_login_tokens', function (Blueprint $table) {
                $table->string('consumed_ip', 45)->nullable()->after('ip_address');
                $table->string('consumed_ua', 255)->nullable()->after('consumed_ip');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customer_login_tokens', 'consumed_ip')) {
            Schema::table('customer_login_tokens', function (Blueprint $table) {
                $table->dropColumn(['consumed_ip', 'consumed_ua']);
            });
        }
    }
};
