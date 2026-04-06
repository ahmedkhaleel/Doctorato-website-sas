<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'description_ar',
        'description_en',
        'monthly_price',
        'yearly_price',
        'currency',
        'is_popular',
        'is_custom',
        'features_ar',
        'features_en',
        'modules_included',
        'max_users',
        'max_patients',
        'support_level',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'features_ar' => 'array',
        'features_en' => 'array',
        'modules_included' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
