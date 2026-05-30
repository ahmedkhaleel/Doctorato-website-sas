<?php

namespace App\Services;

use App\Models\Subscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Subscription-level KPIs for the owner dashboard.
 *
 * We compute everything from the subscriptions table itself —
 * no event-sourcing layer, no separate metrics warehouse. That's
 * fine for the volume this SaaS expects (low thousands of subs
 * for many quarters), and the alternative would mean a new event
 * table we don't have budget to maintain on cPanel.
 *
 * Definitions we commit to here (so the dashboard means the same
 * thing six months from now):
 *
 *   - "Active sub" = status='active' AND paused_at IS NULL.
 *     Paused subs don't bill, so they don't count toward MRR.
 *   - MRR = sum of (monthly: amount, yearly: amount / 12) across
 *     active subs, in the SAR-equivalent (rate_to_sar on the
 *     currency table). We don't try to honour FX swings retro-
 *     actively; the snapshot is "today's rate × today's prices".
 *   - ARR = MRR × 12. Not the sum of yearly commitments — easier
 *     to compare quarter-over-quarter and matches what investors
 *     ask for.
 *   - Churn (period) = subs that flipped to cancelled in the
 *     window / active subs at start of window. Soft-cancels count
 *     on the day the customer hit Cancel, not the ends_at date.
 *   - ARPU = MRR / active sub count.
 *
 * Cached for 15 minutes — the owner refreshes the page a few
 * times a day, not constantly.
 */
class SubscriptionMetricsService
{
    public const CACHE_KEY = 'metrics.subs.snapshot';
    public const CACHE_TTL_SECONDS = 900; // 15 min

    public function snapshot(bool $forceFresh = false): array
    {
        if ($forceFresh) {
            Cache::forget(self::CACHE_KEY);
        }
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn () => $this->compute());
    }

    protected function compute(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOf30 = $now->copy()->subDays(30);
        $startOf90 = $now->copy()->subDays(90);

        $active = Subscription::query()
            ->where('status', 'active')
            ->whereNull('paused_at')
            ->get(['id', 'amount', 'billing_cycle', 'currency', 'starts_at']);

        // MRR aggregation in the SAR base unit. Currencies missing
        // a rate fall back to 1:1 so we don't silently drop revenue
        // — admin sees the underflow and fixes the row.
        $rates = DB::table('currencies')->pluck('rate_to_sar', 'code');
        $mrr = 0.0;
        $byCycle = ['monthly' => 0, 'yearly' => 0];
        foreach ($active as $sub) {
            $monthlyAmount = $sub->billing_cycle === 'yearly'
                ? ((float) $sub->amount) / 12
                : (float) $sub->amount;
            $rate = (float) ($rates[$sub->currency] ?? 1);
            $mrr += $monthlyAmount * $rate;
            $byCycle[$sub->billing_cycle] = ($byCycle[$sub->billing_cycle] ?? 0) + 1;
        }

        $activeCount = $active->count();
        $arpu = $activeCount > 0 ? $mrr / $activeCount : 0;

        return [
            'as_of' => $now->toIso8601String(),
            'mrr_sar' => round($mrr, 2),
            'arr_sar' => round($mrr * 12, 2),
            'active_subs' => $activeCount,
            'paused_subs' => Subscription::whereNotNull('paused_at')->count(),
            'past_due_subs' => Subscription::where('status', 'past_due')->count(),
            'arpu_sar' => round($arpu, 2),
            'by_cycle' => $byCycle,
            'new_30d' => Subscription::where('status', 'active')
                ->where('starts_at', '>=', $startOf30)->count(),
            'new_mtd' => Subscription::where('status', 'active')
                ->where('starts_at', '>=', $startOfMonth)->count(),
            'churn_30d' => $this->churnRate($startOf30, $now),
            'churn_90d' => $this->churnRate($startOf90, $now),
            'cancelled_30d' => Subscription::where('status', 'cancelled')
                ->where('cancelled_at', '>=', $startOf30)->count(),
            'recent_cancellations' => $this->recentCancellations(),
            'currencies_seen' => $active->pluck('currency')->unique()->values()->all(),
        ];
    }

    /** Cancelled in [start..end] / active at start. Returns percent (0-100). */
    protected function churnRate(Carbon $start, Carbon $end): float
    {
        $activeAtStart = Subscription::where('status', 'active')
            ->where(function ($q) use ($start) {
                $q->where('starts_at', '<', $start)
                    ->orWhereNull('starts_at');
            })
            ->count();
        if ($activeAtStart === 0) return 0.0;

        $churned = Subscription::where('status', 'cancelled')
            ->whereBetween('cancelled_at', [$start, $end])
            ->count();

        return round(($churned / $activeAtStart) * 100, 2);
    }

    /** Last 10 cancellations for the dashboard's "recent activity" list. */
    protected function recentCancellations(): array
    {
        return Subscription::query()
            ->where('status', 'cancelled')
            ->whereNotNull('cancelled_at')
            ->with('demoRequest:id,clinic_name')
            ->orderByDesc('cancelled_at')
            ->limit(10)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'clinic' => $s->demoRequest?->clinic_name ?? '—',
                'billing_cycle' => $s->billing_cycle,
                'amount' => (float) $s->amount,
                'currency' => $s->currency,
                'cancelled_at' => $s->cancelled_at?->toIso8601String(),
                'days_active' => $s->starts_at && $s->cancelled_at
                    ? (int) $s->starts_at->diffInDays($s->cancelled_at) : null,
            ])
            ->all();
    }
}
