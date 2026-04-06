<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Faqs', [
            'faqs' => Faq::orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:general,pricing,technical',
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        Faq::create($validated);
        return back()->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'category' => 'required|in:general,pricing,technical',
            'question_ar' => 'required|string',
            'question_en' => 'required|string',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ]);

        $faq->update($validated);
        return back()->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return back()->with('success', 'تم حذف السؤال بنجاح');
    }
}
