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

    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    /**
     * Resolve the pricing row for a given country, falling back to the
     * plan's default (monthly_price/yearly_price on the plan itself).
     * Returns an array with a stable shape so the Vue layer doesn't have
     * to handle nulls.
     */
    public function priceFor(string $countryCode = 'EG'): array
    {
        $row = $this->prices->firstWhere(fn ($p) => $p->country_code === $countryCode && $p->is_active);
        // If the requested country isn't configured, fall back to EG (the home market).
        if (!$row) {
            $row = $this->prices->firstWhere(fn ($p) => $p->country_code === 'EG' && $p->is_active);
        }

        if ($row) {
            $setupFee = (float) ($row->setup_fee ?? 0);
            return [
                'monthly' => (float) $row->monthly_price,
                'yearly' => (float) $row->yearly_price,
                'setup_fee' => $setupFee,
                // Yearly subscribers get 50% off the one-time setup fee.
                'setup_fee_yearly' => round($setupFee * 0.5, 2),
                'currency' => $row->currency_code,
                'currency_symbol' => $row->currency_symbol,
                'country_code' => $row->country_code,
                'country_name_ar' => $row->country_name_ar,
                'country_name_en' => $row->country_name_en,
                'country_flag' => $row->country_flag,
                'source' => 'country',
            ];
        }

        // Last resort — use the plan's own columns. Keeps the site working
        // even before any PlanPrice rows are seeded.
        return [
            'monthly' => (float) $this->monthly_price,
            'yearly' => (float) $this->yearly_price,
            'setup_fee' => 0.0,
            'setup_fee_yearly' => 0.0,
            'currency' => $this->currency ?: 'EGP',
            'currency_symbol' => 'ج.م',
            'country_code' => 'EG',
            'country_name_ar' => 'مصر',
            'country_name_en' => 'Egypt',
            'country_flag' => '🇪🇬',
            'source' => 'default',
        ];
    }
}
