<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin → Coupons → Create/Update. The `code` column has a unique
 * constraint; we detect store-vs-update from the {coupon} route
 * param and only feed the ignore() id when present.
 */
class CouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('coupons.manage') : (bool) $user;
    }

    public function rules(): array
    {
        $coupon = $this->route('coupon');
        $ignoreId = is_object($coupon) ? $coupon->id : $coupon;

        return [
            'code' => [
                'required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/i',
                Rule::unique('coupons', 'code')->ignore($ignoreId),
            ],
            'description_ar' => ['nullable', 'string', 'max:160'],
            'description_en' => ['nullable', 'string', 'max:160'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'max_uses_per_customer' => ['nullable', 'integer', 'min:1'],
            'plan_ids' => ['nullable', 'array'],
            'plan_ids.*' => ['integer', 'exists:pricing_plans,id'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
