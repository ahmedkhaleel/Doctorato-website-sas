<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TestimonialRequest;
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

    public function store(TestimonialRequest $request)
    {
        $t = Testimonial::create($request->validated());
        ActivityLog::record('created', $t, "أضاف شهادة من: {$t->client_name_ar}");
        return back()->with('success', 'تم إضافة الشهادة بنجاح');
    }

    public function update(TestimonialRequest $request, Testimonial $testimonial)
    {
        $testimonial->update($request->validated());
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
