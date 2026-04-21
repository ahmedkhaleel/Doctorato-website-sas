<?php

namespace App\Http\Controllers;

use App\Mail\TrialWelcomeMail;
use App\Models\DemoRequest;
use App\Models\SiteSetting;
use App\Services\CountryDetector;
use App\Services\RecaptchaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Self-serve trial signup — the no-friction path that bypasses the
 * demo-call gate. The user fills in name/email/clinic, picks a
 * subdomain, and lands on a confirmation screen telling them to
 * check their inbox for login credentials.
 *
 * The trial record is stored in demo_requests (with is_instant_trial
 * = true) so existing admin tooling — reminders, conversion tracking,
 * trial-ends-at — works without change.
 */
class TrialController extends Controller
{
    public function show(Request $request): Response
    {
        return Inertia::render('StartTrial', [
            'default_country' => app(CountryDetector::class)->resolve($request),
        ]);
    }

    public function store(Request $request, RecaptchaService $captcha): RedirectResponse
    {
        // Bot defenses — honeypot + time-to-submit + optional reCAPTCHA.
        // On failure we return a generic error so real automation can't
        // deduce which layer rejected them.
        $check = $captcha->verify($request->only(['hp_trap', 'form_rendered_at', 'recaptcha_token']), 'trial_signup');
        if (!$check['ok']) {
            return back()->withInput()->withErrors([
                'clinic_name' => 'تعذر التحقق من الطلب، حاول مرة أخرى.',
            ]);
        }

        $data = $request->validate([
            'clinic_name' => ['required', 'string', 'max:150'],
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'country_code' => ['nullable', 'string', 'max:6'],
            'country' => ['nullable', 'string', 'max:4'],
            'subdomain' => [
                'required',
                'string',
                'min:3',
                'max:40',
                'regex:/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', // lowercase + digits + dashes, can't start/end with dash
                Rule::unique('demo_requests', 'subdomain'),
            ],
        ], [
            'subdomain.regex' => 'الرابط يجب أن يحتوي على أحرف إنجليزية صغيرة وأرقام وشرطات فقط',
            'subdomain.unique' => 'هذا الرابط محجوز بالفعل — جرّب اسماً آخر',
        ]);

        $trial = DemoRequest::create([
            'clinic_name' => $data['clinic_name'],
            'full_name' => $data['full_name'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'],
            'country_code' => $data['country_code'] ?? '+20',
            'country' => $data['country'] ?? 'EG',
            'is_instant_trial' => true,
            'subdomain' => strtolower($data['subdomain']),
            // Reuse the 'new' status so existing admin filters keep working.
            // The is_instant_trial flag is how we tell these apart from demo calls.
            'status' => 'new',
        ]);

        // Fire-and-forget — a mail outage shouldn't block the signup flow.
        // Logs + admin notification stream catch misses.
        try {
            Mail::to($trial->email)->send(new TrialWelcomeMail($trial));
            // CC the operations mailbox so the team provisions credentials.
            $adminEmail = SiteSetting::get('company_email') ?: config('mail.from.address');
            if ($adminEmail && $adminEmail !== $trial->email) {
                Mail::to($adminEmail)->send(new TrialWelcomeMail($trial));
            }
        } catch (\Throwable $e) {
            Log::warning('Trial welcome email failed, signup still succeeded', [
                'trial_id' => $trial->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('trial.success', ['ref' => $trial->id])
            ->with('success', 'تجربتك جاهزة!');
    }

    public function success(Request $request): Response
    {
        $trial = DemoRequest::find($request->query('ref'));

        return Inertia::render('StartTrialSuccess', [
            'trial' => $trial ? [
                'clinic_name' => $trial->clinic_name,
                'full_name' => $trial->full_name,
                'email' => $trial->email,
                'subdomain' => $trial->subdomain,
                'trial_url' => $trial->subdomain ? "https://{$trial->subdomain}.doctorato.app" : null,
                'trial_ends_at' => $trial->trial_ends_at?->toIso8601String(),
                'days_left' => $trial->trial_days_left,
            ] : null,
        ]);
    }

    /** AJAX: quick check if a subdomain is available while the user types. */
    public function checkSubdomain(Request $request)
    {
        $slug = strtolower($request->query('q', ''));
        $valid = (bool) preg_match('/^[a-z0-9][a-z0-9-]{1,38}[a-z0-9]$/', $slug);
        $reserved = in_array($slug, ['www', 'admin', 'app', 'api', 'mail', 'blog', 'demo', 'test', 'dev', 'staging']);
        $taken = $valid && !$reserved && DemoRequest::where('subdomain', $slug)->exists();

        return response()->json([
            'slug' => $slug,
            'valid' => $valid && !$reserved,
            'available' => $valid && !$reserved && !$taken,
            'message' => !$valid
                ? 'الرابط يحتاج أحرف إنجليزية وأرقام فقط'
                : ($reserved
                    ? 'هذا الرابط محجوز للنظام'
                    : ($taken ? 'هذا الرابط محجوز بالفعل' : 'متاح')),
        ]);
    }
}
