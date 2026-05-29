<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Magic-link table for the customer portal.
 *
 * Why magic link instead of a password column on DemoRequest:
 *   - Customers don't need a separate identity — they already
 *     proved ownership of the email when they bought the plan.
 *   - Avoids a password reset flow (and the support tickets that
 *     come with it).
 *   - One less attack surface: nothing to brute-force.
 *
 * Schema:
 *   - email          — the recipient we'll mail the link to
 *   - token          — 64-char URL-safe random, hashed in storage
 *                       so a DB dump doesn't yield usable links
 *   - expires_at     — 15 minutes after issuance
 *   - used_at        — set when the link is clicked; we don't
 *                       delete the row so dupe-click attempts can
 *                       be detected
 *   - ip_address     — captured at issuance for forensics
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_login_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token_hash', 64);     // sha256(plain_token), 64 hex chars
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['email', 'expires_at']);
            $table->index('token_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_login_tokens');
    }
};
