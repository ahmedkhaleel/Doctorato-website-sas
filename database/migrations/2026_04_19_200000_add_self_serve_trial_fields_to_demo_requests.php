<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Self-serve trial support on top of the existing demo_requests table.
 *
 * We reuse demo_requests rather than creating a parallel "trial_signups"
 * table because the downstream tooling (admin reminders, trial-ends-at
 * logic, analytics) already understands this shape. The new columns
 * just mark *how* the trial started and where it's provisioned.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            // Did the user self-serve a trial, or did we book a demo call?
            $table->boolean('is_instant_trial')->default(false)->after('status');
            // e.g. "alshifa" → alshifa.doctorato.app — uniqueness enforced
            // below via index; nullable so demo-call rows don't need it.
            $table->string('subdomain', 60)->nullable()->after('is_instant_trial');
            $table->unique('subdomain');
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            $table->dropUnique(['subdomain']);
            $table->dropColumn(['is_instant_trial', 'subdomain']);
        });
    }
};
