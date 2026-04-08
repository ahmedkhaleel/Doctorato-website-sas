<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $logs = ActivityLog::query()
            ->with('user:id,name,role')
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
            ->when($request->query('to'), fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
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
}
