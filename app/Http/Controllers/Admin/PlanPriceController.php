<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanPriceRequest;
use App\Models\PlanPrice;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class PlanPriceController extends Controller
{
    /**
     * Matrix view: rows = countries, columns = plans, cells = prices.
     * This is the most intuitive layout for comparing prices across
     * markets and spotting gaps (e.g. a country with no Pro price yet).
     */
    public function index(): Response
    {
        // pricing_plans doesn't use soft deletes, so no deleted_at filter.
        $plans = PricingPlan::where('is_active', true)
            ->where('is_custom', false)
            ->orderBy('display_order')
            ->get(['id', 'name_ar', 'name_en', 'slug']);

        $prices = PlanPrice::orderBy('country_code')->get();

        return Inertia::render('Admin/PlanPrices', [
            'plans' => $plans,
            'prices' => $prices,
        ]);
    }

    public function store(PlanPriceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['country_code'] = strtoupper($data['country_code']);
        $data['currency_code'] = strtoupper($data['currency_code']);

        PlanPrice::updateOrCreate(
            ['pricing_plan_id' => $data['pricing_plan_id'], 'country_code' => $data['country_code']],
            $data
        );
        $this->flushCaches();

        return back()->with('success', 'تم حفظ السعر');
    }

    public function update(PlanPriceRequest $request, PlanPrice $price): RedirectResponse
    {
        $data = $request->validated();
        $data['country_code'] = strtoupper($data['country_code']);
        $data['currency_code'] = strtoupper($data['currency_code']);
        $price->update($data);
        $this->flushCaches();

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(PlanPrice $price): RedirectResponse
    {
        $price->delete();
        $this->flushCaches();

        return back()->with('success', 'تم الحذف');
    }

    /** Clear caches that depend on PlanPrice rows (supported countries list). */
    protected function flushCaches(): void
    {
        Cache::forget('plan_prices.supported_countries');
    }
}
