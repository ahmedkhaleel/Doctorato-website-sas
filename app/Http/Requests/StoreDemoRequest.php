<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for the /demo request form.
 *
 * The demo form qualifies a lead, so we capture more shape
 * (specialty, doctor count, interested modules) than /contact does.
 * Required-vs-nullable is tuned so sales gets a complete-enough
 * picture to call prepared without forcing the visitor to fill
 * every field.
 */
class StoreDemoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise + sanity-check the referrer code BEFORE rules() runs.
     * Junk values become null instead of triggering a validation error,
     * which keeps a malformed referral cookie from blocking real demo
     * submissions.
     */
    protected function prepareForValidation(): void
    {
        $code = $this->input('referred_by_code');
        if ($code) {
            $code = strtoupper(trim((string) $code));
            if (!preg_match('/^DOC-[A-Z2-9]{8}$/', $code)) {
                $code = null;
            }
            $this->merge(['referred_by_code' => $code]);
        }
    }

    public function rules(): array
    {
        return [
            // Identifying — all required so we have someone to contact.
            'clinic_name'         => 'required|string|max:255',
            'full_name'           => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'phone'               => 'required|string|max:50',
            'country_code'        => 'required|string|max:10',

            // Qualifying — optional, but each one improves the call.
            'country'             => 'nullable|string|max:100',
            'doctors_count'       => 'nullable|string|max:50',
            'specialty'           => 'nullable|string|max:100',
            'interested_modules'  => 'nullable|array',
            'interested_modules.*' => 'string|max:50',
            'referral_source'     => 'nullable|string|max:100',
            // Referrer code captured from ?ref=. Format-validated here
            // so a junk cookie doesn't poison the DB; we silently drop
            // invalid codes by translating the validation failure into
            // a null prepareForValidation rewrite.
            'referred_by_code'    => ['nullable', 'string', 'max:32', 'regex:/^DOC-[A-Z2-9]{8}$/'],
            'notes'               => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'clinic_name.required'  => 'اسم العيادة مطلوب.',
            'full_name.required'    => 'الاسم الكامل مطلوب.',
            'email.required'        => 'البريد الإلكتروني مطلوب.',
            'email.email'           => 'صيغة البريد الإلكتروني غير صحيحة.',
            'phone.required'        => 'رقم الهاتف مطلوب.',
            'phone.max'             => 'رقم الهاتف طويل جداً.',
            'country_code.required' => 'كود الدولة مطلوب.',
        ];
    }
}
