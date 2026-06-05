<script setup>
/**
 * /for/{scenario} — use-case landing pages.
 *
 * Captures buyer-intent queries that name the facility shape ("نظام عيادة
 * طبيب فرد" / "best system for polyclinic"). Each scenario maps to a
 * plan recommendation + tailored copy so the visitor sees the plan that
 * actually fits their setup, instead of having to read the whole pricing
 * matrix and guess.
 *
 * Schema: WebPage + Recommendation (HowTo + FAQ) — qualifies for Google
 * answer-box and rich-results SERP cards.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import RelatedPages from '@/Components/RelatedPages.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    scenario: { type: String, required: true },
});

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');

// Each scenario maps to a recommended plan + pitch tailored to the
// facility's real pain points. Numbers come from the May 2026 pricing
// reset — change here AND in PricingPlanSeeder to stay in sync.
const SCENARIOS = {
    'solo-doctor': {
        icon: '🩺',
        h1: { ar: 'نظام إدارة عيادة طبيب فرد في مصر', en: 'Solo Doctor Clinic Management in Egypt' },
        intro: {
            ar: 'طبيب فرد بتشتغل لوحدك أو مع موظف استقبال واحد؟ دكتوراتو Starter بـ 1,990 ج.م/شهر مصمم خصيصًا لك — تركيب مجاني وتجربة 30 يوم.',
            en: 'A solo doctor running alone or with one receptionist? Doctorato Starter at 1,990 EGP/month is built for you — free setup, 30-day trial.',
        },
        plan: 'Starter', price: '1,990', setup: 'مجاني',
        painPoints: [
            { ar: 'نظام معقد بتكاليف 5,000+ ج.م شهريًا — أكثر مما تحتاج', en: 'Complex systems at 5,000+ EGP/month — more than you need' },
            { ar: 'فقدان ملفات المرضى بسبب الـ Excel أو الكشف الورقي', en: 'Patient records lost to Excel sheets or paper files' },
            { ar: 'لا وقت لتنظيم المواعيد + تذكير المرضى', en: 'No time to organize appointments + remind patients' },
            { ar: 'صعوبة الالتزام بمتطلبات مصلحة الضرائب', en: 'Hard to comply with Tax Authority e-receipt requirements' },
        ],
        wins: [
            { ar: 'EMR كامل + قوالب لتخصصك', en: 'Full EMR + your specialty templates' },
            { ar: 'حجوزات + تذكيرات تلقائية SMS', en: 'Appointments + automatic SMS reminders' },
            { ar: 'فواتير + إيصال إلكتروني معتمد', en: 'Billing + certified e-receipt' },
            { ar: 'بوابة موبايل للمرضى', en: 'Patient mobile portal' },
        ],
    },
    'small-clinic': {
        icon: '🏥',
        h1: { ar: 'نظام إدارة عيادة صغيرة في مصر', en: 'Small Clinic Management in Egypt' },
        intro: {
            ar: '2-5 أطباء وفريق صغير؟ دكتوراتو Growth بـ 3,990 ج.م/شهر + 700 ج.م لكل طبيب إضافي = نموذج عادل ومرن مع نموك.',
            en: '2-5 doctors and a tight team? Doctorato Growth at 3,990 EGP/month + 700 EGP per extra doctor = fair, flexible scaling.',
        },
        plan: 'Growth', price: '3,990', setup: 'مجاني',
        painPoints: [
            { ar: 'تنسيق المواعيد بين الأطباء = فوضى يومية', en: 'Coordinating schedules across doctors = daily chaos' },
            { ar: 'تأمين Bupa و GIG بدون نظام = ضياع موافقات', en: 'Bupa/GIG insurance without a system = lost approvals' },
            { ar: 'صعوبة تتبع أداء كل طبيب وإيراداته', en: 'Hard to track per-doctor performance and revenue' },
            { ar: 'WhatsApp شخصي = اختلاط حياة عمل بشخصية', en: 'Personal WhatsApp = work/life boundaries blurred' },
        ],
        wins: [
            { ar: 'تقارير أداء لكل طبيب على حدة', en: 'Per-doctor performance reports' },
            { ar: 'WhatsApp Business احترافي للعيادة', en: 'Professional WhatsApp Business for the clinic' },
            { ar: 'CRM طبي + متابعة المرضى', en: 'Medical CRM + patient follow-up' },
            { ar: 'دعم الأدوار (مدير، استقبال، محاسب)', en: 'Role support (admin, reception, accountant)' },
        ],
    },
    'polyclinic': {
        icon: '🏢',
        h1: { ar: 'نظام إدارة مركز طبي / بولي كلينك في مصر', en: 'Polyclinic / Multi-Specialty Center System in Egypt' },
        intro: {
            ar: 'مركز متعدد التخصصات بـ 6+ أطباء وحركة مرضى عالية؟ دكتوراتو Professional بـ 6,990 ج.م/شهر — كل التكاملات مجانية + WhatsApp/Telemedicine/Lab.',
            en: 'A multi-specialty center with 6+ doctors and high patient volume? Doctorato Professional at 6,990 EGP/month — all integrations free + WhatsApp/Telemedicine/Lab.',
        },
        plan: 'Professional', price: '6,990', setup: '7,500 ج.م اختياري (white-glove)',
        painPoints: [
            { ar: 'تنسيق 6+ تخصصات في نظام واحد = صعب', en: 'Coordinating 6+ specialties in one system = hard' },
            { ar: 'تكامل التأمين الكامل (Bupa, GIG, AXA, MetLife)', en: 'Full insurance integration (Bupa, GIG, AXA, MetLife)' },
            { ar: 'تقارير مالية مفصّلة لكل قسم', en: 'Detailed financial reports per department' },
            { ar: 'إدارة المخزون والصيدلية', en: 'Inventory and pharmacy management' },
        ],
        wins: [
            { ar: '3 تخصصات + قوالب جاهزة', en: '3 specialties + ready templates' },
            { ar: 'تكامل تأمين كامل مع كل شركات السوق', en: 'Full insurance integration with all market players' },
            { ar: 'وحدة HR ورواتب', en: 'HR + payroll module' },
            { ar: 'API + Webhooks للتكاملات', en: 'API + Webhooks for integrations' },
        ],
    },
    'hospital': {
        icon: '🏨',
        h1: { ar: 'نظام إدارة مستشفى في مصر', en: 'Hospital Management System in Egypt' },
        intro: {
            ar: 'مستشفى بفروع وأقسام متعددة؟ دكتوراتو Enterprise بتسعير مخصص — HL7/FHIR، SLA مكتوب، ومدير حساب مخصص.',
            en: 'A hospital with multiple branches and departments? Doctorato Enterprise with custom pricing — HL7/FHIR, signed SLA, dedicated account manager.',
        },
        plan: 'Enterprise', price: 'مخصص', setup: 'حسب الحجم',
        painPoints: [
            { ar: 'تكامل HL7/FHIR مع أجهزة المستشفى', en: 'HL7/FHIR integration with hospital devices' },
            { ar: 'إدارة فروع متعددة وأقسام مختلفة', en: 'Multi-branch and multi-department management' },
            { ar: 'White-label وعلامة تجارية مخصصة', en: 'White-label and custom branding' },
            { ar: 'دعم 24/7 وSLA رسمي', en: '24/7 support and signed SLA' },
        ],
        wins: [
            { ar: 'فروع وأطباء بلا حدود', en: 'Unlimited branches and doctors' },
            { ar: 'SLA 99.9% مكتوب', en: 'Signed 99.9% SLA' },
            { ar: 'مدير حساب مخصص', en: 'Dedicated account manager' },
            { ar: 'On-premise اختياري', en: 'Optional on-premise deployment' },
        ],
    },
    'dental-clinic': {
        icon: '🦷',
        h1: { ar: 'نظام إدارة عيادة أسنان في مصر', en: 'Dental Clinic Management System in Egypt' },
        intro: {
            ar: 'عيادة أسنان بحاجة لـ Tooth Chart تفاعلي وخطط علاج بصرية؟ دكتوراتو يوفر أقوى وحدة طب أسنان في السوق المصري.',
            en: 'A dental clinic that needs an interactive tooth chart and visual treatment plans? Doctorato offers the strongest dental module in the Egyptian market.',
        },
        plan: 'Growth', price: '3,990', setup: 'مجاني',
        painPoints: [
            { ar: 'رسم خطط علاج الأسنان على الورق', en: 'Drawing treatment plans on paper' },
            { ar: 'صعوبة تتبع زيارات الأسنان المتعددة', en: 'Tracking multiple dental visits is hard' },
            { ar: 'فقدان معايير FDI / Periodontal', en: 'Missing FDI / Periodontal standards' },
            { ar: 'لا متابعة لخطط التركيبات والتقويم', en: 'No tracking for prosthetics/orthodontics plans' },
        ],
        wins: [
            { ar: 'Tooth Chart تفاعلي بمعيار FDI', en: 'Interactive FDI-standard tooth chart' },
            { ar: 'Periodontal chart كامل', en: 'Full periodontal chart' },
            { ar: 'خطط علاج بصرية + موافقة المريض', en: 'Visual treatment plans + patient consent' },
            { ar: 'تكامل مختبرات الأسنان', en: 'Dental lab integration' },
        ],
    },
    'derma-clinic': {
        icon: '🧴',
        h1: { ar: 'نظام إدارة عيادة جلدية وتجميل في مصر', en: 'Dermatology & Cosmetic Clinic System in Egypt' },
        intro: {
            ar: 'عيادة جلدية وتجميل ليزر؟ دكتوراتو يوفر Before/After Photo Tracker، إدارة جلسات الليزر، ومتابعة المرضى عبر WhatsApp.',
            en: 'A dermatology and laser clinic? Doctorato offers Before/After photo tracker, laser session management, and WhatsApp patient follow-up.',
        },
        plan: 'Growth', price: '3,990', setup: 'مجاني',
        painPoints: [
            { ar: 'صور Before/After متناثرة في الموبايل', en: 'Before/After photos scattered across phones' },
            { ar: 'حسابات جلسات الليزر = صعبة', en: 'Laser session accounting = hard' },
            { ar: 'متابعة نتائج علاج بدون نظام', en: 'Following up treatment results with no system' },
            { ar: 'تسويق العيادة عبر WhatsApp', en: 'Marketing the clinic via WhatsApp' },
        ],
        wins: [
            { ar: 'Before/After Photo Tracker آمن', en: 'Secure Before/After photo tracker' },
            { ar: 'إدارة جلسات الليزر والباقات', en: 'Laser sessions and packages management' },
            { ar: 'حملات WhatsApp تسويقية', en: 'WhatsApp marketing campaigns' },
            { ar: 'نظام نقاط ولاء للمرضى', en: 'Patient loyalty points system' },
        ],
    },
    'psychiatry-clinic': {
        icon: '🧠',
        h1: { ar: 'نظام إدارة عيادة الطب النفسي والعصبي في مصر', en: 'Psychiatry & Neurology Clinic System in Egypt' },
        intro: {
            ar: 'عيادة نفسية أو عصبية تحتاج فحص MSE، موازين رقمية، وسجل أدوية مراقَبة؟ دكتوراتو يقدم منظومة سرّية بتشفير end-to-end + بوابة مريض آمنة.',
            en: 'A psychiatry or neurology clinic that needs MSE, digital scales, and a controlled-substance log? Doctorato provides an end-to-end-encrypted confidential workflow + secure patient portal.',
        },
        plan: 'Growth', price: '3,990', setup: 'مجاني',
        painPoints: [
            { ar: 'موازين نفسية ورقية تأخذ ساعات للتقييم', en: 'Paper scales take hours to score' },
            { ar: 'سجل الأدوية الخاضعة للرقابة بطريقة يدوية', en: 'Controlled substances tracked manually' },
            { ar: 'فقد الجلسات السرّية أو تسريبها', en: 'Confidential sessions lost or leaked' },
            { ar: 'صعوبة متابعة المزاج وتطور الحالة', en: 'Hard to track mood and case progression' },
        ],
        wins: [
            { ar: 'موازين PHQ-9 / GAD-7 / MMSE / MoCA رقمية', en: 'Digital PHQ-9 / GAD-7 / MMSE / MoCA' },
            { ar: 'سجل الأدوية المراقَبة مع توقيع رقمي', en: 'Controlled-substance log with digital signature' },
            { ar: 'قوالب SOAP وتشفير end-to-end', en: 'SOAP templates with end-to-end encryption' },
            { ar: 'تتبّع المزاج وتنبيهات الأزمات', en: 'Mood tracking with crisis alerts' },
        ],
    },
};

const data = computed(() => SCENARIOS[props.scenario] || SCENARIOS['small-clinic']);

const seoTitle = computed(() => isAr.value
    ? `${data.value.h1.ar} | باقة ${data.value.plan} من ${data.value.price} ج.م — دكتوراتو`
    : `${data.value.h1.en} | ${data.value.plan} plan from ${data.value.price} EGP — Doctorato`);

const seoDesc = computed(() => data.value.intro[isAr.value ? 'ar' : 'en']);

const useCaseJsonLd = computed(() => [
    {
        '@context': 'https://schema.org',
        '@type': 'Service',
        name: data.value.h1[isAr.value ? 'ar' : 'en'],
        description: seoDesc.value,
        provider: { '@type': 'Organization', name: 'Doctorato', url: 'https://doctorato.com' },
        areaServed: { '@type': 'Country', name: 'Egypt' },
        offers: {
            '@type': 'Offer',
            name: data.value.plan,
            price: data.value.price === 'مخصص' ? null : data.value.price.replace(',', ''),
            priceCurrency: 'EGP',
            url: 'https://doctorato.com/pricing',
        },
    },
    {
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: [
            {
                '@type': 'Question',
                name: isAr.value ? `ما الباقة المناسبة لـ ${data.value.h1.ar}؟` : `Which plan fits ${data.value.h1.en}?`,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: isAr.value
                        ? `الباقة الموصى بها هي ${data.value.plan} بسعر ${data.value.price} ج.م شهريًا، مع رسوم تركيب ${data.value.setup}.`
                        : `The recommended plan is ${data.value.plan} at ${data.value.price} EGP/month, with setup ${data.value.setup}.`,
                },
            },
        ],
    },
]);
</script>

<template>
    <SeoHead
        :title="seoTitle"
        :description="seoDesc"
        :json-ld="useCaseJsonLd"
        :breadcrumbs="[
            { name: isAr ? 'الرئيسية' : 'Home', url: '/' },
            { name: data.h1[isAr ? 'ar' : 'en'], url: `/for/${scenario}` },
        ]"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-28 pb-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="text-5xl mb-4">{{ data.icon }}</div>
                <h1 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
                    {{ data.h1[isAr ? 'ar' : 'en'] }}
                </h1>
                <p class="text-base sm:text-lg text-white/70 max-w-2xl mx-auto leading-relaxed mb-8">
                    {{ data.intro[isAr ? 'ar' : 'en'] }}
                </p>

                <!-- Plan recommendation card -->
                <div class="inline-block bg-gradient-to-br from-[#C4A265]/20 to-white/5 backdrop-blur-sm rounded-2xl p-5 ring-1 ring-[#C4A265]/30 mb-6">
                    <div class="text-[10px] sm:text-xs text-[#C4A265] font-bold uppercase tracking-widest mb-1">
                        {{ isAr ? 'الباقة الموصى بها' : 'Recommended plan' }}
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-white mb-1">{{ data.plan }}</div>
                    <div class="text-sm text-white/70">
                        {{ data.price === 'مخصص' ? (isAr ? 'تسعير مخصص — تواصل معنا' : 'Custom pricing — contact sales') : `${data.price} ج.م/${isAr ? 'شهر' : 'mo'}` }}
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <Link
                        href="/demo"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl font-bold bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-xl hover:-translate-y-0.5 transition-all"
                    >
                        {{ isAr ? 'احجز عرضك المجاني' : 'Book your free demo' }}
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </Link>
                    <Link
                        href="/pricing"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl font-bold border-2 border-white/30 text-white hover:bg-white/10 transition-all"
                    >
                        {{ isAr ? 'كل الباقات' : 'All plans' }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- Pain points + Wins -->
        <section class="py-16 sm:py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                    <!-- Pain points -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 shadow-sm ring-1 ring-rose-100">
                        <h2 class="text-lg sm:text-xl font-extrabold text-[#1C2833] mb-5 flex items-center gap-2">
                            <span class="inline-flex w-8 h-8 rounded-lg bg-rose-100 items-center justify-center">⚠️</span>
                            {{ isAr ? 'تحديات شغلك اليومي' : 'Your daily challenges' }}
                        </h2>
                        <ul class="space-y-3">
                            <li v-for="(p, i) in data.painPoints" :key="i" class="flex items-start gap-2 text-sm text-gray-700 leading-relaxed">
                                <svg class="mt-0.5 w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>{{ p[isAr ? 'ar' : 'en'] }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Wins with Doctorato -->
                    <div class="bg-gradient-to-br from-emerald-50 to-white rounded-3xl p-6 sm:p-7 shadow-sm ring-1 ring-emerald-100">
                        <h2 class="text-lg sm:text-xl font-extrabold text-[#1C2833] mb-5 flex items-center gap-2">
                            <span class="inline-flex w-8 h-8 rounded-lg bg-emerald-100 items-center justify-center">✨</span>
                            {{ isAr ? 'الحل مع دكتوراتو' : 'The Doctorato solution' }}
                        </h2>
                        <ul class="space-y-3">
                            <li v-for="(w, i) in data.wins" :key="i" class="flex items-start gap-2 text-sm text-gray-700 leading-relaxed">
                                <svg class="mt-0.5 w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ w[isAr ? 'ar' : 'en'] }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related use-cases — keep visitors comparing across facility shapes -->
        <RelatedPages section="usecase" :exclude="`/for/${scenario}`" />

        <!-- Final CTA -->
        <section class="py-16 sm:py-20 bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-4">
                    {{ isAr ? 'جرّب باقة ' + data.plan + ' مجانًا' : `Try ${data.plan} free for 30 days` }}
                </h2>
                <p class="text-white/70 mb-8 max-w-xl mx-auto">
                    {{ isAr ? 'تجربة كاملة 30 يوم — بدون بطاقة ائتمان وبدون التزام.' : 'Full 30-day trial — no credit card, no commitment.' }}
                </p>
                <Link
                    href="/demo"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold text-[#1B4F72] bg-white hover:bg-[#C4A265] hover:text-white transition-all shadow-xl"
                >
                    {{ isAr ? 'احجز عرضك المجاني' : 'Book your free demo' }}
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </Link>
            </div>
        </section>
    </MainLayout>
</template>
