<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PaymobWebhookController;
use App\Models\ActivityLog;
use App\Models\WebhookEvent;
use App\Services\PaymobService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Admin-side webhook inspector + replay tool.
 *
 *   GET  /admin/webhooks            → recent events table
 *   GET  /admin/webhooks/{id}       → single event detail (payload + response)
 *   POST /admin/webhooks/{id}/replay→ re-run the stored payload
 *
 * Replay creates a NEW row (clone) so the original event's audit
 * trail stays intact. The new row has replayed_from_id set to the
 * source so the dashboard can show "this is a replay of #N".
 *
 * Permission gate: webhooks.manage — limited to billing / engineering
 * roles. Read-only viewers shouldn't be able to re-fire a payment
 * webhook against a customer's subscription.
 */
class WebhookController extends Controller
{
    public function index(Request $request)
    {
        $events = WebhookEvent::query()
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('order_id', 'like', "%{$term}%")
                      ->orWhere('gateway_id', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Admin/Webhooks', [
            'events' => $events,
            'filters' => $request->only(['status', 'q']),
            'stats' => [
                'total' => WebhookEvent::count(),
                'received' => WebhookEvent::where('status', WebhookEvent::STATUS_RECEIVED)->count(),
                'processed' => WebhookEvent::where('status', WebhookEvent::STATUS_PROCESSED)->count(),
                'failed' => WebhookEvent::where('status', WebhookEvent::STATUS_FAILED)->count(),
            ],
        ]);
    }

    public function show(WebhookEvent $event)
    {
        return Inertia::render('Admin/WebhookDetail', [
            'event' => $event->load('origin'),
            'replays' => WebhookEvent::where('replayed_from_id', $event->id)
                ->orderByDesc('id')->get(['id', 'status', 'received_at', 'error']),
        ]);
    }

    public function replay(Request $request, WebhookEvent $event, PaymobService $paymob)
    {
        // We don't replay events that never passed HMAC — those are
        // either misconfigured or hostile.
        if (!$event->hmac_valid) {
            return back()->withErrors(['replay' => 'لا يمكن إعادة تشغيل حدث بـ HMAC غير صالح.']);
        }

        // Hard guard: only replay processed/failed events. A row still
        // marked received probably has an async processor running on
        // it RIGHT NOW; re-firing would race.
        if (!in_array($event->status, [WebhookEvent::STATUS_PROCESSED, WebhookEvent::STATUS_FAILED], true)) {
            return back()->withErrors(['replay' => 'الحدث مازال قيد المعالجة، حاول لاحقاً.']);
        }

        // Clone the row first so the original outcome survives.
        $clone = WebhookEvent::create([
            'source' => $event->source,
            'event_type' => $event->event_type,
            'gateway_id' => $event->gateway_id,
            'order_id' => $event->order_id,
            'hmac_valid' => $event->hmac_valid,
            'status' => WebhookEvent::STATUS_RECEIVED,
            'payload' => $event->payload,
            'ip' => $request->ip(),
            'received_at' => now(),
            'replayed_from_id' => $event->id,
        ]);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'webhook_replay',
            'description' => "Replayed webhook event #{$event->id} as #{$clone->id}",
            'subject_type' => WebhookEvent::class,
            'subject_id' => $event->id,
        ]);

        // Re-run the processor against the clone. The idempotency
        // guard inside PaymobWebhookController::process() means a
        // successful original will short-circuit on `duplicate=true`
        // — exactly what we want.
        app(PaymobWebhookController::class)->processStoredEvent($clone, $paymob);

        return redirect()
            ->route('admin.webhooks.show', $clone->id)
            ->with('success', "تمت إعادة التشغيل كحدث #{$clone->id}");
    }
}
