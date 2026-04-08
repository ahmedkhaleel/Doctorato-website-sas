<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use Inertia\Inertia;
use Inertia\Response;

class CaseStudyController extends Controller
{
    public function index(): Response
    {
        $cases = CaseStudy::published()
            ->orderByDesc('is_featured')
            ->orderBy('display_order')
            ->orderByDesc('id')
            ->get();

        return Inertia::render('CaseStudies/Index', [
            'cases' => $cases,
        ]);
    }

    public function show(string $slug): Response
    {
        $case = CaseStudy::published()->where('slug', $slug)->firstOrFail();
        $case->increment('views_count');

        $related = CaseStudy::published()
            ->where('id', '!=', $case->id)
            ->orderByDesc('is_featured')
            ->take(3)
            ->get();

        // NOTE: the prop is named `study` rather than `case` because `case`
        // is a reserved JavaScript keyword and breaks Vue's template compiler
        // in production builds (Rolldown).
        return Inertia::render('CaseStudies/Show', [
            'study' => $case,
            'related' => $related,
        ]);
    }
}
