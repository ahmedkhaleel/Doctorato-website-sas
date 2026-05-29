<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsGeneralRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('settings.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'company_email' => ['nullable', 'email', 'max:150'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_whatsapp' => ['nullable', 'string', 'max:30'],
            'company_address_ar' => ['nullable', 'string', 'max:300'],
            'company_address_en' => ['nullable', 'string', 'max:300'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'banner_enabled' => ['nullable', 'boolean'],
            'banner_text_ar' => ['nullable', 'string', 'max:255'],
            'banner_text_en' => ['nullable', 'string', 'max:255'],
            'banner_cta_label_ar' => ['nullable', 'string', 'max:60'],
            'banner_cta_label_en' => ['nullable', 'string', 'max:60'],
            'banner_cta_url' => ['nullable', 'string', 'max:255'],
            'footer_tagline_ar' => ['nullable', 'string', 'max:300'],
            'footer_tagline_en' => ['nullable', 'string', 'max:300'],
        ];
    }
}
