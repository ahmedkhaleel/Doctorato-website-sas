<script setup>
/**
 * Dermatology & Cosmetics landing page.
 *
 * Mirrors the structure of the Pediatrics page — hero → features
 * grid → 3-step workflow → CTA. Accent color is rose→fuchsia so it
 * reads distinctly from Dental (gold) and Pediatrics (pink/rose).
 *
 * Feature list tuned to what aesthetic and derma clinics actually
 * need: skin charting, before/after photos, session packages (laser,
 * filler, botox runs), product inventory, consent forms.
 */
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
        icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        title: tr('السجل الجلدي الشامل', 'Full Dermatology Record'),
        desc: tr('تقييم نوع البشرة، تاريخ الحساسية، العلاجات السابقة، خريطة جلدية للبقع والشامات — مع قياسات تلقائية على الصور قبل/بعد.', 'Skin-type assessment, allergy history, prior treatments, a body map for moles and lesions — with auto-measurements on before/after photos.'),
        color: '#EC4899',
    },
    {
        icon: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z',
        title: tr('صور قبل وبعد مقارنة', 'Before / After Comparison'),
        desc: tr('معرض صور احترافي لكل مريض، مع سلايدر مقارنة تفاعلي، إضاءة موحّدة، وقفل تعديل بعد التوقيع — بيحمي طبيبك قانونياً ويشوّق المرضى الجدد.', 'Pro photo gallery per patient with interactive slider compare, uniform lighting, and post-signature edit lock — medically defensible, marketing-ready.'),
        color: '#A855F7',
    },
    {
        icon: 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z',
        title: tr('باقات الجلسات والخطط', 'Session Packages & Plans'),
        desc: tr('بيع جلسات متعددة (6 ليزر، 3 فيلر) وتتبّع استهلاكها تلقائياً. كل جلسة مخصومة من الرصيد، مع تذكير بآخر جلسة ومواعيد المتابعة.', 'Sell bundled sessions (6-laser, 3-filler) and track usage automatically. Each visit debits the package balance, with last-session alerts and follow-up reminders.'),
        color: '#D946EF',
    },
    {
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        title: tr('مخزون الأدوية والمنتجات', 'Product & Drug Inventory'),
        desc: tr('تتبّع كل قارورة فيلر ومنتج تجميلي: رقم التشغيلة، تاريخ الصلاحية، الكمية المتبقية، الربط بالجلسة. تحذير تلقائي قبل نفاد المخزون.', 'Track every filler vial and cosmetic product: batch number, expiry date, remaining quantity, linked to sessions. Auto-alerts before stock runs out.'),
        color: '#F472B6',
    },
    {
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        title: tr('موافقات رقمية معتمدة', 'Digital Consent Forms'),
        desc: tr('قوالب موافقة جاهزة لكل إجراء (بوتوكس، فيلر، ليزر، تقشير). المريض يوقّع على التابلت، التوقيع يُختم ويُحفظ في الملف — آمن قانونياً.', 'Ready-made consent templates per procedure (Botox, filler, laser, peels). Tablet signature gets sealed and filed — legally defensible record.'),
        color: '#E11D48',
    },
    {
        icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        title: tr('بوابة المريض بصورها', 'Patient Portal w/ Photos'),
        desc: tr('بوابة خاصة لكل عميلة/عميل: يشوف صور قبل/بعد جلساته، يحجز المتابعة، يشتري منتجات العناية بالبشرة، ويتابع رصيد الباقة.', 'Private portal per client: view before/after photos of their sessions, book follow-ups, buy skincare products, and check package balance.'),
        color: '#C4A265',
    },
    {
        icon: 'M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
        title: tr('تحليلات الإيرادات بالإجراء', 'Revenue Analytics per Procedure'),
        desc: tr('تقارير دقيقة: أعلى الإجراءات مبيعاً، متوسط تذكرة العميل، نسبة العائدين، هامش الربح لكل منتج. تعرف إيه اللي بيكسّبك والعكس.', 'Precise reports: top-selling procedures, average ticket size, return rate, margin per product. Know what earns and what drains.'),
        color: '#8B5CF6',
    },
    {
        icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        title: tr('تذكيرات ذكية وحملات', 'Smart Reminders & Campaigns'),
        desc: tr('تذكير تلقائي بالمتابعة عبر واتساب، وعروض موسمية (عيد الأم، رمضان)، وحملات إعادة تفعيل للعميلات اللي ماجوش من 3 شهور.', 'Auto follow-up reminders via WhatsApp, seasonal promos (Mother\'s Day, Ramadan), and re-engagement campaigns for clients who haven\'t visited in 3 months.'),
        color: '#FB923C',
    },
    {
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        title: tr('وصفات وخطط علاج', 'Rx & Treatment Plans'),
        desc: tr('كتابة روشتات ومنتجات العناية، ربطها بالتشخيص، طباعة أو إرسال واتساب. مكتبة بروتوكولات لحب الشباب، الهالات السوداء، التصبّغات، المسامات.', 'Write prescriptions and skincare plans, link to diagnosis, print or WhatsApp to patient. Protocol library for acne, dark circles, pigmentation, pores.'),
        color: '#1B4F72',
    },
]);

