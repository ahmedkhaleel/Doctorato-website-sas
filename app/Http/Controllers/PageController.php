<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use App\Models\Faq;
use Inertia\Inertia;

class PageController extends Controller
{
    public function features()
    {
        return Inertia::render('Features');
    }

    public function portals()
    {
        return Inertia::render('Portals');
    }

    public function dental()
    {
        return Inertia::render('Dental');
    }

    public function pediatrics()
    {
        return Inertia::render('Pediatrics');
    }

    public function telemedicine()
    {
        return Inertia::render('Telemedicine');
    }

    public function solutions()
    {
        return Inertia::render('Solutions');
    }

    public function technology()
    {
        return Inertia::render('Technology');
    }

    public function reports()
    {
        return Inertia::render('Reports');
    }

    public function about()
    {
        return Inertia::render('About', [
            'testimonials' => Testimonial::where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    public function faq()
    {
        return Inertia::render('Faq', [
            'faqs' => Faq::where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    public function contact()
    {
        return Inertia::render('Contact');
    }

    public function demo()
    {
        return Inertia::render('Demo');
    }
}
