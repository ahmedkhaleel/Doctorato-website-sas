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

    /** General site settings: contact info, social links, launch banner. */
    public function general(): Response
    {
        $keys = [
            'company_email', 'company_phone', 'company_whatsapp', 'company_address_ar', 'company_address_en',
            'social_twitter', 'social_facebook', 'social_instagram', 'social_linkedin', 'social_tiktok', 'social_youtube',
            'banner_enabled', 'banner_text_ar', 'banner_text_en', 'banner_cta_label_ar', 'banner_cta_label_en', 'banner_cta_url',
            'footer_tagline_ar', 'footer_tagline_en',
        ];

        $settings = [];
        foreach ($keys as $k) {
            $settings[$k] = SiteSetting::get($k);
        }
        $settings['banner_enabled'] = $settings['banner_enabled'] === '1';

        return Inertia::render('Admin/SettingsGeneral', [
            'general' => $settings,
        ]);
    }

    public function updateGeneral(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_email' => ['nullable', 'email', 'max:150'],
            'company_phone' => ['nullable', 'string', 'max:30'],
            'company_whatsapp' => ['nullable', 'string', 'max:30'],
            'company_address_ar' => ['nullable', 'string', 'max:300'],
            'company_address_en' => ['nullable', 'string', 'max:300'],
            'social_twitter' => ['nullable', 'url', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_linkedin' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],
            'banner_enabled' => ['nullable', 'boolean'],
            'banner_text_ar' => ['nullable', 'string', 'max:255'],
            'banner_text_en' => ['nullable', 'string', 'max:255'],
            'banner_cta_label_ar' => ['nullable', 'string', 'max:60'],
            'banner_cta_label_en' => ['nullable', 'string', 'max:60'],
            'banner_cta_url' => ['nullable', 'string', 'max:255'],
            'footer_tagline_ar' => ['nullable', 'string', 'max:300'],
            'footer_tagline_en' => ['nullable', 'string', 'max:300'],
        ]);

        $textKeys = [
            'company_email', 'company_phone', 'company_whatsapp', 'company_address_ar', 'company_address_en',
            'social_twitter', 'social_facebook', 'social_instagram', 'social_linkedin', 'social_tiktok', 'social_youtube',
            'banner_text_ar', 'banner_text_en', 'banner_cta_label_ar', 'banner_cta_label_en', 'banner_cta_url',
            'footer_tagline_ar', 'footer_tagline_en',
        ];

        foreach ($textKeys as $k) {
            SiteSetting::put($k, $data[$k] ?? null, 'general');
        }

        SiteSetting::put('banner_enabled', !empty($data['banner_enabled']) ? '1' : '0', 'general');

        return back()->with('success', 'تم حفظ الإعدادات العامة');
    }
}
