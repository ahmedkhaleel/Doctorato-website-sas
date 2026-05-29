<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds invoices.dunning_stage — a small unsigned int (0-5) used by
 * the RunDunning console command to track which dunning step a
 * failed invoice has been through. Idempotent re-run-safe.
 *
 * Stage values map to the state machine in RunDunning:
 *   0 = no action yet (default)
 *   1 = first reminder sent
 *   2 = second reminder sent
 *   3 = final warning sent
 *   4 = subscription marked past_due
 *   5 = subscription canceled
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('invoices', 'dunning_stage')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->unsignedTinyInteger('dunning_stage')->default(0)->after('status');
                $table->index(['status', 'dunning_stage'], 'idx_inv_dunning');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('invoices', 'dunning_stage')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('idx_inv_dunning');
                $table->dropColumn('dunning_stage');
            });
        }
    }
};
