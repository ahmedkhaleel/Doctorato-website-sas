<?php

namespace App\Http\Controllers;

use App\Models\DemoRequest;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
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

        $validated = $request->validate([
            'clinic_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'doctors_count' => 'nullable|string',
            'specialty' => 'nullable|string',
            'interested_modules' => 'nullable|array',
            'referral_source' => 'nullable|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        DemoRequest::create($validated);

        return back()->with('success', true);
    }
}
