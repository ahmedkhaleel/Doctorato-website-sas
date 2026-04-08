<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            // Unique machine name — used by code: EmailTemplate::for('welcome')
            $table->string('key')->unique();

            // Bilingual fields — subject + HTML body
            $table->string('subject_ar');
            $table->string('subject_en');
            $table->longText('body_ar');
            $table->longText('body_en');

            // Friendly label and description (for admin UI)
            $table->string('label_ar');
            $table->string('label_en');
            $table->string('description')->nullable();

            // Documented variables for admins, e.g. {{name}}, {{amount}}
            $table->json('variables')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
