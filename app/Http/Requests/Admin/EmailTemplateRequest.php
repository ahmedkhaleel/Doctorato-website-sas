<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('email_templates.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'subject_ar' => ['required', 'string', 'max:160'],
            'subject_en' => ['required', 'string', 'max:160'],
            'body_ar' => ['required', 'string'],
            'body_en' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
