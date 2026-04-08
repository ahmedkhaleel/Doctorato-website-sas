<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = BlogPost::query()
            ->with('category:id,name_ar,name_en')
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('category'), fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('title_ar', 'like', "%{$term}%")
                        ->orWhere('title_en', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/BlogPosts', [
            'posts' => $posts,
            'categories' => BlogCategory::orderBy('display_order')->get(['id', 'name_ar', 'name_en']),
            'filters' => $request->only(['status', 'category', 'q']),
            'stats' => [
                'total' => BlogPost::count(),
                'published' => BlogPost::where('status', 'published')->count(),
                'draft' => BlogPost::where('status', 'draft')->count(),
                'scheduled' => BlogPost::where('status', 'scheduled')->count(),
                'total_views' => (int) BlogPost::sum('views_count'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePost($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title_en'] ?: $data['title_ar']);

        BlogPost::create($data);

        return back()->with('success', 'تم إنشاء المقال');
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $data = $this->validatePost($request, $post->id);

        // Re-slug only if explicitly changed
        if (!empty($data['slug']) && $data['slug'] !== $post->slug) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $post->id);
        } else {
            unset($data['slug']);
        }

        $post->update($data);

        return back()->with('success', 'تم تحديث المقال');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        // Best-effort: remove featured image file from storage
        if ($post->featured_image && Str::startsWith($post->featured_image, '/storage/')) {
            $relative = Str::after($post->featured_image, '/storage/');
            Storage::disk('public')->delete($relative);
        }

        $post->delete();

        return back()->with('success', 'تم حذف المقال');
    }

    /** Upload blog image (featured or inline) and return its public URL. */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
        ]);

        $path = $request->file('image')->store('blog', 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
            'path' => $path,
        ]);
    }

    protected function validatePost(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
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
        ]);
    }

    protected function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'post-' . Str::random(6);
        $original = $slug;
        $i = 2;

        while (BlogPost::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
