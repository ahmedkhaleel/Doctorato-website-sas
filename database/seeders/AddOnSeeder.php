<?php

namespace Database\Seeders;

use App\Models\AddOn;
use Illuminate\Database\Seeder;

/**
 * Doctorato add-ons — May 2026 v2 (post-review cleanup).
 *
 * Changes from v1:
 *   - REMOVED "Additional doctor seat" — duplicates the per-extra-doctor
 *     pricing already inside Growth/Professional plans. Selling it again
 *     as an add-on would confuse customers and look like double-billing.
 *   - REMOVED "Custom-branded mobile app" — a real custom app is a
 *     one-time build (50-200k EGP) + small maintenance, not a recurring
 *     monthly SaaS line. The 1,490/mo framing was unrealistic and hurt
 *     credibility. We'll surface this as a separate service offer via
 *     contact-sales when the productized version actually exists.
 *   - REPRICED "Additional branch" 990 → 1,490 EGP/mo. A real branch
 *     means more storage, users, data, and ops support. 990 underpriced
 *     the value and the cost.
 *   - REPRICED "Full insurance integration" 790 → 990 EGP/mo. Bupa/GIG/
 *     AXA integration directly drives clinic revenue (claims success
 *     rate); 790 was underselling.
 *   - REPLACED the single 500-msg SMS pack with three volume tiers
 *     (500 / 2,000 / 5,000) — the typical EG clinic needs 2-3k/mo, and
 *     volume tiers let buyers feel the discount.
 *
 * Add-ons bundled FREE on Professional + Enterprise via included_in_plans
 * so those tiers feel genuinely all-inclusive (Cliniko / SimplePractice pattern).
 *
 * is_launch_active is forced to false everywhere — launch pricing
 * decommissioned. activePrice() falls back to price_egp.
 */
