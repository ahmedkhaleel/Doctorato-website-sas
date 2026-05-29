<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Login-attempt audit trail.
 *
 * The Laravel `throttle` middleware caps login attempts but doesn't
 * leave a paper trail — once the cache key expires you lose all
 * forensic evidence of a probe.
 *
 * This table records EVERY admin login attempt (success + failure)
 * so:
 *   - You can answer "did anyone try my account at 3 AM" weeks later.
 *   - The AccountLockoutService can read a sliding window from this
 *     table to make a deterministic lockout decision instead of
 *     relying on cache state that resets across deploys.
 *   - The dashboard can surface "5 failed attempts on admin@x in the
 *     last hour" the moment they happen.
 *
 * Indexed on (email_hashed, attempted_at) for the lockout query and
 * (ip, attempted_at) for the per-IP probe view.
 *
 * email_hashed is SHA-256 of the lowercased email instead of the raw
 * value — so even a leaked DB dump of this table doesn't enumerate
 * the admin email list.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('login_attempts')) return;

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email_hashed', 64)->index();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->boolean('success')->default(false);
            // Why the attempt was rejected (bad_password, inactive, 2fa_fail,
            // locked_out). Helps separate noise from real attacks.
            $table->string('reason', 32)->nullable();
            $table->timestamp('attempted_at')->useCurrent();

            $table->index(['email_hashed', 'attempted_at'], 'idx_la_email_time');
            $table->index(['ip', 'attempted_at'], 'idx_la_ip_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
