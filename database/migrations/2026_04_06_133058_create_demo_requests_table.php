<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('country_code', 10)->default('+966');
            $table->string('country')->nullable();
            $table->string('doctors_count')->nullable();
            $table->string('specialty')->nullable();
            $table->json('interested_modules')->nullable();
            $table->string('referral_source')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['new', 'contacted', 'demo_scheduled', 'demo_done', 'converted', 'lost'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_requests');
    }
};
