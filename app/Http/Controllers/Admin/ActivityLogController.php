<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = $this->filtered($request)
            ->with('user:id,name,role')
            ->latest('id')
            ->paginate(30)
            ->withQueryString();

        return Inertia::render('Admin/ActivityLogs', [
            'logs' => $logs,
            'filters' => $request->only(['q', 'action', 'user', 'from', 'to']),
            'stats' => [
                'total' => ActivityLog::count(),
                'today' => ActivityLog::whereDate('created_at', today())->count(),
                'week' => ActivityLog::where('created_at', '>=', now()->subDays(7))->count(),
                'users_active' => ActivityLog::where('created_at', '>=', now()->subDays(7))
                    ->distinct('user_id')->count('user_id'),
            ],
            'actions' => ActivityLog::query()->distinct()->pluck('action'),
            'users' => \App\Models\User::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Stream a CSV of the filtered log set. Lazy-chunked so the whole
     * activity_logs table doesn't need to load in memory for an
     * export of a 100k-row range.
     *
     * UTF-8 BOM at the start so Excel renders the Arabic correctly
     * instead of mojibake.
     */
    public function export(Request $request): StreamedResponse
    {
        $filename = 'activity-log-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($request) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'ID', 'When', 'User', 'Role', 'Action', 'Subject Type',
                'Subject ID', 'Subject Label', 'Description', 'Changes (JSON)',
                'IP', 'User Agent',
            ]);

            $this->filtered($request)
                ->orderBy('id')
                ->chunkById(500, function ($chunk) use ($out) {
                    foreach ($chunk as $log) {
                        fputcsv($out, [
                            $log->id,
                            $log->created_at?->toIso8601String(),
                            $log->user_name,
                            $log->user_role,
                            $log->action,
                            $log->subject_type,
                            $log->subject_id,
                            $log->subject_label,
                            $log->description,
                            $log->changes ? json_encode($log->changes, JSON_UNESCAPED_UNICODE) : '',
                            $log->ip_address,
                            $log->user_agent,
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Build the base query with the user-supplied filters applied.
     * Shared between the JSON index and the CSV export so both
     * surfaces reflect the same filter semantics.
     */
    protected function filtered(Request $request)
    {
        return ActivityLog::query()
            ->when($request->query('q'), function ($q, $term) {
                $q->where(function ($w) use ($term) {
                    $w->where('description', 'like', "%{$term}%")
                        ->orWhere('user_name', 'like', "%{$term}%")
                        ->orWhere('subject_label', 'like', "%{$term}%")
                        ->orWhere('subject_type', 'like', "%{$term}%");
                });
            })
            ->when($request->query('action'), fn ($q, $a) => $q->where('action', $a))
            ->when($request->query('user'), fn ($q, $u) => $q->where('user_id', $u))
            ->when($request->query('from'), fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->query('to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
    }
}
