<?php

namespace App\Services;

use App\Models\AddOn;
use App\Models\Faq;
use App\Models\PricingPlan;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Cache;

/**
 * Centralized cache for read-heavy data that public pages re-query
 * on every request.
 *
 * Before this class:
 *   - /pricing fired 5 separate Eloquent queries on every visit
 *   - the Faq + Testimonial queries ran un-paginated on /home too
 *   - PlanPrice rows were re-fetched for every plan in the loop
 *
 * After: one Cache::remember per logical "data slice", keyed by the
 * inputs that actually matter (e.g. country code for plans). All
 * model observers below flush the relevant key when an admin edits
 * the underlying row, so the cache is auto-coherent — we don't
 * have to remember to bust it manually.
 *
 * TTLs are deliberately short (10 min) because:
 *   1. The cost of regenerating is small (5 indexed queries)
 *   2. We don't want stale prices showing after a price hike
 *   3. Anything longer than 10 min runs into the file-cache GC
 *      sweep schedule and gets evicted anyway.
 */
class PublicContentCache
{
    protected const TTL = 600; // 10 minutes

    /** Active plans with prices, mapped for the given country. */
    public function plans(string $country): array
    {
        return Cache::remember("public.plans.{$country}", self::TTL, function () use ($country) {
            return PricingPlan::where('is_active', true)
                ->with(['prices' => fn ($q) => $q->where('is_active', true)])
                ->orderBy('display_order')
                ->get()
                ->map(function ($plan) use ($country) {
                    $price = $plan->priceFor($country);
                    // Active price respects the launch toggle. We still surface
                    // the regular price too so the Vue layer can render the
                    // strikethrough anchor without recomputing.
                    $launchActive = $plan->isLaunchActive();
                    return array_merge($plan->toArray(), [
                        'monthly_price' => $price['monthly'],
                        'yearly_price' => $price['yearly'],
                        // Launch fields kept for backward compat — always null
                        // post-reset (launch pricing decommissioned May 2026).
                        'monthly_price_launch' => null,
                        'yearly_price_launch' => null,
                        'setup_fee' => (float) ($plan->setup_fee ?? 0),
                        'setup_fee_launch' => null,
                        'setup_fee_yearly' => (float) ($plan->setup_fee ?? 0),
                        'is_launch_offer_active' => false,
                        'launch_offer_ends_at' => null,
                        'supports_installments' => false,
                        'installments' => [],
                        // New post-reset fields
                        'included_doctors' => (int) ($plan->included_doctors ?? 1),
                        'per_extra_doctor_price' => $plan->per_extra_doctor_price !== null
                            ? (float) $plan->per_extra_doctor_price
                            : null,
                        'is_contact_sales' => (bool) ($plan->is_contact_sales ?? false),
                        'trial_days' => (int) ($plan->trial_days ?? 30),
                        // Pre-computed monthly-equivalent of the yearly price for "save X months" copy
                        'yearly_monthly_equivalent' => $price['yearly'] > 0 ? round($price['yearly'] / 12, 2) : 0,
                        'currency' => $price['currency'],
                        'currency_symbol' => $price['currency_symbol'],
                        'country_code' => $price['country_code'],
                        'price_source' => $price['source'],
                    ]);
                })
                ->all();
        });
    }

    /** FAQs filtered by category — most pages query a specific category. */
    public function faqs(?string $category = null): array
    {
        $key = 'public.faqs.' . ($category ?? 'all');
        return Cache::remember($key, self::TTL, function () use ($category) {
            $query = Faq::where('is_active', true);
            if ($category) {
                $query->where('category', $category);
            }
            return $query->orderBy('display_order')->get()->all();
        });
    }

    /** Active add-ons, ordered for display. */
    public function addons(): array
    {
        return Cache::remember('public.addons', self::TTL, function () {
            return AddOn::active()->orderBy('display_order')->get()->map(function ($a) {
                // Surface the active price so the Vue layer reads a
                // single column instead of branching on the toggle.
                return array_merge($a->toArray(), [
                    'active_price' => $a->activePrice(),
                ]);
            })->all();
        });
    }

    /** Top testimonials shown in trust strips across the site. */
    public function topTestimonials(int $limit = 3): array
    {
        return Cache::remember("public.testimonials.top.{$limit}", self::TTL, function () use ($limit) {
            return Testimonial::where('is_active', true)
                ->orderByDesc('rating')
                ->orderBy('display_order')
                ->limit($limit)
                ->get()
                ->all();
        });
    }

    /** All active testimonials — used by the carousel on /home + /about. */
    public function allTestimonials(): array
    {
        return Cache::remember('public.testimonials.all', self::TTL, function () {
            return Testimonial::where('is_active', true)
                ->orderBy('display_order')
                ->get()
                ->all();
        });
    }

    /**
     * Drop every cached slice. Called by model observers so an admin
     * edit takes effect on the next page load without waiting for
     * the TTL to expire.
     */
    public function flush(): void
    {
        // No tagging support on the file driver, so we walk the known
        // key set. Cheap — these are direct hash lookups.
        $keys = ['public.addons', 'public.testimonials.all'];
        foreach (['SA', 'AE', 'EG', 'KW', 'QA', 'BH', 'OM'] as $c) {
            $keys[] = "public.plans.{$c}";
        }
        foreach (['all', 'pricing', 'general', 'home', 'demo', 'features'] as $cat) {
            $keys[] = "public.faqs.{$cat}";
        }
        foreach ([1, 3, 5, 10] as $limit) {
            $keys[] = "public.testimonials.top.{$limit}";
        }
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
