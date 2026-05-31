<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('demo_requests', 'facility_type')) {
                // solo / clinic / polyclinic / hospital
                $table->string('facility_type', 32)->nullable()->after('doctors_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('demo_requests', function (Blueprint $table) {
            if (Schema::hasColumn('demo_requests', 'facility_type')) {
                $table->dropColumn('facility_type');
            }
        });
    }
};
