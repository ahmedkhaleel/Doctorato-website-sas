<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Admin-side outbound email visibility.
 *
 *   GET /admin/email-logs           list + filter + stats
 *
 * Filters supported:
 *   - status   = queued | sending | sent | failed
 *   - class    = mailable FQCN or class-basename match
 *   - email    = freetext; we hash it and look up by hashed_recipient
 *                so the admin can search the full address without
 *                the DB ever holding it plaintext
 *
 * Permission: emails.view — read-only by design. There's no edit/
 * delete surface; the retention pruner handles cleanup.
 */
class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = EmailLog::query()
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->query('class'), function ($q, $cls) {
                // Allow both basename ("TrialDripMail") and full FQCN.
                $q->where('mailable_class', 'like', "%{$cls}%");
            })
            ->when($request->query('email'), function ($q, $email) {
                $q->where('hashed_recipient', EmailLog::hashEmail($email));
            });

        $logs = $query->orderByDesc('id')->paginate(50)->withQueryString();

        return Inertia::render('Admin/EmailLogs', [
            'logs' => $logs,
            'filters' => $request->only(['status', 'class', 'email']),
            'stats' => [
                'total' => EmailLog::count(),
                'today' => EmailLog::whereDate('queued_at', today())->count(),
                'sent_7d' => EmailLog::where('status', EmailLog::STATUS_SENT)
                    ->where('queued_at', '>=', now()->subDays(7))->count(),
                'failed_7d' => EmailLog::where('status', EmailLog::STATUS_FAILED)
                    ->where('queued_at', '>=', now()->subDays(7))->count(),
            ],
            // Distinct mailable classes in the last 30 days, for the
            // class-filter dropdown.
            'classes' => EmailLog::query()
                ->where('queued_at', '>=', now()->subDays(30))
                ->whereNotNull('mailable_class')
                ->distinct()->pluck('mailable_class')->values(),
        ]);
    }
}
