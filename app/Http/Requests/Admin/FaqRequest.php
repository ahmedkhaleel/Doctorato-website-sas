<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('faqs.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|in:general,pricing,technical',
            'question_ar' => 'required|string|max:500',
            'question_en' => 'required|string|max:500',
            'answer_ar' => 'required|string|max:5000',
            'answer_en' => 'required|string|max:5000',
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
