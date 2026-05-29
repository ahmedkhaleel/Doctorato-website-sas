<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Admin → Blog → Create/Update. Slug regex-locks lowercase a-z,
 * digits, and hyphens — this matches the public route constraint
 * in routes/web.php so a mistyped slug can't 404 the post.
 */
class BlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && method_exists($user, 'hasPermission') ? $user->hasPermission('blog.manage') : (bool) $user;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:blog_categories,id'],
            'title_ar' => ['required', 'string', 'max:255'],
            'title_en' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
            'excerpt_ar' => ['nullable', 'string', 'max:500'],
            'excerpt_en' => ['nullable', 'string', 'max:500'],
            'content_ar' => ['required', 'string'],
            'content_en' => ['required', 'string'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'seo_title_ar' => ['nullable', 'string', 'max:160'],
            'seo_title_en' => ['nullable', 'string', 'max:160'],
            'seo_desc_ar' => ['nullable', 'string', 'max:300'],
            'seo_desc_en' => ['nullable', 'string', 'max:300'],
            'status' => ['required', 'in:draft,published,scheduled'],
            'is_featured' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}
