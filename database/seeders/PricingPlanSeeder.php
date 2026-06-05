<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

/**
 * Doctorato pricing — May 2026 RESET (competitive market positioning).
 *
 * Strategy commit (based on competitor research — Cliniko, SimplePractice,
 * Jane, ClinicGateway, Clinicea):
 *
 *   1. No more dual anchor/launch prices. One real price per plan.
 *   2. Annual = "2 months free" (~17% saving) — the SaaS standard.
 *      Replaces the 50% blanket discount that looked like a souk markdown.
 *   3. Setup fee dropped to zero on Starter + Growth. Optional 7,500 EGP
 *      white-glove migration on Professional only. Matches the dominant
 *      Egyptian local-vendor norm ("no setup fee").
 *   4. Per-doctor pricing on Growth + Professional — 1 doctor included,
 *      each extra doctor adds a transparent per-seat amount. This is the
 *      Cliniko / Jane pattern and lets solo doctors enter cheap while the
 *      math for multi-doctor clinics is fair and visible.
 *   5. Enterprise is "Contact Sales" — no public price. Universal SaaS
 *      pattern; removes the credibility hit of a 5-figure number on a
 *      public page next to a "50% off" badge.
 *   6. 30-day free trial, no credit card. Trust signal every credible
 *      competitor publishes.
 *
 * Each tier steps up on clean dimensions:
 *   - Doctors included (1 → 1+per-seat → 1+per-seat → custom)
 *   - Specialties (1 → 1 → 3 → all)
 *   - Branches (1 → 1 → 1 → custom)
 *   - Storage (5 → 10 → 20 → custom)
 *   - Support (email → priority email → priority + phone → dedicated)
 */
