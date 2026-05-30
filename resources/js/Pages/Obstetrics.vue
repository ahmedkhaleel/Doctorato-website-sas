<script setup>
/**
 * Obstetrics & Gynecology — specialty module page.
 *
 * Built on the same skeleton as Pediatrics.vue / Dermatology.vue so
 * the site reads as one product across modules. Variants per page:
 *   - Hero accent colour: rose-pink (#E91E63) — clinical but warm,
 *     consistent with how the women's-health module signs itself.
 *   - JSON-LD positions the module as Obstetrics + Gynecology so
 *     search engines index both queries.
 *   - Workflow uses the antenatal → delivery → follow-up arc that
 *     matches the real clinical journey rather than the generic
 *     'register / examine / bill' triad.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import RelatedModules from '@/Components/RelatedModules.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { locale } = useI18n();
useScrollAnimation();

const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

const features = computed(() => [
    {
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        title: tr('متابعة الحمل بمعايير WHO', 'WHO-standard Antenatal Care'),
        desc: tr('بروتوكول متابعة الحمل الكامل: 8 زيارات وفق منظمة الصحة العالمية، حساب EDD التلقائي، تنبيهات الفحوصات بحسب الأسبوع.', 'Complete antenatal protocol: 8 visits per WHO guidelines, automatic EDD calculation, test reminders by gestational week.'),
        color: '#E91E63',
    },
    {
        icon: 'M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z',
        title: tr('سونار التوليد وتتبّع نمو الجنين', 'Obstetric Ultrasound & Fetal Growth'),
        desc: tr('تسجيل قياسات السونار (BPD, HC, AC, FL)، رسم بياني لنمو الجنين مقابل المعدل، أرشيف صور السونار مع كل زيارة.', 'Record ultrasound measurements (BPD, HC, AC, FL), fetal growth chart vs. norms, image archive linked to each visit.'),
        color: '#9C27B0',
    },
    {
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        title: tr('مسحة عنق الرحم (Pap Smear)', 'Pap Smear & Cervical Screening'),
        desc: tr('جدول الكشف المبكر لسرطان عنق الرحم، نتائج HPV، تذكيرات سنوية تلقائية، وتقارير جاهزة للمختبر.', 'Cervical cancer screening schedule, HPV results, automatic annual reminders, lab-ready reports.'),
        color: '#1B4F72',
    },
    {
        icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        title: tr('تنظيم الأسرة', 'Family Planning'),
        desc: tr('استشارة وسائل منع الحمل، متابعة الـ IUD، تذكيرات تحديث الوصفة، وتقييم الحالات الخاصة.', 'Contraception counseling, IUD follow-up, prescription renewal reminders, and special-case assessment.'),
        color: '#10B981',
    },
    {
        icon: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z',
        title: tr('أرشيف السونار والصور الطبية', 'Imaging Archive'),
        desc: tr('رفع صور السونار 2D/3D/4D وربطها بزيارة الحمل، مشاركة آمنة مع الأم عبر بوابة المريضة.', 'Upload 2D/3D/4D ultrasound images linked to each antenatal visit; share securely with the patient via her portal.'),
        color: '#F59E0B',
    },
    {
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        title: tr('تذكيرات الأم عبر WhatsApp', 'Mother Reminders via WhatsApp'),
        desc: tr('تذكير المواعيد، التحاليل المطلوبة لكل أسبوع حمل، ونصائح موسمية عبر WhatsApp و SMS بدون تطبيق إضافي.', 'Appointment reminders, week-specific lab requests, and seasonal tips via WhatsApp and SMS — no extra app required.'),
        color: '#25D366',
    },
    {
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        title: tr('بوابة المريضة الخاصة', 'Patient Portal for Expecting Mothers'),
        desc: tr('عمر الحمل، عدّاد الموعد المتوقّع للولادة، نتائج السونار والتحاليل، ومواعيد المتابعة القادمة — في مكان واحد.', 'Gestational age, EDD countdown, ultrasound + lab results, and upcoming visits — all in one private portal.'),
        color: '#C4A265',
    },
    {
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        title: tr('تقارير الخصوبة والتبويض', 'Fertility & Ovulation Reports'),
        desc: tr('متابعة التبويض بالسونار، تحديد أنسب أوقات الحمل، تقييم تأخّر الإنجاب، وتقارير قابلة للمشاركة مع الزوج.', 'Ultrasound-tracked ovulation, optimal conception windows, infertility assessment, and shareable reports for partners.'),
        color: '#EC4899',
    },
]);

const workflow = computed(() => [
    {
        step: '01',
        title: tr('فتح ملف الحمل', 'Open the pregnancy file'),
        desc: tr('سجّل تاريخ آخر دورة وحساب EDD تلقائياً، أضف التاريخ الطبي والولادات السابقة، واربط الملف بـ Pap Smear والفحوصات السابقة.', 'Log LMP and auto-calculate EDD, add medical and obstetric history, link the file to prior Pap smears and labs.'),
    },
    {
        step: '02',
        title: tr('متابعة الحمل والسونار', 'Antenatal follow-up & ultrasound'),
        desc: tr('سجّل القياسات الحيوية ومقاسات السونار، شاهد مخطط نمو الجنين يُحدّث فورياً، وأرسل التذكيرات للأم تلقائياً.', 'Log vitals and ultrasound measurements, watch the fetal growth chart update live, and auto-send the next reminder to the mother.'),
    },
    {
        step: '03',
        title: tr('الولادة والمتابعة', 'Delivery & postnatal'),
        desc: tr('سجّل تفاصيل الولادة (طبيعية/قيصرية)، أصدر الشهادة الطبية للمولود، وحدد جدول متابعة الـ 6 أسابيع بعد الولادة.', 'Record delivery details (vaginal / C-section), issue the birth medical note, and schedule the 6-week postnatal review.'),
    },
]);

const stats = computed(() => [
    { value: tr('8', '8'),     label: tr('زيارات WHO القياسية', 'WHO standard visits'), suffix: '' },
    { value: tr('+40', '+40'), label: tr('قياس سونار مدعوم', 'Ultrasound parameters'),  suffix: '' },
    { value: tr('100', '100'), label: tr('بوابة المريضة', 'Patient portal access'),     suffix: '%' },
    { value: tr('24/7', '24/7'), label: tr('تذكيرات WhatsApp', 'WhatsApp reminders'),   suffix: '' },
]);

const ldJson = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: 'Doctorato Obstetrics & Gynecology',
    applicationCategory: 'HealthApplication',
    applicationSubCategory: 'OB/GYN Practice Management',
    operatingSystem: 'Web, iOS, Android',
    description: tr(
        'نظام عيادات النساء والتوليد: متابعة الحمل بمعايير WHO، سونار التوليد، مسحة عنق الرحم، تنظيم الأسرة، بوابة المريضة، وتذكيرات WhatsApp.',
        'OB/GYN clinic software: WHO antenatal care, obstetric ultrasound, Pap smear scheduling, family planning, patient portal, and WhatsApp reminders.'
    ),
    inLanguage: ['ar', 'en'],
    audience: { '@type': 'MedicalAudience', audienceType: 'Obstetricians, Gynecologists' },
    featureList: [
        'WHO 8-visit antenatal protocol',
        'Obstetric ultrasound (BPD, HC, AC, FL) with growth charts',
        'Pap smear and cervical cancer screening',
        'Contraception and IUD follow-up',
        'Fertility and ovulation tracking',
        'WhatsApp and SMS mother reminders',
        'Patient portal with EDD countdown',
    ],
    offers: {
        '@type': 'AggregateOffer',
        priceCurrency: 'SAR',
        lowPrice: '297',
        highPrice: '1200',
    },
    aggregateRating: {
        '@type': 'AggregateRating',
        ratingValue: '4.9',
        reviewCount: '28',
        bestRating: '5',
    },
}));
</script>

<template>
    <SeoHead
        :title="tr('برنامج عيادات النساء والتوليد | متابعة الحمل والسونار — دكتوراتو', 'OB/GYN Clinic Software | Antenatal Care & Ultrasound — Doctorato')"
        :description="tr('أفضل نظام عيادات نساء وتوليد في الخليج: متابعة الحمل بمعايير WHO، سونار التوليد مع منحنيات نمو الجنين، مسحة عنق الرحم، تنظيم الأسرة، متابعة التبويض، بوابة مريضة. جرّب مجاناً 14 يوم.', 'The leading OB/GYN clinic software in the Middle East: WHO antenatal care, obstetric ultrasound with fetal growth curves, Pap smear scheduling, family planning, ovulation tracking, patient portal. Free 14-day trial.')"
        :json-ld="ldJson"
        :breadcrumbs="[
            { name: tr('الرئيسية', 'Home'), url: '/' },
            { name: tr('النساء والتوليد', 'Obstetrics & Gynecology'), url: '/obstetrics' },
        ]"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative py-32 lg:py-40 overflow-hidden bg-[#070F1B]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#070F1B]"></div>
            <div class="absolute inset-0">
                <div class="absolute top-[-15%] start-[10%] w-[600px] h-[600px] bg-[#E91E63]/45 rounded-full blur-[140px] animate-aurora"></div>
                <div class="absolute bottom-[-20%] end-[5%] w-[700px] h-[700px] bg-[#C4A265]/25 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>
                <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#9C27B0]/20 rounded-full blur-[120px] animate-aurora" style="animation-delay: -12s"></div>
            </div>
            <div
                class="absolute inset-0 opacity-[0.06] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.6) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.6) 1px, transparent 1px); background-size: 60px 60px;"
            ></div>
            <svg class="absolute inset-0 w-full h-full opacity-[0.035] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="ob-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#ob-hex)"/>
            </svg>
            <div class="absolute inset-0 opacity-[0.04] mix-blend-overlay pointer-events-none" style="background-image: url(&quot;data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E&quot;);"></div>

            <!-- Floating dots — rose/violet for OB/GYN signature -->
            <div class="absolute top-[15%] start-[8%] w-2 h-2 rounded-full bg-[#E91E63] shadow-[0_0_20px_#E91E63] animate-float opacity-60"></div>
            <div class="absolute top-[30%] end-[12%] w-1.5 h-1.5 rounded-full bg-[#9C27B0] shadow-[0_0_15px_#9C27B0] animate-float opacity-50" style="animation-delay: -2s"></div>
            <div class="absolute bottom-[25%] start-[18%] w-1.5 h-1.5 rounded-full bg-[#C4A265] shadow-[0_0_15px_#C4A265] animate-float opacity-40" style="animation-delay: -4s"></div>
            <div class="absolute bottom-[15%] end-[20%] w-2 h-2 rounded-full bg-[#F8BBD0] shadow-[0_0_20px_#F8BBD0] animate-float opacity-50" style="animation-delay: -1s"></div>

            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>

            <div class="relative container mx-auto px-4 text-center">
                <div class="relative inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-8 border border-white/10 animate-fade-up overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden rounded-full pointer-events-none">
                        <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                    <span class="relative flex w-2 h-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#E91E63] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#E91E63]"></span>
                    </span>
                    <span class="relative text-sm font-semibold text-white tracking-wide">{{ tr('وحدة جديدة', 'New Module') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white via-white to-[#E91E63] bg-clip-text text-transparent drop-shadow-[0_0_30px_rgba(233,30,99,0.3)]">
                        {{ tr('قسم النساء والتوليد المتكامل', 'Complete Obstetrics & Gynecology Module') }}
                    </span>
                </h1>

                <div class="flex items-center justify-center gap-3 mb-6 animate-fade-up">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-[#E91E63]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[#E91E63] animate-pulse"></div>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-[#E91E63]"></div>
                </div>

                <p class="text-lg md:text-xl text-white/70 max-w-3xl mx-auto mb-10 animate-fade-up leading-relaxed">
                    {{ tr('متابعة الحمل بمعايير WHO، سونار التوليد مع منحنيات نمو الجنين، مسحة عنق الرحم، تنظيم الأسرة، ومتابعة التبويض — مع بوابة مريضة وتذكيرات WhatsApp تلقائية.', 'WHO-standard antenatal care, obstetric ultrasound with fetal growth curves, Pap smear scheduling, family planning, and ovulation tracking — with a private patient portal and automated WhatsApp reminders.') }}
                </p>

                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link href="/demo" class="group relative px-8 py-4 bg-gradient-to-br from-[#E91E63] to-[#C2185B] text-white font-bold rounded-full transition-all duration-500 hover:shadow-2xl hover:shadow-[#E91E63]/40 hover:-translate-y-1 overflow-hidden">
                        <div class="absolute inset-0 overflow-hidden rounded-full pointer-events-none">
                            <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/30 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"></div>
                        </div>
                        <span class="relative flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ tr('اطلب عرضاً تجريبياً', 'Request a demo') }}
                        </span>
                    </Link>
                    <a href="#ob-features" class="group px-8 py-4 bg-white/[0.06] hover:bg-white/[0.12] text-white font-bold rounded-full backdrop-blur-md transition-all duration-500 border border-white/15 hover:border-white/30 hover:-translate-y-0.5">
                        <span class="flex items-center gap-2">
                            {{ tr('استكشف المميزات', 'Explore features') }}
                            <svg class="w-5 h-5 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </span>
                    </a>
                </div>

                <!-- Mini stats -->
                <div class="mt-14 grid grid-cols-2 md:grid-cols-4 gap-4 max-w-3xl mx-auto animate-stagger">
                    <div v-for="(s, i) in stats" :key="i" class="bg-white/[0.06] backdrop-blur-sm rounded-2xl border border-white/10 p-4 text-center">
                        <div class="text-2xl md:text-3xl font-black text-[#E91E63] tabular-nums">{{ s.value }}{{ s.suffix }}</div>
                        <div class="text-[11px] text-white/60 mt-1">{{ s.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features grid -->
        <section id="ob-features" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#E91E63]/10 border border-[#E91E63]/20 mb-5">
                        <svg class="w-4 h-4 text-[#E91E63]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-sm font-semibold text-[#E91E63]">{{ tr('مميزات الوحدة', 'Module features') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كل ما يحتاجه طبيب النساء والتوليد', 'Everything an OB/GYN clinic needs') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto">
                        {{ tr('8 أدوات متكاملة لمرافقة المرأة في كل مرحلة — من تنظيم الأسرة إلى ما بعد الولادة', '8 integrated tools for every stage of the womans health journey — from family planning to postnatal care') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
                    <div
                        v-for="(f, i) in features"
                        :key="i"
                        class="group relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-transparent hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden"
                    >
                        <div class="absolute top-0 inset-x-0 h-1 scale-x-0 group-hover:scale-x-100 origin-center transition-transform duration-500" :style="{ background: f.color }"></div>
                        <div
                            class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500"
                            :style="{ background: f.color + '12', color: f.color }"
                        >
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="f.icon"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-[#1C2833] mb-2 group-hover:text-[#1B4F72] transition-colors">{{ f.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Workflow / How it works — antenatal → delivery → postnatal -->
        <section class="py-20 bg-[#F8FAFC]">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كيف يعمل في 3 خطوات', 'How it works in 3 steps') }}
                    </h2>
                    <p class="text-gray-500 max-w-xl mx-auto">
                        {{ tr('من فتح ملف الحمل إلى متابعة ما بعد الولادة — في منظومة واحدة', 'From opening the pregnancy file to postnatal follow-up — one connected workflow') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 animate-stagger">
                    <div v-for="(w, i) in workflow" :key="i" class="relative group">
                        <div v-if="i < 2" class="hidden md:block absolute top-12 start-full w-full h-px bg-gradient-to-r from-[#E91E63]/40 to-transparent z-0"></div>
                        <div class="relative bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#E91E63] to-[#C2185B] text-white flex items-center justify-center font-black text-lg mb-4 shadow-lg shadow-[#E91E63]/25">
                                {{ w.step }}
                            </div>
                            <h3 class="text-lg font-bold text-[#1C2833] mb-2">{{ w.title }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ w.desc }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 bg-gradient-to-br from-[#0D2B45] to-[#1B4F72] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-[#E91E63] to-[#C2185B] flex items-center justify-center mb-6 shadow-xl shadow-[#E91E63]/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">
                    {{ tr('ابدأ بإدارة عيادة النساء والتوليد باحترافية', 'Start running your OB/GYN clinic professionally') }}
                </h2>
                <p class="text-white/70 mb-8 max-w-2xl mx-auto">
                    {{ tr('جرّب وحدة النساء والتوليد مجاناً لمدة 14 يوم — بدون بطاقة ائتمان أو التزام', 'Try the OB/GYN module free for 14 days — no credit card or commitment required') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/demo" class="px-8 py-3.5 bg-gradient-to-br from-[#E91E63] to-[#C2185B] text-white font-bold rounded-full transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#E91E63]/30">
                        {{ tr('اطلب عرضاً تجريبياً مجانياً', 'Request a free demo') }}
                    </Link>
                    <Link href="/pricing" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm border border-white/20 transition-all">
                        {{ tr('تعرّف على الأسعار', 'View pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <RelatedModules current="obstetrics" />
    </MainLayout>
</template>
