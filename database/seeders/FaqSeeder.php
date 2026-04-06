<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'general',
                'question_ar' => 'ما هو نظام دكتوراتو؟',
                'question_en' => 'What is Doctorato?',
                'answer_ar' => 'دكتوراتو هو نظام متكامل لإدارة العيادات والمراكز الطبية يتضمن 6 بوابات مستقلة (المدير، الطبيب، السكرتيرة، المريض، مدير الموقع، الموقع العام) وأكثر من 800 خاصية تغطي إدارة المرضى والحجوزات والفواتير وطب الأسنان وCRM والموارد البشرية والمخزون والتأمين. تم بناؤه بأحدث التقنيات (Laravel 11 + Vue.js 3) ويدعم اللغتين العربية والإنجليزية بشكل كامل.',
                'answer_en' => 'Doctorato is a comprehensive clinic & medical center management system featuring 6 independent portals (Admin, Doctor, Secretary, Patient, Webmaster, Public Website) with 800+ features covering patient management, bookings, invoicing, dental, CRM, HR, inventory, and insurance. Built with the latest technologies (Laravel 11 + Vue.js 3) with full Arabic & English support.',
                'display_order' => 1,
            ],
            [
                'category' => 'general',
                'question_ar' => 'هل يدعم النظام اللغة العربية بالكامل؟',
                'question_en' => 'Does the system fully support Arabic?',
                'answer_ar' => 'نعم، يدعم النظام اللغتين العربية والإنجليزية بشكل كامل مع أكثر من 3,000 مفتاح ترجمة. يتضمن دعم كامل لاتجاه الكتابة من اليمين لليسار (RTL) باستخدام خط Tajawal للعربية وخط Poppins للإنجليزية مع تبديل تلقائي حسب اللغة المختارة.',
                'answer_en' => 'Yes, the system fully supports both Arabic and English with 3,000+ translation keys. It includes complete RTL support using Tajawal font for Arabic and Poppins for English with automatic switching.',
                'display_order' => 2,
            ],
            [
                'category' => 'technical',
                'question_ar' => 'هل يمكنني تفعيل وحدة طب الأسنان فقط بدون باقي الوحدات؟',
                'question_en' => 'Can I enable only the dental module without others?',
                'answer_ar' => 'نعم، النظام مبني بتصميم معياري (Modular Architecture) يتيح لك تفعيل أو تعطيل أي وحدة حسب حاجتك. يمكنك تفعيل وحدة طب الأسنان فقط، أو CRM فقط، أو أي مجموعة تناسب عيادتك. كل وحدة محمية بحارس وحدة (Module Guard) مستقل.',
                'answer_en' => 'Yes, the system uses a modular architecture that allows you to enable or disable any module as needed. You can enable only the dental module, only CRM, or any combination that suits your clinic. Each module is protected by an independent Module Guard.',
                'display_order' => 3,
            ],
            [
                'category' => 'pricing',
                'question_ar' => 'هل يمكنني الترقية من خطة لأخرى؟',
                'question_en' => 'Can I upgrade from one plan to another?',
                'answer_ar' => 'نعم، يمكنك الترقية في أي وقت من خطة لأخرى. سيتم احتساب الفرق بشكل تناسبي (Pro-Rata) حسب المدة المتبقية من اشتراكك الحالي. جميع بياناتك وإعداداتك ستبقى كما هي عند الترقية.',
                'answer_en' => 'Yes, you can upgrade at any time. The difference will be calculated pro-rata based on the remaining period of your current subscription. All your data and settings will remain intact upon upgrade.',
                'display_order' => 4,
            ],
            [
                'category' => 'pricing',
                'question_ar' => 'ما هي مدة العرض التجريبي المجاني؟',
                'question_en' => 'How long is the free trial?',
                'answer_ar' => 'نوفر عرضاً تجريبياً مجانياً لمدة 14 يوماً يشمل جميع ميزات الخطة المتقدمة (Enterprise) بدون الحاجة لبطاقة ائتمان. خلال الفترة التجريبية، ستحصل على دعم فني مجاني لمساعدتك في إعداد النظام واستكشاف جميع الميزات.',
                'answer_en' => 'We offer a 14-day free trial that includes all Enterprise plan features with no credit card required. During the trial, you get free technical support to help you set up the system and explore all features.',
                'display_order' => 5,
            ],
            [
                'category' => 'technical',
                'question_ar' => 'كيف يتم تخزين البيانات الطبية؟ هل هي آمنة؟',
                'question_en' => 'How is medical data stored? Is it secure?',
                'answer_ar' => 'نعم، أمان البيانات أولويتنا القصوى. نستخدم تشفير SSL/TLS لجميع الاتصالات، تشفير Bcrypt لكلمات المرور، حماية CSRF لجميع النماذج، نظام صلاحيات RBAC بأكثر من 80 صلاحية دقيقة، سجل تدقيق شامل لجميع العمليات، سجل وصول خاص للبيانات الطبية الحساسة، ونسخ احتياطي منتظم. البيانات مخزنة على سيرفرات محمية مع إمكانية التركيب على سيرفر خاص (On-Premise).',
                'answer_en' => 'Yes, data security is our top priority. We use SSL/TLS encryption, Bcrypt password hashing, CSRF protection, RBAC with 80+ permissions, comprehensive audit logging, medical access logging, and regular backups. Data is stored on secure servers with On-Premise installation option.',
                'display_order' => 6,
            ],
            [
                'category' => 'technical',
                'question_ar' => 'هل يدعم النظام التكامل مع أنظمة أخرى؟',
                'question_en' => 'Does the system support integration with other systems?',
                'answer_ar' => 'في الخطة المخصصة (Custom)، نوفر تكامل مع أنظمة خارجية مثل ERP وأنظمة المختبرات (LIS) وأنظمة الأشعة (RIS/PACS) وأي نظام آخر عبر API. كما يتضمن النظام تكامل جاهز مع Google Tag Manager و Google Analytics 4 و Facebook Pixel و TikTok و Snapchat و Twitter Pixels.',
                'answer_en' => 'In the Custom plan, we offer integration with external systems like ERP, LIS, RIS/PACS, and any other system via API. The system also includes built-in integration with Google Tag Manager, GA4, Facebook Pixel, TikTok, Snapchat, and Twitter Pixels.',
                'display_order' => 7,
            ],
            [
                'category' => 'general',
                'question_ar' => 'هل يمكن تركيب النظام على سيرفر خاص؟',
                'question_en' => 'Can the system be installed on a private server?',
                'answer_ar' => 'نعم، في الخطة المخصصة (Custom) نوفر خيار التركيب على سيرفر خاص (On-Premise) مع تحكم كامل في البيانات والبنية التحتية. هذا الخيار مثالي للمراكز الطبية الكبيرة التي لديها متطلبات أمنية صارمة أو تنظيمية تتطلب بقاء البيانات داخل الدولة.',
                'answer_en' => 'Yes, in the Custom plan we offer On-Premise installation with full control over data and infrastructure. This option is ideal for large medical centers with strict security or regulatory requirements that mandate data residency.',
                'display_order' => 8,
            ],
            [
                'category' => 'pricing',
                'question_ar' => 'ما هي طرق الدفع المقبولة؟',
                'question_en' => 'What payment methods are accepted?',
                'answer_ar' => 'نقبل الدفع عبر: بطاقات الائتمان (Visa, Mastercard, Mada)، التحويل البنكي المباشر، وApple Pay. للخطط السنوية والمخصصة، يمكن ترتيب دفعات شهرية أو ربع سنوية حسب الاتفاق.',
                'answer_en' => 'We accept payment via: Credit cards (Visa, Mastercard, Mada), bank transfer, and Apple Pay. For annual and custom plans, monthly or quarterly payments can be arranged.',
                'display_order' => 9,
            ],
            [
                'category' => 'general',
                'question_ar' => 'هل يوفر النظام تطبيق جوال؟',
                'question_en' => 'Does the system provide a mobile app?',
                'answer_ar' => 'النظام مبني بتصميم متجاوب (Responsive) يعمل بشكل مثالي على جميع الأجهزة (كمبيوتر، لوحي، هاتف) عبر متصفح الويب. تطبيق جوال مخصص (iOS + Android) قيد التطوير وسيكون متاحاً في التحديثات القادمة.',
                'answer_en' => 'The system is built with a responsive design that works perfectly on all devices (desktop, tablet, phone) via web browser. A dedicated mobile app (iOS + Android) is under development and will be available in upcoming updates.',
                'display_order' => 10,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question_en' => $faq['question_en']],
                $faq
            );
        }
    }
}
