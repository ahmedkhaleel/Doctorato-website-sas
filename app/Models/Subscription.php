<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subscription extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'pricing_plan_id',
        'demo_request_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'clinic_name',
        'country',
        'city',
        'billing_cycle',
        'amount',
        'currency',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'reference',
        'metadata',
        'referral_code',
        'referred_by_subscription_id',
        'referral_credit_cents',
        'paused_at',
        'paused_until',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
        'referral_credit_cents' => 'integer',
        'paused_at' => 'datetime',
        'paused_until' => 'datetime',
    ];

    /** True iff the subscription is currently paused. */
    public function isPaused(): bool
    {
        return $this->paused_at !== null;
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by_subscription_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by_subscription_id');
    }

    /**
     * Generate a globally-unique, human-readable referral code.
     * Format: DOC- + 8 chars from the unambiguous alphabet (no O/0,
     * no I/1/L) so customers can read their code out loud without
     * confusion. Collisions are vanishingly rare (32^8 ≈ 1T) but we
     * still loop-until-unique to be safe.
     */
    public static function generateReferralCode(): string
    {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        do {
            $code = 'DOC-';
            for ($i = 0; $i < 8; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (self::where('referral_code', $code)->exists());
        return $code;
    }

    protected static function booted(): void
    {
        static::creating(function (self $sub) {
            if (empty($sub->reference)) {
                $sub->reference = 'SUB-' . strtoupper(Str::random(10));
            }
        });
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class, 'pricing_plan_id');
    }

    public function demoRequest(): BelongsTo
    {
        return $this->belongsTo(DemoRequest::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function latestInvoice()
    {
        return $this->hasOne(Invoice::class)->latestOfMany();
    }
}
