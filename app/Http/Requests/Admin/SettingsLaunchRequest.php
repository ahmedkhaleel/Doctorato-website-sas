<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsLaunchRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('settings.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'active' => ['nullable', 'boolean'],
            'total_slots' => ['required', 'integer', 'min:1', 'max:10000'],
            'recaptcha_site_key' => ['nullable', 'string', 'max:120'],
            'recaptcha_secret_key' => ['nullable', 'string', 'max:120'],
        ];
    }
}
