<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePrice($request);
        $data['country_code'] = strtoupper($data['country_code']);
        $data['currency_code'] = strtoupper($data['currency_code']);

        PlanPrice::updateOrCreate(
            ['pricing_plan_id' => $data['pricing_plan_id'], 'country_code' => $data['country_code']],
            $data
        );
        $this->flushCaches();

        return back()->with('success', 'تم حفظ السعر');
    }

    public function update(Request $request, PlanPrice $price): RedirectResponse
    {
        $data = $this->validatePrice($request, $price->id);
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

    protected function validatePrice(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'pricing_plan_id' => ['required', 'integer', 'exists:pricing_plans,id'],
            'country_code' => ['required', 'string', 'size:2'],
            'country_name_ar' => ['required', 'string', 'max:80'],
            'country_name_en' => ['required', 'string', 'max:80'],
            'country_flag' => ['nullable', 'string', 'max:8'],
            'currency_code' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:12'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'yearly_price' => ['required', 'numeric', 'min:0'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    /** Clear caches that depend on PlanPrice rows (supported countries list). */
    protected function flushCaches(): void
    {
        Cache::forget('plan_prices.supported_countries');
    }
}
