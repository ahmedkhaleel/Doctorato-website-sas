<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Faq, PricingPlan, Testimonial, Currency, ContactMessage, DemoRequest, NewsletterSubscriber, BlogPost, User};
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);

        // Build a 7-day trend for demos & contacts
        $trend = collect(range(6, 0))->map(function ($daysAgo) use ($now) {
            $date = $now->copy()->subDays($daysAgo)->startOfDay();
            return [
                'label' => $date->format('M d'),
                'day' => $date->format('D'),
                'demos' => DemoRequest::whereDate('created_at', $date)->count(),
                'contacts' => ContactMessage::whereDate('created_at', $date)->count(),
            ];
        })->values();

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
                'users' => User::count(),
                'unread_contacts' => ContactMessage::where('is_read', false)->count(),
                'new_demos' => DemoRequest::where('status', 'new')->count(),
                'demos_this_week' => DemoRequest::where('created_at', '>=', $weekAgo)->count(),
                'contacts_this_week' => ContactMessage::where('created_at', '>=', $weekAgo)->count(),
            ],
            'trend' => $trend,
            'recentDemos' => DemoRequest::latest()->take(5)->get(['id', 'clinic_name', 'full_name', 'status', 'created_at']),
            'recentContacts' => ContactMessage::latest()->take(5)->get(['id', 'name', 'subject', 'is_read', 'created_at']),
        ]);
    }
}
