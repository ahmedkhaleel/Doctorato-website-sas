<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\Testimonial;
use App\Models\Faq;
use App\Models\Currency;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home', [
            'plans' => PricingPlan::where('is_active', true)->orderBy('display_order')->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('display_order')->get(),
            'faqs' => Faq::where('is_active', true)->orderBy('display_order')->get(),
            'currencies' => Currency::where('is_active', true)->orderBy('display_order')->get(),
            'currentCurrency' => session('currency', 'EGP'),
        ]);
    }
}
