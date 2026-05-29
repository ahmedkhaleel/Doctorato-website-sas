<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Admin → Plan prices → Create/Update. The (pricing_plan_id,
 * country_code) pair is unique — one Basic-EG, one Pro-EG. We scope
 * the unique check by plan id and ignore the current row on update.
 */
class PlanPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('plans.manage') : (bool) $user;
    }

    public function rules(): array
    {
        $price = $this->route('price');
        $ignoreId = is_object($price) ? $price->id : $price;
        $planId = is_object($price) ? $price->pricing_plan_id : $this->input('pricing_plan_id');

        return [
            'pricing_plan_id' => ['required', 'integer', 'exists:pricing_plans,id'],
            'country_code' => [
                'required', 'string', 'size:2',
                Rule::unique('plan_prices', 'country_code')
                    ->where(fn ($q) => $q->where('pricing_plan_id', $planId))
                    ->ignore($ignoreId),
            ],
            'country_name_ar' => ['required', 'string', 'max:80'],
            'country_name_en' => ['required', 'string', 'max:80'],
            'country_flag' => ['nullable', 'string', 'max:8'],
            'currency_code' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:12'],
            'monthly_price' => ['required', 'numeric', 'min:0'],
            'yearly_price' => ['required', 'numeric', 'min:0'],
            'setup_fee' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
