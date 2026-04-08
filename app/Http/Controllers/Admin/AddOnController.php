<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function store(Request $request): RedirectResponse
    {
        AddOn::create($this->validateAddon($request));

        return back()->with('success', 'تمت إضافة الـ Add-on');
    }

    public function update(Request $request, AddOn $addon): RedirectResponse
    {
        $addon->update($this->validateAddon($request));

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(AddOn $addon): RedirectResponse
    {
        $addon->delete();

        return back()->with('success', 'تم الحذف');
    }

    protected function validateAddon(Request $request): array
    {
        return $request->validate([
            'name_ar' => ['required', 'string', 'max:120'],
            'name_en' => ['required', 'string', 'max:120'],
            'description_ar' => ['nullable', 'string', 'max:255'],
            'description_en' => ['nullable', 'string', 'max:255'],
            'price_egp' => ['required', 'numeric', 'min:0'],
            'period' => ['required', 'in:monthly,yearly,one_time'],
            'icon' => ['nullable', 'string', 'max:50'],
            'badge_ar' => ['nullable', 'string', 'max:50'],
            'badge_en' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
