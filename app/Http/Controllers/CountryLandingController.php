<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\PricingPlan;
use App\Models\Testimonial;
use App\Services\CountryDetector;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Country-specific landing pages — /sa, /ae, /eg, ...
 *
 * Each URL locks the visitor's session country to that market (so
 * prices, currency, and contact numbers all localize), and renders
 * a single Vue page fed with country-tuned hero copy, pricing
 * preview, and local trust signals.
 *
 * Adding a market is a one-row edit to the $markets map below; no
 * new controller or route is needed.
 */
class CountryLandingController extends Controller
{
    /** Mapping from URL slug → market config. */
    protected array $markets = [
        'sa' => [
            'country_code' => 'SA',
            'flag' => '🇸🇦',
            'name_ar' => 'المملكة العربية السعودية',
            'name_en' => 'Saudi Arabia',
            'hero_title_ar' => 'نظام إدارة العيادات المعتمد في السعودية',
            'hero_title_en' => 'The clinic management system trusted across Saudi Arabia',
            'hero_subtitle_ar' => 'نظام متوافق مع متطلبات هيئة الصحة السعودية، مع ربط مباشر بشركات التأمين الكبرى وفوترة بالريال السعودي.',
            'hero_subtitle_en' => 'Compliant with Saudi Health Authority requirements. Direct integration with major insurers and SAR-native billing.',
            'phone' => '+966 55 555 5555',
            'whatsapp' => '+966555555555',
            'insurers' => ['بوبا', 'التعاونية', 'MedGulf', 'Tawuniya', 'الدرع العربي'],
            'highlights_ar' => [
                'تقارير مطابقة لمنصة NPHIES الموحّدة للتأمين',
                'دعم فني ناطق بالعربية 24/7 من الرياض',
                'فوترة VAT 15% تلقائية',
                'استضافة على خوادم داخل المملكة',
            ],
            'highlights_en' => [
                'Reports aligned with the unified NPHIES insurance platform',
                'Arabic-first 24/7 support based in Riyadh',
                'Automatic 15% VAT invoicing',
                'Hosted on servers inside the Kingdom',
            ],
        ],
        'ae' => [
            'country_code' => 'AE',
            'flag' => '🇦🇪',
            'name_ar' => 'الإمارات العربية المتحدة',
            'name_en' => 'United Arab Emirates',
            'hero_title_ar' => 'نظام إدارة العيادات الأول في الإمارات',
            'hero_title_en' => 'The #1 clinic management system in the UAE',
            'hero_subtitle_ar' => 'متوافق مع متطلبات هيئة الصحة بدبي وأبوظبي، مع ربط مباشر بنظام Riayati ودعم للتأمينات الإقليمية.',
            'hero_subtitle_en' => 'Compliant with DHA & DoH requirements. Direct Riayati integration and support for all regional insurers.',
            'phone' => '+971 55 796 1688',
            'whatsapp' => '+971557961688',
            'insurers' => ['Daman', 'AXA', 'Oman Insurance', 'ADNIC', 'MetLife'],
            'highlights_ar' => [
                'متوافق مع Riayati و Malaffi',
                'دعم إماراتي محلي + وثائق بالإنجليزية والعربية',
                'فوترة VAT 5% تلقائية',
                'تكامل مع بوابات الدفع الإماراتية',
            ],
            'highlights_en' => [
                'Riayati & Malaffi compliant',
                'UAE-based support in Arabic and English',
                'Automatic 5% VAT invoicing',
                'Integrates with UAE payment gateways',
            ],
        ],
        'eg' => [
            'country_code' => 'EG',
            'flag' => '🇪🇬',
            'name_ar' => 'مصر',
            'name_en' => 'Egypt',
            'hero_title_ar' => 'نظام إدارة العيادات الأول في مصر',
            'hero_title_en' => 'The #1 clinic management system in Egypt',
            'hero_subtitle_ar' => 'يخدم أكثر من 200 عيادة في القاهرة والإسكندرية والدلتا. فوترة بالجنيه المصري، دعم يومي، وتكاملات محلية.',
            'hero_subtitle_en' => 'Serving 200+ clinics across Cairo, Alexandria, and the Delta. EGP-native billing, daily support, and local integrations.',
            'phone' => '+20 101 296 7285',
            'whatsapp' => '201012967285',
            'insurers' => ['الهيئة العامة للتأمين الصحي', 'MetLife', 'AXA Egypt', 'Allianz', 'بوبا مصر'],
            'highlights_ar' => [
                'متوافق مع متطلبات وزارة الصحة المصرية',
                'فريق دعم من القاهرة بالعربية',
                'تكامل مع SMS Misr وبوابات الدفع المحلية',
                'أسعار بالجنيه المصري بدون تقلبات',
            ],
            'highlights_en' => [
                'Aligned with Egyptian Ministry of Health requirements',
                'Cairo-based Arabic support team',
                'SMS Misr and local payment gateway integrations',
                'Prices in EGP — no FX volatility',
            ],
        ],
    ];

    public function show(Request $request, string $slug, CountryDetector $detector): Response
    {
        $slug = strtolower($slug);
        abort_unless(isset($this->markets[$slug]), 404);

        $market = $this->markets[$slug];
        // Lock the visitor's session to this country so every downstream
        // page (pricing, checkout, demo form) stays in the same market.
        $detector->setCountry($request, $market['country_code']);

        // Load plans priced for this country so the landing page can
        // show a teaser ("starting at 297 SAR/month") without requiring
        // a second request.
        $plans = PricingPlan::where('is_active', true)
            ->where('is_custom', false)
            ->with(['prices' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('display_order')
            ->get()
            ->map(function ($plan) use ($market) {
                $price = $plan->priceFor($market['country_code']);
                return [
                    'id' => $plan->id,
                    'slug' => $plan->slug,
                    'name_ar' => $plan->name_ar,
                    'name_en' => $plan->name_en,
                    'monthly_price' => $price['monthly'],
                    'yearly_price' => $price['yearly'],
                    'setup_fee' => $price['setup_fee'] ?? 0,
                    'currency' => $price['currency'],
                    'currency_symbol' => $price['currency_symbol'],
                    'is_popular' => (bool) $plan->is_popular,
                ];
            });

        return Inertia::render('CountryLanding', [
            'market' => $market,
            'plans' => $plans,
            'testimonials' => Testimonial::where('is_active', true)
                ->orderByDesc('rating')
                ->orderBy('display_order')
                ->limit(3)
                ->get(),
        ]);
    }
}
