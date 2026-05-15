<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\PricingPlan;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\Currency;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Featured posts for the homepage strip — featured first, then
        // most recent. Keeps the section non-empty even before any post
        // gets the featured flag.
        $featuredPosts = BlogPost::with('category')
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'slug', 'title_ar', 'title_en', 'excerpt_ar', 'excerpt_en', 'featured_image', 'published_at', 'category_id']);

        return Inertia::render('Home', [
            'plans' => PricingPlan::where('is_active', true)->orderBy('display_order')->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('display_order')->get(),
            'faqs' => Faq::where('is_active', true)->orderBy('display_order')->get(),
            'currencies' => Currency::where('is_active', true)->orderBy('display_order')->get(),
            'currentCurrency' => session('currency', 'EGP'),
            'featuredPosts' => $featuredPosts,
        ]);
    }
}
