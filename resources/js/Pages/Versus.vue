<script setup>
/**
 * /vs/{competitor} — head-to-head comparison page.
 *
 * Each competitor lives as a single data row in COMPETITORS below.
 * The page renders the matrix, FAQ, hero, and CTA all from the same
 * Vue template — so adding a new competitor is one entry in the
 * COMPETITORS object, no extra page needed.
 *
 * SEO targeting: "Doctorato vs Vezeeta" + Arabic variants. Each VS
 * page ships its own Product/Review schema + FAQPage so Google can
 * render a rich-results card on competitive SERPs.
 *
 * Honest comparisons matter for E-E-A-T — we list each competitor's
 * genuine strengths alongside the gaps. Fudging gets flagged and tanks
 * rankings on Google's "Helpful Content" axis.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    competitor: { type: String, required: true },
});

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

// Honest competitor matrix. Each `theirs` answer reflects what the
// competitor actually does as of June 2026 — verified from public
// pricing pages and feature lists.
const COMPETITORS = {
    vezeeta: {
        name: 'Vezeeta Practice',
        nameAr: 'فيزيتا برو',
        heroPunch: {
            ar: 'دكتوراتو مقابل فيزيتا برو — أيهم أنسب لعيادتك في مصر؟',
            en: 'Doctorato vs Vezeeta Practice — which one fits your Egyptian clinic?',
        },
        heroDesc: {
            ar: 'فيزيتا مشهور بشبكة المرضى، لكن دكتوراتو يقدّم نظام إدارة كامل بدون Lock-in. هذي المقارنة العملية بناءً على ما يقدمه كل نظام فعليًا.',
            en: 'Vezeeta is known for its patient network, but Doctorato offers a complete management system without lock-in. A practical comparison of what each actually delivers.',
        },
        // Truth-table of features. Order: pricing → tech → support
        pricing: { theirs: tr('غير معلن — Contact Sales', 'Hidden — Contact Sales'), ours: tr('شفاف من 1,990 ج.م/شهر', 'Transparent from 1,990 EGP/mo') },
        setup: { theirs: tr('غير معلن', 'Hidden'), ours: tr('مجاني (Starter + Growth)', 'Free (Starter + Growth)') },
        trial: { theirs: tr('Demo فقط', 'Demo only'), ours: tr('30 يوم مجاني بدون بطاقة', '30-day free, no card') },
        wins: [
            { feat: tr('شبكة مرضى Vezeeta', 'Vezeeta patient network'), ours: false, theirs: true,
              note: tr('فيزيتا قاعدة مرضى موجودة', 'Vezeeta has an existing patient base') },
            { feat: tr('استقلال عيادتك', 'Clinic independence (no lock-in)'), ours: true, theirs: false,
              note: tr('دكتوراتو يخليك مالك بياناتك', 'Doctorato keeps your data fully owned') },
            { feat: tr('تسعير شفاف معلن', 'Transparent published pricing'), ours: true, theirs: false, note: '' },
            { feat: tr('Per-doctor pricing', 'Per-doctor pricing'), ours: true, theirs: false, note: '' },
            { feat: tr('تركيب مجاني على باقتين', 'Free setup on entry plans'), ours: true, theirs: false, note: '' },
            { feat: tr('WhatsApp Business API', 'WhatsApp Business API'), ours: true, theirs: true, note: '' },
            { feat: tr('إيصال إلكتروني مصري', 'Egyptian e-receipt'), ours: true, theirs: true, note: '' },
            { feat: tr('تكامل تأمين كامل', 'Full insurance integration'), ours: true, theirs: 'basic',
              note: tr('فيزيتا أساسي، دكتوراتو Bupa+GIG+AXA+MetLife', 'Vezeeta basic; Doctorato Bupa+GIG+AXA+MetLife') },
            { feat: tr('6 بوابات منفصلة بالأدوار', '6 role-based portals'), ours: true, theirs: false, note: '' },
        ],
    },
    cliniko: {
        name: 'Cliniko',
        nameAr: 'Cliniko',
        heroPunch: {
            ar: 'دكتوراتو مقابل Cliniko — هل يناسبك نظام أسترالي في عيادتك المصرية؟',
            en: 'Doctorato vs Cliniko — does an Australian system fit your Egyptian clinic?',
        },
        heroDesc: {
            ar: 'Cliniko نظام قوي عالميًا، لكنه ما يدعمش العربية الكاملة ولا الإيصال الإلكتروني المصري. هذي مقارنة موضوعية.',
            en: 'Cliniko is a strong global system, but lacks full Arabic and Egyptian e-receipt. An objective comparison.',
        },
        pricing: { theirs: tr('$45-145/شهر (~1,400-4,500 ج.م)', '$45-145/mo (~1,400-4,500 EGP)'), ours: tr('1,990-6,990 ج.م/شهر', '1,990-6,990 EGP/mo') },
        setup: { theirs: tr('مجاني', 'Free'), ours: tr('مجاني (Starter + Growth)', 'Free (Starter + Growth)') },
        trial: { theirs: tr('30 يوم بدون بطاقة', '30-day no card'), ours: tr('30 يوم بدون بطاقة', '30-day no card') },
        wins: [
            { feat: tr('واجهة مستخدم ممتازة', 'Excellent UX'), ours: true, theirs: true, note: tr('الاتنين قويين', 'Both strong') },
            { feat: tr('دعم اللغة العربية الكامل (RTL)', 'Full Arabic (RTL) support'), ours: true, theirs: false, note: '' },
            { feat: tr('إيصال إلكتروني مصري', 'Egyptian e-receipt'), ours: true, theirs: false, note: '' },
            { feat: tr('تكامل التأمين المصري', 'Egyptian insurance integration'), ours: true, theirs: false, note: '' },
            { feat: tr('دعم بالعربي على واتساب', 'Arabic support on WhatsApp'), ours: true, theirs: false, note: '' },
            { feat: tr('Per-practitioner pricing', 'Per-practitioner pricing'), ours: true, theirs: true, note: '' },
            { feat: tr('WhatsApp Business API', 'WhatsApp Business API'), ours: true, theirs: false, note: '' },
            { feat: tr('قوالب طب أسنان متقدمة', 'Advanced dental templates'), ours: true, theirs: 'basic', note: '' },
            { feat: tr('سعر بالجنيه المصري', 'Pricing in EGP'), ours: true, theirs: false,
              note: tr('Cliniko بالدولار — متقلب', 'Cliniko in USD — exchange-rate exposed') },
        ],
    },
    clinicgateway: {
        name: 'ClinicGateway',
        nameAr: 'ClinicGateway',
        heroPunch: {
            ar: 'دكتوراتو مقابل ClinicGateway — السعر الأرخص ولا القيمة الأفضل؟',
            en: 'Doctorato vs ClinicGateway — cheapest price or best value?',
        },
        heroDesc: {
            ar: 'ClinicGateway يسوّق "بدون رسوم تركيب" كمميزة رئيسية، لكن المميزات محدودة. دكتوراتو يضمن لك تركيب مجاني + مميزات احترافية.',
            en: 'ClinicGateway markets "no setup fees" as a main feature, but its capabilities are limited. Doctorato gives you free setup AND professional capabilities.',
        },
        pricing: { theirs: tr('~2,500 ج.م/شهر باقة واحدة', '~2,500 EGP/mo single plan'), ours: tr('4 باقات من 1,990 ج.م', '4 plans from 1,990 EGP') },
        setup: { theirs: tr('مجاني', 'Free'), ours: tr('مجاني (Starter + Growth)', 'Free (Starter + Growth)') },
        trial: { theirs: tr('Demo فقط', 'Demo only'), ours: tr('30 يوم مجاني بدون بطاقة', '30-day free no card') },
        wins: [
            { feat: tr('سعر منخفض', 'Low entry price'), ours: true, theirs: true, note: tr('الاتنين تنافسيين', 'Both competitive') },
            { feat: tr('باقات متعددة (4)', 'Multiple plans (4)'), ours: true, theirs: false, note: '' },
            { feat: tr('Per-doctor pricing', 'Per-doctor pricing'), ours: true, theirs: false, note: '' },
            { feat: tr('قوالب 6 تخصصات', 'Templates for 6 specialties'), ours: true, theirs: 'basic', note: '' },
            { feat: tr('تكامل تأمين كامل', 'Full insurance integration'), ours: true, theirs: false, note: '' },
            { feat: tr('PACS للأشعة', 'PACS imaging'), ours: true, theirs: false, note: '' },
            { feat: tr('6 بوابات منفصلة', '6 role-based portals'), ours: true, theirs: false, note: '' },
            { feat: tr('API + Webhooks', 'API + Webhooks'), ours: true, theirs: false, note: '' },
            { feat: tr('White-label', 'White-label'), ours: true, theirs: false, note: '' },
        ],
    },
    drsoft: {
        name: 'Dr. Soft Egypt',
        nameAr: 'دكتور سوفت',
        heroPunch: {
            ar: 'دكتوراتو مقابل دكتور سوفت — لماذا اختار 200+ عيادة دكتوراتو؟',
            en: 'Doctorato vs Dr. Soft — why 200+ clinics chose Doctorato',
        },
        heroDesc: {
            ar: 'دكتور سوفت نظام مصري قديم. واجهة تقليدية ومميزات محدودة. دكتوراتو نظام حديث بمعايير 2026.',
            en: 'Dr. Soft is a legacy Egyptian system. Traditional UI and limited features. Doctorato is a modern 2026-standard platform.',
        },
        pricing: { theirs: tr('غير معلن', 'Hidden'), ours: tr('شفاف من 1,990 ج.م', 'Transparent from 1,990 EGP') },
        setup: { theirs: tr('غالبًا برسوم', 'Usually charged'), ours: tr('مجاني', 'Free') },
        trial: { theirs: tr('Demo فقط', 'Demo only'), ours: tr('30 يوم مجاني', '30-day free') },
        wins: [
            { feat: tr('سحابي (Cloud)', 'Cloud-based'), ours: true, theirs: false, note: '' },
            { feat: tr('تطبيق موبايل للأطباء', 'Mobile app for doctors'), ours: true, theirs: false, note: '' },
            { feat: tr('بوابة موبايل للمرضى', 'Patient mobile portal'), ours: true, theirs: false, note: '' },
            { feat: tr('WhatsApp Business API', 'WhatsApp Business API'), ours: true, theirs: false, note: '' },
            { feat: tr('Telemedicine', 'Telemedicine'), ours: true, theirs: false, note: '' },
            { feat: tr('تكامل التأمين الحديث', 'Modern insurance integration'), ours: true, theirs: 'basic', note: '' },
            { feat: tr('Audit Log كامل', 'Full Audit Log'), ours: true, theirs: false, note: '' },
            { feat: tr('تشفير AES-256', 'AES-256 encryption'), ours: true, theirs: false, note: '' },
            { feat: tr('تكامل API + Webhooks', 'API + Webhooks'), ours: true, theirs: false, note: '' },
        ],
    },
    practo: {
        name: 'Practo Ray',
        nameAr: 'Practo Ray',
        heroPunch: {
            ar: 'دكتوراتو مقابل Practo Ray — أيهما أنسب للعيادات المصرية؟',
            en: 'Doctorato vs Practo Ray — which fits Egyptian clinics better?',
        },
        heroDesc: {
            ar: 'Practo نظام هندي قوي عالميًا، لكنه ما يدعم متطلبات السوق المصري (إيصال إلكتروني، تأمين، عربي كامل).',
            en: 'Practo is a strong Indian system globally, but does not support Egyptian market requirements (e-receipt, insurance, full Arabic).',
        },
        pricing: { theirs: tr('غير شفاف، بالروبية', 'Opaque, in INR'), ours: tr('شفاف بالجنيه المصري', 'Transparent in EGP') },
        setup: { theirs: tr('برسوم', 'Charged'), ours: tr('مجاني', 'Free') },
        trial: { theirs: tr('14 يوم', '14 days'), ours: tr('30 يوم', '30 days') },
        wins: [
            { feat: tr('دعم اللغة العربية (RTL)', 'Arabic (RTL) support'), ours: true, theirs: false, note: '' },
            { feat: tr('إيصال إلكتروني مصري', 'Egyptian e-receipt'), ours: true, theirs: false, note: '' },
            { feat: tr('تكامل التأمين المصري', 'Egyptian insurance integration'), ours: true, theirs: false, note: '' },
            { feat: tr('سعر بالجنيه المصري', 'EGP-denominated pricing'), ours: true, theirs: false, note: '' },
            { feat: tr('دعم محلي بالعربي', 'Local Arabic support'), ours: true, theirs: false, note: '' },
            { feat: tr('تركيب مجاني', 'Free setup'), ours: true, theirs: false, note: '' },
            { feat: tr('30 يوم تجربة بدون بطاقة', '30-day no-card trial'), ours: true, theirs: false, note: '' },
            { feat: tr('شبكة مرضى عالمية', 'Global patient network'), ours: false, theirs: true,
              note: tr('Practo فيه شبكة هندية', 'Practo has Indian patient network') },
        ],
    },
};

const data = computed(() => COMPETITORS[props.competitor] || COMPETITORS.vezeeta);

const seoTitle = computed(() => isAr.value
    ? `دكتوراتو مقابل ${data.value.nameAr} 2026 | مقارنة عملية`
    : `Doctorato vs ${data.value.name} 2026 | Honest Comparison`);

const seoDesc = computed(() => isAr.value
    ? `مقارنة عملية بين دكتوراتو و${data.value.nameAr} للعيادات المصرية: السعر، المميزات، التركيب، التأمين، والدعم. اتخذ قرارك بناءً على الأرقام والحقائق.`
    : `An honest comparison between Doctorato and ${data.value.name} for Egyptian clinics: pricing, features, setup, insurance, and support. Decide based on facts and numbers.`);

// Product comparison + FAQ schemas for SERP rich results.
const versusJsonLd = computed(() => [
    {
        '@context': 'https://schema.org',
        '@type': 'Product',
        name: 'Doctorato',
        description: seoDesc.value,
        brand: { '@type': 'Brand', name: 'Doctorato' },
        offers: {
            '@type': 'Offer',
            price: '1990',
            priceCurrency: 'EGP',
            availability: 'https://schema.org/InStock',
        },
        review: {
            '@type': 'Review',
            reviewBody: data.value.heroDesc[isAr.value ? 'ar' : 'en'],
            author: { '@type': 'Organization', name: 'Doctorato' },
            itemReviewed: {
                '@type': 'Product',
                name: data.value.name,
            },
        },
    },
    {
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: [
            {
                '@type': 'Question',
                name: isAr.value
                    ? `هل دكتوراتو أفضل من ${data.value.nameAr}؟`
                    : `Is Doctorato better than ${data.value.name}?`,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: isAr.value
                        ? `للعيادات المصرية، دكتوراتو يقدم مميزات لا تتوفر في ${data.value.nameAr}: تركيب مجاني، تكامل مع مصلحة الضرائب، per-doctor pricing شفاف، ودعم عربي كامل.`
                        : `For Egyptian clinics, Doctorato offers features unavailable in ${data.value.name}: free setup, Egyptian Tax Authority integration, transparent per-doctor pricing, and full Arabic support.`,
                },
            },
            {
                '@type': 'Question',
                name: isAr.value
                    ? `كم سعر دكتوراتو مقارنة بـ ${data.value.nameAr}؟`
                    : `How does Doctorato pricing compare to ${data.value.name}?`,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: isAr.value
                        ? `دكتوراتو يبدأ من 1,990 ج.م/شهر بسعر معلن وشفاف. ${data.value.nameAr}: ${data.value.pricing.theirs}.`
                        : `Doctorato starts at 1,990 EGP/month with transparent published pricing. ${data.value.name}: ${data.value.pricing.theirs}.`,
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
        :json-ld="versusJsonLd"
        :breadcrumbs="[
            { name: isAr ? 'الرئيسية' : 'Home', url: '/' },
            { name: isAr ? 'مقارنات' : 'Comparisons', url: '/compare' },
            { name: `Doctorato vs ${data.name}`, url: `/vs/${competitor}` },
        ]"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-28 pb-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>
            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm ring-1 ring-[#C4A265]/30 text-[11px] font-bold uppercase tracking-widest text-[#C4A265] mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></span>
                    {{ isAr ? 'مقارنة عملية' : 'Honest comparison' }}
                </span>
                <h1 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
                    {{ data.heroPunch[isAr ? 'ar' : 'en'] }}
                </h1>
                <p class="text-base sm:text-lg text-white/70 max-w-2xl mx-auto leading-relaxed mb-8">
                    {{ data.heroDesc[isAr ? 'ar' : 'en'] }}
                </p>

                <!-- 3-key-stats card -->
                <div class="grid grid-cols-3 gap-3 max-w-3xl mx-auto mt-8">
                    <div v-for="(item, key) in { pricing: 'السعر', setup: 'التركيب', trial: 'التجربة' }" :key="key" class="bg-white/5 backdrop-blur-sm rounded-2xl p-4 ring-1 ring-white/10">
                        <div class="text-[10px] sm:text-xs text-white/50 uppercase tracking-wider mb-1.5">
                            {{ isAr ? item : (key === 'pricing' ? 'Pricing' : key === 'setup' ? 'Setup' : 'Trial') }}
                        </div>
                        <div class="text-xs sm:text-sm font-bold text-[#C4A265] mb-1">Doctorato</div>
                        <div class="text-[11px] sm:text-xs text-white/80 mb-3">{{ data[key].ours }}</div>
                        <div class="text-xs sm:text-sm font-bold text-white/40 mb-1">{{ data.name }}</div>
                        <div class="text-[11px] sm:text-xs text-white/50">{{ data[key].theirs }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comparison Matrix -->
        <section class="py-16 sm:py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ isAr ? 'مقارنة المميزات' : 'Feature-by-feature comparison' }}
                    </h2>
                    <p class="text-sm sm:text-base text-gray-500 max-w-2xl mx-auto">
                        {{ isAr ? 'كل سطر تم التحقق منه من المواقع الرسمية للنظامين — آخر تحديث يونيو 2026.' : 'Every row verified against each system\'s official pages — last updated June 2026.' }}
                    </p>
                </div>

                <div class="overflow-x-auto rounded-3xl shadow-lg ring-1 ring-gray-100 bg-white">
                    <table class="w-full min-w-[600px]">
                        <thead>
                            <tr class="bg-gradient-to-r from-gray-50 to-white">
                                <th class="text-start p-5 text-sm font-bold text-[#1C2833]">
                                    {{ isAr ? 'الميزة' : 'Feature' }}
                                </th>
                                <th class="p-5 text-center text-sm font-bold text-[#1B4F72] bg-[#1B4F72]/5">
                                    Doctorato
                                </th>
                                <th class="p-5 text-center text-sm font-bold text-gray-600">
                                    {{ data.name }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(row, idx) in data.wins" :key="idx" class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4 text-sm text-[#1C2833]">
                                    <div class="font-medium">{{ row.feat }}</div>
                                    <div v-if="row.note" class="text-xs text-gray-400 mt-1 leading-tight">{{ row.note }}</div>
                                </td>
                                <td class="px-5 py-4 text-center bg-[#1B4F72]/[0.02]">
                                    <span v-if="row.ours === true" class="inline-flex w-7 h-7 rounded-full bg-emerald-100 items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span v-else-if="row.ours === false" class="inline-flex w-7 h-7 rounded-full bg-gray-100 items-center justify-center">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase">
                                        {{ row.ours }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span v-if="row.theirs === true" class="inline-flex w-7 h-7 rounded-full bg-emerald-100 items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span v-else-if="row.theirs === false" class="inline-flex w-7 h-7 rounded-full bg-gray-100 items-center justify-center">
                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase">
                                        {{ row.theirs }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 sm:py-20 bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-4">
                    {{ isAr ? 'جرّب دكتوراتو 30 يوم مجانًا' : 'Try Doctorato free for 30 days' }}
                </h2>
                <p class="text-white/70 mb-8 max-w-xl mx-auto">
                    {{ isAr ? 'بدون بطاقة ائتمان. بدون التزام. شوف الفرق بنفسك.' : 'No credit card. No commitment. See the difference yourself.' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <Link
                        href="/demo"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl font-bold text-[#1B4F72] bg-white hover:bg-[#C4A265] hover:text-white transition-all duration-300 shadow-xl"
                    >
                        {{ isAr ? 'احجز عرضك المجاني' : 'Book your free demo' }}
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </Link>
                    <Link
                        href="/pricing"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-2xl font-bold border-2 border-white/30 hover:bg-white/10 transition-all"
                    >
                        {{ isAr ? 'شوف الأسعار' : 'View pricing' }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
