<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\PricingPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Plans', [
            'plans' => PricingPlan::orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'slug' => 'required|string|unique:pricing_plans,slug',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string',
            'is_popular' => 'boolean',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'features_ar' => 'nullable|array',
            'features_en' => 'nullable|array',
            'modules_included' => 'nullable|array',
            'max_users' => 'nullable|integer',
            'max_patients' => 'nullable|integer',
            'support_level' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);

        // Defaults for columns that don't have a DB-level default.
        // Without these the insert throws "Field '...' doesn't have a default value".
        $validated['features_ar'] = $validated['features_ar'] ?? [];
        $validated['features_en'] = $validated['features_en'] ?? [];
        $validated['modules_included'] = $validated['modules_included'] ?? [];
        $validated['support_level'] = $validated['support_level'] ?? 'standard';

        $plan = PricingPlan::create($validated);
        ActivityLog::record('created', $plan, "أضاف خطة: {$plan->name_ar}");
        return back()->with('success', 'تم إضافة الخطة بنجاح');
    }

    public function update(Request $request, PricingPlan $plan)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string',
            'is_popular' => 'boolean',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'features_ar' => 'nullable|array',
            'features_en' => 'nullable|array',
            'modules_included' => 'nullable|array',
            'max_users' => 'nullable|integer',
            'max_patients' => 'nullable|integer',
            'support_level' => 'nullable|string',
            'display_order' => 'nullable|integer',
        ]);

        $plan->update($validated);
        ActivityLog::record('updated', $plan, "عدّل خطة: {$plan->name_ar}");
        return back()->with('success', 'تم تحديث الخطة بنجاح');
    }

    public function destroy(PricingPlan $plan)
    {
        // Protect against FK violations — killing a plan with live
        // subscriptions or seeded prices would throw a raw MySQL error.
        // We surface a readable message and ask the admin to clean up first.
        $subs = $plan->subscriptions()->count();
        if ($subs > 0) {
            return back()->with('error', "لا يمكن حذف الخطة — مرتبطة بـ {$subs} اشتراك. عطّل الخطة بدلاً من حذفها.");
        }
        $prices = $plan->prices()->count();
        if ($prices > 0) {
            return back()->with('error', "لا يمكن حذف الخطة — فيها {$prices} سعر حسب الدولة. احذف الأسعار أولاً من /admin/plan-prices.");
        }

        $name = $plan->name_ar;
        $plan->delete();
        ActivityLog::record('deleted', null, "حذف خطة: {$name}");
        return back()->with('success', 'تم حذف الخطة بنجاح');
    }
}
