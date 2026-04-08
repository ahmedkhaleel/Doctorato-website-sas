<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\CaseStudy;
use App\Models\ContactMessage;
use App\Models\DemoRequest;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Unified search across all admin-facing entities.
     * Returns a flat list of results grouped by type on the frontend.
     */
    public function global(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $like = "%{$q}%";
        $results = [];

        // Subscriptions
        foreach (Subscription::query()
            ->where(fn ($w) => $w->where('reference', 'like', $like)
                ->orWhere('customer_name', 'like', $like)
                ->orWhere('customer_email', 'like', $like)
                ->orWhere('customer_phone', 'like', $like))
            ->latest('id')->take(5)->get() as $s) {
            $results[] = [
                'type' => 'subscription',
                'type_label' => 'اشتراك',
                'title' => $s->customer_name,
                'subtitle' => "{$s->reference} · {$s->status}",
                'url' => "/admin/subscriptions/{$s->id}",
                'icon' => '💳',
            ];
        }

        // Invoices
        foreach (Invoice::query()->where('number', 'like', $like)->latest('id')->take(5)->get() as $i) {
            $results[] = [
                'type' => 'invoice',
                'type_label' => 'فاتورة',
                'title' => $i->number,
                'subtitle' => number_format((float) $i->total, 2) . ' ' . $i->currency . ' · ' . $i->status,
                'url' => "/admin/invoices/{$i->id}",
                'icon' => '📄',
            ];
        }

        // Demos
        foreach (DemoRequest::query()
            ->where(fn ($w) => $w->where('full_name', 'like', $like)
                ->orWhere('clinic_name', 'like', $like)
                ->orWhere('email', 'like', $like))
            ->latest('id')->take(5)->get() as $d) {
            $results[] = [
                'type' => 'demo',
                'type_label' => 'طلب تجربة',
                'title' => $d->full_name ?? $d->clinic_name,
                'subtitle' => $d->clinic_name . ' · ' . ($d->status ?? 'new'),
                'url' => '/admin/demos',
                'icon' => '🎯',
            ];
        }

        // Contacts
        foreach (ContactMessage::query()
            ->where(fn ($w) => $w->where('name', 'like', $like)
                ->orWhere('email', 'like', $like)
                ->orWhere('subject', 'like', $like))
            ->latest('id')->take(5)->get() as $c) {
            $results[] = [
                'type' => 'contact',
                'type_label' => 'رسالة',
                'title' => $c->name,
                'subtitle' => $c->subject,
                'url' => '/admin/contacts',
                'icon' => '✉️',
            ];
        }

        // Blog posts
        foreach (BlogPost::query()
            ->where(fn ($w) => $w->where('title_ar', 'like', $like)
                ->orWhere('title_en', 'like', $like)
                ->orWhere('slug', 'like', $like))
            ->latest('id')->take(5)->get() as $p) {
            $results[] = [
                'type' => 'blog',
                'type_label' => 'مقال',
                'title' => $p->title_ar,
                'subtitle' => "/blog/{$p->slug}",
                'url' => '/admin/blog/posts',
                'icon' => '📝',
            ];
        }

        // Case studies
        foreach (CaseStudy::query()
            ->where(fn ($w) => $w->where('title_ar', 'like', $like)
                ->orWhere('client_name_ar', 'like', $like)
                ->orWhere('slug', 'like', $like))
            ->latest('id')->take(3)->get() as $cs) {
            $results[] = [
                'type' => 'case_study',
                'type_label' => 'دراسة حالة',
                'title' => $cs->title_ar,
                'subtitle' => $cs->client_name_ar,
                'url' => '/admin/case-studies',
                'icon' => '⭐',
            ];
        }

        return response()->json(['results' => $results]);
    }
}
