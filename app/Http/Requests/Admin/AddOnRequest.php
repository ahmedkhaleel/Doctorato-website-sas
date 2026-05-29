<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin → Add-ons → Create/Update. Same shape for both actions
 * (no unique constraints on this model) so one class serves both.
 */
class AddOnRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('addons.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:120'],
            'name_en' => ['required', 'string', 'max:120'],
            'description_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string', 'max:255'],
            'price_egp' => ['required', 'numeric', 'min:0'],
            'period' => ['required', 'in:monthly,yearly,one_time'],
            'icon' => ['nullable', 'string', 'max:50'],
            'badge_ar' => ['nullable', 'string', 'max:50'],
            'badge_en' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
