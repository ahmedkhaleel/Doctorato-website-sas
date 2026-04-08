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
        $data['slug'] = Str::slug($data['name_en'] ?: $data['name_ar']) ?: 'cat-' . Str::random(6);

        BlogCategory::create($data);

        return back()->with('success', 'تم إنشاء التصنيف');
    }

    public function update(Request $request, BlogCategory $category): RedirectResponse
    {
        $data = $this->validateCategory($request);
        if (empty($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name_en'] ?: $data['name_ar']);
        }
        $category->update($data);

        return back()->with('success', 'تم تحديث التصنيف');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
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
}
