<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
            'currency' => 'string',
            'is_popular' => 'boolean',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'features_ar' => 'array',
            'features_en' => 'array',
            'max_users' => 'nullable|integer',
            'max_patients' => 'nullable|integer',
            'support_level' => 'string',
            'display_order' => 'integer',
        ]);

        PricingPlan::create($validated);
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
            'is_popular' => 'boolean',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'features_ar' => 'array',
            'features_en' => 'array',
            'max_users' => 'nullable|integer',
            'max_patients' => 'nullable|integer',
            'support_level' => 'string',
            'display_order' => 'integer',
        ]);

        $plan->update($validated);
        return back()->with('success', 'تم تحديث الخطة بنجاح');
    }

    public function destroy(PricingPlan $plan)
    {
        $plan->delete();
        return back()->with('success', 'تم حذف الخطة بنجاح');
    }
}
