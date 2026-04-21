<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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
            // min:3 prevents a single space / punctuation character from
            // passing the required rule as a valid review.
            'review_ar' => 'required|string|min:3',
            'review_en' => 'required|string|min:3',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $t = Testimonial::create($validated);
        ActivityLog::record('created', $t, "أضاف شهادة من: {$t->client_name_ar}");
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
            // min:3 prevents a single space / punctuation character from
            // passing the required rule as a valid review.
            'review_ar' => 'required|string|min:3',
            'review_en' => 'required|string|min:3',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $testimonial->update($validated);
        ActivityLog::record('updated', $testimonial, "عدّل شهادة: {$testimonial->client_name_ar}");
        return back()->with('success', 'تم تحديث الشهادة بنجاح');
    }

    public function destroy(Testimonial $testimonial)
    {
        $name = $testimonial->client_name_ar;
        $testimonial->delete();
        ActivityLog::record('deleted', null, "حذف شهادة: {$name}");
        return back()->with('success', 'تم حذف الشهادة بنجاح');
    }
}
