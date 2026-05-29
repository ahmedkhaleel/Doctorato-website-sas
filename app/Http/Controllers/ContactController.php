<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactAdminNotification;
use App\Mail\ContactCustomerConfirmation;
use App\Models\ContactMessage;
use App\Services\RecaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request, RecaptchaService $captcha)
    {
        // Bot defenses fire first — honeypot + timing + (optional)
        // reCAPTCHA. Validation is already done by the form request.
        $check = $captcha->verify($request->only(['hp_trap', 'form_rendered_at', 'recaptcha_token']), 'contact');
        if (!$check['ok']) {
            return back()->withInput()->withErrors(['message' => 'تعذر التحقق من الرسالة، حاول مرة أخرى.']);
        }

        $validated = $request->validated();

        // Combine country code + phone if both provided
        if (!empty($validated['phone']) && !empty($validated['country_code'])) {
            $validated['phone'] = $validated['country_code'] . ' ' . $validated['phone'];
        }
        unset($validated['country_code']);

        try {
            $contact = ContactMessage::create($validated);
        } catch (\Throwable $e) {
            Log::error('Contact form save failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'] ?? null,
            ]);
            return back()
                ->withInput()
                ->withErrors(['message' => 'حدث خطأ أثناء حفظ الرسالة. حاول مرة أخرى أو راسلنا على info@doctorato.com مباشرة.']);
        }

        // Fire emails asynchronously-on-best-effort. Save is the source
        // of truth — even if SMTP is down, the lead is already in the DB
        // and admin can see it in the dashboard, so a mail failure
        // never blocks the form's success state.
        $this->sendEmails($contact);

        return back()->with('success', true);
    }

    /**
     * Sends a customer confirmation + admin notification for a contact
     * submission. Each send is wrapped independently so a failure on
     * one address doesn't cascade.
     */
    protected function sendEmails(ContactMessage $contact): void
    {
        try {
            Mail::to($contact->email)->send(new ContactCustomerConfirmation($contact));
        } catch (\Throwable $e) {
            Log::warning('Contact: customer confirmation email failed', [
                'error' => $e->getMessage(),
                'to' => $contact->email,
            ]);
        }

        try {
            Mail::to('info@doctorato.com')->send(new ContactAdminNotification($contact));
        } catch (\Throwable $e) {
            Log::warning('Contact: admin notification email failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
