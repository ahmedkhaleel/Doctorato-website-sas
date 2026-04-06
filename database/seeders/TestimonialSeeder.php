<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'client_name_ar' => 'د. عبدالله المنصور',
                'client_name_en' => 'Dr. Abdullah Al-Mansour',
                'clinic_name_ar' => 'عيادات الابتسامة لطب الأسنان',
                'clinic_name_en' => 'Al-Ibtisama Dental Clinics',
                'role_ar' => 'مدير العيادة',
                'role_en' => 'Clinic Director',
                'review_ar' => 'نظام دكتوراتو غيّر طريقة إدارتنا للعيادة بالكامل. وحدة طب الأسنان مع مخطط الأسنان التفاعلي وخطط العلاج المتقدمة وفّرت علينا ساعات من العمل اليدوي يومياً. أنصح به بشدة لكل عيادة أسنان تبحث عن نظام متكامل يدعم العربية بشكل ممتاز.',
                'review_en' => 'Doctorato completely transformed how we manage our clinic. The dental module with interactive dental chart and advanced treatment plans saved us hours of manual work daily. I highly recommend it for any dental clinic looking for a comprehensive system with excellent Arabic support.',
                'rating' => 5,
                'photo' => null,
                'display_order' => 1,
            ],
            [
                'client_name_ar' => 'د. فاطمة الشامسي',
                'client_name_en' => 'Dr. Fatima Al-Shamsi',
                'clinic_name_ar' => 'مركز النخبة الطبي',
                'clinic_name_en' => 'Al-Nukhba Medical Center',
                'role_ar' => 'المدير التنفيذي',
                'role_en' => 'CEO',
                'review_ar' => 'بعد تجربة عدة أنظمة، وجدنا في دكتوراتو كل ما نحتاجه. نظام CRM ساعدنا في زيادة عدد المرضى الجدد بنسبة 40%، ووحدة الموارد البشرية سهّلت إدارة فريقنا المكون من 25 موظفاً. الدعم الفني سريع ومتجاوب دائماً.',
                'review_en' => 'After trying several systems, we found everything we need in Doctorato. The CRM system helped us increase new patients by 40%, and the HR module simplified managing our team of 25 employees. Technical support is always fast and responsive.',
                'rating' => 5,
                'photo' => null,
                'display_order' => 2,
            ],
            [
                'client_name_ar' => 'د. أحمد حسن',
                'client_name_en' => 'Dr. Ahmed Hassan',
                'clinic_name_ar' => 'عيادة الشفاء التخصصية',
                'clinic_name_en' => 'Al-Shefaa Specialized Clinic',
                'role_ar' => 'طبيب أسنان - مالك العيادة',
                'role_en' => 'Dentist - Clinic Owner',
                'review_ar' => 'كطبيب أسنان مصري يعمل في السعودية، كنت أبحث عن نظام يدعم العربية والإنجليزية بشكل حقيقي وليس مجرد ترجمة سطحية. دكتوراتو يقدم تجربة كاملة باللغتين مع دعم RTL ممتاز. نظام الفواتير والتأمين وفّر علينا الكثير من الجهد في التعامل مع شركات التأمين.',
                'review_en' => 'As an Egyptian dentist working in Saudi Arabia, I was looking for a system that truly supports Arabic and English, not just a superficial translation. Doctorato delivers a complete bilingual experience with excellent RTL support. The invoicing and insurance system saved us significant effort in dealing with insurance companies.',
                'rating' => 5,
                'photo' => null,
                'display_order' => 3,
            ],
            [
                'client_name_ar' => 'د. نورة العتيبي',
                'client_name_en' => 'Dr. Noura Al-Otaibi',
                'clinic_name_ar' => 'مركز لمسة جمال',
                'clinic_name_en' => 'Lamsat Jamal Center',
                'role_ar' => 'أخصائية جلدية - مديرة المركز',
                'role_en' => 'Dermatologist - Center Director',
                'review_ar' => 'بدأنا مع الخطة الأساسية وخلال 3 أشهر ترقينا للخطة الاحترافية. نظام المحفظة الإلكترونية وأكواد الخصم ساعدونا في برامج الولاء للمرضى. استبيان رضا المرضى مع NPS أعطانا رؤية واضحة عن نقاط التحسين. تجربة رائعة من البداية.',
                'review_en' => 'We started with the Basic plan and within 3 months upgraded to Professional. The e-wallet system and discount codes helped us with patient loyalty programs. The patient satisfaction survey with NPS gave us clear insights on improvement areas. A wonderful experience from the start.',
                'rating' => 4,
                'photo' => null,
                'display_order' => 4,
            ],
            [
                'client_name_ar' => 'د. محمد الفضلي',
                'client_name_en' => 'Dr. Mohammed Al-Fadhli',
                'clinic_name_ar' => 'مجمع الكويت الطبي',
                'clinic_name_en' => 'Kuwait Medical Complex',
                'role_ar' => 'المدير العام',
                'role_en' => 'General Manager',
                'review_ar' => 'نحن مجمع طبي كبير في الكويت ونستخدم الخطة المتقدمة. إدارة المخزون مع تنبيهات انتهاء الصلاحية ووحدة الموارد البشرية مع مسيرات الرواتب غيّرت طريقة عملنا بالكامل. نظام الصلاحيات RBAC مع 80+ صلاحية أعطانا تحكم دقيق في وصول كل موظف.',
                'review_en' => 'We are a large medical complex in Kuwait using the Enterprise plan. Inventory management with expiry alerts and the HR module with payroll completely changed how we operate. The RBAC system with 80+ permissions gave us precise control over each employee\'s access.',
                'rating' => 5,
                'photo' => null,
                'display_order' => 5,
            ],
            [
                'client_name_ar' => 'د. سارة الحمادي',
                'client_name_en' => 'Dr. Sara Al-Hammadi',
                'clinic_name_ar' => 'عيادة الرعاية المتميزة',
                'clinic_name_en' => 'Premium Care Clinic',
                'role_ar' => 'طبيبة عامة',
                'role_en' => 'General Practitioner',
                'review_ar' => 'كعيادة صغيرة في عمّان، كنا نحتاج نظام بسيط وفعّال وبسعر مناسب. الخطة الأساسية بـ 299 ريال شهرياً كانت مثالية لنا. نظام الحجوزات والفواتير سهل الاستخدام والدعم الفني يرد بسرعة. خطوة ممتازة في رحلتنا نحو التحول الرقمي.',
                'review_en' => 'As a small clinic in Amman, we needed a simple, effective system at a reasonable price. The Basic plan at 299 SAR per month was perfect for us. The booking and invoicing system is easy to use and technical support responds quickly. An excellent step in our digital transformation journey.',
                'rating' => 4,
                'photo' => null,
                'display_order' => 6,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                ['client_name_en' => $testimonial['client_name_en']],
                $testimonial
            );
        }
    }
}
