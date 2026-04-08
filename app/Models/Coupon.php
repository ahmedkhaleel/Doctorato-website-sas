<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description_ar', 'description_en',
        'type', 'value',
        'min_amount', 'max_uses', 'used_count', 'max_uses_per_customer',
        'plan_ids',
        'starts_at', 'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'plan_ids' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /** Is the coupon currently valid (active + within window + under max uses)? */
    public function isValid(?float $amount = null, ?int $planId = null): bool
    {
        if (!$this->is_active) return false;

        $now = now();
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;

        if ($amount !== null && $this->min_amount && $amount < (float) $this->min_amount) return false;

        if ($planId && !empty($this->plan_ids) && !in_array($planId, $this->plan_ids, true)) return false;

        return true;
    }

    /** Compute the discount amount for a given subtotal. */
    public function computeDiscount(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * ((float) $this->value / 100), 2);
        }
        return min($amount, (float) $this->value);
    }

    /** Atomically register a new usage — call after a successful checkout. */
    public function markUsed(): void
    {
        $this->increment('used_count');
    }

    /** Humanized label: e.g. "30% off" or "100 EGP off". */
    public function getLabelAttribute(): string
    {
        return $this->type === 'percentage'
            ? ((int) $this->value) . '% off'
            : ((int) $this->value) . ' EGP off';
    }
}
