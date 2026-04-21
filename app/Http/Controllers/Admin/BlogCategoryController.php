<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/BlogCategories', [
            'categories' => BlogCategory::withCount('posts')->orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCategory($request);
        $data['slug'] = $this->uniqueSlug($data['name_en'] ?: $data['name_ar']);

        BlogCategory::create($data);

        return back()->with('success', 'تم إنشاء التصنيف');
    }

    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $data = $this->validateCategory($request);
        // Regenerate the slug only if the base name changed, and skip
        // the category's own row when checking uniqueness.
        if ($category->name_en !== $data['name_en'] || $category->name_ar !== $data['name_ar']) {
            $data['slug'] = $this->uniqueSlug($data['name_en'] ?: $data['name_ar'], $category->id);
        }
        $category->update($data);

        return back()->with('success', 'تم تحديث التصنيف');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        // Refuse the delete if the category still owns posts — otherwise
        // the foreign-key nullOnDelete() would silently orphan them and
        // the admin would never know their taxonomy was being emptied.
        $postCount = $category->posts()->count();
        if ($postCount > 0) {
            return back()->with('error',
                "لا يمكن حذف التصنيف لأنه يحتوي على {$postCount} مقال — انقل المقالات أو احذفها أولاً.");
        }

        $category->delete();

        return back()->with('success', 'تم حذف التصنيف');
    }

    protected function validateCategory(Request $request): array
    {
        return $request->validate([
            'name_ar' => ['required', 'string', 'max:120'],
            'name_en' => ['required', 'string', 'max:120'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    /** Generate a unique slug, suffixing -2, -3, ... on collision. */
    protected function uniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: 'cat-' . Str::random(6);
        $slug = $base;
        $i = 2;
        while (BlogCategory::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }
        return $slug;
    }
}
