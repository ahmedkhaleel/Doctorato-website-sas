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

        // Demo notifications go to every address in notifications.demo_recipients.
        // Each address gets a separate visible TO message (not BCC) so admins
        // can reply directly from whichever inbox picked it up. Failures on
        // one address don't block the others.
        $recipients = config('notifications.demo_recipients', ['info@doctorato.com', 'demo@doctorato.com']);
        foreach ($recipients as $to) {
            try {
                Mail::to($to)->send(new DemoAdminNotification($demo));
                Log::info('Demo: admin notification sent', ['to' => $to, 'demo_id' => $demo->id]);
            } catch (\Throwable $e) {
                Log::warning('Demo: admin notification email failed', [
                    'error' => $e->getMessage(),
                    'to' => $to,
                    'demo_id' => $demo->id,
                ]);
            }
        }
    }
}
