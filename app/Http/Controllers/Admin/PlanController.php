<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;
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

    public function store(PlanRequest $request)
    {
        $validated = $request->validated();

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

    public function update(PlanRequest $request, PricingPlan $plan)
    {
        $validated = $request->validated();

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
