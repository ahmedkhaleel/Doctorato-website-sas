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
     * Each tuple: [monthly, yearly, setup_fee, currency, symbol, flag, name_ar, name_en]
     * setup_fee is a one-time onboarding charge; yearly subscribers get
     * 50% off it (enforced in PricingPlan::priceFor()).
     */
    public function run(): void
    {
        $prices = [
            'basic' => [
                'EG' => [958.80,  9204,    1500,    'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [297,     2847,    500,     'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [297,     2847,    500,     'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [24,      237,     40,      'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [297,     2847,    500,     'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [30,      285,     50,      'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [30,      285,     50,      'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [60,      576,     100,     'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [105000,  1008000, 175000,  'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [75,      720,     125,     'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [747,     7170,    1250,    'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [87,      837,     150,     'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
            'professional' => [
                'EG' => [1918.80, 18420,   3500,    'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [597,     5730,    1000,    'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [597,     5730,    1000,    'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [48,      465,     90,      'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [597,     5730,    1000,    'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [60,      570,     100,     'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [60,      570,     100,     'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [120,     1152,    200,     'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [210000,  2016000, 350000,  'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [147,     1410,    250,     'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [1497,    14370,   2500,    'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [177,     1695,    300,     'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
            'enterprise' => [
                'EG' => [3598.80, 34548,   7500,    'EGP', 'ج.م',  '🇪🇬', 'مصر',        'Egypt'],
                'SA' => [1197,    11490,   2000,    'SAR', 'ر.س',  '🇸🇦', 'السعودية',   'Saudi Arabia'],
                'AE' => [1197,    11490,   2000,    'AED', 'د.إ',  '🇦🇪', 'الإمارات',   'UAE'],
                'KW' => [99,      945,     180,     'KWD', 'د.ك',  '🇰🇼', 'الكويت',     'Kuwait'],
                'QA' => [1197,    11490,   2000,    'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [120,     1152,    200,     'BHD', 'د.ب',  '🇧🇭', 'البحرين',    'Bahrain'],
                'OM' => [120,     1152,    200,     'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [225,     2160,    400,     'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'IQ' => [420000,  4032000, 700000,  'IQD', 'د.ع',  '🇮🇶', 'العراق',      'Iraq'],
                'LB' => [297,     2850,    500,     'USD', '$',    '🇱🇧', 'لبنان',       'Lebanon'],
                'MA' => [2997,    28770,   5000,    'MAD', 'د.م',  '🇲🇦', 'المغرب',      'Morocco'],
                'US' => [327,     3150,    550,     'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
        ];

        foreach ($prices as $slug => $countries) {
            $plan = PricingPlan::where('slug', $slug)->first();
            if (!$plan) continue;

            foreach ($countries as $code => $row) {
                [$monthly, $yearly, $setupFee, $currency, $symbol, $flag, $nameAr, $nameEn] = $row;

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
                        'setup_fee' => $setupFee,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
