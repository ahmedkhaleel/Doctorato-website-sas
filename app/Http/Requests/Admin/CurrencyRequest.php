<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CurrencyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('currencies.manage') : (bool) $user;
    }

    public function rules(): array
    {
        $currency = $this->route('currency');
        $ignoreId = is_object($currency) ? $currency->id : $currency;

        return [
            'code' => ['required', 'string', 'size:3', Rule::unique('currencies', 'code')->ignore($ignoreId)],
            'name_ar' => 'required|string|max:100',
            'name_en' => 'required|string|max:100',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'decimal_places' => 'integer|min:0|max:4',
            'rate_to_sar' => 'required|numeric|min:0',
            'country_code' => 'nullable|string|max:5',
            'flag_emoji' => 'nullable|string|max:8',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
