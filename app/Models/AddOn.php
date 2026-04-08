<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    use HasFactory;

    protected $table = 'add_ons';

    protected $fillable = [
        'name_ar', 'name_en',
        'description_ar', 'description_en',
        'price_egp', 'period',
        'icon', 'badge_ar', 'badge_en',
        'is_active', 'is_featured', 'display_order',
    ];

    protected $casts = [
        'price_egp' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }
}
