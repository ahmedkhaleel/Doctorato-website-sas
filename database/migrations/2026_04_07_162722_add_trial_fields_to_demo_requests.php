<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->timestamp('trial_started_at')->nullable()->after('contacted_at');
            $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at');
            $table->enum('trial_status', ['active', 'expired', 'extended', 'converted', 'cancelled'])
                ->default('active')
                ->after('trial_ends_at');
            $table->boolean('trial_expiry_notified')->default(false)->after('trial_status');
            $table->timestamp('trial_expiry_notified_at')->nullable()->after('trial_expiry_notified');
            $table->boolean('admin_reminder_seen')->default(false)->after('trial_expiry_notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->dropColumn([
                'trial_started_at',
                'trial_ends_at',
                'trial_status',
                'trial_expiry_notified',
                'trial_expiry_notified_at',
                'admin_reminder_seen',
            ]);
        });
    }
};
