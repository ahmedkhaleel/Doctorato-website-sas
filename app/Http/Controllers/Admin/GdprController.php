<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GdprService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Admin-only GDPR data-subject rights surface.
 *
 *   GET  /admin/gdpr           → search page
 *   POST /admin/gdpr/export    → JSON download of everything we hold
 *   POST /admin/gdpr/erase     → tombstone + redact (irreversible)
 *
 * Permission gate: 'gdpr.manage' — only the privacy officer / DPO
 * should be granted this. Operations are logged into activity_logs
 * with the actor's user_id so the audit trail survives the erasure.
 */
class GdprController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Gdpr');
    }

    public function export(Request $request, GdprService $gdpr)
    {
        $request->validate(['email' => 'required|email|max:255']);
        $this->gate($request);

        $email = strtolower(trim($request->input('email')));
        $report = $gdpr->export($email);

        \App\Models\ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'gdpr_export',
            'description' => "GDPR export generated for {$email}",
        ]);

        $filename = 'gdpr-export-' . preg_replace('/[^A-Za-z0-9]/', '_', $email) . '-' . now()->format('Ymd-His') . '.json';

        return response()->streamDownload(
            fn () => print json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $filename,
            ['Content-Type' => 'application/json']
        );
    }

    public function erase(Request $request, GdprService $gdpr)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'reason' => 'required|string|max:255',
            'confirm' => 'required|in:ERASE',
        ]);
        $this->gate($request);

        $result = $gdpr->erase(
            strtolower(trim($request->input('email'))),
            $request->input('reason'),
        );

        if ($result['status'] === 'no_data') {
            return back()->with('gdprMessage', 'لا يوجد بيانات لهذا البريد.');
        }
        return back()->with('gdprMessage', 'تم تنفيذ الحذف. الـ ID المحفوظ للتدقيق: #' . $result['demo_request_id']);
    }

    /** Centralised permission gate so future routes inherit it consistently. */
    protected function gate(Request $request): void
    {
        $user = $request->user();
        if (!$user || (method_exists($user, 'hasPermission') && !$user->hasPermission('gdpr.manage'))) {
            abort(403, 'لا تملك صلاحية إدارة طلبات الخصوصية.');
        }
    }
}
