<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable(); // cached snapshot
            $table->string('user_role')->nullable();

            $table->string('action'); // e.g. "created", "updated", "deleted", "logged_in"
            $table->string('subject_type')->nullable(); // Morph target class
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_label')->nullable(); // human-readable snapshot

            $table->text('description')->nullable();
            $table->json('changes')->nullable(); // {before: {...}, after: {...}}
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
