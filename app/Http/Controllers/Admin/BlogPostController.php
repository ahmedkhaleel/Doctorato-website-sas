<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogPostRequest;
use App\Models\ActivityLog;
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

    public function store(BlogPostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // When the admin leaves slug blank, fall back to the English then
        // Arabic title. Note: validator marks `slug` as nullable, so the
        // key may not exist in the validated output — use null coalescing
        // instead of indexing directly.
        $data['slug'] = $this->uniqueSlug(($data['slug'] ?? null) ?: ($data['title_en'] ?? '') ?: ($data['title_ar'] ?? ''));

        $post = BlogPost::create($data);
        ActivityLog::record('created', $post, "أنشأ مقال: {$post->title_ar}");

        return back()->with('success', 'تم إنشاء المقال');
    }

    public function update(BlogPostRequest $request, BlogPost $post): RedirectResponse
    {
        $data = $request->validated();

        // Re-slug only if explicitly changed
        if (!empty($data['slug']) && $data['slug'] !== $post->slug) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $post->id);
        } else {
            unset($data['slug']);
        }

        $post->update($data);
        ActivityLog::record('updated', $post, "عدّل مقال: {$post->title_ar}");

        return back()->with('success', 'تم تحديث المقال');
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        // Best-effort: remove featured image file from storage
        if ($post->featured_image && Str::startsWith($post->featured_image, '/storage/')) {
            $relative = Str::after($post->featured_image, '/storage/');
            Storage::disk('public')->delete($relative);
        }

        $title = $post->title_ar;
        $post->delete();
        ActivityLog::record('deleted', null, "حذف مقال: {$title}");

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
