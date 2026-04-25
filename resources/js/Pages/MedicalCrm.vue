<script setup>
/**
 * /medical-crm — keyword-targeted landing for "CRM للعيادات الطبية"
 * + "medical CRM Middle East".
 *
 * Same SEO scaffolding as Emr.vue: long-form bilingual content,
 * FAQPage + SoftwareApplication + BreadcrumbList schema.
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
        icon: '🎯',
        title: tr('تتبع العملاء المحتملين', 'Lead tracking'),
        body: tr('كل اتصال، رسالة، أو زيارة بتدخل قاعدة بياناتك مع مصدرها (Google، Facebook، إحالة طبيب). حدّد ساخن/دافئ/بارد وتابعهم آلياً.', 'Every call, message, or visit lands in your database with its source (Google, Facebook, doctor referral). Tag hot/warm/cold and follow up automatically.'),
    },
    {
        icon: '📞',
        title: tr('إدارة المتابعات', 'Follow-up management'),
        body: tr('جدول متابعات تلقائي لكل مريض — فحص دوري، انتهاء وصفة، موعد قادم. تنبيهات للسكرتارية والطبيب على نفس الجدول.', 'Automatic follow-up schedule per patient — periodic check, prescription expiry, upcoming appointment. Same schedule alerts both reception and doctor.'),
    },
    {
        icon: '✉️',
        title: tr('حملات تسويقية موجّهة', 'Targeted campaigns'),
        body: tr('قسّم قاعدتك (مرضى تجميل، أسنان، حوامل، إلخ) وابعث حملات SMS/WhatsApp/Email موجّهة بدقة لكل شريحة.', 'Segment your base (cosmetic, dental, expectant mothers, etc.) and send precisely targeted SMS / WhatsApp / Email campaigns per segment.'),
    },
    {
        icon: '📊',
        title: tr('تسجيل نقاط ذكي (Lead Scoring)', 'Smart lead scoring'),
        body: tr('النظام يقيّم كل عميل محتمل تلقائياً بناءً على تفاعله مع رسائلك، زياراته للموقع، وردوده — ركّز جهد فريقك على الأعلى احتمالية.', 'The system scores every lead automatically based on email engagement, site visits, and replies — focus your team on the highest-likelihood prospects.'),
    },
    {
        icon: '🔁',
        title: tr('برامج الإحالة', 'Referral programs'),
        body: tr('شغّل برنامج إحالة لمرضاك الحاليين بكوبونات خصم ومكافآت تلقائية لكل مريض جديد بيجي بترشيحهم.', 'Run a referral program for existing patients with automatic discount coupons and rewards for every new referred patient.'),
    },
    {
        icon: '⏰',
        title: tr('قواعد أتمتة قابلة للتخصيص', 'Customizable automation rules'),
        body: tr('"لو مريض ما رجعش 6 شهور، ابعت رسالة تذكير". قواعد if-this-then-that بسيطة بدون كود.', '"If a patient hasn\'t returned in 6 months, send a reminder." Simple if-this-then-that rules, no code.'),
    },
]);

const stats = computed(() => [
    { stat: '3.2x', label: tr('زيادة معدل المتابعات', 'increase in follow-up rate') },
    { stat: '45%', label: tr('تحسين معدل التحويل', 'improvement in conversion') },
    { stat: '60%', label: tr('تقليل وقت السكرتارية', 'less time spent by reception') },
    { stat: '24/7', label: tr('متابعة آلية للعملاء', 'automated lead nurturing') },
]);

const faqs = computed(() => isAr.value ? [
    { q: 'إيه الفرق بين CRM طبي و CRM عادي؟', a: 'CRM الطبي مصمم لاحتياجات العيادات: ربط مع السجل الطبي، التزام بسرية البيانات الصحية، حملات حسب التشخيص والتخصص، تنبيهات الفحوصات الدورية. CRM عام مش بيفهم اللغة الطبية ولا بيتوافق مع متطلبات الخصوصية الطبية.' },
    { q: 'هل النظام بيتكامل مع الواتساب؟', a: 'نعم. تكامل كامل مع WhatsApp Business API لإرسال تذكيرات المواعيد، تأكيدات، حملات تسويقية، ومتابعات بشكل آلي.' },
    { q: 'هل أقدر أبعت رسائل SMS لقاعدة بياناتي؟', a: 'نعم، عبر تكاملات مع SMS Misr، Twilio، Unifonic، وغيرهم. تحدّد الشريحة، تكتب الرسالة، يبعتها النظام.' },
    { q: 'هل بياناتي بتفضل ملك عيادتي؟', a: 'بالكامل. أي وقت تقدر تصدّر كل بيانات المرضى والعملاء المحتملين بصيغ قياسية (CSV، Excel). دكتوراتو مجرد بنية تحتية لتنظيم بياناتك، مش مالكها.' },
    { q: 'هل بيشتغل مع عيادة لها فرع واحد؟', a: 'طبعاً. النظام مصمم لكل الأحجام — من عيادة طبيب واحد لمؤسسة بـ 100+ طبيب وعدة فروع.' },
] : [
    { q: 'How is medical CRM different from regular CRM?', a: 'A medical CRM is built for clinic needs: it ties into the medical record, complies with health-data privacy, runs campaigns by diagnosis and specialty, and triggers periodic-exam reminders. A generic CRM doesn\'t speak medical and doesn\'t meet healthcare privacy requirements.' },
    { q: 'Does it integrate with WhatsApp?', a: 'Yes. Full WhatsApp Business API integration for appointment reminders, confirmations, marketing campaigns, and automated follow-ups.' },
    { q: 'Can I send SMS to my database?', a: 'Yes — via SMS Misr, Twilio, Unifonic, and others. Choose the segment, write the message, the system sends.' },
    { q: 'Do I keep ownership of my data?', a: 'Completely. You can export all patient and lead data anytime in standard formats (CSV, Excel). Doctorato is just the infrastructure that organises your data, not its owner.' },
    { q: 'Does it work for a single-branch clinic?', a: 'Of course. The system is built for every size — from a one-doctor practice to a 100+ doctor multi-branch organisation.' },
]);

const openFaq = ref(null);
function toggleFaq(idx) { openFaq.value = openFaq.value === idx ? null : idx; }

const ldJson = computed(() => ({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'SoftwareApplication',
            '@id': 'https://doctorato.com/medical-crm#software',
            name: 'Doctorato Medical CRM',
            applicationCategory: 'BusinessApplication',
            operatingSystem: 'Web, iOS, Android',
            offers: { '@type': 'Offer', price: '958', priceCurrency: 'EGP', availability: 'https://schema.org/InStock' },
        },
        {
            '@type': 'FAQPage',
            mainEntity: faqs.value.map(f => ({ '@type': 'Question', name: f.q, acceptedAnswer: { '@type': 'Answer', text: f.a } })),
        },
        {
            '@type': 'BreadcrumbList',
            itemListElement: [
                { '@type': 'ListItem', position: 1, name: 'Doctorato', item: 'https://doctorato.com' },
                { '@type': 'ListItem', position: 2, name: tr('الخصائص', 'Features'), item: 'https://doctorato.com/features' },
                { '@type': 'ListItem', position: 3, name: tr('CRM للعيادات', 'Medical CRM'), item: 'https://doctorato.com/medical-crm' },
            ],
        },
    ],
}));
</script>

<template>
    <SeoHead
        :title="tr('CRM للعيادات الطبية | نظام إدارة علاقات المرضى — Doctorato', 'Medical CRM for Clinics | Patient Relationship Management — Doctorato')"
        :description="tr('أفضل CRM طبي للعيادات في الشرق الأوسط: تتبع العملاء المحتملين، حملات SMS/WhatsApp، متابعات تلقائية، Lead Scoring، تكامل مع السجل الطبي. زوّد تحويلاتك 3.2x.', 'The leading medical CRM for Middle East clinics: lead tracking, SMS/WhatsApp campaigns, automated follow-ups, lead scoring, EMR integration. 3.2x lift in conversion.')"
        :json-ld="ldJson"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-28 sm:pt-36 pb-16 sm:pb-20 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute -top-20 -start-20 w-96 h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-20 -end-20 w-[28rem] h-[28rem] bg-[#2471A3]/20 rounded-full blur-[120px]"></div>

            <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/15 ring-1 ring-emerald-400/30 mb-5 animate-fade-up">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="text-xs sm:text-sm text-emerald-200 font-semibold">{{ tr('CRM طبي مبني للعيادات تحديداً', 'A CRM built specifically for clinics') }}</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-5 leading-tight animate-fade-up">
                    {{ tr('نظام CRM للعيادات الطبية', 'Medical CRM for clinics') }}
                    <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[#C4A265] to-[#D4B876]">
                        {{ tr('يحوّل المرضى من مرة واحدة لعملاء دائمين', 'turns one-time patients into lifelong customers') }}
                    </span>
                </h1>
                <p class="text-base sm:text-lg md:text-xl text-white/70 max-w-3xl mx-auto leading-relaxed mb-8 animate-fade-up">
                    {{ tr(
                        'تتبّع كل عميل محتمل من أول ضغطة على إعلان لحد ما يبقى مريض دائم — حملات تسويقية، متابعات تلقائية، تذكيرات، وأدوات بناء علاقة طويلة الأمد.',
                        'Track every lead from the first ad click to becoming a loyal patient — campaigns, automated follow-ups, reminders, and long-term relationship tools.'
                    ) }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 animate-fade-up">
                    <Link href="/demo" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold text-sm shadow-xl shadow-[#C4A265]/25 hover:-translate-y-0.5 transition">
                        {{ tr('احجز عرضاً مجانياً', 'Book a free demo') }}
                    </Link>
                    <Link href="/pricing" class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full border-2 border-white/20 text-white font-semibold text-sm hover:bg-white/10 transition">
                        {{ tr('شاهد الأسعار', 'View pricing') }}
                    </Link>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 max-w-3xl mx-auto mt-12 pt-8 border-t border-white/10 animate-fade-up">
                    <div v-for="b in stats" :key="b.label" class="text-center">
                        <div class="text-2xl md:text-3xl font-extrabold text-[#C4A265] mb-1">{{ b.stat }}</div>
                        <div class="text-[11px] sm:text-xs text-white/60 leading-snug">{{ b.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-16 sm:py-20 bg-gradient-to-b from-light-blue/40 to-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 sm:mb-14 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">{{ tr('المزايا', 'Capabilities') }}</p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ tr('كل اللي محتاجه عيادتك لإدارة علاقاتها', 'Everything your clinic needs to manage its relationships') }}
                    </h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 animate-stagger">
                    <article
                        v-for="(f, idx) in features"
                        :key="idx"
                        class="group bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500"
                    >
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#C4A265]/15 to-[#1B4F72]/10 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">{{ f.icon }}</div>
                        <h3 class="text-base sm:text-lg font-bold text-[#1C2833] mb-2 group-hover:text-[#1B4F72] transition-colors">{{ f.title }}</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ f.body }}</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="py-16 sm:py-20 bg-light-blue/30">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 animate-fade-up">
                    <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">{{ tr('أسئلة شائعة', 'FAQ') }}</p>
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833]">
                        {{ tr('سؤال قبل ما تبدأ؟', 'A question before you start?') }}
                    </h2>
                </div>
                <div class="space-y-3 animate-fade-up">
                    <div
                        v-for="(f, idx) in faqs"
                        :key="idx"
                        class="bg-white rounded-2xl border border-gray-100 overflow-hidden transition-all"
                        :class="openFaq === idx ? 'shadow-lg ring-1 ring-[#C4A265]/30' : 'shadow-sm'"
                    >
                        <button type="button" @click="toggleFaq(idx)" class="w-full flex items-center justify-between gap-3 px-5 py-4 sm:px-6 sm:py-5 text-start" :aria-expanded="openFaq === idx">
                            <span class="text-sm sm:text-base font-bold text-[#1C2833] leading-snug flex-1">{{ f.q }}</span>
                            <span class="shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center transition-all" :class="openFaq === idx ? 'bg-[#C4A265] text-white rotate-45' : 'bg-gray-100 text-gray-500'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </span>
                        </button>
                        <Transition enter-active-class="transition-all duration-300 ease-out" enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-96" leave-active-class="transition-all duration-200 ease-in" leave-from-class="opacity-100 max-h-96" leave-to-class="opacity-0 max-h-0">
                            <div v-if="openFaq === idx" class="px-5 pb-5 sm:px-6 sm:pb-6 -mt-1">
                                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-4"></div>
                                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">{{ f.a }}</p>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-16 sm:py-20 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-4">
                    {{ tr('وقت توقّف خسارة المرضى', 'Time to stop losing patients') }}
                </h2>
                <p class="text-sm sm:text-base text-white/60 mb-8 max-w-xl mx-auto leading-relaxed">
                    {{ tr('CRM طبي + EMR + بوابات + فوترة — كله في منصة واحدة. 14 يوم تجربة مجانية.', 'Medical CRM + EMR + portals + billing — all in one platform. 14-day free trial.') }}
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
