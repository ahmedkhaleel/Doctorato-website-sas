<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function store(Request $request, RecaptchaService $captcha)
    {
        $check = $captcha->verify($request->only(['hp_trap', 'form_rendered_at', 'recaptcha_token']), 'contact');
        if (!$check['ok']) {
            return back()->withInput()->withErrors(['message' => 'تعذر التحقق من الرسالة، حاول مرة أخرى.']);
        }

        // Phone is generous on length because formatted numbers carry
        // brackets, spaces, and a country prefix ("+1 (963) 646-4167"
        // is 19 chars on its own, hits 23+ once we prepend the dial
        // code). The previous max:20 was rejecting realistic numbers
        // silently. Country code can be 7 chars in odd cases (e.g.
        // "+1-242" Bahamas) — bumped to 8.
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'country_code' => 'nullable|string|max:8',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Combine country code + phone if both provided
        if (!empty($validated['phone']) && !empty($validated['country_code'])) {
            $validated['phone'] = $validated['country_code'] . ' ' . $validated['phone'];
        }
        unset($validated['country_code']);

        try {
            ContactMessage::create($validated);
        } catch (\Throwable $e) {
            Log::error('Contact form save failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? null,
            ]);
            return back()
                ->withInput()
                ->withErrors(['message' => 'حدث خطأ أثناء حفظ الرسالة. حاول مرة أخرى أو راسلنا على info@doctorato.com مباشرة.']);
        }

        return back()->with('success', true);
    }
}
