<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CurrencyRequest;
use App\Models\ActivityLog;
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

    public function store(CurrencyRequest $request)
    {
        $validated = $request->validated();

        // Trim + uppercase so "egp " and "EGP" can't be stored as two
        // different currencies. Validation already enforced size:3.
        $validated['code'] = strtoupper(trim($validated['code']));

        $currency = Currency::create($validated);
        ActivityLog::record('created', $currency, "أضاف عملة: {$currency->code}");
        return back()->with('success', 'تم إضافة العملة بنجاح');
    }

    public function update(CurrencyRequest $request, Currency $currency)
    {
        $validated = $request->validated();
        $validated['code'] = strtoupper(trim($validated['code']));

        $currency->update($validated);
        ActivityLog::record('updated', $currency, "عدّل عملة: {$currency->code}");
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
        $code = $currency->code;
        $currency->delete();
        ActivityLog::record('deleted', null, "حذف عملة: {$code}");
        return back()->with('success', 'تم حذف العملة بنجاح');
    }
}
