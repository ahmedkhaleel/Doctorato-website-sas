<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            // Client identity
            $table->string('client_name_ar');
            $table->string('client_name_en');
            $table->string('industry_ar')->nullable();   // e.g. "Dental clinic chain"
            $table->string('industry_en')->nullable();
            $table->string('location')->nullable();      // e.g. "Cairo, EG"
            $table->string('logo')->nullable();          // logo URL/path
            $table->string('hero_image')->nullable();    // featured image

            // Content (bilingual)
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('summary_ar')->nullable();
            $table->text('summary_en')->nullable();
            $table->longText('challenge_ar')->nullable();
            $table->longText('challenge_en')->nullable();
            $table->longText('solution_ar')->nullable();
            $table->longText('solution_en')->nullable();
            $table->longText('results_ar')->nullable();
            $table->longText('results_en')->nullable();

            // Quantified results — JSON: [{label_ar, label_en, value, suffix, icon}]
            $table->json('metrics')->nullable();

            // Tags / modules used — JSON array of strings
            $table->json('modules_used')->nullable();

            // Testimonial snippet from this client
            $table->text('testimonial_ar')->nullable();
            $table->text('testimonial_en')->nullable();
            $table->string('testimonial_author')->nullable();
            $table->string('testimonial_role')->nullable();

            // SEO (per-case)
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();

            // State
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('display_order')->default(0);
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();
            $table->index(['is_published', 'display_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};
