<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $cases = [
            [
                'slug' => 'sahel-dental-cairo',
                'client_name_ar' => 'مجمع الساحل لطب الأسنان',
                'client_name_en' => 'Sahel Dental Group',
                'industry_ar' => 'سلسلة عيادات أسنان',
                'industry_en' => 'Dental clinic chain',
                'location' => 'القاهرة، مصر',
                'title_ar' => 'كيف خفّض مجمع الساحل وقت الإدارة 60٪ بعد دكتوراتو',
                'title_en' => 'How Sahel Dental Cut Admin Time by 60% with Doctorato',
                'summary_ar' => 'سلسلة من 4 فروع في القاهرة استبدلت 3 أنظمة منفصلة بمنصة دكتوراتو الموحّدة وحققت نمواً 35٪ في 8 أشهر.',
                'summary_en' => '4-branch chain in Cairo replaced 3 separate systems with the unified Doctorato platform and grew 35% in 8 months.',
                'challenge_ar' => 'كان المجمع يدير 4 فروع باستخدام برنامج محاسبي قديم + Excel + WhatsApp للحجوزات. تكرار البيانات، أخطاء فواتير، وفقدان مرضى بسبب تأخر التواصل كانت مشاكل يومية.',
                'challenge_en' => 'The group managed 4 branches with legacy accounting software + Excel + WhatsApp for bookings. Duplicate data, invoice errors, and lost patients due to delayed communication were daily problems.',
                'solution_ar' => 'انتقال كامل إلى دكتوراتو خلال 3 أسابيع. استخدام مخطط الأسنان التفاعلي، CRM المرضى، ومركز الفواتير الموحّد. ربط بوابة المرضى للسماح لهم بحجز المواعيد ذاتياً.',
                'solution_en' => 'Full migration to Doctorato in 3 weeks. Using interactive dental chart, patient CRM, and unified billing center. Patient portal connected to enable self-booking.',
                'results_ar' => 'بعد 8 أشهر من التشغيل: انخفاض وقت الإدارة 60٪، نمو الإيرادات 35٪، رضا المرضى 92٪، وانخفاض معدّل عدم الحضور إلى 4٪.',
                'results_en' => 'After 8 months: 60% reduction in admin time, 35% revenue growth, 92% patient satisfaction, and no-show rate dropped to 4%.',
                'metrics' => [
                    ['label_ar' => 'انخفاض وقت الإدارة', 'label_en' => 'Admin time reduction', 'value' => 60, 'suffix' => '%', 'icon' => 'clock'],
                    ['label_ar' => 'نمو الإيرادات', 'label_en' => 'Revenue growth', 'value' => 35, 'suffix' => '%', 'icon' => 'chart'],
                    ['label_ar' => 'رضا المرضى', 'label_en' => 'Patient satisfaction', 'value' => 92, 'suffix' => '%', 'icon' => 'heart'],
                    ['label_ar' => 'معدل عدم الحضور', 'label_en' => 'No-show rate', 'value' => 4, 'suffix' => '%', 'icon' => 'calendar'],
                ],
                'modules_used' => ['EHR', 'مخطط أسنان', 'CRM', 'فواتير', 'بوابة المرضى', 'تقارير'],
                'testimonial_ar' => 'دكتوراتو غيّر طريقة عملنا تماماً. وفّرنا 60٪ من وقت السكرتارية وقدرنا نركّز على المرضى بدل الأوراق.',
                'testimonial_en' => 'Doctorato completely changed how we work. We saved 60% of our secretarial time and could focus on patients instead of paperwork.',
                'testimonial_author' => 'د. أحمد الفضلي',
                'testimonial_role' => 'المدير التنفيذي — مجمع الساحل',
                'is_published' => true,
                'is_featured' => true,
                'display_order' => 1,
            ],
            [
                'slug' => 'cairo-medical-center',
                'client_name_ar' => 'مركز القاهرة الطبي',
                'client_name_en' => 'Cairo Medical Center',
                'industry_ar' => 'مركز طبي متعدد التخصصات',
                'industry_en' => 'Multi-specialty medical center',
                'location' => 'الجيزة، مصر',
                'title_ar' => 'مركز القاهرة الطبي يضاعف عدد المرضى بعد إطلاق بوابة الحجز الذاتي',
                'title_en' => 'Cairo Medical Center Doubles Patient Volume After Self-Booking Portal',
                'summary_ar' => 'مركز بـ 12 طبيب ضاعف عدد المرضى من 800 إلى 1,650 شهرياً خلال 6 أشهر بفضل بوابة المرضى وأتمتة الحجوزات.',
                'summary_en' => 'A 12-doctor center doubled patient volume from 800 to 1,650/month in 6 months thanks to patient portal and booking automation.',
                'challenge_ar' => 'كان المركز يفقد 30٪ من المكالمات بسبب انشغال السكرتارية. طوابير طويلة في الاستقبال وخلافات على المواعيد كانت تسبب مغادرة المرضى.',
                'challenge_en' => 'The center was losing 30% of calls due to busy reception. Long queues at check-in and appointment conflicts caused patients to leave.',
                'solution_ar' => 'تفعيل بوابة المرضى مع إمكانية الحجز الذاتي 24/7، إرسال تذكيرات تلقائية بالـ SMS والـ WhatsApp قبل الموعد بـ 24 ساعة و2 ساعة. تكامل مع نظام الفوترة لطباعة الفواتير فور انتهاء الكشف.',
                'solution_en' => 'Activated patient portal with 24/7 self-booking, automated SMS and WhatsApp reminders 24h and 2h before appointment. Billing integration prints invoices right after consultation.',
                'results_ar' => 'في 6 أشهر: عدد المرضى تضاعف، معدّل عدم الحضور انخفض من 18٪ إلى 6٪، رضا المرضى وصل 89٪، والإيرادات نمت 110٪.',
                'results_en' => 'In 6 months: patient count doubled, no-show rate dropped from 18% to 6%, patient satisfaction reached 89%, and revenue grew 110%.',
                'metrics' => [
                    ['label_ar' => 'نمو عدد المرضى', 'label_en' => 'Patient growth', 'value' => 106, 'suffix' => '%', 'icon' => 'users'],
                    ['label_ar' => 'نمو الإيرادات', 'label_en' => 'Revenue growth', 'value' => 110, 'suffix' => '%', 'icon' => 'chart'],
                    ['label_ar' => 'انخفاض الـ No-show', 'label_en' => 'No-show drop', 'value' => 67, 'suffix' => '%', 'icon' => 'calendar'],
                    ['label_ar' => 'حجوزات ذاتية', 'label_en' => 'Self-bookings', 'value' => 78, 'suffix' => '%', 'icon' => 'phone'],
                ],
                'modules_used' => ['بوابة المرضى', 'EHR', 'فواتير', 'WhatsApp', 'تقارير', 'CRM'],
                'testimonial_ar' => 'بوابة المرضى لوحدها كانت كافية لتغيير كل شيء. الحجوزات الذاتية وفّرت لنا موظفين كاملين.',
                'testimonial_en' => 'The patient portal alone was enough to change everything. Self-bookings saved us entire staff positions.',
                'testimonial_author' => 'د. سارة الحلواني',
                'testimonial_role' => 'مدير التشغيل — مركز القاهرة الطبي',
                'is_published' => true,
                'is_featured' => true,
                'display_order' => 2,
            ],
            [
                'slug' => 'noor-eye-clinic-alex',
                'client_name_ar' => 'عيادة النور للعيون',
                'client_name_en' => 'Noor Eye Clinic',
                'industry_ar' => 'عيادة عيون تخصصية',
                'industry_en' => 'Specialty eye clinic',
                'location' => 'الإسكندرية، مصر',
                'title_ar' => 'عيادة النور تختصر يومين عمل أسبوعياً بفضل التقارير الآلية',
                'title_en' => 'Noor Eye Clinic Saves 2 Working Days/Week with Automated Reports',
                'summary_ar' => 'طبيب واحد + 3 موظفين كانوا يقضون 16 ساعة أسبوعياً في إعداد التقارير المالية والإحصائيات. الآن صاروا يستلمونها تلقائياً كل صباح.',
                'summary_en' => 'A solo doctor + 3 staff used to spend 16 hours/week preparing financial reports and stats. Now they get them automatically every morning.',
                'challenge_ar' => 'إعداد تقارير شهرية لتأمين صحي و 5 شركات تأمين مختلفة. كل تقرير كان يأخذ 4-6 ساعات ويصل غالباً متأخراً مما يسبب تأخير المدفوعات.',
                'challenge_en' => 'Preparing monthly reports for 5 different insurance companies. Each report took 4-6 hours and was often late, causing payment delays.',
                'solution_ar' => 'إعداد قوالب تقارير مخصّصة لكل شركة تأمين مع جدولة تلقائية. لوحة تحكم Real-time للأرباح والمصروفات. تنبيهات للفواتير المتأخرة.',
                'solution_en' => 'Custom report templates for each insurance company with auto-scheduling. Real-time dashboard for revenue and expenses. Alerts for overdue invoices.',
                'results_ar' => 'وفّرت العيادة 16 ساعة أسبوعياً (يومان عمل كاملان)، تسريع المدفوعات بـ 14 يوم في المتوسط، وتقليل الفواتير المتأخرة 80٪.',
                'results_en' => 'The clinic saved 16 hours/week (2 full work days), accelerated payments by 14 days on average, and reduced overdue invoices by 80%.',
                'metrics' => [
                    ['label_ar' => 'ساعات موفّرة أسبوعياً', 'label_en' => 'Hours saved/week', 'value' => 16, 'suffix' => 'h', 'icon' => 'clock'],
                    ['label_ar' => 'تسريع التحصيل', 'label_en' => 'Faster collection', 'value' => 14, 'suffix' => 'd', 'icon' => 'cash'],
                    ['label_ar' => 'تقليل الفواتير المتأخرة', 'label_en' => 'Overdue reduction', 'value' => 80, 'suffix' => '%', 'icon' => 'chart'],
                    ['label_ar' => 'شركات تأمين', 'label_en' => 'Insurance partners', 'value' => 5, 'suffix' => '', 'icon' => 'shield'],
                ],
                'modules_used' => ['تقارير', 'فواتير', 'تأمين', 'محاسبة'],
                'testimonial_ar' => 'كنّا نعتبر التقارير عبء أسبوعي. الآن صارت تصلني تلقائياً قبل ما أطلبها.',
                'testimonial_en' => 'We used to consider reports a weekly burden. Now they reach me automatically before I even ask.',
                'testimonial_author' => 'د. محمد السيد',
                'testimonial_role' => 'مدير عيادة النور للعيون',
                'is_published' => true,
                'is_featured' => false,
                'display_order' => 3,
            ],
        ];

        foreach ($cases as $case) {
            CaseStudy::updateOrCreate(['slug' => $case['slug']], $case);
        }
    }
}
