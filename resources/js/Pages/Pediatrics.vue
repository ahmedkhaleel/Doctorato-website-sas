<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
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
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        title: tr('السجل الطبي الإلكتروني للأطفال', 'Pediatric Electronic Health Record'),
        desc: tr('سجل صحي شامل مصمم للأطفال: تطور النمو، التطعيمات، التاريخ المرضي، الحساسيات، مع مخططات النمو التفاعلية حسب معايير WHO.', 'Comprehensive child health record: growth milestones, vaccinations, medical history, allergies — with interactive WHO growth charts.'),
        color: '#3B82F6',
    },
    {
        icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        title: tr('مخططات النمو التفاعلية', 'Interactive Growth Charts'),
        desc: tr('مخططات الوزن والطول ومحيط الرأس حسب العمر والجنس (WHO + CDC). رسم بياني تلقائي مع تنبيهات عند الانحراف عن المعدل الطبيعي.', 'Weight, height, and head circumference charts by age and gender (WHO + CDC). Auto-plotted with alerts when deviating from normal ranges.'),
        color: '#10B981',
    },
    {
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        title: tr('متابعة التطعيمات', 'Vaccination Tracking'),
        desc: tr('جدول التطعيمات المصري والدولي مدمج مع تنبيهات تلقائية عبر SMS وواتساب قبل الموعد. تقرير تطعيمات جاهز للطباعة لكل طفل.', 'Egyptian and international immunization schedules integrated with auto SMS and WhatsApp reminders. Print-ready vaccination report per child.'),
        color: '#8B5CF6',
    },
    {
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        title: tr('مواعيد الأطفال الذكية', 'Smart Pediatric Scheduling'),
        desc: tr('حجز مواعيد مخصص للأطفال: فترات أطول للكشف الأول، حجز عائلي (أكثر من طفل)، تذكيرات للأم بالـ WhatsApp، وربط المواعيد بجدول التطعيمات.', 'Child-specific scheduling: longer slots for first visits, family booking (multiple children), mom WhatsApp reminders, and vaccination-linked appointments.'),
        color: '#F59E0B',
    },
    {
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        title: tr('ملف المريض العائلي', 'Family Patient Profile'),
        desc: tr('ربط ملفات الأطفال بملف الأم/الأب. عرض شجرة العائلة الطبية، التاريخ الوراثي، والحساسيات المشتركة. دفع فاتورة واحدة لزيارات متعددة.', 'Link child records to parent profiles. View family medical tree, genetic history, shared allergies. Single invoice for multiple visit bookings.'),
        color: '#EC4899',
    },
    {
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        title: tr('تقارير وإحصائيات الأطفال', 'Pediatric Reports & Analytics'),
        desc: tr('إحصائيات شاملة: أكثر الأمراض شيوعاً، معدلات التطعيم، توزيع الأعمار، إيرادات قسم الأطفال، ومعدلات المتابعة.', 'Comprehensive analytics: most common conditions, vaccination rates, age distribution, pediatric revenue, and follow-up rates.'),
        color: '#06B6D4',
    },
    {
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        title: tr('تواصل مع أولياء الأمور', 'Parent Communication Hub'),
        desc: tr('إرسال تذكيرات التطعيم، نتائج الفحوصات، ونصائح صحية موسمية عبر SMS وواتساب. بوابة أولياء أمور للحجز ومتابعة ملف الطفل.', 'Send vaccination reminders, test results, and seasonal health tips via SMS and WhatsApp. Parent portal for booking and tracking child records.'),
        color: '#C4A265',
    },
    {
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
        title: tr('بروتوكولات العلاج المعيارية', 'Standard Treatment Protocols'),
        desc: tr('بروتوكولات علاج مبنية على أحدث المراجع (Nelson, AAP) لأكثر الحالات شيوعاً: حمى، إسهال، التهابات الأذن، الربو. جرعات الأدوية تُحسب تلقائياً حسب الوزن.', 'Evidence-based treatment protocols (Nelson, AAP) for common conditions: fever, diarrhea, ear infections, asthma. Drug dosages auto-calculated by weight.'),
        color: '#1B4F72',
    },
]);

