<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Services\CountryDetector;
use App\Services\PublicContentCache;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index(Request $request, CountryDetector $detector, PublicContentCache $cache)
    {
        $country = $detector->resolve($request);

        // All four reads below now hit Cache::remember inside the
        // service (file driver, 10 min TTL). Admin edits invalidate
        // via the model observers, so we get freshness on save.
        $plans = $cache->plans($country);
        $faqs = $cache->faqs('pricing');
        $addons = $cache->addons();
        $testimonials = $cache->topTestimonials(3);

        // The currency lookup stays uncached because it's a single
        // primary-key fetch with the country's currency code, which
        // is already in the cached plans array.
        $countryCurrencyCode = $plans[0]['currency'] ?? 'EGP';
        $currency = Currency::where('code', $countryCurrencyCode)->first()
            ?? Currency::where('code', 'EGP')->first();

        $activeCurrency = [
            'code' => $currency->code ?? 'EGP',
            'symbol' => $currency->symbol ?? 'ج.م',
            'rate_from_egp' => (float) ($currency->rate_to_sar ?? 1),
            'decimal_places' => (int) ($currency->decimal_places ?? 2),
            'symbol_position' => $currency->symbol_position ?? 'after',
        ];

        return Inertia::render('Pricing', [
            'plans' => $plans,
            'faqs' => $faqs,
            'addons' => $addons,
            'activeCurrency' => $activeCurrency,
            'testimonials' => $testimonials,
        ]);
    }
}
