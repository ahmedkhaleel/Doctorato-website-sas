<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use App\Models\DemoRequest;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'currencies' => fn () => Currency::where('is_active', true)->orderBy('display_order')->get(),
            'currentCurrency' => fn () => session('currency', 'EGP'),
            'auth' => [
                'user' => $request->user(),
            ],
            'trialReminders' => fn () => $request->user()
                ? DemoRequest::where('trial_status', 'expired')
                    ->where('admin_reminder_seen', false)
                    ->count()
                : 0,
            'tracking' => fn () => [
                'ga4_id' => SiteSetting::get('ga4_id'),
                'meta_pixel_id' => SiteSetting::get('meta_pixel_id'),
                'tiktok_pixel_id' => SiteSetting::get('tiktok_pixel_id'),
                'snapchat_pixel_id' => SiteSetting::get('snapchat_pixel_id'),
                'gtm_id' => SiteSetting::get('gtm_id'),
                // Only enable in production by default; admin can override
                'enabled' => SiteSetting::get('tracking_enabled', app()->environment('production') ? '1' : '0') === '1',
            ],
        ];
    }
}
