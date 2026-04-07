<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemoRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DemoController extends Controller
{
    public function index()
    {
        $demos = DemoRequest::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Demos', [
            'demos' => $demos,
            'trialStats' => [
                'active' => $demos->where('is_trial_active', true)->count(),
                'ending_soon' => $demos->where('is_trial_ending_soon', true)->count(),
                'expired' => $demos->where('trial_status', 'expired')->count(),
                'unseen_reminders' => $demos->where('trial_status', 'expired')->where('admin_reminder_seen', false)->count(),
            ],
        ]);
    }

    public function updateStatus(Request $request, DemoRequest $demo)
    {
        $validated = $request->validate([
            'status' => 'required|in:new,contacted,demo_scheduled,demo_done,converted,lost',
            'admin_notes' => 'nullable|string',
        ]);

        $demo->update($validated);
        return back()->with('success', 'تم تحديث حالة الطلب');
    }

    public function extendTrial(Request $request, DemoRequest $demo)
    {
        $days = (int) $request->input('days', 7);
        $base = $demo->trial_ends_at && $demo->trial_ends_at->isFuture()
            ? $demo->trial_ends_at
            : now();

        $demo->update([
            'trial_ends_at' => $base->copy()->addDays($days),
            'trial_status' => 'extended',
            'trial_expiry_notified' => false,
            'admin_reminder_seen' => true,
        ]);

        return back()->with('success', "تم تمديد التجربة بـ {$days} يوم");
    }

    public function markReminderSeen(DemoRequest $demo)
    {
        $demo->update(['admin_reminder_seen' => true]);
        return back();
    }

    public function destroy(DemoRequest $demo)
    {
        $demo->delete();
        return back()->with('success', 'تم حذف الطلب');
    }
}