class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // ───── 1. STARTER — Solo doctor, single clinic ─────
            [
                'name_ar' => 'المبتدئ',
                'name_en' => 'Starter',
                'slug' => 'starter',
                'description_ar' => 'للطبيب الفرد الذي يبدأ تحوّله الرقمي',
                'description_en' => 'For solo doctors going digital',

                'monthly_price' => 1990,
                'yearly_price' => 19900,           // 2 months free
                'setup_fee' => 0,                  // FREE setup
                'yearly_setup_discount_pct' => 0,

                'monthly_price_launch' => null,
                'yearly_price_launch' => null,
                'setup_fee_launch' => null,
                'is_launch_offer_active' => false,
                'launch_offer_ends_at' => null,
                'supports_installments' => false,
                'installment_count' => null,
                'installment_split' => null,

                'included_doctors' => 1,
                'per_extra_doctor_price' => null,  // not extensible — upgrade to Growth
                'is_contact_sales' => false,
                'trial_days' => 30,

                'included_specialties_count' => 'one',
                'included_specialties_pool' => ['general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine', 'psychiatry'],

                'currency' => 'EGP',
                'is_popular' => false,
                'is_custom' => false,
                'features_ar' => [
                    'طبيب واحد + موظف استقبال',
                    'تخصص طبي واحد (تختار من 7 تخصصات)',
                    'حتى 500 ملف مريض',
                    'حجوزات وجدول المواعيد',
                    'الملف الطبي الإلكتروني (EMR) الأساسي',
                    'الوصفات وروشتات الأدوية',
                    'فواتير وإيصالات ضريبية مصرية',
                    'بوابة المرضى عبر الجوال',
                    'دعم بريد إلكتروني',
                    'تجربة مجانية 30 يوم بدون بطاقة ائتمان',
                    'تركيب وترحيل بيانات مجاني',
                ],
                'features_en' => [
                    '1 doctor + 1 receptionist',
                    '1 medical specialty (pick from 7)',
                    'Up to 500 patient records',
                    'Appointments & calendar',
                    'Basic EMR / patient charting',
                    'Prescriptions',
                    'Egyptian e-receipt & invoicing',
                    'Patient mobile portal',
                    'Email support',
                    '30-day free trial, no credit card',
                    'Free setup & data migration',
                ],
                'modules_included' => ['scheduling', 'emr_basic', 'billing', 'patient_portal'],
                'max_users' => 2,
                'max_patients' => 500,
                'max_doctors' => 1,
                'max_staff' => 1,
                'max_branches' => 1,
                'storage_gb' => 5,
                'support_level' => 'email',
                'support_response_hours' => 48,
                'display_order' => 1,
                'is_active' => true,
            ],

            // ───── 2. GROWTH — Small clinic (most popular) ─────
            [
                'name_ar' => 'النمو',
                'name_en' => 'Growth',
                'slug' => 'growth',
                'description_ar' => 'العيادة الصغيرة بفريق محدود — مع نمو مرن',
                'description_en' => 'Small clinics with a tight team — scale doctors as you grow',

                'monthly_price' => 3990,
                'yearly_price' => 39900,
                'setup_fee' => 0,                  // FREE setup
                'yearly_setup_discount_pct' => 0,

                'monthly_price_launch' => null,
                'yearly_price_launch' => null,
                'setup_fee_launch' => null,
                'is_launch_offer_active' => false,
                'launch_offer_ends_at' => null,
                'supports_installments' => false,
                'installment_count' => null,
                'installment_split' => null,

                'included_doctors' => 1,
                'per_extra_doctor_price' => 700,   // per extra doctor/month
                'is_contact_sales' => false,
                'trial_days' => 30,

                'included_specialties_count' => 'one',
                'included_specialties_pool' => ['general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine', 'psychiatry'],

                'currency' => 'EGP',
                'is_popular' => true,              // ⭐ Most popular
                'is_custom' => false,
                'features_ar' => [
                    'طبيب مشمول + 700 ج.م لكل طبيب إضافي',
                    'حتى 3 موظفين (استقبال / محاسب / مساعد)',
                    'تخصص طبي واحد (تختار من 7 تخصصات)',
                    'حتى 2,000 ملف مريض',
                    'EMR كامل + قوالب التخصص',
                    'تقارير مالية ولوحات أداء',
                    'تذكير المواعيد بـ SMS (Add-on)',
                    'تكامل WhatsApp Business (Add-on)',
                    'نظام الصلاحيات بالأدوار',
                    'دعم بريد إلكتروني ذو أولوية',
                    'تجربة مجانية 30 يوم بدون بطاقة ائتمان',
                    'تركيب وترحيل بيانات مجاني',
                ],
                'features_en' => [
                    '1 doctor included + 700 EGP per extra doctor',
                    'Up to 3 staff (receptionist / accountant / assistant)',
                    '1 medical specialty (pick from 7)',
                    'Up to 2,000 patient records',
                    'Full EMR + specialty templates',
                    'Financial reports & dashboards',
                    'SMS appointment reminders (add-on)',
                    'WhatsApp Business integration (add-on)',
                    'Role-based access control',
                    'Priority email support',
                    '30-day free trial, no credit card',
                    'Free setup & data migration',
                ],
                'modules_included' => ['scheduling', 'emr_full', 'billing', 'patient_portal', 'reports', 'roles'],
                'max_users' => 4,
                'max_patients' => 2000,
                'max_doctors' => 5,
                'max_staff' => 3,
                'max_branches' => 1,
                'storage_gb' => 10,
                'support_level' => 'priority_email',
                'support_response_hours' => 24,
                'display_order' => 2,
                'is_active' => true,
            ],

            // ───── 3. PROFESSIONAL — Multi-specialty clinic ─────
            [
                'name_ar' => 'الاحترافي',
                'name_en' => 'Professional',
                'slug' => 'professional',
                'description_ar' => 'العيادة متعددة التخصصات بفريق كامل',
                'description_en' => 'Multi-specialty clinic with full team',

                'monthly_price' => 6990,
                'yearly_price' => 69900,
                'setup_fee' => 7500,               // Optional white-glove
                'yearly_setup_discount_pct' => 0,  // Setup price flat — no discount needed

                'monthly_price_launch' => null,
                'yearly_price_launch' => null,
                'setup_fee_launch' => null,
                'is_launch_offer_active' => false,
                'launch_offer_ends_at' => null,
                'supports_installments' => false,
                'installment_count' => null,
                'installment_split' => null,

                'included_doctors' => 1,
                'per_extra_doctor_price' => 900,   // per extra doctor/month
                'is_contact_sales' => false,
                'trial_days' => 30,

                'included_specialties_count' => 'three',
                'included_specialties_pool' => ['general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine', 'psychiatry'],

                'currency' => 'EGP',
                'is_popular' => false,
                'is_custom' => false,
                'features_ar' => [
                    'طبيب مشمول + 900 ج.م لكل طبيب إضافي',
                    'موظفون بلا حدود (استقبال / محاسبة / تمريض)',
                    '3 تخصصات طبية (تختار من 6)',
                    'حتى 10,000 ملف مريض',
                    'كل مزايا الـ EMR + قوالب متعددة التخصصات',
                    'تكاملات WhatsApp / Telemedicine / Lab مجانية',
                    'إدارة المخزون والأدوية',
                    'وحدة الموارد البشرية والرواتب',
                    'تكامل التأمين الطبي',
                    'تقارير تحليلية متقدمة + Custom Reports',
                    'API للتكاملات الخارجية',
                    'دعم أولوية: بريد + هاتف',
                    'تجربة مجانية 30 يوم بدون بطاقة ائتمان',
                    'White-glove setup اختياري (7,500 ج.م — مرة واحدة)',
                ],
                'features_en' => [
                    '1 doctor included + 900 EGP per extra doctor',
                    'Unlimited staff (reception / accounting / nursing)',
                    '3 medical specialties (pick from 7)',
                    'Up to 10,000 patient records',
                    'All EMR features + multi-specialty templates',
                    'WhatsApp / Telemedicine / Lab integrations FREE',
                    'Inventory & pharmacy management',
                    'HR & payroll module',
                    'Insurance module',
                    'Advanced analytics + custom reports',
                    'API access for external integrations',
                    'Priority support: email + phone',
                    '30-day free trial, no credit card',
                    'Optional white-glove setup (7,500 EGP one-time)',
                ],
                'modules_included' => [
                    'scheduling', 'emr_full', 'billing', 'patient_portal',
                    'reports_advanced', 'roles', 'inventory', 'hr_payroll',
                    'insurance', 'api', 'whatsapp', 'telemedicine', 'lab',
                ],
                'max_users' => null,               // unlimited staff
                'max_patients' => 10000,
                'max_doctors' => 15,
                'max_staff' => null,
                'max_branches' => 1,
                'storage_gb' => 20,
                'support_level' => 'priority_phone',
                'support_response_hours' => 8,
                'display_order' => 3,
                'is_active' => true,
            ],

            // ───── 4. ENTERPRISE — Contact Sales (no public price) ─────
            [
                'name_ar' => 'المؤسسات',
                'name_en' => 'Enterprise',
                'slug' => 'enterprise',
                'description_ar' => 'شبكات العيادات والمستشفيات — مفصّل لك بالكامل',
                'description_en' => 'Clinic networks & hospitals — fully tailored',

                'monthly_price' => 0,              // hidden — use is_contact_sales
                'yearly_price' => 0,
                'setup_fee' => 0,
                'yearly_setup_discount_pct' => 0,

                'monthly_price_launch' => null,
                'yearly_price_launch' => null,
                'setup_fee_launch' => null,
                'is_launch_offer_active' => false,
                'launch_offer_ends_at' => null,
                'supports_installments' => false,
                'installment_count' => null,
                'installment_split' => null,

                'included_doctors' => 0,
                'per_extra_doctor_price' => null,
                'is_contact_sales' => true,        // ← key flag
                'trial_days' => 30,

                'included_specialties_count' => 'all',
                'included_specialties_pool' => ['general', 'dental', 'pediatrics', 'obstetrics', 'dermatology', 'telemedicine', 'psychiatry'],

                'currency' => 'EGP',
                'is_popular' => false,
                'is_custom' => false,
                'features_ar' => [
                    'كل مزايا الاحترافي + ',
                    'فروع وأطباء بلا حدود',
                    'كل التخصصات الطبية الستة',
                    'تخزين ودعم تشغيل بلا حدود',
                    'SLA مخصص + مدير حساب مخصص',
                    'تكامل HL7 / FHIR للمستشفيات',
                    'White-label وعلامة تجارية خاصة',
                    'نشر داخل البنية التحتية الخاصة بك (On-prem) — اختياري',
                    'تدريب موسّع للفرق الطبية والإدارية',
                    'دعم 24/7 وهاتف مباشر',
                ],
                'features_en' => [
                    'Everything in Professional, plus:',
                    'Unlimited branches & doctors',
                    'All 7 medical specialties',
                    'Unlimited storage & implementation support',
                    'Custom SLA + dedicated account manager',
                    'HL7 / FHIR integration for hospitals',
                    'White-label and custom branding',
                    'On-premise deployment (optional)',
                    'Extended training for clinical & admin teams',
                    '24/7 support with direct phone line',
                ],
                'modules_included' => ['*'],
                'max_users' => null,
                'max_patients' => null,
                'max_doctors' => null,
                'max_staff' => null,
                'max_branches' => null,
                'storage_gb' => null,
                'support_level' => 'dedicated_24_7',
                'support_response_hours' => 1,
                'display_order' => 4,
                'is_active' => true,
            ],
        ];

        $newSlugs = [];
        foreach ($plans as $plan) {
            $slug = $plan['slug'];
            $newSlugs[] = $slug;
            PricingPlan::updateOrCreate(['slug' => $slug], $plan);
        }

        // Deactivate any legacy plans (old basic/premium/custom slugs from previous strategies)
        PricingPlan::query()
            ->whereNotIn('slug', $newSlugs)
            ->update(['is_active' => false]);
    }
}
