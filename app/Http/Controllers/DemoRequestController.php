<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemoRequest;
use App\Mail\DemoAdminNotification;
use App\Mail\DemoCustomerConfirmation;
use App\Models\DemoRequest;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DemoRequestController extends Controller
{
    public function store(StoreDemoRequest $request, RecaptchaService $captcha)
    {
        // Bot filter (honeypot + timing + optional reCAPTCHA v3).
        $check = $captcha->verify($request->only(['hp_trap', 'form_rendered_at', 'recaptcha_token']), 'demo_request');
        if (!$check['ok']) {
            return back()->withInput()->withErrors(['clinic_name' => 'تعذر التحقق من الطلب، حاول مرة أخرى.']);
        }

        $validated = $request->validated();

        try {
            $demo = DemoRequest::create($validated);
        } catch (\Throwable $e) {
            Log::error('Demo request save failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? null,
            ]);
            return back()
                ->withInput()
                ->withErrors(['clinic_name' => 'حدث خطأ أثناء حفظ الطلب. حاول مرة أخرى.']);
        }

        $this->sendEmails($demo);

        return back()->with('success', true);
    }

    /**
     * Customer confirmation + admin notification, each wrapped so a
     * single send failure doesn't block the other or the form.
     */
    protected function sendEmails(DemoRequest $demo): void
    {
        try {
            Mail::to($demo->email)->send(new DemoCustomerConfirmation($demo));
        } catch (\Throwable $e) {
            Log::warning('Demo: customer confirmation email failed', [
                'error' => $e->getMessage(),
                'to' => $demo->email,
            ]);
        }

        try {
            Mail::to('info@doctorato.com')->send(new DemoAdminNotification($demo));
        } catch (\Throwable $e) {
            Log::warning('Demo: admin notification email failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
