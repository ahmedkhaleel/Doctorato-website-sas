<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CurrencyController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Currencies', [
            'currencyList' => Currency::orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'symbol' => 'required|string',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'integer|min:0|max:4',
            'rate_to_sar' => 'required|numeric|min:0',
            'country_code' => 'nullable|string',
            'flag_emoji' => 'nullable|string',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        // Trim + uppercase so "egp " and "EGP" can't be stored as two
        // different currencies. Validation already enforced size:3.
        $validated['code'] = strtoupper(trim($validated['code']));

        Currency::create($validated);
        return back()->with('success', 'تم إضافة العملة بنجاح');
    }

    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            // Ignore the current row so editing decimals/rate on an existing
            // currency doesn't fail the uniqueness check against itself.
            'code' => ['required', 'string', 'size:3', Rule::unique('currencies', 'code')->ignore($currency->id)],
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'symbol' => 'required|string',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'integer|min:0|max:4',
            'rate_to_sar' => 'required|numeric|min:0',
            'country_code' => 'nullable|string',
            'flag_emoji' => 'nullable|string',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        $currency->update($validated);
        return back()->with('success', 'تم تحديث العملة بنجاح');
    }

    public function destroy(Currency $currency)
    {
        // Block delete if any subscriptions are billed in this currency —
        // FK cleanup would leave orphaned invoices with no rate context.
        $inUse = \App\Models\Subscription::where('currency', $currency->code)->count();
        if ($inUse > 0) {
            return back()->with('error', "لا يمكن حذف {$currency->code} — فيه {$inUse} اشتراك يستخدمها.");
        }
        $currency->delete();
        return back()->with('success', 'تم حذف العملة بنجاح');
    }
}
