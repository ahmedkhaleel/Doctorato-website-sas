<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared validation for Admin → Plans create + update. Same shape
 * for both, with two divergences:
 *   - 'slug' is required+unique on store, omitted on update (we
 *     don't let admins rename a plan's slug — it would break every
 *     URL that hard-codes it).
 *   - We detect which one we're in by checking for the {plan}
 *     route parameter.
 */
class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('plans.manage') : (bool) $user;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('plan') !== null;

        $base = [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string|max:5000',
            'description_en' => 'nullable|string|max:5000',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'is_popular' => 'boolean',
            'is_custom' => 'boolean',
            'is_active' => 'boolean',
            'features_ar' => 'nullable|array',
            'features_ar.*' => 'string|max:500',
            'features_en' => 'nullable|array',
            'features_en.*' => 'string|max:500',
            'modules_included' => 'nullable|array',
            'modules_included.*' => 'string|max:100',
            'max_users' => 'nullable|integer|min:0',
            'max_patients' => 'nullable|integer|min:0',
            'support_level' => 'nullable|string|max:50',
            'display_order' => 'nullable|integer',
        ];

        if (!$isUpdate) {
            $base['slug'] = 'required|string|max:100|unique:pricing_plans,slug';
        }

        return $base;
    }
}
