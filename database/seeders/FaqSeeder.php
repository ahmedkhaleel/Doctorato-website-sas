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
                'answer_ar' => 'نقبل الدفع عبر: بطاقات الائتمان (Visa, Mastercard, Mada)، التحويل البنكي المباشر، وApple Pay. يمكنك اختيار الاشتراك الشهري أو السنوي. الاشتراك السنوي يحصل على خصم 21% على القيمة + 50% خصم على رسوم التشغيل.',
                'answer_en' => 'We accept: Credit cards (Visa, Mastercard, Mada), bank transfer, and Apple Pay. You can choose monthly or annual billing. Annual subscriptions get 21% off the rate + 50% off the setup fee.',
                'display_order' => 9,
            ],
            // ─── Phase F — new pricing FAQs for the launch tier model ───
            [
                'category' => 'pricing',
                'question_ar' => 'ما هي رسوم التشغيل ولماذا منفصلة عن الاشتراك؟',
                'question_en' => 'What is the setup fee and why is it separate from the subscription?',
                'answer_ar' => 'رسوم التشغيل هي مقابل خدمات بدء الاستخدام: جلسة Implementation kickoff، نقل بياناتك من نظامك القديم (حتى 2,000 مريض)، تخصيص النظام بهويتك (لوجو، ألوان، قوالب)، تدريب الفريق، قوالب طبية مخصصة لتخصصك، 30 يوم Priority Support بعد الإطلاق، وإعداد التكاملات. تُدفع مرة واحدة عند بدء الاستخدام. مع الاشتراك السنوي تحصل على خصم 50% على رسوم التشغيل تلقائياً.',
                'answer_en' => 'The setup fee covers the kickoff session, data migration (up to 2,000 patients), branded customisation, team training, specialty-specific templates, 30 days of priority support after launch, and integration setup. Paid once at signup. Annual subscribers get an automatic 50% off the setup fee.',
                'display_order' => 11,
            ],
            [
                'category' => 'pricing',
                'question_ar' => 'هل عرض الإطلاق سيستمر للأبد؟',
                'question_en' => 'Will the launch offer last forever?',
                'answer_ar' => 'لا. عرض الإطلاق ساري حتى نهاية 2026 فقط. بعد هذا التاريخ ستعود الأسعار للقيمة الأصلية (الأرقام المشطوبة). العداد في أعلى الموقع يعرض الوقت المتبقي بدقّة. ينصح بالاشتراك السنوي الآن لتثبيت السعر الحالي لمدة سنة كاملة.',
                'answer_en' => 'No. The launch offer runs only through end of 2026. After that date prices revert to the regular (strikethrough) amounts. The countdown at the top of the site shows the exact time remaining. We recommend the annual plan now to lock in current pricing for a full year.',
                'display_order' => 13,
            ],
            [
                'category' => 'pricing',
                'question_ar' => 'ما الفرق بين باقة Growth و Professional؟',
                'question_en' => 'What is the difference between Growth and Professional?',
                'answer_ar' => 'Growth (2,290 ج.م/شهر إطلاق) تشمل 3 تخصصات طبية، حتى 3 أطباء و 3,000 مريض، CRM طبي، WhatsApp Business API، مخزون، وتأمين أساسي. Professional (3,490 ج.م/شهر إطلاق) تشمل كل التخصصات الـ 6، أطباء حتى 7، مرضى بلا حدود، HR كامل، تكامل تأمين كامل (Bupa, GIG, ELAJI, AXA)، PACS، Premium Analytics مع AI، ومدير حساب مخصص. الفرق 1,200 ج.م لكن القيمة المضافة تساوي أضعاف ذلك للعيادات متعددة التخصصات.',
                'answer_en' => 'Growth (2,290 EGP/mo launch) includes 3 medical specialties, up to 3 doctors and 3,000 patients, medical CRM, WhatsApp Business API, inventory, and basic insurance. Professional (3,490 EGP/mo launch) includes all 6 specialties, up to 7 doctors, unlimited patients, full HR, full insurance integration (Bupa, GIG, ELAJI, AXA), PACS, premium AI analytics, and a dedicated account manager. The 1,200 EGP gap delivers multiples in value for multi-specialty clinics.',
                'display_order' => 14,
            ],
            [
                'category' => 'pricing',
                'question_ar' => 'هل يمكنني إلغاء الاشتراك أو طلب استرداد؟',
                'question_en' => 'Can I cancel or get a refund?',
                'answer_ar' => 'نعم. الاشتراك الشهري يُلغى في أي وقت من بوابتك، يبقى مفعّلاً حتى نهاية الفترة المدفوعة (Soft Cancel). الاشتراك السنوي قابل للإيقاف المؤقت (Pause) لمدة 90 يوم. ضمان استرداد كامل خلال أول 30 يوم بدون أسئلة. بعد 30 يوم، نسترد القيمة المتبقية pro-rata في الاشتراك السنوي.',
                'answer_en' => 'Yes. Monthly subscriptions can be cancelled anytime from your portal — access stays active until the end of the paid period (soft cancel). Annual subscriptions support pause-up-to-90-days. Full refund within the first 30 days, no questions asked. After day 30, annual customers get a pro-rata refund of the unused portion.',
                'display_order' => 15,
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
