<?php

namespace App\Http\Controllers;

use App\Mail\DemoAdminNotification;
use App\Mail\DemoCustomerConfirmation;
use App\Models\DemoRequest;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DemoRequestController extends Controller
{
    public function store(Request $request, RecaptchaService $captcha)
    {
        // Bot filter (honeypot + timing + optional reCAPTCHA v3).
        $check = $captcha->verify($request->only(['hp_trap', 'form_rendered_at', 'recaptcha_token']), 'demo_request');
        if (!$check['ok']) {
            return back()->withInput()->withErrors(['clinic_name' => 'تعذر التحقق من الطلب، حاول مرة أخرى.']);
        }

        // Phone limit was max:20 which rejects realistic international
        // formats like "+1 (963) 646-4167". Bumped to 50 to accept
        // brackets/dashes/spaces in any reasonable input.
        $validated = $request->validate([
            'clinic_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'country_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'doctors_count' => 'nullable|string',
            'specialty' => 'nullable|string',
            'interested_modules' => 'nullable|array',
            'referral_source' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

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