class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // 1 — Branch expansion (the upsell from single-clinic plans)
            [
                'name_ar' => 'فرع إضافي',
                'name_en' => 'Additional branch',
                'description_ar' => 'فرع جديد لعيادتك — مستخدمون منفصلون، تخزين خاص، وتكامل كامل مع الباقة الحالية',
                'description_en' => 'New branch — separate users, dedicated storage, fully integrated with your plan',
                'price_egp' => 1490, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'branch',
                'badge_ar' => null, 'badge_en' => null,
                'is_featured' => true, 'display_order' => 1, 'is_active' => true,
                'included_in_plans' => null,
            ],

            // 2 — WhatsApp Business API (high-value, free on Pro+)
            [
                'name_ar' => 'WhatsApp Business API',
                'name_en' => 'WhatsApp Business API',
                'description_ar' => 'تكامل رسمي مع واتساب — تذكيرات، تأكيدات، حملات، ووصفات مباشرة',
                'description_en' => 'Official WhatsApp integration — reminders, confirmations, campaigns, direct prescriptions',
                'price_egp' => 590, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'whatsapp',
                'badge_ar' => 'مجاناً مع Pro+', 'badge_en' => 'Free with Pro+',
                'is_featured' => true, 'display_order' => 2, 'is_active' => true,
                'included_in_plans' => ['professional', 'enterprise'],
            ],

            // 3 — Telemedicine
            [
                'name_ar' => 'وحدة الاستشارات أونلاين (Telemedicine)',
                'name_en' => 'Telemedicine module',
                'description_ar' => 'مكالمات فيديو HD، وصفات إلكترونية، دفع قبل الجلسة، تكامل EMR',
                'description_en' => 'HD video, e-prescriptions, pre-session payment, EMR integration',
                'price_egp' => 490, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'video',
                'badge_ar' => 'مجاناً مع Pro+', 'badge_en' => 'Free with Pro+',
                'is_featured' => true, 'display_order' => 3, 'is_active' => true,
                'included_in_plans' => ['professional', 'enterprise'],
            ],

            // 4 — Lab integration
            [
                'name_ar' => 'تكامل المختبرات',
                'name_en' => 'Lab integration',
                'description_ar' => 'تكامل مع معامل التحاليل والأشعة — نتائج تلقائية في ملف المريض',
                'description_en' => 'Integration with labs and radiology — results land directly in patient records',
                'price_egp' => 390, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'flask',
                'badge_ar' => 'مجاناً مع Pro+', 'badge_en' => 'Free with Pro+',
                'is_featured' => false, 'display_order' => 4, 'is_active' => true,
                'included_in_plans' => ['professional', 'enterprise'],
            ],

            // 5 — Full insurance (Enterprise free)
            [
                'name_ar' => 'تكامل التأمين الكامل',
                'name_en' => 'Full insurance integration',
                'description_ar' => 'تكامل مع Bupa, GIG, ELAJI, AXA, MetLife — مطالبات وتتبع موافقات وتقارير مالية',
                'description_en' => 'Integrations with Bupa, GIG, ELAJI, AXA, MetLife — claims, approval tracking, financial reports',
                'price_egp' => 990, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'shield',
                'badge_ar' => 'مجاناً مع Enterprise', 'badge_en' => 'Free with Enterprise',
                'is_featured' => true, 'display_order' => 5, 'is_active' => true,
                'included_in_plans' => ['enterprise'],
            ],

            // 6 — SMS Starter (500 messages)
            [
                'name_ar' => 'باقة SMS Starter — 500 رسالة',
                'name_en' => 'SMS Starter — 500 messages',
                'description_ar' => '500 رسالة SMS شهرياً للتذكيرات والتأكيدات — للعيادات الصغيرة',
                'description_en' => '500 SMS messages/month for reminders and confirmations — small clinics',
                'price_egp' => 250, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'sms',
                'badge_ar' => null, 'badge_en' => null,
                'is_featured' => false, 'display_order' => 6, 'is_active' => true,
                'included_in_plans' => null,
            ],

            // 7 — SMS Growth (2,000 messages — most popular)
            [
                'name_ar' => 'باقة SMS Growth — 2,000 رسالة',
                'name_en' => 'SMS Growth — 2,000 messages',
                'description_ar' => '2,000 رسالة SMS شهرياً — توفير 30% مقارنة بسعر الرسالة في باقة Starter',
                'description_en' => '2,000 SMS messages/month — 30% lower per-message cost vs Starter pack',
                'price_egp' => 690, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'sms',
                'badge_ar' => 'الأكثر طلباً', 'badge_en' => 'Most popular',
                'is_featured' => false, 'display_order' => 7, 'is_active' => true,
                'included_in_plans' => null,
            ],

            // 8 — SMS Pro (5,000 messages)
            [
                'name_ar' => 'باقة SMS Pro — 5,000 رسالة',
                'name_en' => 'SMS Pro — 5,000 messages',
                'description_ar' => '5,000 رسالة SMS شهرياً — للعيادات الكبيرة والحملات التسويقية المستمرة (وفّر 40%)',
                'description_en' => '5,000 SMS messages/month — large clinics and ongoing marketing campaigns (40% savings)',
                'price_egp' => 1490, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'sms',
                'badge_ar' => null, 'badge_en' => null,
                'is_featured' => false, 'display_order' => 8, 'is_active' => true,
                'included_in_plans' => null,
            ],

            // 9 — Extra storage
            [
                'name_ar' => 'تخزين إضافي — 50 GB',
                'name_en' => 'Extra storage — 50 GB',
                'description_ar' => '50 GB تخزين إضافي للصور والأشعة والتقارير والملفات الطبية',
                'description_en' => '50 GB additional storage for images, X-rays, reports, and medical files',
                'price_egp' => 150, 'price_egp_launch' => null, 'is_launch_active' => false,
                'period' => 'monthly', 'icon' => 'box',
                'badge_ar' => null, 'badge_en' => null,
                'is_featured' => false, 'display_order' => 9, 'is_active' => true,
                'included_in_plans' => null,
            ],
        ];

        $newNames = [];
        foreach ($items as $item) {
            $newNames[] = $item['name_en'];
            AddOn::updateOrCreate(
                ['name_en' => $item['name_en']],
                $item
            );
        }

        // Deactivate any legacy add-ons that are no longer in the catalog
        // (e.g. "Additional doctor seat", "Custom-branded mobile app",
        // and the old "SMS bundle — 3,000 messages" / "SMS pack — 500 messages").
        AddOn::query()
            ->whereNotIn('name_en', $newNames)
            ->update(['is_active' => false]);
    }
}
