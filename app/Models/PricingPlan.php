<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name_ar', 'name_en', 'slug',
        'description_ar', 'description_en',
        'monthly_price', 'yearly_price',
        'monthly_price_launch', 'yearly_price_launch',
        'setup_fee', 'setup_fee_launch', 'yearly_setup_discount_pct',
        'is_launch_offer_active', 'launch_offer_ends_at',
        'supports_installments', 'installment_count', 'installment_split',
        'included_specialties_count', 'included_specialties_pool',
        'currency',
        'is_popular', 'is_custom',
        'features_ar', 'features_en', 'modules_included',
        'max_users', 'max_patients', 'max_doctors', 'max_staff',
        'max_branches', 'storage_gb',
        'support_level', 'support_response_hours',
        'display_order', 'is_active',
    ];

    protected $casts = [
        'features_ar' => 'array',
        'features_en' => 'array',
        'modules_included' => 'array',
        'installment_split' => 'array',
        'included_specialties_pool' => 'array',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'monthly_price_launch' => 'decimal:2',
        'yearly_price_launch' => 'decimal:2',
        'setup_fee' => 'decimal:2',
        'setup_fee_launch' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
        'is_launch_offer_active' => 'boolean',
        'supports_installments' => 'boolean',
        'launch_offer_ends_at' => 'datetime',
    ];

    /**
     * The actual price the customer pays right now, given the launch
     * offer state. Drops back to the regular column when the offer is
     * inactive or expired. Centralised so every surface (Vue, API,
     * checkout, ROI calc) reads the same source of truth.
     */
    public function activeMonthlyPrice(): float
    {
        return $this->isLaunchActive()
            ? (float) ($this->monthly_price_launch ?? $this->monthly_price)
            : (float) $this->monthly_price;
    }

    public function activeYearlyPrice(): float
    {
        return $this->isLaunchActive()
            ? (float) ($this->yearly_price_launch ?? $this->yearly_price)
            : (float) $this->yearly_price;
    }

    public function activeSetupFee(string $cycle = 'monthly'): float
    {
        $base = $this->isLaunchActive()
            ? (float) ($this->setup_fee_launch ?? $this->setup_fee)
            : (float) $this->setup_fee;
        if ($cycle === 'yearly' && $this->yearly_setup_discount_pct > 0) {
            return round($base * (1 - $this->yearly_setup_discount_pct / 100), 2);
        }
        return $base;
    }

    public function isLaunchActive(): bool
    {
        if (!$this->is_launch_offer_active) return false;
        if ($this->launch_offer_ends_at && $this->launch_offer_ends_at->isPast()) return false;
        return true;
    }

    /**
     * Build the per-instalment schedule for the yearly + 3-payment
     * option. Returns an empty array when the plan doesn't support
     * instalments. Percentages come from installment_split (defaults
     * to 40/30/30) so the model stays config-driven.
     */
    public function yearlyInstallments(): array
    {
        if (!$this->supports_installments) return [];
        $split = $this->installment_split ?: [40, 30, 30];
        $total = $this->activeYearlyPrice() + $this->activeSetupFee('yearly');
        $out = [];
        $running = 0;
        foreach ($split as $i => $pct) {
            $isLast = $i === count($split) - 1;
            $amount = $isLast
                ? round($total - $running, 2)            // last one absorbs rounding
                : round($total * $pct / 100, 2);
            $running += $amount;
            $out[] = [
                'index' => $i + 1,
                'pct' => $pct,
                'amount' => $amount,
                'due_after_months' => $i === 0 ? 0 : $i * 4,
            ];
        }
        return $out;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
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
