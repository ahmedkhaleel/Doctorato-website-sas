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
        'kw' => [
            'country_code' => 'KW',
            'flag' => '🇰🇼',
            'name_ar' => 'الكويت',
            'name_en' => 'Kuwait',
            'hero_title_ar' => 'نظام إدارة العيادات الأول في الكويت',
            'hero_title_en' => 'The leading clinic management system in Kuwait',
            'hero_subtitle_ar' => 'متوافق مع متطلبات وزارة الصحة الكويتية، فوترة بالدينار الكويتي، ودعم عربي محلي على مدار الساعة.',
            'hero_subtitle_en' => 'Aligned with Kuwait Ministry of Health requirements. KWD-native billing and 24/7 Arabic support.',
            'phone' => '+965 9000 0000',
            'whatsapp' => '96590000000',
            'insurers' => ['Gulf Insurance', 'Warba Insurance', 'AXA Gulf', 'MetLife', 'Bupa Arabia'],
            'highlights_ar' => [
                'متوافق مع متطلبات وزارة الصحة الكويتية',
                'فوترة بالدينار الكويتي بدون رسوم تحويل',
                'دعم عربي 24/7 من الكويت والخليج',
                'تكامل مع بوابات الدفع المحلية (KNET)',
            ],
            'highlights_en' => [
                'Compliant with Kuwait MoH requirements',
                'KWD billing — no FX conversion fees',
                '24/7 Arabic support across the Gulf',
                'KNET and local payment gateway integrations',
            ],
        ],
        'qa' => [
            'country_code' => 'QA',
            'flag' => '🇶🇦',
            'name_ar' => 'قطر',
            'name_en' => 'Qatar',
            'hero_title_ar' => 'نظام إدارة العيادات الأول في قطر',
            'hero_title_en' => 'The leading clinic management system in Qatar',
            'hero_subtitle_ar' => 'متوافق مع متطلبات وزارة الصحة القطرية وحمد الطبية، فوترة بالريال القطري، ودعم عربي محلي.',
            'hero_subtitle_en' => 'Aligned with Qatar MoPH and Hamad Medical requirements. QAR-native billing and local Arabic support.',
            'phone' => '+974 3000 0000',
            'whatsapp' => '97430000000',
            'insurers' => ['QLM', 'AXA Gulf', 'MetLife', 'Bupa Arabia', 'Daman'],
            'highlights_ar' => [
                'متوافق مع متطلبات وزارة الصحة العامة القطرية',
                'فوترة بالريال القطري',
                'دعم عربي وإنجليزي على مدار الساعة',
                'تكامل مع بوابات الدفع القطرية',
            ],
            'highlights_en' => [
                'Compliant with Qatar Ministry of Public Health',
                'QAR-native billing',
                '24/7 Arabic and English support',
                'Integrates with Qatari payment gateways',
            ],
        ],
        'bh' => [
            'country_code' => 'BH',
            'flag' => '🇧🇭',
            'name_ar' => 'البحرين',
            'name_en' => 'Bahrain',
            'hero_title_ar' => 'نظام إدارة العيادات الأول في البحرين',
            'hero_title_en' => 'The leading clinic management system in Bahrain',
            'hero_subtitle_ar' => 'متوافق مع متطلبات NHRA البحرينية، فوترة بالدينار البحريني، ودعم عربي على مدار الساعة.',
            'hero_subtitle_en' => 'Aligned with NHRA Bahrain requirements. BHD-native billing and 24/7 Arabic support.',
            'phone' => '+973 3000 0000',
            'whatsapp' => '97330000000',
            'insurers' => ['Solidarity', 'Bahrain National Insurance', 'AXA Gulf', 'MetLife', 'Bupa Arabia'],
            'highlights_ar' => [
                'متوافق مع متطلبات الهيئة الوطنية لتنظيم المهن والخدمات الصحية (NHRA)',
                'فوترة بالدينار البحريني',
                'دعم عربي 24/7',
                'تكامل مع بوابات الدفع البحرينية (BenefitPay)',
            ],
            'highlights_en' => [
                'NHRA Bahrain compliant',
                'BHD-native billing',
                '24/7 Arabic support',
                'BenefitPay and local payment gateway integrations',
            ],
        ],
        'om' => [
            'country_code' => 'OM',
            'flag' => '🇴🇲',
            'name_ar' => 'سلطنة عُمان',
            'name_en' => 'Oman',
            'hero_title_ar' => 'نظام إدارة العيادات الأول في سلطنة عُمان',
            'hero_title_en' => 'The leading clinic management system in Oman',
            'hero_subtitle_ar' => 'متوافق مع متطلبات وزارة الصحة العُمانية، فوترة بالريال العُماني، ودعم عربي محلي.',
            'hero_subtitle_en' => 'Aligned with Oman Ministry of Health requirements. OMR-native billing and local Arabic support.',
            'phone' => '+968 9000 0000',
            'whatsapp' => '96890000000',
            'insurers' => ['Dhofar Insurance', 'AXA Gulf', 'MetLife', 'Oman United Insurance', 'Bupa Arabia'],
            'highlights_ar' => [
                'متوافق مع متطلبات وزارة الصحة العُمانية',
                'فوترة بالريال العُماني',
                'دعم عربي على مدار الساعة',
                'تكامل مع بوابات الدفع العُمانية',
            ],
            'highlights_en' => [
                'Compliant with Oman MoH requirements',
                'OMR-native billing',
                '24/7 Arabic support',
                'Integrates with Omani payment gateways',
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
