<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    /** Tracking & Analytics settings (GA4, Meta, TikTok, Snap, GTM). */
    public function tracking(): Response
    {
        return Inertia::render('Admin/Settings', [
            'tracking' => [
                'ga4_id' => SiteSetting::get('ga4_id'),
                'gtm_id' => SiteSetting::get('gtm_id'),
                'meta_pixel_id' => SiteSetting::get('meta_pixel_id'),
                'tiktok_pixel_id' => SiteSetting::get('tiktok_pixel_id'),
                'snapchat_pixel_id' => SiteSetting::get('snapchat_pixel_id'),
                'tracking_enabled' => SiteSetting::get('tracking_enabled', '0') === '1',
            ],
        ]);
    }

    public function updateTracking(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ga4_id' => ['nullable', 'string', 'max:30'],
            'gtm_id' => ['nullable', 'string', 'max:30'],
            'meta_pixel_id' => ['nullable', 'string', 'max:30'],
            'tiktok_pixel_id' => ['nullable', 'string', 'max:30'],
            'snapchat_pixel_id' => ['nullable', 'string', 'max:60'],
            'tracking_enabled' => ['nullable', 'boolean'],
        ]);

        foreach (['ga4_id', 'gtm_id', 'meta_pixel_id', 'tiktok_pixel_id', 'snapchat_pixel_id'] as $key) {
            SiteSetting::put($key, $data[$key] ?? null, 'tracking');
        }
        SiteSetting::put('tracking_enabled', !empty($data['tracking_enabled']) ? '1' : '0', 'tracking');

        return back()->with('success', 'تم حفظ إعدادات التتبع');
    }
}
