<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TestimonialRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('testimonials.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'client_name_ar' => 'required|string|max:150',
            'client_name_en' => 'required|string|max:150',
            'clinic_name_ar' => 'nullable|string|max:150',
            'clinic_name_en' => 'nullable|string|max:150',
            'role_ar' => 'nullable|string|max:100',
            'role_en' => 'nullable|string|max:100',
            // min:3 prevents a single space / punctuation character from
            // passing the required rule as a valid review.
            'review_ar' => 'required|string|min:3',
            'review_en' => 'required|string|min:3',
            'rating' => 'required|integer|min:1|max:5',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
