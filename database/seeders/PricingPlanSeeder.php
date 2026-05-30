<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

/**
 * Doctorato launch pricing — May 2026.
 *
 * 4 paid tiers + 1 Custom (contact-us). Targeted at Egyptian
 * middle-to-upper-tier clinics: established practitioners who
 * value quality positioning over the cheapest option.
 *
 * Strategy commits documented per row:
 *   monthly_price            = the regular anchor (strikethrough on site)
 *   monthly_price_launch     = what customers actually pay now
 *   yearly_price_launch      = annual upfront; ~20% saving vs 12 × monthly_launch
 *   setup_fee                = one-time implementation fee (regular)
 *   setup_fee_launch         = setup fee under the launch offer
 *   yearly_setup_discount_pct= additional 50% off setup for annual subscribers
 *   supports_installments    = false on launch (instalments disabled
 *                              by product decision — column kept so
 *                              we can flip it back without a migration
 *                              if we re-enable later)
 *   installment_split        = [40, 30, 30] — kept for the future
 *
 * Each tier steps up on five clean dimensions:
 *   - Specialties (1 → 3 → all → all + early access)
 *   - Patients (750 → 3000 → unlimited → unlimited)
 *   - Doctors (1 → 3 → 7 → unlimited)
 *   - Storage (10 → 40 → 150 → 500 GB)
 *   - Support response (24h → 8h → 4h → 1h 24/7)
 *
 * Launch offer runs through 2026-12-31 — the countdown banner reads
 * launch_offer_ends_at to drive urgency. Set is_launch_offer_active
 * to false in the admin to revert to the regular prices instantly.
 */
