<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqRequest;
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

    public function store(FaqRequest $request)
    {
        $faq = Faq::create($request->validated());
        ActivityLog::record('created', $faq, "أضاف سؤال: " . mb_substr($faq->question_ar, 0, 50));
        return back()->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function update(FaqRequest $request, Faq $faq)
    {
        $faq->update($request->validated());
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