const workflow = computed(() => [
    {
        step: '01',
        title: tr('تسجيل الطفل', 'Register the child'),
        desc: tr('أضف بيانات الطفل والأم/الأب، التاريخ المرضي، الحساسيات، والتطعيمات السابقة — يُربط تلقائياً بملف العائلة.', 'Add child and parent data, medical history, allergies, and prior vaccinations — auto-linked to the family profile.'),
    },
    {
        step: '02',
        title: tr('الكشف والتقييم', 'Examination & assessment'),
        desc: tr('سجّل القياسات (وزن، طول، حرارة) وشاهد مخطط النمو يُحدّث فورياً. اختر من بروتوكولات العلاج أو اكتب وصفة مخصّصة بجرعات محسوبة.', 'Log measurements (weight, height, temp) and watch the growth chart update live. Pick from treatment protocols or write a custom prescription with weight-based doses.'),
    },
    {
        step: '03',
        title: tr('الفاتورة والمتابعة', 'Billing & follow-up'),
        desc: tr('أصدر الفاتورة فورياً (نقد / فيزا / تأمين). حدد موعد المتابعة أو التطعيم القادم — الأم تستلم تذكير تلقائي قبل الموعد.', 'Issue the invoice instantly (cash / card / insurance). Schedule the next follow-up or vaccination — the parent gets an automatic reminder.'),
    },
]);

const stats = computed(() => [
    { value: tr('+50', '+50'), label: tr('بروتوكول علاج أطفال', 'Pediatric protocols'), suffix: '' },
    { value: tr('+30', '+30'), label: tr('مخطط نمو تفاعلي', 'Interactive growth charts'), suffix: '' },
    { value: tr('100', '100'), label: tr('تطعيم مدعوم', 'Supported vaccines'), suffix: '%' },
    { value: tr('24/7', '24/7'), label: tr('بوابة أولياء الأمور', 'Parent portal access'), suffix: '' },
]);
</script>

