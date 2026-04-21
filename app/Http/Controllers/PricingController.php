<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\Currency;
use App\Models\Faq;
use App\Models\PricingPlan;
use App\Services\CountryDetector;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index(Request $request, CountryDetector $detector)
    {
        $country = $detector->resolve($request);

        // Eager-load prices so priceFor() doesn't trigger N+1 queries.
        $plans = PricingPlan::where('is_active', true)
            ->with(['prices' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('display_order')
            ->get()
            ->map(function ($plan) use ($country) {
                $price = $plan->priceFor($country);
                return array_merge($plan->toArray(), [
                    'monthly_price' => $price['monthly'],
                    'yearly_price' => $price['yearly'],
                    'setup_fee' => $price['setup_fee'] ?? 0,
                    'setup_fee_yearly' => $price['setup_fee_yearly'] ?? 0,
                    'currency' => $price['currency'],
                    'currency_symbol' => $price['currency_symbol'],
                    'country_code' => $price['country_code'],
                    'price_source' => $price['source'],
                ]);
            });

        // Figure out the active country's currency so add-ons (stored in
        // EGP) can be rendered in the same currency as the plans without
        // needing a user-facing currency selector. If the country's
        // currency isn't in the `currencies` table we fall back to EGP
        // (base rate = 1) which keeps the numbers truthful.
        $countryCurrencyCode = optional($plans->first())['currency'] ?? 'EGP';
        $currency = Currency::where('code', $countryCurrencyCode)->first()
            ?? Currency::where('code', 'EGP')->first();

        $activeCurrency = [
            'code' => $currency->code ?? 'EGP',
            'symbol' => $currency->symbol ?? 'ج.م',
            'rate_from_egp' => (float) ($currency->rate_to_sar ?? 1), // column reused as "rate from EGP"
            'decimal_places' => (int) ($currency->decimal_places ?? 2),
            'symbol_position' => $currency->symbol_position ?? 'after',
        ];

        return Inertia::render('Pricing', [
            'plans' => $plans,
            'faqs' => Faq::where('is_active', true)->where('category', 'pricing')->orderBy('display_order')->get(),
            'addons' => AddOn::active()->orderBy('display_order')->get(),
            'activeCurrency' => $activeCurrency,
        ]);
    }
}
