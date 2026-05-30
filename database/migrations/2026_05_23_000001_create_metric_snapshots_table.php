<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Daily one-row-per-day snapshot of the headline KPIs.
 *
 * The owner dashboard's "today" numbers come from a live query
 * against subscriptions (Phase 26). That answers "where are we
 * now?" but not "are we trending up or down?" — the historical
 * shape of MRR over a quarter is more useful than the spot value.
 *
 * One row per day, written by `metrics:snapshot` at 23:55 server
 * time. The date column has a unique index so a re-run on the
 * same day overwrites rather than inserts a duplicate.
 *
 * 5 years × 365 rows ≈ 1825 rows — tiny. No retention pruning
 * planned (the historical trend gets more valuable over time and
 * the storage cost is in the kilobyte range).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('metric_snapshots')) return;

        Schema::create('metric_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('snapshot_date')->unique();
            $table->decimal('mrr_sar', 14, 2)->default(0);
            $table->decimal('arr_sar', 14, 2)->default(0);
            $table->unsignedInteger('active_subs')->default(0);
            $table->unsignedInteger('paused_subs')->default(0);
            $table->unsignedInteger('past_due_subs')->default(0);
            $table->decimal('arpu_sar', 14, 2)->default(0);
            $table->decimal('churn_30d_pct', 6, 2)->default(0);
            $table->unsignedInteger('new_subs')->default(0);
            $table->unsignedInteger('cancelled_subs')->default(0);
            $table->timestamp('captured_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metric_snapshots');
    }
};
