<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Outbound email audit + observability.
 *
 * Why a dedicated table when Laravel already logs to laravel.log:
 *   - The PII scrubber rewrites email addresses in log lines (Phase
 *     14) — correct for the log, but useless for "did jane@x.com
 *     get the welcome drip?" reachability questions.
 *   - Logs rotate (14d). A customer complaining 3 weeks later that
 *     they never got an invoice receipt has no answer there.
 *   - SMTP transport errors land in failed_jobs, not the log, so
 *     "is mail working?" requires two queries instead of one.
 *
 * MessageSending/MessageSent listeners (App\Listeners\LogEmailDelivery)
 * write one row per attempt. Status moves:
 *   queued    → row created when Mailable hits the queue
 *   sending   → row updated when the worker begins the SMTP handoff
 *   sent      → row updated when the SMTP server accepted (250 OK)
 *   failed    → row updated when the transport throws
 *
 * Why we store hashed_recipient AND a redacted display value:
 *   - hashed_recipient lets the admin search by full address without
 *     the DB itself carrying the email roster in plaintext.
 *   - recipient_display is "j***@example.com" (same shape as the
 *     PII scrubber) so the dashboard is human-readable.
 *
 * The mailable_class column carries the FQCN so a filter can ask
 * "show me all TrialDripMail sends" without joining anything.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_logs')) return;

        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('mailable_class', 191)->nullable();
            $table->string('subject', 255)->nullable();
            $table->string('hashed_recipient', 64)->index();
            $table->string('recipient_display', 80)->nullable();
            $table->string('status', 16)->default('queued');
            $table->string('message_id', 191)->nullable();
            $table->text('error')->nullable();
            $table->timestamp('queued_at')->useCurrent();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            // Hot paths: dashboard list (status+recency), per-recipient
            // search via hashed column (already indexed above),
            // failure-only view, and CSV export of mailable_class.
            $table->index(['status', 'queued_at'], 'idx_em_status_recent');
            $table->index('mailable_class', 'idx_em_class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
