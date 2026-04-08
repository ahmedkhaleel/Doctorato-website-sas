<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseStudy extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'client_name_ar', 'client_name_en',
        'industry_ar', 'industry_en',
        'location', 'logo', 'hero_image',
        'title_ar', 'title_en',
        'summary_ar', 'summary_en',
        'challenge_ar', 'challenge_en',
        'solution_ar', 'solution_en',
        'results_ar', 'results_en',
        'metrics', 'modules_used',
        'testimonial_ar', 'testimonial_en', 'testimonial_author', 'testimonial_role',
        'seo_title', 'seo_description',
        'is_published', 'is_featured', 'display_order',
    ];

    protected $casts = [
        'metrics' => 'array',
        'modules_used' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