const workflow = computed(() => [
    {
        step: '01',
        title: tr('الاستشارة والصور', 'Consultation & photos'),
        desc: tr('افحص البشرة، سجّل التقييم على الخريطة الجلدية، التقط صور قبل/بعد موحّدة. المريض يوقّع رقمياً على الاستشارة والإجراءات المحتملة.', 'Assess the skin, tag the body map, capture standardized before photos. Patient digitally signs the consultation and potential procedures.'),
    },
    {
        step: '02',
        title: tr('الإجراء والباقة', 'Procedure & package'),
        desc: tr('اختر الإجراء من المكتبة (ليزر/فيلر/بوتكس)، اخصم من مخزون المنتج تلقائياً، واستخدم جلسة من الباقة. صورة ما بعد تُضاف لحظياً.', 'Pick the procedure from the library (laser/filler/Botox), auto-debit product inventory, and consume a session from the package. After photo attaches live.'),
    },
    {
        step: '03',
        title: tr('المتابعة والمبيعات', 'Follow-up & upsell'),
        desc: tr('فاتورة فورية، بيع منتجات العناية المرتبطة، موعد المتابعة أوتوماتيك، وتذكير للعميلة قبل موعدها — مع عرض صور تقدّمها في البوابة.', 'Instant invoice, upsell linked skincare products, auto-schedule follow-up, WhatsApp reminder — and a progress photo tour in the client portal.'),
    },
]);

const stats = computed(() => [
    { value: tr('+80', '+80'), label: tr('بروتوكول تجميلي', 'Aesthetic protocols'), suffix: '' },
    { value: tr('HD', 'HD'), label: tr('صور قبل/بعد', 'Before / after photos'), suffix: '' },
    { value: tr('+200', '+200'), label: tr('منتج تجميلي مدعوم', 'Supported products'), suffix: '' },
    { value: tr('100', '100'), label: tr('موافقات رقمية', 'Digital consent rate'), suffix: '%' },
]);
</script>