class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $launchEndsAt = '2026-12-31 23:59:59';
        $installmentSplit = [40, 30, 30];

        $plans = [
            // ───── 1. STARTER — Solo high-end practitioner ─────
            [
                'name_ar' => 'المبتدئ',
                'name_en' => 'Starter',
                'slug' => 'starter',
                'description_ar' => 'مثالي للطبيب الفردي وعيادة بدء التشغيل',
                'description_en' => 'For solo practitioners and clinics starting digital',
                'monthly_price' => 3590,
                'monthly_price_launch' => 1790,
                'yearly_price' => 35900,
                'yearly_price_launch' => 17900,
                'setup_fee' => 5990,
                'setup_fee_launch' => 2390,
                'yearly_setup_discount_pct' => 50,
                'is_launch_offer_active' => true,
                'launch_offer_ends_at' => $launchEndsAt,
                'supports_installments' => false,
                'installment_count' => 3,
                'installment_split' => $installmentSplit,
                'included_specialties_count' => 'one',
                'included_specialties_pool' => [
                    'general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine',
                ],
                'currency' => 'EGP',
                'is_popular' => false,
                'is_custom' => false,
                'features_ar' => [
                    'تخصص طبي واحد (تختار من 6 تخصصات)',
                    'EMR + قوالب التخصص',
                    'مواعيد وحجز أونلاين 24/7',
                    'فواتير ومدفوعات أساسية',
                    'تذكيرات WhatsApp و SMS',
                    'بوابة المريض الأساسية',
                    'تقارير أساسية',
                ],
                'features_en' => [
                    '1 medical specialty (pick from 6)',
                    'EMR with specialty templates',
                    'Appointments and online booking 24/7',
                    'Basic invoicing and payments',
                    'WhatsApp + SMS reminders',
                    'Basic patient portal',
                    'Basic reports',
                ],
                'modules_included' => [
                    'patients', 'bookings', 'invoices', 'payments',
                    'website', 'notifications', 'prescriptions',
                ],
                'max_users' => 3,
                'max_doctors' => 1,
                'max_staff' => 2,
                'max_patients' => 750,
                'max_branches' => 1,
                'storage_gb' => 5,
                'support_level' => 'email',
                'support_response_hours' => 24,
                'display_order' => 1,
            ],

            // ───── 2. GROWTH — Established 2-3 doctor clinic ─────
            [
                'name_ar' => 'النمو',
                'name_en' => 'Growth',
                'slug' => 'growth',
                'description_ar' => 'للعيادات الراسخة بـ 2-3 أطباء',
                'description_en' => 'For established 2-3 doctor clinics',
                'monthly_price' => 5490,
                'monthly_price_launch' => 2750,
                'yearly_price' => 54900,
                'yearly_price_launch' => 27500,
                'setup_fee' => 9590,
                'setup_fee_launch' => 4790,
                'yearly_setup_discount_pct' => 50,
                'is_launch_offer_active' => true,
                'launch_offer_ends_at' => $launchEndsAt,
                'supports_installments' => false,
                'installment_count' => 3,
                'installment_split' => $installmentSplit,
                'included_specialties_count' => 'one',
                'included_specialties_pool' => [
                    'general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine',
                ],
                'currency' => 'EGP',
                'is_popular' => false,
                'is_custom' => false,
                'features_ar' => [
                    'تخصص طبي واحد (تختار من 6 تخصصات)',
                    'كل ما في Starter +',
                    'WhatsApp Business API مدمج',
                    'CRM طبي وحملات تسويقية',
                    'Insurance integration أساسي (Bupa, GIG)',
                    'مخزون وتنبيهات نفاذ',
                    'محفظة المريض ونقاط ولاء',
                    'تقييم رضا المريض (NPS)',
                    'تقارير وتحليلات متقدمة',
                ],
                'features_en' => [
                    '1 medical specialty (pick from 6)',
                    'Everything in Starter +',
                    'WhatsApp Business API integrated',
                    'Medical CRM and marketing campaigns',
                    'Basic insurance integration (Bupa, GIG)',
                    'Inventory with stock alerts',
                    'Patient wallet and loyalty points',
                    'Patient satisfaction (NPS)',
                    'Advanced reports and analytics',
                ],
                'modules_included' => [
                    'patients', 'bookings', 'invoices', 'payments', 'website',
                    'crm', 'wallet', 'discounts', 'chat', 'notifications',
                    'prescriptions', 'satisfaction', 'expenses',
                ],
                'max_users' => 9,
                'max_doctors' => 3,
                'max_staff' => 6,
                'max_patients' => 3000,
                'max_branches' => 1,
                'storage_gb' => 10,
                'support_level' => 'chat',
                'support_response_hours' => 8,
                'display_order' => 2,
            ],

            // ───── 3. PROFESSIONAL — Most popular ⭐ ─────
            [
                'name_ar' => 'الاحترافي',
                'name_en' => 'Professional',
                'slug' => 'professional',
                'description_ar' => 'للمراكز الطبية متعددة التخصصات (الأكثر طلباً)',
                'description_en' => 'For multi-specialty medical centres (most popular)',
                'monthly_price' => 8390,
                'monthly_price_launch' => 4190,
                'yearly_price' => 83900,
                'yearly_price_launch' => 41900,
                'setup_fee' => 14390,
                'setup_fee_launch' => 7190,
                'yearly_setup_discount_pct' => 50,
                'is_launch_offer_active' => true,
                'launch_offer_ends_at' => $launchEndsAt,
                'supports_installments' => false,
                'installment_count' => 3,
                'installment_split' => $installmentSplit,
                'included_specialties_count' => 'three',
                'included_specialties_pool' => [
                    'general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine',
                ],
                'currency' => 'EGP',
                'is_popular' => true,
                'is_custom' => false,
                'features_ar' => [
                    '3 تخصصات طبية (تختار من 6)',
                    'كل ما في Growth +',
                    'HR والرواتب كامل',
                    'Insurance integration كامل (Bupa, GIG, ELAJI, AXA, MetLife)',
                    'المحاسبة المالية الكاملة وتقارير ضريبية',
                    'PACS أساسي وتصوير 3D/4D',
                    'Premium Analytics مع AI insights',
                    '6 بوابات كاملة',
                    'Account Manager مخصص',
                    '2 جلسة تدريب حضورية + 4 أونلاين',
                ],
                'features_en' => [
                    '3 medical specialties (pick from 6)',
                    'Everything in Growth +',
                    'Full HR and payroll',
                    'Full insurance integration (Bupa, GIG, ELAJI, AXA, MetLife)',
                    'Full financial accounting and tax reports',
                    'Basic PACS with 3D/4D imaging',
                    'Premium analytics with AI insights',
                    'All 6 portals',
                    'Dedicated account manager',
                    '2 in-person + 4 online training sessions',
                ],
                'modules_included' => [
                    'patients', 'bookings', 'invoices', 'payments', 'website',
                    'crm', 'wallet', 'discounts', 'chat', 'notifications',
                    'prescriptions', 'satisfaction', 'expenses',
                    'dental', 'dermatology', 'pediatrics', 'obstetrics', 'telemedicine',
                    'hr', 'insurance', 'inventory', 'analytics',
                ],
                'max_users' => 22,
                'max_doctors' => 7,
                'max_staff' => 15,
                'max_patients' => null,    // unlimited
                'max_branches' => 1,
                'storage_gb' => 20,
                'support_level' => 'phone',
                'support_response_hours' => 4,
                'display_order' => 3,
            ],

            // ───── 4. ENTERPRISE — Networks / hospitals ─────
            [
                'name_ar' => 'المؤسّسي',
                'name_en' => 'Enterprise',
                'slug' => 'enterprise',
                'description_ar' => 'للشبكات والمستشفيات والمجموعات الطبية',
                'description_en' => 'For networks, hospitals, and medical groups',
                'monthly_price' => 16790,
                'monthly_price_launch' => 8390,
                'yearly_price' => 167900,
                'yearly_price_launch' => 83900,
                'setup_fee' => 23990,
                'setup_fee_launch' => 11990,
                'yearly_setup_discount_pct' => 50,
                'is_launch_offer_active' => true,
                'launch_offer_ends_at' => $launchEndsAt,
                'supports_installments' => false,
                'installment_count' => 3,
                'installment_split' => $installmentSplit,
                'included_specialties_count' => 'all',
                'included_specialties_pool' => [
                    'general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine',
                ],
                'currency' => 'EGP',
                'is_popular' => false,
                'is_custom' => false,
                'features_ar' => [
                    'كل التخصصات الطبية (6 تخصصات)',
                    'كل ما في Professional +',
                    'مرضى بلا حدود + دعم تشغيل كامل',
                    'PACS متقدم مع AI imaging',
                    'API + Webhooks + Custom Integrations',
                    'White-label (تطبيق بشعارك)',
                    'Multi-currency invoicing',
                    'RBAC متقدم + Audit Log 3 سنوات',
                    'SLA 99.9% مكتوب وموقّع',
                    'Backup ساعي',
                    'Dedicated Success Manager',
                    'Priority support 1 ساعة (24/7)',
                    '5 أيام Onboarding حضوري',
                    'جلسات تدريب شهرية',
                ],
                'features_en' => [
                    'All medical specialties (6)',
                    'Everything in Professional +',
                    'Unlimited patients + full implementation support',
                    'Advanced PACS with AI imaging',
                    'API + Webhooks + custom integrations',
                    'White-label (app with your branding)',
                    'Multi-currency invoicing',
                    'Advanced RBAC + 3-year audit log',
                    'Signed 99.9% SLA',
                    'Hourly backup',
                    'Dedicated success manager',
                    'Priority 1-hour 24/7 support',
                    '5-day in-person onboarding',
                    'Monthly training sessions',
                ],
                'modules_included' => [
                    'patients', 'bookings', 'invoices', 'payments', 'website',
                    'crm', 'wallet', 'discounts', 'chat', 'notifications',
                    'prescriptions', 'satisfaction', 'expenses',
                    'dental', 'dermatology', 'pediatrics', 'obstetrics', 'telemedicine',
                    'hr', 'insurance', 'inventory', 'analytics',
                    'webmaster', 'rbac', 'audit', 'api', 'whitelabel',
                ],
                'max_users' => null,
                'max_doctors' => null,
                'max_staff' => null,
                'max_patients' => null,
                'max_branches' => 1,
                'storage_gb' => 30,
                'support_level' => 'priority',
                'support_response_hours' => 1,
                'display_order' => 4,
            ],

            // ───── 5. CUSTOM — Contact-us (no public price) ─────
            [
                'name_ar' => 'مخصص',
                'name_en' => 'Custom',
                'slug' => 'custom',
                'description_ar' => 'للشبكات الكبرى والمستشفيات بأكثر من 50 فرع',
                'description_en' => 'For large networks and hospitals with 50+ branches',
                'monthly_price' => 0,
                'monthly_price_launch' => 0,
                'yearly_price' => 0,
                'yearly_price_launch' => 0,
                'setup_fee' => 0,
                'setup_fee_launch' => 0,
                'yearly_setup_discount_pct' => 0,
                'is_launch_offer_active' => false,
                'launch_offer_ends_at' => null,
                'supports_installments' => false,
                'installment_count' => 0,
                'installment_split' => null,
                'included_specialties_count' => 'all_plus_early',
                'included_specialties_pool' => null,
                'currency' => 'EGP',
                'is_popular' => false,
                'is_custom' => true,
                'features_ar' => [
                    'كل ما في Enterprise +',
                    'On-premise option',
                    'Dedicated infrastructure',
                    'DPA / BAA على الطلب',
                    'SLA مخصصة',
                    'Custom development',
                    'استشارات تقنية مجانية',
                ],
                'features_en' => [
                    'Everything in Enterprise +',
                    'On-premise option',
                    'Dedicated infrastructure',
                    'DPA / BAA on request',
                    'Custom SLA',
                    'Custom development',
                    'Free technical consultations',
                ],
                'modules_included' => ['all'],
                'max_users' => null,
                'max_doctors' => null,
                'max_staff' => null,
                'max_patients' => null,
                'max_branches' => null,
                'storage_gb' => null,
                'support_level' => 'dedicated',
                'support_response_hours' => null,
                'display_order' => 5,
            ],
        ];

        // Upsert the new lineup
        $newSlugs = collect($plans)->pluck('slug')->all();
        foreach ($plans as $plan) {
            PricingPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        // Clean-up: deactivate any plan whose slug is NOT in the new
        // catalogue. Common case after this seeder runs over a v1
        // database: the legacy 'basic' row stays orphaned because
        // updateOrCreate keys on slug and doesn't touch rows that
        // weren't in the new list. Soft-deactivating (rather than
        // hard-deleting) preserves the foreign keys from subscriptions
        // and invoices that may still reference the old plan id.
        //
        // If a customer is still on a deactivated plan, their
        // subscription keeps working — the cache layer just stops
        // surfacing the plan on the public pricing page (it filters
        // on is_active = true).
        PricingPlan::query()
            ->whereNotIn('slug', $newSlugs)
            ->update(['is_active' => false]);
    }
}
