<?php

namespace Database\Seeders;

use App\Models\AddOn;
use Illuminate\Database\Seeder;

class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name_ar' => 'مستخدم إضافي', 'name_en' => 'Extra user',
                'description_ar' => 'أضف مستخدم إضافي لعيادتك', 'description_en' => 'Add an extra user to your clinic',
                'price_egp' => 99, 'period' => 'monthly',
                'icon' => 'user', 'display_order' => 1, 'is_featured' => true,
            ],
            [
                'name_ar' => 'باقة SMS', 'name_en' => 'SMS bundle',
                'description_ar' => '1,000 رسالة نصية شهرياً', 'description_en' => '1,000 SMS messages per month',
                'price_egp' => 249, 'period' => 'monthly',
                'icon' => 'sms', 'display_order' => 2,
            ],
            [
                'name_ar' => 'باقة واتساب',
                'name_en' => 'WhatsApp bundle',
                'description_ar' => '2,000 رسالة واتساب شهرياً',
                'description_en' => '2,000 WhatsApp messages per month',
                'price_egp' => 399, 'period' => 'monthly',
                'icon' => 'whatsapp', 'display_order' => 3, 'is_featured' => true,
                'badge_ar' => 'الأكثر طلباً', 'badge_en' => 'Most popular',
            ],
            [
                'name_ar' => 'نطاق مخصص', 'name_en' => 'Custom domain',
                'description_ar' => 'استخدم نطاقك الخاص مع بوابة المرضى', 'description_en' => 'Use your own domain with the patient portal',
                'price_egp' => 199, 'period' => 'monthly',
                'icon' => 'domain', 'display_order' => 4,
            ],
            [
                'name_ar' => 'نسخ احتياطي كل ساعة', 'name_en' => 'Hourly backup',
                'description_ar' => 'نسخة احتياطية من بياناتك كل ساعة', 'description_en' => 'Back up your data every hour',
                'price_egp' => 299, 'period' => 'monthly',
                'icon' => 'backup', 'display_order' => 5,
            ],
            [
                'name_ar' => 'علامة تجارية بيضاء', 'name_en' => 'White label',
                'description_ar' => 'شعارك وهويتك بدلاً من دكتوراتو', 'description_en' => 'Your branding instead of Doctorato',
                'price_egp' => 1999, 'period' => 'monthly',
                'icon' => 'brand', 'display_order' => 6,
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
