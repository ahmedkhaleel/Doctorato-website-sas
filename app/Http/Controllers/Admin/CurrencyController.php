<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
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

        Currency::create($validated);
        return back()->with('success', 'تم إضافة العملة بنجاح');
    }

    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3',
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

        $currency->update($validated);
        return back()->with('success', 'تم تحديث العملة بنجاح');
    }

    public function destroy(Currency $currency)
    {
        $currency->delete();
        return back()->with('success', 'تم حذف العملة بنجاح');
    }
}
