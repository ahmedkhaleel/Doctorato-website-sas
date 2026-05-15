<?php

namespace App\Http\Middleware;

use App\Models\BlogPost;
use App\Models\Currency;
use App\Models\DemoRequest;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use App\Services\CountryDetector;
use App\Services\CountryMarkets;
use App\Services\LaunchOfferService;
use App\Services\RecaptchaService;
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
        $detector = app(CountryDetector::class);
        $activeCountry = $detector->resolve($request);
        // True when no server-side signal worked and the system fell
        // back to the default 'EG'. The frontend reads this flag and
        // runs a browser-side detection (bypasses PHP config issues).
        $geoNeedsClient = $detector->didServerDetectionFail();

        return [
            ...parent::share($request),
            'currencies' => fn () => Currency::where('is_active', true)->orderBy('display_order')->get(),
            'currentCurrency' => fn () => session('currency', 'EGP'),
            // Active country + list of supported markets — consumed by the
            // Navbar country switcher and the Pricing page to pick prices.
            'activeCountry' => $activeCountry,
            'geoNeedsClient' => $geoNeedsClient,
            'supportedCountries' => fn () => $detector->supportedCountries(),
            // Live scarcity counter for the launch offer. Lazy closure so
            // the DB hit is only paid on pages that actually read it.
            'launchOffer' => fn () => app(LaunchOfferService::class)->snapshot(),
            // Expose the reCAPTCHA site key to forms that need it. Null
            // when reCAPTCHA isn't configured — frontend falls back to
            // the honeypot + timing defenses only.
            'recaptcha' => fn () => [
                'site_key' => app(RecaptchaService::class)->siteKey(),
                'enabled' => app(RecaptchaService::class)->isEnabled(),
            ],
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
                'contact' => (function () use ($activeCountry) {
                    // Per-country override: when the visitor is detected
                    // in one of our supported markets, swap the phone +
                    // WhatsApp for the local pair. Email + address stay
                    // global because we operate from one HQ.
                    $market = CountryMarkets::find($activeCountry);
                    return [
                        'email' => SiteSetting::get('company_email'),
                        'phone' => $market['phone'] ?? SiteSetting::get('company_phone'),
                        'whatsapp' => $market['whatsapp'] ?? SiteSetting::get('company_whatsapp'),
                        'country_code' => $activeCountry,
                        'country_flag' => $market['flag'] ?? null,
                        'address_ar' => SiteSetting::get('company_address_ar'),
                        'address_en' => SiteSetting::get('company_address_en'),
                    ];
                })(),
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
            // Latest 4 blog posts for the footer "Latest from the blog"
            // column. Cached 10 minutes so the footer doesn't pay for a
            // DB hit on every request — invalidates on next cache flush.
            'footerLatestPosts' => fn () => Cache::remember('footer.latest_posts', 600, function () {
                return BlogPost::where('status', 'published')
                    ->where('published_at', '<=', now())
                    ->orderByDesc('published_at')
                    ->limit(4)
                    ->get(['id', 'slug', 'title_ar', 'title_en', 'published_at']);
            }),
        ];
    }
}
