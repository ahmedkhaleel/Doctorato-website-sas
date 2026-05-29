<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for the public /contact form.
 *
 * Lives separate from the controller so:
 *  - The same rules are reused if we ever expose a /api/v1/contact
 *  - Field-level messages stay localized in one place
 *  - The controller body stays focused on the success path
 */
class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Public form — anyone can submit; the bot defenses live in
        // RecaptchaService, not in authorization.
        return true;
    }

    public function rules(): array
    {
        // phone:max 50 covers any realistic international format
        // including dial code + brackets + spaces + extension.
        // The previous max:20 silently rejected normal numbers like
        // "+971 (50) 123-4567".
        return [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:50',
            'country_code' => 'nullable|string|max:8',
            'subject'      => 'required|string|max:255',
            'message'      => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        // Arabic-first because the form is Arabic-first. Each field
        // gets one short sentence the user can act on.
        return [
            'name.required'    => 'الاسم مطلوب.',
            'email.required'   => 'البريد الإلكتروني مطلوب.',
            'email.email'      => 'صيغة البريد الإلكتروني غير صحيحة.',
            'subject.required' => 'الموضوع مطلوب.',
            'message.required' => 'الرسالة مطلوبة.',
            'message.max'      => 'الرسالة طويلة جداً (الحد الأقصى 5000 حرف).',
            'phone.max'        => 'رقم الهاتف طويل جداً.',
        ];
    }
}
