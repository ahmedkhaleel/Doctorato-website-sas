<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the columns the TwoFactorService writes to. TOTP secrets are
 * stored encrypted via Laravel's cast layer (set on the User model)
 * so a DB dump leak doesn't immediately give an attacker the codes.
 *
 * Idempotent across re-runs — each column is added only if the
 * schema doesn't already have it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                // Encrypted base32 TOTP secret. Nullable so legacy
                // users without 2FA stay valid.
                $table->text('two_factor_secret')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                // JSON array of single-use hex codes a user can punch
                // in if they lose their authenticator app.
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
            }
            if (!Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                // Set the moment the user finishes the enrolment flow.
                // We don't enforce 2FA until this is non-null so a
                // mid-setup browser close can be retried cleanly.
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('two_factor_confirmed_at');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at', 'last_login_ip'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
