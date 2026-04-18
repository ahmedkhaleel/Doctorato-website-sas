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
                'EG' => [958.80,  9204,    'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [297,     2847,    'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [297,     2847,    'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [24,      237,     'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [297,     2847,    'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [30,      285,     'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [30,      285,     'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [60,      576,     'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [105000,  1008000, 'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [75,      720,     'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [747,     7170,    'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [87,      837,     'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
            'professional' => [
                'EG' => [1918.80, 18420,   'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [597,     5730,    'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [597,     5730,    'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [48,      465,     'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [597,     5730,    'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [60,      570,     'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [60,      570,     'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [120,     1152,    'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [210000,  2016000, 'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [147,     1410,    'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [1497,    14370,   'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [177,     1695,    'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
            'enterprise' => [
                'EG' => [3598.80, 34548,   'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [1197,    11490,   'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [1197,    11490,   'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [99,      945,     'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [1197,    11490,   'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [120,     1152,    'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [120,     1152,    'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [225,     2160,    'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [420000,  4032000, 'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [297,     2850,    'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [2997,    28770,   'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [327,     3150,    'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
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
