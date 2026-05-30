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

    public function dermatology()
    {
        return Inertia::render('Dermatology');
    }

    public function pediatrics()
    {
        return Inertia::render('Pediatrics');
    }

    public function telemedicine()
    {
        return Inertia::render('Telemedicine');
    }

    public function obstetrics()
    {
        return Inertia::render('Obstetrics');
    }

    /**
     * Public add-ons page. Reads the full add-on catalogue via the
     * cache so the page stays a single DB read per 10-min window.
     */
    public function addOns(\App\Services\PublicContentCache $cache)
    {
        return Inertia::render('AddOns', [
            'addons' => $cache->addons(),
        ]);
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
