<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name_ar',
        'name_en',
        'symbol',
        'symbol_position',
        'decimal_places',
        'rate_to_sar',
        'country_code',
        'flag_emoji',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'rate_to_sar' => 'decimal:6',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function convertFromSar(float $amount): float
    {
        return round($amount * $this->rate_to_sar, $this->decimal_places);
    }

    public function formatPrice(float $amount, string $locale = 'ar'): string
    {
        $converted = $this->convertFromSar($amount);
        $formatted = number_format($converted, $this->decimal_places);

        if ($this->symbol_position === 'before') {
            return $this->symbol . $formatted;
        }

        return $formatted . ' ' . $this->symbol;
    }
}
