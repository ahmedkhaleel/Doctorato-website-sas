<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CaseStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('case_studies.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'client_name_ar' => ['required', 'string', 'max:150'],
            'client_name_en' => ['required', 'string', 'max:150'],
            'industry_ar' => ['nullable', 'string', 'max:120'],
            'industry_en' => ['nullable', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:120'],
            'logo' => ['nullable', 'string', 'max:500'],
            'hero_image' => ['nullable', 'string', 'max:500'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'summary_ar' => ['nullable', 'string'],
            'summary_en' => ['nullable', 'string'],
            'challenge_ar' => ['nullable', 'string'],
            'challenge_en' => ['nullable', 'string'],
            'solution_ar' => ['nullable', 'string'],
            'solution_en' => ['nullable', 'string'],
            'results_ar' => ['nullable', 'string'],
            'results_en' => ['nullable', 'string'],
            'metrics' => ['nullable', 'array'],
            'modules_used' => ['nullable', 'array'],
            'testimonial_ar' => ['nullable', 'string'],
            'testimonial_en' => ['nullable', 'string'],
            'testimonial_author' => ['nullable', 'string', 'max:120'],
            'testimonial_role' => ['nullable', 'string', 'max:120'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