<template>
    <SeoHead
        :title="tr('نظام إدارة عيادات الجلدية والتجميل', 'Dermatology & Cosmetic Clinic System')"
        :description="tr('نظام متخصص لعيادات الجلدية والتجميل: سجلات جلدية، صور قبل/بعد، باقات جلسات، موافقات رقمية، مخزون منتجات، وتحليلات بالإجراء.', 'Specialized clinic system for dermatology and aesthetics: skin records, before/after photos, session packages, digital consents, product inventory, and revenue analytics per procedure.')"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative py-32 lg:py-40 overflow-hidden bg-[#070F1B]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#1A0B2E] via-[#0D1B3E] to-[#070F1B]"></div>
            <div class="absolute inset-0">
                <div class="absolute top-[-15%] start-[10%] w-[600px] h-[600px] bg-[#EC4899]/40 rounded-full blur-[140px] animate-aurora"></div>
                <div class="absolute bottom-[-20%] end-[5%] w-[700px] h-[700px] bg-[#A855F7]/25 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>
                <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#C4A265]/15 rounded-full blur-[120px] animate-aurora" style="animation-delay: -12s"></div>
            </div>
            <div
                class="absolute inset-0 opacity-[0.06] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(236,72,153,0.5) 1px, transparent 1px), linear-gradient(-45deg, rgba(168,85,247,0.5) 1px, transparent 1px); background-size: 60px 60px;"
            ></div>
            <svg class="absolute inset-0 w-full h-full opacity-[0.035] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="derma-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#derma-hex)"/>
            </svg>

            <!-- Floating dots -->
            <div class="absolute top-[15%] start-[8%] w-2 h-2 rounded-full bg-[#EC4899] shadow-[0_0_20px_#EC4899] animate-float opacity-60"></div>
            <div class="absolute top-[30%] end-[12%] w-1.5 h-1.5 rounded-full bg-[#A855F7] shadow-[0_0_15px_#A855F7] animate-float opacity-50" style="animation-delay: -2s"></div>
            <div class="absolute bottom-[25%] start-[18%] w-1.5 h-1.5 rounded-full bg-[#C4A265] shadow-[0_0_15px_#C4A265] animate-float opacity-40" style="animation-delay: -4s"></div>
            <div class="absolute bottom-[15%] end-[20%] w-2 h-2 rounded-full bg-[#F472B6] shadow-[0_0_20px_#F472B6] animate-float opacity-50" style="animation-delay: -1s"></div>

            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#EC4899]/50 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#A855F7]/50 to-transparent"></div>

            <div class="relative container mx-auto px-4 text-center">
                <div class="relative inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-8 border border-white/10 animate-fade-up overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden rounded-full pointer-events-none">
                        <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                    <span class="relative flex w-2 h-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#EC4899] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#EC4899]"></span>
                    </span>
                    <span class="relative text-sm font-semibold text-white tracking-wide">
                        {{ tr('التخصص الأساسي · جلدية وتجميل', 'Flagship specialty · Dermatology & Cosmetics') }}
                    </span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white via-[#F9A8D4] to-[#A855F7] bg-clip-text text-transparent drop-shadow-[0_0_30px_rgba(236,72,153,0.35)]">
                        {{ tr('نظام إدارة عيادات الجلدية والتجميل', 'Dermatology & Cosmetics Clinic System') }}
                    </span>
                </h1>

                <div class="flex items-center justify-center gap-3 mb-6 animate-fade-up">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-[#EC4899]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[#EC4899] animate-pulse"></div>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-[#A855F7]"></div>
                </div>

                <p class="text-lg md:text-xl text-white/70 max-w-3xl mx-auto mb-10 animate-fade-up leading-relaxed">
                    {{ tr('صور قبل/بعد احترافية، باقات جلسات الليزر والفيلر، موافقات رقمية معتمدة، مخزون منتجات تجميلية، وتحليلات إيرادات لكل إجراء — كل ده متكامل في نظام واحد.', 'Pro before/after galleries, laser and filler session packages, legally-defensible digital consents, cosmetic product inventory, and per-procedure revenue analytics — all integrated in one system.') }}
                </p>

                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link href="/demo" class="group relative px-8 py-4 bg-gradient-to-br from-[#EC4899] to-[#A855F7] text-white font-bold rounded-full transition-all duration-500 hover:shadow-2xl hover:shadow-[#EC4899]/40 hover:-translate-y-1 overflow-hidden">
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
                    <a href="#derma-features" class="group px-8 py-4 bg-white/[0.06] hover:bg-white/[0.12] text-white font-bold rounded-full backdrop-blur-md transition-all duration-500 border border-white/15 hover:border-white/30 hover:-translate-y-0.5">
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
        <section id="derma-features" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#EC4899]/10 border border-[#EC4899]/20 mb-5">
                        <svg class="w-4 h-4 text-[#EC4899]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span class="text-sm font-semibold text-[#EC4899]">{{ tr('مميزات النظام', 'System features') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كل ما يحتاجه طبيب الجلدية والتجميل', 'Everything a derma & aesthetic doctor needs') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto">
                        {{ tr('9 أدوات متخصّصة — من الاستشارة الأولى للعميلة للمتابعة طويلة المدى', '9 specialized tools — from the first client consultation to long-term follow-up') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 animate-stagger">
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

        <!-- Workflow -->
        <section class="py-20 bg-[#F8FAFC]">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-4">
                        {{ tr('كيف يعمل في 3 خطوات', 'How it works in 3 steps') }}
                    </h2>
                    <p class="text-gray-500 max-w-xl mx-auto">
                        {{ tr('من الاستشارة للمتابعة في دقائق — بدون أوراق، كل شيء رقمي', 'From consultation to follow-up in minutes — paperless, fully digital') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 animate-stagger">
                    <div v-for="(w, i) in workflow" :key="i" class="relative group">
                        <div v-if="i < 2" class="hidden md:block absolute top-12 start-full w-full h-px bg-gradient-to-r from-[#EC4899]/40 to-transparent z-0"></div>

                        <div class="relative bg-white rounded-2xl p-6 border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#EC4899] to-[#A855F7] text-white flex items-center justify-center font-black text-lg mb-4 shadow-lg shadow-[#EC4899]/25">
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
        <section class="py-20 bg-gradient-to-br from-[#1A0B2E] via-[#1B4F72] to-[#0D2B45] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-[#EC4899] to-[#A855F7] flex items-center justify-center mb-6 shadow-xl shadow-[#EC4899]/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">
                    {{ tr('ابدأ بإدارة عيادة الجلدية باحترافية', 'Start managing your derma clinic professionally') }}
                </h2>
                <p class="text-white/70 mb-8 max-w-2xl mx-auto">
                    {{ tr('جرّب النظام مجاناً لمدة 14 يوم — بدون بطاقة ائتمان أو التزام', 'Try the system free for 14 days — no credit card, no commitment') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/demo" class="px-8 py-3.5 bg-gradient-to-br from-[#EC4899] to-[#A855F7] text-white font-bold rounded-full transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#EC4899]/30">
                        {{ tr('احجز عرضاً تجريبياً', 'Book a demo') }}
                    </Link>
                    <Link href="/pricing" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm border border-white/20 transition-all">
                        {{ tr('تعرّف على الأسعار', 'View pricing') }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
