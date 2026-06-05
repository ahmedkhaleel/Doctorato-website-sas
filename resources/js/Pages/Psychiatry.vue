<script setup>
/**
 * Psychiatry & Neurology — specialty module page.
 *
 * Built on the same skeleton as Obstetrics.vue / Pediatrics.vue so the
 * site reads as one product across modules. Variants per page:
 *   - Hero accent: indigo (#6366F1) — calm + clinical, matches the
 *     contemplative tone of mental-health work.
 *   - Secondary: cyan (#06B6D4) — neurology / brain-imaging side.
 *   - JSON-LD positions the module as Psychiatry + Neurology so search
 *     engines index both queries.
 *   - Workflow uses the assessment → treatment → follow-up arc that
 *     matches the real clinical journey.
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
        icon: 'M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4',
        title: tr('فحص الحالة العقلية (MSE)', 'Mental Status Examination (MSE)'),
        desc: tr('قوالب MSE منظمة: المظهر، السلوك، المزاج، التفكير، الإدراك، البصيرة، والحكم — جاهزة لكل جلسة.', 'Structured MSE templates: appearance, behavior, mood, thought, perception, insight, and judgment — ready for every session.'),
        color: '#6366F1',
    },
    {
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        title: tr('الاختبارات النفسية المعيارية', 'Standardized Psychometric Scales'),
        desc: tr('PHQ-9 للاكتئاب، GAD-7 للقلق، MMSE و MoCA للإدراك، PCL-5 للصدمة — كلها رقمية مع حساب النتيجة تلقائياً.', 'PHQ-9 for depression, GAD-7 for anxiety, MMSE & MoCA for cognition, PCL-5 for trauma — all digital with automatic scoring.'),
        color: '#7C3AED',
    },
    {
        icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        title: tr('الأدوية الخاضعة للرقابة', 'Controlled Substance Prescriptions'),
        desc: tr('سجل رسمي للأدوية المخدّرة والمنوّمات (Schedule II/III)، تتبّع الجرعات، توقيع رقمي، وتقارير جاهزة لوزارة الصحة.', 'Formal Schedule II/III narcotic and sedative log, dose tracking, digital signature, and Ministry-of-Health-ready reports.'),
        color: '#DC2626',
    },
    {
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
        title: tr('الرسم الدماغي (EEG / EMG)', 'EEG / EMG Studies'),
        desc: tr('رفع تسجيلات الرسم الدماغي والعصبي، إرفاق التقارير، ربطها بزيارات المريض، وتقارير قابلة للمشاركة مع الزملاء.', 'Upload EEG and EMG recordings, attach reports, link to patient visits, and share with referring colleagues.'),
        color: '#06B6D4',
    },
    {
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        title: tr('تتبّع المزاج والأعراض', 'Mood & Symptom Tracking'),
        desc: tr('سجل يومي للمريض عبر بوابته الخاصة، رسم بياني لتطور المزاج، تنبيه فوري عند الإشارات الحرجة (انتحار، تدهور).', 'Daily patient log via portal, mood trend chart, instant alert on red flags (suicide ideation, deterioration).'),
        color: '#10B981',
    },
    {
        icon: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        title: tr('قوالب SOAP للجلسات', 'SOAP Notes for Therapy Sessions'),
        desc: tr('قالب SOAP جاهز (Subjective, Objective, Assessment, Plan)، توقيع المعالج، تأمين سرّية الجلسات بالتشفير.', 'Ready SOAP template (Subjective, Objective, Assessment, Plan), therapist signature, encrypted session confidentiality.'),
        color: '#F59E0B',
    },
    {
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        title: tr('تذكيرات تأخذ الدواء عبر WhatsApp', 'Medication Reminders via WhatsApp'),
        desc: tr('تذكير المرضى بمواعيد الدواء، السماح بتأكيد التناول، رصد الانقطاع وإشعار الطبيب — بدون تطبيق إضافي.', 'Remind patients of medication times, allow confirmation, detect missed doses, notify the doctor — no extra app required.'),
        color: '#25D366',
    },
    {
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        title: tr('بوابة المريض الآمنة', 'Secure Patient Portal'),
        desc: tr('وصول المريض لجلساته، الأدوية، الموازين النفسية، ومواعيده القادمة — مع تشفير end-to-end وسرّية كاملة.', 'Patient access to sessions, medications, psychometric scales, and upcoming visits — end-to-end encrypted, fully confidential.'),
        color: '#C4A265',
    },
]);

const workflow = computed(() => [
    {
        step: '01',
        title: tr('التقييم الأوّلي', 'Initial assessment'),
        desc: tr('جلسة استقبال شاملة: السيرة المرضية، MSE، الموازين النفسية الأولية (PHQ-9، GAD-7)، وتشخيص أوّلي.', 'Comprehensive intake: history, MSE, baseline scales (PHQ-9, GAD-7), and initial diagnosis.'),
    },
    {
        step: '02',
        title: tr('خطة العلاج والمتابعة الدوائية', 'Treatment plan & medication management'),
        desc: tr('اختيار العلاج النفسي أو الدوائي، توثيق وصفات الأدوية المراقَبة، جدولة جلسات المتابعة، ومتابعة الآثار الجانبية.', 'Select psychotherapy or pharmacotherapy, document controlled prescriptions, schedule follow-ups, monitor side effects.'),
    },
    {
        step: '03',
        title: tr('المتابعة وقياس النتائج', 'Follow-up & outcome measurement'),
        desc: tr('إعادة قياس الموازين النفسية، رسم تطور المزاج، تعديل الخطة بناءً على البيانات، وتقارير دورية للأهل (للقاصرين).', 'Re-measure scales, chart mood progression, adjust the plan with data, and periodic family reports (for minors).'),
    },
]);

const stats = computed(() => [
    { value: tr('+12', '+12'),   label: tr('ميزان نفسي رقمي', 'Digital psychometric scales'), suffix: '' },
    { value: tr('SOAP', 'SOAP'), label: tr('قوالب جلسات منظمة', 'Structured session templates'), suffix: '' },
    { value: tr('100', '100'),   label: tr('سرّية المريض', 'Patient confidentiality'), suffix: '%' },
    { value: tr('24/7', '24/7'), label: tr('تنبيهات أزمة', 'Crisis alerts'), suffix: '' },
]);

const ldJson = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: 'Doctorato Psychiatry & Neurology',
    applicationCategory: 'HealthApplication',
    applicationSubCategory: 'Psychiatry & Neurology Practice Management',
    operatingSystem: 'Web, iOS, Android',
    description: tr(
        'نظام عيادات الطب النفسي والعصبي: فحص الحالة العقلية MSE، الموازين النفسية (PHQ-9, GAD-7, MMSE, MoCA)، تسجيل الأدوية الخاضعة للرقابة، رفع تسجيلات EEG/EMG، تتبّع المزاج عبر بوابة المريض، وتذكيرات WhatsApp للأدوية.',
        'Psychiatry and neurology clinic software: Mental Status Examination (MSE), psychometric scales (PHQ-9, GAD-7, MMSE, MoCA), controlled-substance logging, EEG/EMG uploads, patient-portal mood tracking, and WhatsApp medication reminders.'
    ),
    inLanguage: ['ar', 'en'],
    audience: { '@type': 'MedicalAudience', audienceType: 'Psychiatrists, Neurologists, Clinical Psychologists' },
    featureList: [
        'Mental Status Examination (MSE) templates',
        'PHQ-9, GAD-7, MMSE, MoCA, PCL-5 psychometric scales',
        'Controlled substance prescription tracking',
        'EEG / EMG study uploads',
        'Mood tracking with crisis alerts',
        'SOAP session notes',
        'WhatsApp medication reminders',
        'Encrypted patient portal',
    ],
    offers: {
        '@type': 'AggregateOffer',
        priceCurrency: 'EGP',
        lowPrice: '1990',
        highPrice: '6990',
    },
    aggregateRating: {
        '@type': 'AggregateRating',
        ratingValue: '4.9',
        reviewCount: '24',
        bestRating: '5',
    },
}));
</script>

<template>
    <SeoHead
        :title="tr('برنامج عيادات الطب النفسي والعصبي | MSE والموازين النفسية — دكتوراتو', 'Psychiatry & Neurology Clinic Software | MSE & Psychometric Scales — Doctorato')"
        :description="tr('أفضل نظام عيادات نفسية وعصبية في مصر: فحص الحالة العقلية، PHQ-9 و GAD-7 و MMSE و MoCA رقمية، سجل الأدوية المخدّرة، رفع EEG/EMG، تتبّع المزاج، وبوابة مريض آمنة. جرّب مجاناً 14 يوم.', 'The leading psychiatry & neurology clinic software in Egypt: MSE, digital PHQ-9, GAD-7, MMSE, MoCA, controlled-substance log, EEG/EMG uploads, mood tracking, and secure patient portal. Free 14-day trial.')"
        :json-ld="ldJson"
        :breadcrumbs="[
            { name: tr('الرئيسية', 'Home'), url: '/' },
            { name: tr('الطب النفسي والعصبي', 'Psychiatry & Neurology'), url: '/psychiatry' },
        ]"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative py-32 lg:py-40 overflow-hidden bg-[#070F1B]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#1E1B4B] to-[#070F1B]"></div>
            <div class="absolute inset-0">
                <div class="absolute top-[-15%] start-[10%] w-[600px] h-[600px] bg-[#6366F1]/45 rounded-full blur-[140px] animate-aurora"></div>
                <div class="absolute bottom-[-20%] end-[5%] w-[700px] h-[700px] bg-[#C4A265]/25 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>
                <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#06B6D4]/20 rounded-full blur-[120px] animate-aurora" style="animation-delay: -12s"></div>
            </div>
            <div
                class="absolute inset-0 opacity-[0.06] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.6) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.6) 1px, transparent 1px); background-size: 60px 60px;"
            ></div>

            <!-- Floating brain-wave dots -->
            <div class="absolute top-[15%] start-[8%] w-2 h-2 rounded-full bg-[#6366F1] shadow-[0_0_20px_#6366F1] animate-float opacity-60"></div>
            <div class="absolute top-[30%] end-[12%] w-1.5 h-1.5 rounded-full bg-[#06B6D4] shadow-[0_0_15px_#06B6D4] animate-float opacity-50" style="animation-delay: -2s"></div>
            <div class="absolute bottom-[25%] start-[18%] w-1.5 h-1.5 rounded-full bg-[#C4A265] shadow-[0_0_15px_#C4A265] animate-float opacity-40" style="animation-delay: -4s"></div>
            <div class="absolute bottom-[15%] end-[20%] w-2 h-2 rounded-full bg-[#A5B4FC] shadow-[0_0_20px_#A5B4FC] animate-float opacity-50" style="animation-delay: -1s"></div>

            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>

            <div class="relative container mx-auto px-4 text-center">
                <div class="relative inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-8 border border-white/10 animate-fade-up overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden rounded-full pointer-events-none">
                        <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                    <span class="relative flex w-2 h-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#6366F1] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#6366F1]"></span>
                    </span>
                    <span class="relative text-sm font-semibold text-white tracking-wide">{{ tr('وحدة جديدة', 'New Module') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white via-white to-[#6366F1] bg-clip-text text-transparent drop-shadow-[0_0_30px_rgba(99,102,241,0.3)]">
                        {{ tr('قسم الطب النفسي والعصبي المتكامل', 'Complete Psychiatry & Neurology Module') }}
                    </span>
                </h1>

                <div class="flex items-center justify-center gap-3 mb-6 animate-fade-up">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-[#6366F1]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[#6366F1] animate-pulse"></div>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-[#6366F1]"></div>
                </div>

                <p class="text-lg md:text-xl text-white/70 max-w-3xl mx-auto mb-10 animate-fade-up leading-relaxed">
                    {{ tr('فحص الحالة العقلية MSE، الموازين النفسية الرقمية (PHQ-9, GAD-7, MMSE, MoCA)، سجل الأدوية الخاضعة للرقابة، رفع تسجيلات EEG/EMG، وتتبّع المزاج عبر بوابة مريض آمنة بتشفير end-to-end.', 'Mental Status Examination, digital psychometric scales (PHQ-9, GAD-7, MMSE, MoCA), controlled substance logging, EEG/EMG uploads, and mood tracking via an end-to-end-encrypted patient portal.') }}
                </p>

                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link href="/demo" class="group relative px-8 py-4 bg-gradient-to-br from-[#6366F1] to-[#4F46E5] text-white font-bold rounded-full transition-all duration-500 hover:shadow-2xl hover:shadow-[#6366F1]/40 hover:-translate-y-1 overflow-hidden">
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
                    <a href="#psy-features" class="group px-8 py-4 bg-white/[0.06] hover:bg-white/[0.12] text-white font-bold rounded-full backdrop-blur-md transition-all duration-500 border border-white/15 hover:border-white/30 hover:-translate-y-0.5">
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
                        <div class="text-2xl md:text-3xl font-black text-[#6366F1] tabular-nums">{{ s.value }}{{ s.suffix }}</div>
                        <div class="text-[11px] text-white/60 mt-1">{{ s.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features grid -->
        <section id="psy-features" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#6366F1]/10 border border-[#6366F1]/20 mb-5">
                        <svg class="w-4 h-4 text-[#6366F1]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-sm font-semibold text-[#6366F1]">{{ tr('مميزات الوحدة', 'Module features') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كل ما يحتاجه طبيب النفسية والعصبية', 'Everything a psychiatry & neurology clinic needs') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto">
                        {{ tr('8 أدوات سرّية ومتكاملة — من التقييم الأوّلي إلى تتبّع تطور الحالة على المدى الطويل', '8 confidential, integrated tools — from initial assessment to long-term outcome tracking') }}
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

        <!-- Workflow / How it works — assessment → treatment → follow-up -->
        <section class="py-20 bg-[#F8FAFC]">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كيف يعمل في 3 خطوات', 'How it works in 3 steps') }}
                    </h2>
                    <p class="text-gray-500 max-w-xl mx-auto">
                        {{ tr('من التقييم الأوّلي إلى متابعة النتائج — منظومة سرّية متكاملة', 'From initial assessment to outcome tracking — one confidential, connected workflow') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 animate-stagger">
                    <div v-for="(w, i) in workflow" :key="i" class="relative group">
                        <div v-if="i < 2" class="hidden md:block absolute top-12 start-full w-full h-px bg-gradient-to-r from-[#6366F1]/40 to-transparent z-0"></div>
                        <div class="relative bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#6366F1] to-[#4F46E5] text-white flex items-center justify-center font-black text-lg mb-4 shadow-lg shadow-[#6366F1]/25">
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
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-[#6366F1] to-[#4F46E5] flex items-center justify-center mb-6 shadow-xl shadow-[#6366F1]/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">
                    {{ tr('ابدأ بإدارة عيادتك النفسية والعصبية باحترافية', 'Start running your psychiatry & neurology clinic professionally') }}
                </h2>
                <p class="text-white/70 mb-8 max-w-2xl mx-auto">
                    {{ tr('جرّب وحدة الطب النفسي والعصبي مجاناً لمدة 14 يوم — بدون بطاقة ائتمان أو التزام', 'Try the psychiatry & neurology module free for 14 days — no credit card or commitment') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/demo" class="px-8 py-3.5 bg-gradient-to-br from-[#6366F1] to-[#4F46E5] text-white font-bold rounded-full transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#6366F1]/30">
                        {{ tr('اطلب عرضاً تجريبياً مجانياً', 'Request a free demo') }}
                    </Link>
                    <Link href="/pricing" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm border border-white/20 transition-all">
                        {{ tr('تعرّف على الأسعار', 'View pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <RelatedModules current="psychiatry" />
    </MainLayout>
</template>
