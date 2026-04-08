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
            'site' => fn () => [
                'contact' => [
                    'email' => SiteSetting::get('company_email'),
                    'phone' => SiteSetting::get('company_phone'),
                    'whatsapp' => SiteSetting::get('company_whatsapp'),
                    'address_ar' => SiteSetting::get('company_address_ar'),
                    'address_en' => SiteSetting::get('company_address_en'),
                ],
                'social' => [
                    'twitter' => SiteSetting::get('social_twitter'),
                    'facebook' => SiteSetting::get('social_facebook'),
                    'instagram' => SiteSetting::get('social_instagram'),
                    'linkedin' => SiteSetting::get('social_linkedin'),
                    'tiktok' => SiteSetting::get('social_tiktok'),
                    'youtube' => SiteSetting::get('social_youtube'),
                ],
                'banner' => [
                    'enabled' => SiteSetting::get('banner_enabled') === '1',
                    'text_ar' => SiteSetting::get('banner_text_ar'),
                    'text_en' => SiteSetting::get('banner_text_en'),
                    'cta_label_ar' => SiteSetting::get('banner_cta_label_ar'),
                    'cta_label_en' => SiteSetting::get('banner_cta_label_en'),
                    'cta_url' => SiteSetting::get('banner_cta_url'),
                ],
                'footer' => [
                    'tagline_ar' => SiteSetting::get('footer_tagline_ar'),
                    'tagline_en' => SiteSetting::get('footer_tagline_en'),
                ],
            ],
        ];
    }
}
