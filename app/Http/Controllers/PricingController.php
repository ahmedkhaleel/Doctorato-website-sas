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
                // Inject the resolved price onto the serialized payload so
                // the Vue page reads one flat shape regardless of fallback.
                return array_merge($plan->toArray(), [
                    'monthly_price' => $price['monthly'],
                    'yearly_price' => $price['yearly'],
                    'currency' => $price['currency'],
                    'currency_symbol' => $price['currency_symbol'],
                    'country_code' => $price['country_code'],
                    'price_source' => $price['source'],
                ]);
            });

        return Inertia::render('Pricing', [
            'plans' => $plans,
            'faqs' => Faq::where('is_active', true)->where('category', 'pricing')->orderBy('display_order')->get(),
            'currencies' => Currency::where('is_active', true)->orderBy('display_order')->get(),
            'currentCurrency' => session('currency', 'EGP'),
            'addons' => AddOn::active()->orderBy('display_order')->get(),
        ]);
    }
}
