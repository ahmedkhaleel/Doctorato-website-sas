<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Faq, PricingPlan, Testimonial, Currency, ContactMessage, DemoRequest, NewsletterSubscriber, BlogPost, BlogCategory};
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'faqs' => Faq::count(),
                'plans' => PricingPlan::count(),
                'testimonials' => Testimonial::count(),
                'currencies' => Currency::count(),
                'contacts' => ContactMessage::count(),
                'demos' => DemoRequest::count(),
                'subscribers' => NewsletterSubscriber::count(),
                'posts' => BlogPost::count(),
                'unread_contacts' => ContactMessage::where('is_read', false)->count(),
                'new_demos' => DemoRequest::where('status', 'new')->count(),
            ],
        ]);
    }
}
