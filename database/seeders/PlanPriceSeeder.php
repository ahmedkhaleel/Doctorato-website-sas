<?php

namespace Database\Seeders;

use App\Models\PlanPrice;
use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PlanPriceSeeder extends Seeder
{
    /**
     * Seed per-country prices for the three commercial plans.
     * Pricing by market is informed by purchasing power + competitive
     * landscape, not by a straight FX conversion from EGP. Values are
     * rounded to clean local-market numbers.
     *
     * Prices kept in sync with the PricingPlan seeder: Basic / Professional /
     * Enterprise. The custom plan has no monthly price.
     */
    public function run(): void
    {
        // [plan_slug => [country_code => [monthly, yearly, currency, symbol, flag, name_ar, name_en]]]
        $prices = [
            'basic' => [
                'EG' => [958.80,  9204,  'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [99,    949,   'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [99,    949,   'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [8,     79,    'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [99,    949,   'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [10,    95,    'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [10,    95,    'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [20,    192,   'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [35000, 336000,'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [25,    240,   'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [249,   2390,  'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [29,    279,   'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
            'professional' => [
                'EG' => [1918.80, 18420, 'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [199,   1910,  'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [199,   1910,  'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [16,    155,   'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [199,   1910,  'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [20,    190,   'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [20,    190,   'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [40,    384,   'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [70000, 672000,'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [49,    470,   'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [499,   4790,  'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [59,    565,   'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
            'enterprise' => [
                'EG' => [3598.80, 34548, 'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [399,   3830,  'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [399,   3830,  'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [33,    315,   'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [399,   3830,  'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [40,    384,   'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [40,    384,   'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [75,    720,   'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [140000,1344000,'IQD','د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [99,    950,   'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [999,   9590,  'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [109,   1050,  'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
        ];

        foreach ($prices as $slug => $countries) {
            $plan = PricingPlan::where('slug', $slug)->first();
            if (!$plan) continue;

            foreach ($countries as $code => $row) {
                [$monthly, $yearly, $currency, $symbol, $flag, $nameAr, $nameEn] = $row;

                PlanPrice::updateOrCreate(
                    ['pricing_plan_id' => $plan->id, 'country_code' => $code],
                    [
                        'country_name_ar' => $nameAr,
                        'country_name_en' => $nameEn,
                        'country_flag' => $flag,
                        'currency_code' => $currency,
                        'currency_symbol' => $symbol,
                        'monthly_price' => $monthly,
                        'yearly_price' => $yearly,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
