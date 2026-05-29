<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\ActivityLog;
use App\Models\Coupon;
use App\Models\PricingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Coupons', [
            'coupons' => Coupon::latest('id')->get(),
            'plans' => PricingPlan::where('is_active', true)->get(['id', 'name_ar', 'name_en']),
            'stats' => [
                'total' => Coupon::count(),
                'active' => Coupon::where('is_active', true)->count(),
                'expired' => Coupon::whereNotNull('expires_at')->where('expires_at', '<', now())->count(),
                'total_redemptions' => (int) Coupon::sum('used_count'),
            ],
        ]);
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $coupon = Coupon::create($data);
        ActivityLog::record('created', $coupon, "أنشأ كوبون: {$coupon->code}");

        return back()->with('success', 'تم إنشاء الكوبون');
    }

    public function update(CouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $data = $request->validated();
        $data['code'] = strtoupper(trim($data['code']));
        $coupon->update($data);
        ActivityLog::record('updated', $coupon, "عدّل كوبون: {$coupon->code}");

        return back()->with('success', 'تم تحديث الكوبون');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $code = $coupon->code;
        $coupon->delete();
        ActivityLog::record('deleted', null, "حذف كوبون: {$code}");

        return back()->with('success', 'تم حذف الكوبون');
    }
}
