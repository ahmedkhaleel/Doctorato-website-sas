<?php

namespace Database\Seeders;

use App\Models\AddOn;
use Illuminate\Database\Seeder;

/**
 * Doctorato launch add-ons — May 2026.
 *
 * Each row carries:
 *   price_egp           — anchor price shown crossed out
 *   price_egp_launch    — what the customer pays during the offer
 *   is_launch_active    — toggle. When false the launch price is
 *                         ignored and price_egp shows un-crossed.
 *   included_in_plans   — which plans bundle this add-on FREE,
 *                         so the public page can label it
 *                         'مجاناً مع Pro' instead of selling it twice.
 *
 * Strategy: list the things competitors charge extra for, but make
 * the top-tier ones (WhatsApp, telemedicine, e-prescription, advanced
 * analytics) FREE in Professional + Enterprise so those plans feel
 * genuinely all-inclusive.
 */
class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name_ar' => 'طبيب إضافي',
                'name_en' => 'Additional doctor seat',
                'description_ar' => 'أضف طبيب فوق حد الباقة الأساسي — بدون رسوم إعداد إضافية',
                'description_en' => 'Add a doctor above your plan limit — no extra setup fee',
                'price_egp' => 470, 'price_egp_launch' => 230, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'doctor',
                'badge_ar' => 'الأكثر طلباً', 'badge_en' => 'Most popular',
                'is_featured' => true, 'display_order' => 1, 'is_active' => true,
                'included_in_plans' => null,
            ],
            [
                'name_ar' => 'فرع إضافي',
                'name_en' => 'Additional branch',
                'description_ar' => 'فرع جديد لعيادتك — تكامل كامل مع الباقة الحالية',
                'description_en' => 'New branch — fully integrated with your existing plan',
                'price_egp' => 590, 'price_egp_launch' => 350, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'branch',
                'is_featured' => false, 'display_order' => 2, 'is_active' => true,
                'included_in_plans' => null,
            ],
            [
                'name_ar' => 'WhatsApp Business API',
                'name_en' => 'WhatsApp Business API',
                'description_ar' => 'تكامل رسمي مع واتساب — تذكيرات، تأكيدات، حملات، ووصفات مباشرة',
                'description_en' => 'Official WhatsApp integration — reminders, confirmations, campaigns, direct prescriptions',
                'price_egp' => 710, 'price_egp_launch' => 350, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'whatsapp',
                'badge_ar' => 'مجاناً مع Pro+', 'badge_en' => 'Free with Pro+',
                'is_featured' => true, 'display_order' => 3, 'is_active' => true,
                'included_in_plans' => ['professional', 'enterprise'],
            ],
            [
                'name_ar' => 'باقة SMS — 3,000 رسالة',
                'name_en' => 'SMS bundle — 3,000 messages',
                'description_ar' => '3,000 رسالة SMS شهرياً للتذكيرات والتأكيدات والحملات',
                'description_en' => '3,000 SMS messages per month for reminders, confirmations, campaigns',
                'price_egp' => 470, 'price_egp_launch' => 230, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'sms',
                'is_featured' => false, 'display_order' => 4, 'is_active' => true,
                'included_in_plans' => null,
            ],
            [
                'name_ar' => 'وحدة الاستشارات أونلاين (Telemedicine)',
                'name_en' => 'Telemedicine module',
                'description_ar' => 'مكالمات فيديو HD، وصفات إلكترونية، دفع قبل الجلسة، تكامل EMR',
                'description_en' => 'HD video, e-prescriptions, pre-session payment, EMR integration',
                'price_egp' => 1190, 'price_egp_launch' => 590, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'video',
                'badge_ar' => 'مجاناً مع Pro+', 'badge_en' => 'Free with Pro+',
                'is_featured' => false, 'display_order' => 5, 'is_active' => true,
                'included_in_plans' => ['professional', 'enterprise'],
            ],
            [
                'name_ar' => 'تكامل التأمين الكامل',
                'name_en' => 'Full insurance integration',
                'description_ar' => 'تكامل مع Bupa, GIG, ELAJI, AXA, MetLife — مطالبات وتتبع موافقات',
                'description_en' => 'Integrations with Bupa, GIG, ELAJI, AXA, MetLife — claims and authorization tracking',
                'price_egp' => 950, 'price_egp_launch' => 470, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'shield',
                'badge_ar' => 'مجاناً مع Enterprise', 'badge_en' => 'Free with Enterprise',
                'is_featured' => true, 'display_order' => 6, 'is_active' => true,
                'included_in_plans' => ['enterprise'],
            ],
            [
                'name_ar' => 'تطبيق mobile مخصّص بشعارك',
                'name_en' => 'Custom-branded mobile app',
                'description_ar' => 'تطبيق iOS و Android بشعار عيادتك وألوانك — جاهز للنشر على المتاجر',
                'description_en' => 'iOS and Android app with your branding — store-ready',
                'price_egp' => 2390, 'price_egp_launch' => 1190, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'phone',
                'is_featured' => true, 'display_order' => 7, 'is_active' => true,
                'included_in_plans' => null,
            ],
            [
                'name_ar' => 'تكامل المختبرات',
                'name_en' => 'Lab integration',
                'description_ar' => 'تكامل مع معامل التحاليل والأشعة — نتائج تلقائية في ملف المريض',
                'description_en' => 'Integration with labs and radiology — results land directly in patient records',
                'price_egp' => 470, 'price_egp_launch' => 230, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'flask',
                'is_featured' => false, 'display_order' => 8, 'is_active' => true,
                'included_in_plans' => null,
            ],
            [
                'name_ar' => 'تخزين إضافي — 50 GB',
                'name_en' => 'Extra storage — 50 GB',
                'description_ar' => '50 GB تخزين إضافي للصور والأشعة والتقارير',
                'description_en' => '50 GB additional storage for images, X-rays, reports',
                'price_egp' => 230, 'price_egp_launch' => 110, 'is_launch_active' => true,
                'period' => 'monthly', 'icon' => 'box',
                'is_featured' => false, 'display_order' => 9, 'is_active' => true,
                'included_in_plans' => null,
            ],
        ];

        foreach ($items as $item) {
            AddOn::updateOrCreate(
                ['name_en' => $item['name_en']],
                $item
            );
        }
    }
}