<template>
    <SeoHead
        :title="tr('وحدة طب الأطفال', 'Pediatrics Module')"
        :description="tr('وحدة طب الأطفال المتكاملة في دكتوراتو: مخططات نمو WHO، تطعيمات، بروتوكولات علاج، ملف عائلي، تواصل مع أولياء الأمور', 'Doctorato Pediatrics Module: WHO growth charts, vaccinations, treatment protocols, family profiles, parent communication hub')"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative py-32 lg:py-40 overflow-hidden bg-[#070F1B]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#070F1B]"></div>
            <div class="absolute inset-0">
                <div class="absolute top-[-15%] start-[10%] w-[600px] h-[600px] bg-[#1B4F72]/50 rounded-full blur-[140px] animate-aurora"></div>
                <div class="absolute bottom-[-20%] end-[5%] w-[700px] h-[700px] bg-[#C4A265]/25 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>
                <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#2E86C1]/20 rounded-full blur-[120px] animate-aurora" style="animation-delay: -12s"></div>
            </div>
            <div
                class="absolute inset-0 opacity-[0.06] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.6) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.6) 1px, transparent 1px); background-size: 60px 60px;"
            ></div>
            <svg class="absolute inset-0 w-full h-full opacity-[0.035] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="ped-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#ped-hex)"/>
            </svg>
            <div class="absolute inset-0 opacity-[0.04] mix-blend-overlay pointer-events-none" style="background-image: url(&quot;data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E&quot;);"></div>

            <!-- Floating dots -->
            <div class="absolute top-[15%] start-[8%] w-2 h-2 rounded-full bg-[#EC4899] shadow-[0_0_20px_#EC4899] animate-float opacity-60"></div>
            <div class="absolute top-[30%] end-[12%] w-1.5 h-1.5 rounded-full bg-[#5DADE2] shadow-[0_0_15px_#5DADE2] animate-float opacity-50" style="animation-delay: -2s"></div>
            <div class="absolute bottom-[25%] start-[18%] w-1.5 h-1.5 rounded-full bg-[#C4A265] shadow-[0_0_15px_#C4A265] animate-float opacity-40" style="animation-delay: -4s"></div>
            <div class="absolute bottom-[15%] end-[20%] w-2 h-2 rounded-full bg-[#10B981] shadow-[0_0_20px_#10B981] animate-float opacity-50" style="animation-delay: -1s"></div>

            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>

            <div class="relative container mx-auto px-4 text-center">
                <div class="relative inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-8 border border-white/10 animate-fade-up overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden rounded-full pointer-events-none">
                        <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                    <span class="relative flex w-2 h-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#EC4899] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#EC4899]"></span>
                    </span>
                    <span class="relative text-sm font-semibold text-white tracking-wide">{{ tr('وحدة جديدة', 'New Module') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white via-white to-[#EC4899] bg-clip-text text-transparent drop-shadow-[0_0_30px_rgba(236,72,153,0.3)]">
                        {{ tr('وحدة طب الأطفال المتكاملة', 'Complete Pediatrics Module') }}
                    </span>
                </h1>

                <div class="flex items-center justify-center gap-3 mb-6 animate-fade-up">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-[#EC4899]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[#EC4899] animate-pulse"></div>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-[#EC4899]"></div>
                </div>

                <p class="text-lg md:text-xl text-white/70 max-w-3xl mx-auto mb-10 animate-fade-up leading-relaxed">
                    {{ tr('مخططات نمو WHO تفاعلية، متابعة تطعيمات مع تذكيرات تلقائية، بروتوكولات علاج بجرعات محسوبة حسب الوزن، وملف عائلي متكامل.', 'Interactive WHO growth charts, vaccination tracking with auto reminders, treatment protocols with weight-based dosing, and integrated family profiles.') }}
                </p>

                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link href="/demo" class="group relative px-8 py-4 bg-gradient-to-br from-[#EC4899] to-[#DB2777] text-white font-bold rounded-full transition-all duration-500 hover:shadow-2xl hover:shadow-[#EC4899]/40 hover:-translate-y-1 overflow-hidden">
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
                    <a href="#ped-features" class="group px-8 py-4 bg-white/[0.06] hover:bg-white/[0.12] text-white font-bold rounded-full backdrop-blur-md transition-all duration-500 border border-white/15 hover:border-white/30 hover:-translate-y-0.5">
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
                        <div class="text-2xl md:text-3xl font-black text-[#EC4899] tabular-nums">{{ s.value }}{{ s.suffix }}</div>
                        <div class="text-[11px] text-white/60 mt-1">{{ s.label }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Grid -->
        <section id="ped-features" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#EC4899]/10 border border-[#EC4899]/20 mb-5">
                        <svg class="w-4 h-4 text-[#EC4899]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span class="text-sm font-semibold text-[#EC4899]">{{ tr('مميزات الوحدة', 'Module features') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كل ما يحتاجه طبيب الأطفال', 'Everything a pediatrician needs') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto">
                        {{ tr('8 أدوات متكاملة مصممة خصيصاً لعيادات الأطفال — من التسجيل إلى المتابعة', '8 integrated tools designed specifically for pediatric clinics — from registration to follow-up') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
                    <div
                        v-for="(f, i) in features"
                        :key="i"
                        class="group relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-transparent hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden"
                    >
                        <!-- Top accent -->
                        <div class="absolute top-0 inset-x-0 h-1 scale-x-0 group-hover:scale-x-100 origin-center transition-transform duration-500" :style="{ background: f.color }"></div>

                        <!-- Icon -->
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

        <!-- Workflow / How it works -->
        <section class="py-20 bg-[#F8FAFC]">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كيف يعمل في 3 خطوات', 'How it works in 3 steps') }}
                    </h2>
                    <p class="text-gray-500 max-w-xl mx-auto">
                        {{ tr('من تسجيل الطفل إلى الفاتورة في دقائق — بدون أوراق', 'From child registration to billing in minutes — paperless') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 animate-stagger">
                    <div v-for="(w, i) in workflow" :key="i" class="relative group">
                        <!-- Connector line -->
                        <div v-if="i < 2" class="hidden md:block absolute top-12 start-full w-full h-px bg-gradient-to-r from-[#EC4899]/40 to-transparent z-0"></div>

                        <div class="relative bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#EC4899] to-[#DB2777] text-white flex items-center justify-center font-black text-lg mb-4 shadow-lg shadow-[#EC4899]/25">
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
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-[#EC4899] to-[#DB2777] flex items-center justify-center mb-6 shadow-xl shadow-[#EC4899]/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">
                    {{ tr('ابدأ بإدارة عيادة الأطفال باحترافية', 'Start managing your pediatric clinic professionally') }}
                </h2>
                <p class="text-white/70 mb-8 max-w-2xl mx-auto">
                    {{ tr('جرّب وحدة طب الأطفال مجاناً لمدة 14 يوم — بدون بطاقة ائتمان أو التزام', 'Try the Pediatrics module free for 14 days — no credit card or commitment required') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/demo" class="px-8 py-3.5 bg-gradient-to-br from-[#EC4899] to-[#DB2777] text-white font-bold rounded-full transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#EC4899]/30">
                        {{ tr('اطلب عرضاً تجريبياً مجانياً', 'Request a free demo') }}
                    </Link>
                    <Link href="/pricing" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm border border-white/20 transition-all">
                        {{ tr('تعرّف على الأسعار', 'View pricing') }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
