<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DemoRequest extends Model
{
    use HasFactory;

    public const TRIAL_DAYS = 15;

    protected $fillable = [
        'clinic_name',
        'full_name',
        'email',
        'phone',
        'country_code',
        'country',
        'doctors_count',
        'specialty',
        'interested_modules',
        'referral_source',
        'notes',
        'status',
        'admin_notes',
        'contacted_at',
        'trial_started_at',
        'trial_ends_at',
        'trial_status',
        'trial_expiry_notified',
        'trial_expiry_notified_at',
        'admin_reminder_seen',
    ];

    protected $casts = [
        'interested_modules' => 'array',
        'contacted_at' => 'datetime',
        'trial_started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'trial_expiry_notified' => 'boolean',
        'trial_expiry_notified_at' => 'datetime',
        'admin_reminder_seen' => 'boolean',
    ];

    protected $appends = [
        'trial_days_left',
        'trial_progress',
        'is_trial_active',
        'is_trial_expired',
        'is_trial_ending_soon',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $demo) {
            if (! $demo->trial_started_at) {
                $demo->trial_started_at = now();
            }
            if (! $demo->trial_ends_at) {
                $demo->trial_ends_at = Carbon::parse($demo->trial_started_at)->addDays(self::TRIAL_DAYS);
            }
            if (! $demo->trial_status) {
                $demo->trial_status = 'active';
            }
        });
    }

    public function getTrialDaysLeftAttribute(): int
    {
        if (! $this->trial_ends_at) return 0;
        $diff = now()->diffInDays($this->trial_ends_at, false);
        return (int) max(0, ceil($diff));
    }

    public function getTrialProgressAttribute(): int
    {
        if (! $this->trial_started_at || ! $this->trial_ends_at) return 0;
        $total = $this->trial_started_at->diffInSeconds($this->trial_ends_at);
        if ($total <= 0) return 100;
        $elapsed = $this->trial_started_at->diffInSeconds(now());
        return (int) min(100, max(0, round(($elapsed / $total) * 100)));
    }

    public function getIsTrialActiveAttribute(): bool
    {
        return $this->trial_status === 'active' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function getIsTrialExpiredAttribute(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isPast();
    }

    public function getIsTrialEndingSoonAttribute(): bool
    {
        return $this->is_trial_active && $this->trial_days_left <= 3;
    }

    public function scopeTrialActive($query)
    {
        return $query->where('trial_status', 'active')->where('trial_ends_at', '>', now());
    }

    public function scopeTrialExpired($query)
    {
        return $query->where('trial_ends_at', '<=', now())->whereIn('trial_status', ['active', 'expired']);
    }

    public function scopeNeedsExpiryNotification($query)
    {
        return $query->where('trial_status', 'active')
            ->where('trial_ends_at', '<=', now())
            ->where('trial_expiry_notified', false);
    }
}
