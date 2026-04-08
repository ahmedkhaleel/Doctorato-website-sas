<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/EmailTemplates', [
            'templates' => EmailTemplate::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, EmailTemplate $template): RedirectResponse
    {
        $data = $request->validate([
            'subject_ar' => ['required', 'string', 'max:160'],
            'subject_en' => ['required', 'string', 'max:160'],
            'body_ar' => ['required', 'string'],
            'body_en' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $template->update($data);

        return back()->with('success', 'تم حفظ القالب');
    }
}
