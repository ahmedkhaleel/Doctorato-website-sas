<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCoupon($request);
        $data['code'] = strtoupper(trim($data['code']));
        Coupon::create($data);

        return back()->with('success', 'تم إنشاء الكوبون');
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $data = $this->validateCoupon($request, $coupon->id);
        $data['code'] = strtoupper(trim($data['code']));
        $coupon->update($data);

        return back()->with('success', 'تم تحديث الكوبون');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        return back()->with('success', 'تم حذف الكوبون');
    }

    protected function validateCoupon(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => [
                'required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/i',
                'unique:coupons,code' . ($ignoreId ? ',' . $ignoreId : ''),
            ],
            'description_ar' => ['nullable', 'string', 'max:160'],
            'description_en' => ['nullable', 'string', 'max:160'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'max_uses_per_customer' => ['nullable', 'integer', 'min:1'],
            'plan_ids' => ['nullable', 'array'],
            'plan_ids.*' => ['integer', 'exists:pricing_plans,id'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
