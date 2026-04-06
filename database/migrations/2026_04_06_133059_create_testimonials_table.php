<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name_ar');
            $table->string('client_name_en');
            $table->string('clinic_name_ar')->nullable();
            $table->string('clinic_name_en')->nullable();
            $table->string('role_ar')->nullable();
            $table->string('role_en')->nullable();
            $table->text('review_ar');
            $table->text('review_en');
            $table->unsignedTinyInteger('rating')->default(5);
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
