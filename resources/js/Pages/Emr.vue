<script setup>
/**
 * /emr — keyword-targeted landing page.
 *
 * Targets the AR query "EMR عربي" + "سجلات طبية إلكترونية" and the EN
 * queries "EMR software for clinics" + "EHR for Middle East".
 *
 * Built specifically for organic search: long-form content, deep
 * H2/H3 hierarchy, FAQPage schema, MedicalBusiness/SoftwareApplication
 * schema, breadcrumbs. The visual design re-uses the brand language so
 * it doesn't read as a template page.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, ref } from 'vue';

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

const features = computed(() => [
    {
        icon: '📋',
        title: tr('سجل طبي إلكتروني كامل', 'Complete Electronic Medical Record'),
        body: tr('ملف لكل مريض يجمع التاريخ المرضي، الفحوصات، الوصفات، الأشعة، التقارير، والملاحظات السريرية في مكان واحد آمن — متاح فوراً من أي جهاز.', 'A single record per patient consolidating history, exams, prescriptions, imaging, reports, and clinical notes — instantly accessible from any device.'),
    },
    {
        icon: '🩺',
        title: tr('قوالب فحوصات قابلة للتخصيص', 'Customizable exam templates'),
        body: tr('أكثر من 30 قالب جاهز لكل تخصص — جلدية، أسنان، أطفال، نساء، عظام، قلبية. عدّل أو ابن قوالبك الخاصة في دقائق.', 'More than 30 ready templates per specialty — derma, dental, pediatrics, OB-GYN, ortho, cardio. Edit or build your own in minutes.'),
    },
    {
        icon: '💊',
        title: tr('وصفات طبية إلكترونية', 'Electronic prescriptions (e-prescribe)'),
        body: tr('قاعدة أدوية شاملة بالعربي والإنجليزي مع تنبيهات التفاعلات الدوائية، الجرعات حسب الوزن، وتوقيع رقمي معتمد.', 'A bilingual drug database with interaction alerts, weight-based dosing, and signed digital scripts.'),
    },
    {
        icon: '🔬',
        title: tr('تكامل المختبر والأشعة', 'Lab + radiology integration'),
        body: tr('استلام نتائج التحاليل والأشعة مباشرة في ملف المريض، مع رسوم بيانية لتتبع التغيرات عبر الزمن.', 'Lab and imaging results land directly in the patient record, with charts to track changes over time.'),
    },
    {
        icon: '📱',
        title: tr('وصول من أي مكان', 'Access anywhere'),
        body: tr('تطبيقات للأطباء والمرضى على iOS وAndroid، مع متصفح ويب كامل المزايا — عيادتك معاك في كل مكان.', 'Native iOS + Android apps for doctors and patients, plus a full-featured web client. Your clinic, anywhere.'),
    },
    {
        icon: '🔒',
        title: tr('أمان طبي متوافق', 'Healthcare-grade security'),
        body: tr('تشفير AES-256، صلاحيات دقيقة، سجل تدقيق لكل عملية وصول لبيانات المريض، نسخ احتياطي يومي مشفّر.', 'AES-256 encryption, granular permissions, full audit trail on every patient-data access, daily encrypted backups.'),
    },
]);

const benefits = computed(() => [
    { stat: '85%', label: tr('تقليل وقت كتابة التقارير', 'less time writing reports') },
    { stat: '60%', label: tr('تقليل أخطاء الوصفات', 'reduction in prescription errors') },
    { stat: '40%', label: tr('زيادة وقت الطبيب مع المريض', 'more doctor-patient time') },
    { stat: '24/7', label: tr('وصول فوري للسجلات', 'instant record access') },
]);

const faqs = computed(() => isAr.value ? [
    {
        q: 'إيه الفرق بين EMR و EHR؟',
        a: 'EMR (Electronic Medical Record) هو السجل الإلكتروني لمريض داخل عيادة واحدة. EHR (Electronic Health Record) أوسع — يربط بين عدة منشآت طبية ويتيح تبادل البيانات بأمان. دكتوراتو يدعم النوعين.',
    },
    {
        q: 'هل النظام يدعم عيادات متعددة الفروع؟',
        a: 'نعم. تقدر تربط أي عدد من الفروع تحت نفس الحساب، مع تحكم دقيق في صلاحيات كل فرع وتقارير موحّدة على مستوى المؤسسة.',
    },
    {
        q: 'هل بياناتي آمنة على السحابة؟',
        a: 'بالكامل. نستخدم تشفير AES-256 للبيانات المخزّنة، SSL/TLS 256-bit للنقل، نسخ احتياطية يومية، وسجل تدقيق لكل عملية. الخوادم في مراكز بيانات متوافقة مع ISO 27001.',
    },
    {
        q: 'هل أقدر أنقل بيانات عيادتي الحالية للنظام؟',
        a: 'نعم. فريقنا يساعدك في نقل البيانات من Excel، Access، أو أي نظام تاني — بالمجان كجزء من إعداد الباقة.',
    },
    {
        q: 'هل النظام يدعم اللغة العربية؟',
        a: 'النظام مبني عربي/إنجليزي بالكامل: واجهة، تقارير، وصفات، فواتير، كل شيء. دعم RTL/LTR تلقائي، أكثر من 3,000 مفتاح ترجمة.',
    },
    {
        q: 'كم تكلفة EMR للعيادات الصغيرة؟',
        a: 'تبدأ من 958 ج.م شهرياً في مصر (~99 ر.س / 99 د.إ). فيه تجربة 14 يوم مجانية بدون بطاقة ائتمان. شوف صفحة الأسعار للتفاصيل حسب دولتك.',
    },
] : [
    { q: 'What\'s the difference between EMR and EHR?', a: 'EMR (Electronic Medical Record) is a clinic\'s internal patient record. EHR (Electronic Health Record) is broader — it links multiple health entities and allows secure data exchange. Doctorato supports both models.' },
    { q: 'Does the system support multi-branch clinics?', a: 'Yes. Link any number of branches under one account with granular per-branch permissions and consolidated organisation-level reporting.' },
    { q: 'Is my data safe on the cloud?', a: 'Absolutely. We use AES-256 at-rest encryption, SSL/TLS 256-bit in-transit, daily backups, and an audit trail on every action. Servers run in ISO 27001-compliant data centers.' },
    { q: 'Can I import my existing clinic data?', a: 'Yes. Our team helps you migrate from Excel, Access, or any other system — included free as part of plan setup.' },
    { q: 'Does the system support Arabic?', a: 'It\'s built bilingual end-to-end: interface, reports, prescriptions, invoices. Automatic RTL/LTR, 3,000+ translation keys.' },
    { q: 'How much does EMR for a small clinic cost?', a: 'Starts at 958 EGP / month in Egypt (~99 SAR / 99 AED). 14-day free trial, no credit card. See the pricing page for country-specific rates.' },
]);

const openFaq = ref(null);
function toggleFaq(idx) { openFaq.value = openFaq.value === idx ? null : idx; }

// Schema — FAQPage + SoftwareApplication + BreadcrumbList @graph
const ldJson = computed(() => ({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'SoftwareApplication',
            '@id': 'https://doctorato.com/emr#software',
            name: 'Doctorato EMR / EHR',
            applicationCategory: 'HealthApplication',
            operatingSystem: 'Web, iOS, Android',
            offers: { '@type': 'Offer', price: '958', priceCurrency: 'EGP', availability: 'https://schema.org/InStock' },
            aggregateRating: { '@type': 'AggregateRating', ratingValue: '4.9', reviewCount: '127' },
        },
        {
            '@type': 'FAQPage',
            mainEntity: faqs.value.map(f => ({
                '@type': 'Question',
                name: f.q,
                acceptedAnswer: { '@type': 'Answer', text: f.a },
            })),
        },
        {
            '@type': 'BreadcrumbList',
            itemListElement: [
                { '@type': 'ListItem', position: 1, name: 'Doctorato', item: 'https://doctorato.com' },
                { '@type': 'ListItem', position: 2, name: tr('الخصائص', 'Features'), item: 'https://doctorato.com/features' },
                { '@type': 'ListItem', position: 3, name: tr('السجلات الطبية الإلكترونية', 'EMR / EHR'), item: 'https://doctorato.com/emr' },
            ],
        },
    ],
}));
</script>

<template>
    <SeoHead
        :title="tr('نظام EMR عربي | برنامج السجلات الطبية الإلكترونية للعيادات — Doctorato', 'Arabic EMR Software | EHR System for Middle East Clinics — Doctorato')"
        :description="tr('أفضل نظام EMR / EHR عربي للعيادات الطبية في الشرق الأوسط: سجلات طبية إلكترونية، وصفات رقمية، تكامل مختبر وأشعة، قوالب لكل تخصص، وأمان طبي معتمد. جرّب 14 يوم مجاناً.', 'The leading Arabic-first EMR / EHR for Middle East clinics: electronic medical records, e-prescriptions, lab + imaging integration, per-specialty templates, healthcare-grade security. 14-day free trial.')"
        :json-ld="ldJson"
    />
    <MainLayout>
        <!-- ─── Hero ─────────────────────────────────────────────── -->
        <section class="relative pt-28 sm:pt-36 pb-16 sm:pb-20 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute -top-20 -start-20 w-96 h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-20 -end-20 w-[28rem] h-[28rem] bg-[#2471A3]/20 rounded-full blur-[120px]"></div>

            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/15 ring-1 ring-emerald-400/30 mb-5 animate-fade-up">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs sm:text-sm text-emerald-200 font-semibold">{{ tr('السجل الطبي الإلكتروني #1 في الشرق الأوسط', '#1 Electronic Medical Record in the Middle East') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-5 leading-tight animate-fade-up">
                    {{ tr('نظام EMR / EHR عربي', 'Arabic EMR / EHR system') }}
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#C4A265] to-[#D4B876]">
                        {{ tr('مصمم خصيصاً لعيادات المنطقة', 'built for clinics in the region') }}
                    </span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl text-white/70 max-w-3xl mx-auto leading-relaxed mb-8 animate-fade-up">
                    {{ tr(
                        'سجلات طبية إلكترونية كاملة، قوالب لكل تخصص، وصفات رقمية، تكامل مختبر وأشعة، وأمان طبي معتمد — كل اللي عيادتك محتاجاه في منصة واحدة بالعربي.',
                        'Complete electronic medical records, per-specialty templates, e-prescriptions, lab + imaging integration, healthcare-grade security — everything your clinic needs, in Arabic.'
                    ) }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 animate-fade-up">
                    <Link href="/demo" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold text-sm shadow-xl shadow-[#C4A265]/25 hover:shadow-2xl hover:-translate-y-0.5 transition">
                        {{ tr('احجز عرضاً تجريبياً', 'Book a free demo') }}
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </Link>
                    <Link href="/pricing" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full border-2 border-white/20 text-white font-semibold text-sm hover:bg-white/10 transition">
                        {{ tr('شاهد الأسعار', 'View pricing') }}
                    </Link>
                </div>

                <!-- Stats inline -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 max-w-3xl mx-auto mt-12 pt-8 border-t border-white/10 animate-fade-up">
                    <div v-for="b in benefits" :key="b.label" class="text-center">
                        <div class="text-2xl md:text-3xl font-extrabold text-[#C4A265] mb-1">{{ b.stat }}</div>
                        <div class="text-[11px] sm:text-xs text-white/60 leading-snug">{{ b.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── EMR vs EHR explainer ────────────────────────────── -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">{{ tr('الأساسيات', 'Foundations') }}</p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833]">
                        {{ tr('ما هو نظام EMR / EHR؟', 'What is an EMR / EHR system?') }}
                    </h2>
                </div>
                <div class="grid md:grid-cols-2 gap-5 animate-fade-up">
                    <div class="rounded-2xl bg-gradient-to-br from-light-blue/40 to-white border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-[#1B4F72] mb-3">EMR — Electronic Medical Record</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ tr(
                                'السجل الطبي الإلكتروني داخل العيادة الواحدة. يحتوي على كل بيانات المرضى، الفحوصات، الوصفات، التشخيصات، والملاحظات السريرية. هدفه الأساسي: تنظيم الرعاية داخل عيادتك.',
                                'A clinic\'s internal electronic patient record. Contains patient data, exams, prescriptions, diagnoses, and clinical notes. Primary goal: organizing care inside your clinic.'
                            ) }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-[#C4A265]/10 to-white border border-[#C4A265]/20 p-6">
                        <h3 class="text-lg font-bold text-[#C4A265] mb-3">EHR — Electronic Health Record</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ tr(
                                'السجل الصحي الإلكتروني الشامل. يربط عدة منشآت طبية (عيادات، مختبرات، مستشفيات) ويتيح تبادل بيانات المريض بأمان. هدفه: استمرارية الرعاية عبر مقدمي الخدمة المختلفين.',
                                'A comprehensive electronic health record that links multiple entities (clinics, labs, hospitals) and allows secure patient-data exchange. Goal: continuity of care across providers.'
                            ) }}
                        </p>
                    </div>
                </div>
                <p class="text-center text-sm text-gray-500 mt-6 italic">
                    {{ tr('Doctorato يدعم النموذجين في منصة واحدة', 'Doctorato supports both models in one platform') }}
                </p>
            </div>
        </section>

        <!-- ─── Features grid ───────────────────────────────────── -->
        <section class="py-16 sm:py-20 bg-gradient-to-b from-light-blue/40 to-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 sm:mb-14 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">{{ tr('المميزات', 'Features') }}</p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ tr('ماذا يقدم EMR من Doctorato؟', 'What does Doctorato\'s EMR offer?') }}
                    </h2>
                    <p class="text-sm sm:text-base text-gray-500 max-w-2xl mx-auto">
                        {{ tr('نظام سجلات طبية إلكترونية كامل المزايا، مع كل الأدوات اللي طبيب اليوم محتاجها.', 'A full-featured electronic medical record system with everything today\'s doctor needs.') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 animate-stagger">
                    <article
                        v-for="(f, idx) in features"
                        :key="idx"
                        class="group relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500"
                    >
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#C4A265]/15 to-[#1B4F72]/10 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                            {{ f.icon }}
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-[#1C2833] mb-2 group-hover:text-[#1B4F72] transition-colors">{{ f.title }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ f.body }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- ─── Cross-link to specialty modules ─────────────────── -->
        <section class="py-16 sm:py-20 bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">{{ tr('التخصصات', 'Specialties') }}</p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ tr('EMR متخصص حسب نوع عيادتك', 'EMR tailored to your specialty') }}
                    </h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                    <Link href="/dental" class="group relative bg-gradient-to-br from-[#C4A265]/10 to-white border border-gray-100 rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition text-center">
                        <div class="text-3xl mb-2">🦷</div>
                        <p class="text-sm font-bold text-[#1C2833]">{{ tr('عيادات الأسنان', 'Dental clinics') }}</p>
                        <p class="text-[11px] text-gray-500 mt-1 group-hover:text-[#C4A265] transition">{{ tr('شوف التفاصيل ←', 'Learn more →') }}</p>
                    </Link>
                    <Link href="/dermatology" class="group relative bg-gradient-to-br from-rose-100/40 to-white border border-gray-100 rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition text-center">
                        <div class="text-3xl mb-2">✨</div>
                        <p class="text-sm font-bold text-[#1C2833]">{{ tr('جلدية وتجميل', 'Derma + Cosmetic') }}</p>
                        <p class="text-[11px] text-gray-500 mt-1 group-hover:text-[#C4A265] transition">{{ tr('شوف التفاصيل ←', 'Learn more →') }}</p>
                    </Link>
                    <Link href="/pediatrics" class="group relative bg-gradient-to-br from-sky-100/40 to-white border border-gray-100 rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition text-center">
                        <div class="text-3xl mb-2">👶</div>
                        <p class="text-sm font-bold text-[#1C2833]">{{ tr('طب الأطفال', 'Pediatrics') }}</p>
                        <p class="text-[11px] text-gray-500 mt-1 group-hover:text-[#C4A265] transition">{{ tr('شوف التفاصيل ←', 'Learn more →') }}</p>
                    </Link>
                    <Link href="/telemedicine" class="group relative bg-gradient-to-br from-emerald-100/40 to-white border border-gray-100 rounded-2xl p-5 hover:shadow-lg hover:-translate-y-1 transition text-center">
                        <div class="text-3xl mb-2">📹</div>
                        <p class="text-sm font-bold text-[#1C2833]">{{ tr('استشارات أون لاين', 'Telemedicine') }}</p>
                        <p class="text-[11px] text-gray-500 mt-1 group-hover:text-[#C4A265] transition">{{ tr('شوف التفاصيل ←', 'Learn more →') }}</p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ─── FAQ ─────────────────────────────────────────────── -->
        <section class="py-16 sm:py-20 bg-light-blue/30">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">{{ tr('أسئلة شائعة', 'FAQ') }}</p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833]">
                        {{ tr('أسئلة شائعة عن أنظمة EMR / EHR', 'Common questions about EMR / EHR') }}
                    </h2>
                </div>
                <div class="space-y-3 animate-fade-up">
                    <div
                        v-for="(f, idx) in faqs"
                        :key="idx"
                        class="bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all"
                        :class="openFaq === idx ? 'shadow-lg ring-1 ring-[#C4A265]/30' : 'shadow-sm'"
                    >
                        <button
                            type="button"
                            @click="toggleFaq(idx)"
                            class="w-full flex items-center justify-between gap-3 px-5 py-4 sm:px-6 sm:py-5 text-start"
                            :aria-expanded="openFaq === idx"
                        >
                            <span class="text-sm sm:text-base font-bold text-[#1C2833] leading-snug flex-1">{{ f.q }}</span>
                            <span
                                class="shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center transition-all"
                                :class="openFaq === idx ? 'bg-[#C4A265] text-white rotate-45' : 'bg-gray-100 text-gray-500'"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </button>
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 max-h-0"
                            enter-to-class="opacity-100 max-h-96"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 max-h-96"
                            leave-to-class="opacity-0 max-h-0"
                        >
                            <div v-if="openFaq === idx" class="px-5 pb-5 sm:px-6 sm:pb-6 -mt-1">
                                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-4"></div>
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ f.a }}</p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── Final CTA ───────────────────────────────────────── -->
        <section class="py-16 sm:py-20 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-4">
                    {{ tr('جاهز تنقل عيادتك للسحابة؟', 'Ready to take your clinic to the cloud?') }}
                </h2>
                <p class="text-sm sm:text-base text-white/60 mb-8 max-w-xl mx-auto leading-relaxed">
                    {{ tr('14 يوم مجاناً، بدون بطاقة ائتمان، إعداد كامل لفريقك، ودعم بالعربي.', '14 days free, no credit card, full team onboarding, Arabic-first support.') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <Link href="/demo" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold shadow-xl hover:-translate-y-0.5 transition">
                        {{ tr('احجز عرضاً مجانياً', 'Book a free demo') }}
                    </Link>
                    <Link href="/pricing" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full border-2 border-white/20 font-semibold hover:bg-white/10 transition">
                        {{ tr('شاهد الأسعار', 'View pricing') }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
