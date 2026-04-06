<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Testimonials', [
            'testimonials' => Testimonial::orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name_ar' => 'required|string',
            'client_name_en' => 'required|string',
            'clinic_name_ar' => 'nullable|string',
            'clinic_name_en' => 'nullable|string',
            'role_ar' => 'nullable|string',
            'role_en' => 'nullable|string',
            'review_ar' => 'required|string',
            'review_en' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        Testimonial::create($validated);
        return back()->with('success', 'تم إضافة الشهادة بنجاح');
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'client_name_ar' => 'required|string',
            'client_name_en' => 'required|string',
            'clinic_name_ar' => 'nullable|string',
            'clinic_name_en' => 'nullable|string',
            'role_ar' => 'nullable|string',
            'role_en' => 'nullable|string',
            'review_ar' => 'required|string',
            'review_en' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $testimonial->update($validated);
        return back()->with('success', 'تم تحديث الشهادة بنجاح');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return back()->with('success', 'تم حذف الشهادة بنجاح');
    }
}
