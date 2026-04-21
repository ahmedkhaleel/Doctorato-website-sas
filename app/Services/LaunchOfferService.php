<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;

/**
 * Scarcity engine for the "first 50 clinics" launch offer.
 *
 * We count how many active/paid subscriptions exist, subtract from the
 * configured total (default 50), and expose the remainder to the UI so
 * the banner can read "باقي 37 مقعد من 50" — a real number, not a
 * cosmetic one. When the slots run out, the banner flips to "sold out"
 * state and the 30% + waived-setup perks stop being offered.
 *
 * Settings keys (all in the 'launch' group, editable from admin):
 *   - launch_offer_active : '1' | '0' (default '1')
 *   - launch_offer_total  : integer slots (default 50)
 */
class LaunchOfferService
{
    protected const CACHE_TTL = 60; // seconds — tight enough to feel live, cheap enough to skip DB on every page load

    public function snapshot(): array
    {
        return Cache::remember('launch_offer.snapshot', self::CACHE_TTL, function () {
            $active = SiteSetting::get('launch_offer_active', '1') === '1';
            $total = (int) SiteSetting::get('launch_offer_total', 50);

            // Only count subscriptions that actually converted — pending
            // and failed rows shouldn't eat into the limited slots.
            $used = Subscription::whereIn('status', ['active', 'trialing'])->count();
            $remaining = max(0, $total - $used);

            // % filled for the progress bar. Capped at 100 so a config
            // change (e.g. lowering the total) doesn't blow past 100%.
            $progress = $total > 0 ? min(100, (int) round(($used / $total) * 100)) : 0;

            return [
                'is_active' => $active && $remaining > 0,
                'is_sold_out' => $active && $remaining === 0,
                'total_slots' => $total,
                'used_slots' => $used,
                'remaining_slots' => $remaining,
                'progress_percent' => $progress,
            ];
        });
    }

    /** Forget the cached snapshot — call after a successful subscription so the counter updates immediately. */
    public static function flush(): void
    {
        Cache::forget('launch_offer.snapshot');
    }
}
