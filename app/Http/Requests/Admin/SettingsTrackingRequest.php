<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SettingsTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('settings.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'ga4_id' => ['nullable', 'string', 'max:30'],
            'gtm_id' => ['nullable', 'string', 'max:30'],
            'meta_pixel_id' => ['nullable', 'string', 'max:30'],
            'tiktok_pixel_id' => ['nullable', 'string', 'max:30'],
            'snapchat_pixel_id' => ['nullable', 'string', 'max:60'],
            'tracking_enabled' => ['nullable', 'boolean'],
        ];
    }
}
