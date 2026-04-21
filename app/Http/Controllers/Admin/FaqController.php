<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
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

        $faq = Faq::create($validated);
        ActivityLog::record('created', $faq, "أضاف سؤال: " . mb_substr($faq->question_ar, 0, 50));
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
        ActivityLog::record('updated', $faq, "عدّل سؤال: " . mb_substr($faq->question_ar, 0, 50));
        return back()->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroy(Faq $faq)
    {
        $label = mb_substr($faq->question_ar, 0, 50);
        $faq->delete();
        ActivityLog::record('deleted', null, "حذف سؤال: {$label}");
        return back()->with('success', 'تم حذف السؤال بنجاح');
    }
}
