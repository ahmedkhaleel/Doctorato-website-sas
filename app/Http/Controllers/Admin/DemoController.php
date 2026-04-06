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
        return Inertia::render('Admin/Demos', [
            'demos' => DemoRequest::orderBy('created_at', 'desc')->get(),
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

    public function destroy(DemoRequest $demo)
    {
        $demo->delete();
        return back()->with('success', 'تم حذف الطلب');
    }
}
