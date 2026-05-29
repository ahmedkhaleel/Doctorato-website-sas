<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddOnRequest;
use App\Models\ActivityLog;
use App\Models\AddOn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AddOnController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/AddOns', [
            'addons' => AddOn::orderBy('display_order')->orderBy('id')->get(),
        ]);
    }

    public function store(AddOnRequest $request): RedirectResponse
    {
        $addon = AddOn::create($request->validated());
        ActivityLog::record('created', $addon, "أضاف إضافة: {$addon->name_ar}");

        return back()->with('success', 'تمت إضافة الـ Add-on');
    }

    public function update(AddOnRequest $request, AddOn $addon): RedirectResponse
    {
        $addon->update($request->validated());
        ActivityLog::record('updated', $addon, "عدّل إضافة: {$addon->name_ar}");

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(AddOn $addon): RedirectResponse
    {
        $name = $addon->name_ar;
        $addon->delete();
        ActivityLog::record('deleted', null, "حذف إضافة: {$name}");

        return back()->with('success', 'تم الحذف');
    }
}
