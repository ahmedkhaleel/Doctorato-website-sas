<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CaseStudy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CaseStudyController extends Controller
{
    public function index(Request $request): Response
    {
        $cases = CaseStudy::query()
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('title_ar', 'like', "%{$term}%")
                        ->orWhere('title_en', 'like', "%{$term}%")
                        ->orWhere('client_name_ar', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('Admin/CaseStudies', [
            'cases' => $cases,
            'filters' => $request->only('q'),
            'stats' => [
                'total' => CaseStudy::count(),
                'published' => CaseStudy::where('is_published', true)->count(),
                'featured' => CaseStudy::where('is_featured', true)->count(),
                'total_views' => (int) CaseStudy::sum('views_count'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCase($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?: $data['title_en'] ?: $data['title_ar']);

        $case = CaseStudy::create($data);
        ActivityLog::record('created', $case, "أنشأ دراسة حالة: {$case->title_ar}");

        return back()->with('success', 'تم إنشاء دراسة الحالة');
    }

    public function update(Request $request, CaseStudy $caseStudy): RedirectResponse
    {
        $data = $this->validateCase($request, $caseStudy->id);

        if (!empty($data['slug']) && $data['slug'] !== $caseStudy->slug) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $caseStudy->id);
        } else {
            unset($data['slug']);
        }

        $caseStudy->update($data);
        ActivityLog::record('updated', $caseStudy, "عدّل دراسة حالة: {$caseStudy->title_ar}");

        return back()->with('success', 'تم تحديث دراسة الحالة');
    }

    public function destroy(CaseStudy $caseStudy): RedirectResponse
    {
        $title = $caseStudy->title_ar;
        $caseStudy->delete();
        ActivityLog::record('deleted', null, "حذف دراسة حالة: {$title}");

        return back()->with('success', 'تم حذف دراسة الحالة');
    }

    protected function validateCase(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
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
        ]);
    }

    protected function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base) ?: 'case-' . Str::random(6);
        $original = $slug;
        $i = 2;

        while (CaseStudy::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }
}
