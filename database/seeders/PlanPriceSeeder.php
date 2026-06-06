<?php

namespace Database\Seeders;

use App\Models\PlanPrice;
use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

/**
 * Per-country prices — May 2026 RESET.
 *
 * IMPORTANT: EGP prices MUST equal the PricingPlanSeeder exactly,
 * because PricingPlan::priceFor('EG') reads from this table first and
 * a mismatch makes the public site display the wrong number (which is
 * exactly the bug we just fixed). When you change a plan's EGP price,
 * change it in BOTH seeders.
 *
 * Other-country prices are informed by purchasing power + competitive
 * landscape, not a straight FX conversion. They're rounded to clean
 * local-market numbers.
 *
 * Enterprise is contact-sales (is_contact_sales=true on the plan) — we
 * don't seed per-country prices for it because the public card hides
 * the number and routes to /contact.
 *
 * Legacy rows from previous pricing strategies (basic, premium, etc.)
 * are auto-deactivated at the end so the cache doesn't surface them.
 */
class PlanPriceSeeder extends Seeder
{
    public function run(): void
    {
        // Per-country tuples: [monthly, yearly, setup_fee, currency, symbol, flag, name_ar, name_en]
        $prices = [
            // ─── STARTER — 1,990 EGP/mo, 1,750 EGP one-time setup ───
            // Yearly subscribers get the setup FREE (handled by
            // PricingPlan.yearly_setup_discount_pct = 100).
            'starter' => [
                'EG' => [1990,    19900,   1750,    'EGP', 'ج.م',  '🇪🇬', 'مصر',         'Egypt'],
                'SA' => [199,     1990,    175,     'SAR', 'ر.س',  '🇸🇦', 'السعودية',    'Saudi Arabia'],
                'AE' => [199,     1990,    175,     'AED', 'د.إ',  '🇦🇪', 'الإمارات',    'UAE'],
                'KW' => [16,      160,     14,      'KWD', 'د.ك',  '🇰🇼', 'الكويت',      'Kuwait'],
                'QA' => [199,     1990,    175,     'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [20,      200,     18,      'BHD', 'د.ب',  '🇧🇭', 'البحرين',     'Bahrain'],
                'OM' => [20,      200,     18,      'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [40,      400,     35,      'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'US' => [49,      490,     43,      'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],

            // ─── GROWTH — 3,990 EGP/mo, 3,500 EGP one-time setup ───
            'growth' => [
                'EG' => [3990,    39900,   3500,    'EGP', 'ج.م',  '🇪🇬', 'مصر',         'Egypt'],
                'SA' => [399,     3990,    350,     'SAR', 'ر.س',  '🇸🇦', 'السعودية',    'Saudi Arabia'],
                'AE' => [399,     3990,    350,     'AED', 'د.إ',  '🇦🇪', 'الإمارات',    'UAE'],
                'KW' => [32,      320,     28,      'KWD', 'د.ك',  '🇰🇼', 'الكويت',      'Kuwait'],
                'QA' => [399,     3990,    350,     'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [40,      400,     35,      'BHD', 'د.ب',  '🇧🇭', 'البحرين',     'Bahrain'],
                'OM' => [40,      400,     35,      'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [80,      800,     70,      'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'US' => [99,      990,     87,      'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],

            // ─── PROFESSIONAL — 6,990 EGP/mo, 5,250 EGP one-time setup ───
            'professional' => [
                'EG' => [6990,    69900,   5250,    'EGP', 'ج.م',  '🇪🇬', 'مصر',         'Egypt'],
                'SA' => [699,     6990,    525,     'SAR', 'ر.س',  '🇸🇦', 'السعودية',    'Saudi Arabia'],
                'AE' => [699,     6990,    525,     'AED', 'د.إ',  '🇦🇪', 'الإمارات',    'UAE'],
                'KW' => [56,      560,     42,      'KWD', 'د.ك',  '🇰🇼', 'الكويت',      'Kuwait'],
                'QA' => [699,     6990,    525,     'QAR', 'ر.ق',  '🇶🇦', 'قطر',         'Qatar'],
                'BH' => [70,      700,     53,      'BHD', 'د.ب',  '🇧🇭', 'البحرين',     'Bahrain'],
                'OM' => [70,      700,     53,      'OMR', 'ر.ع',  '🇴🇲', 'عُمان',       'Oman'],
                'JO' => [140,     1400,    105,     'JOD', 'د.أ',  '🇯🇴', 'الأردن',      'Jordan'],
                'US' => [179,     1790,    130,     'USD', '$',    '🇺🇸', 'الولايات المتحدة', 'United States'],
            ],
            // Enterprise: NOT seeded — is_contact_sales=true hides the number.
        ];

        $seededPairs = []; // [planId, country] tuples we just wrote — used for orphan cleanup.

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

                $seededPairs[] = $plan->id . ':' . $code;
            }
        }

        // Deactivate every PlanPrice row we did NOT just touch. This kills
        // stale rows left over from previous strategies (basic, old
        // professional EGP=1918.80, old enterprise EGP=3598.80, etc.)
        // without deleting history.
        PlanPrice::query()->get()->each(function ($row) use ($seededPairs) {
            $key = $row->pricing_plan_id . ':' . $row->country_code;
            if (!in_array($key, $seededPairs, true) && $row->is_active) {
                $row->update(['is_active' => false]);
            }
        });
    }
}
