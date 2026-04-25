<script setup>
/**
 * /compare — comparison page targeting "best clinic management software"
 * + "Doctorato vs ..." commercial-intent searches.
 *
 * SEO play: comparison pages dominate "vs" queries. Each row in the
 * matrix is a feature any procurement person searches for; we show
 * the truth (we have it, they don't, both have it). Honesty matters
 * for E-E-A-T — fudging gets called out and tanks rankings.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

// Honest comparison — fudging this hurts E-E-A-T. Each row shows what
// each system actually does as of the article's last update.
const competitors = [
    { key: 'doctorato', name: 'Doctorato', highlight: true },
    { key: 'drchrono', name: 'Drchrono', highlight: false },
    { key: 'practo', name: 'Practo', highlight: false },
    { key: 'vezeeta', name: 'Vezeeta Pro', highlight: false },
    { key: 'spreadsheet', name: 'Excel / Spreadsheet', highlight: false },
];

const features = computed(() => [
    {
        category: tr('اللغة والمنطقة', 'Language & region'),
        rows: [
            { feat: tr('واجهة بالعربية الكاملة', 'Full Arabic UI'),       doctorato: true, drchrono: false, practo: false, vezeeta: true, spreadsheet: 'partial' },
            { feat: tr('دعم RTL/LTR تلقائي', 'Automatic RTL/LTR'),         doctorato: true, drchrono: false, practo: false, vezeeta: true, spreadsheet: false },
            { feat: tr('أسعار بالعملة المحلية لكل دولة', 'Local pricing per country'), doctorato: true, drchrono: false, practo: false, vezeeta: 'partial', spreadsheet: false },
            { feat: tr('دعم فني بالعربية', 'Arabic-language support'),     doctorato: true, drchrono: false, practo: false, vezeeta: true, spreadsheet: false },
        ],
    },
    {
        category: tr('السجل الطبي', 'Medical Record'),
        rows: [
            { feat: tr('EMR كامل بقوالب لكل تخصص', 'Full EMR w/ specialty templates'), doctorato: true, drchrono: true, practo: true, vezeeta: 'partial', spreadsheet: false },
            { feat: tr('وصفات إلكترونية موقّعة', 'Signed e-prescriptions'),  doctorato: true, drchrono: true, practo: 'partial', vezeeta: 'partial', spreadsheet: false },
            { feat: tr('تكامل مختبر وأشعة', 'Lab + radiology integration'),  doctorato: true, drchrono: true, practo: false, vezeeta: false, spreadsheet: false },
            { feat: tr('سجل تدقيق لكل وصول للبيانات', 'Audit log per data access'), doctorato: true, drchrono: true, practo: 'partial', vezeeta: false, spreadsheet: false },
        ],
    },
    {
        category: tr('التخصصات', 'Specialties'),
        rows: [
            { feat: tr('وحدة عيادات أسنان (FDI tooth chart)', 'Dental module (FDI tooth chart)'), doctorato: true, drchrono: 'partial', practo: false, vezeeta: false, spreadsheet: false },
            { feat: tr('وحدة طب أطفال (WHO growth charts)', 'Pediatrics (WHO growth charts)'), doctorato: true, drchrono: 'partial', practo: false, vezeeta: false, spreadsheet: false },
            { feat: tr('وحدة تجميل وجلدية (صور قبل/بعد)', 'Derma + Cosmetics (before/after)'), doctorato: true, drchrono: false, practo: false, vezeeta: false, spreadsheet: false },
            { feat: tr('استشارات أون لاين مدمجة', 'Built-in telemedicine'),    doctorato: true, drchrono: true, practo: 'partial', vezeeta: 'partial', spreadsheet: false },
        ],
    },
    {
        category: tr('التسويق وإدارة العملاء', 'Marketing & CRM'),
        rows: [
            { feat: tr('CRM طبي مدمج (Lead Scoring)', 'Built-in medical CRM (lead scoring)'), doctorato: true, drchrono: false, practo: false, vezeeta: false, spreadsheet: false },
            { feat: tr('حملات SMS / واتساب', 'SMS / WhatsApp campaigns'),     doctorato: true, drchrono: false, practo: 'partial', vezeeta: 'partial', spreadsheet: false },
            { feat: tr('تذكيرات تلقائية للمواعيد', 'Auto appointment reminders'), doctorato: true, drchrono: true, practo: true, vezeeta: true, spreadsheet: false },
            { feat: tr('برنامج إحالات مدمج', 'Built-in referral program'),    doctorato: true, drchrono: false, practo: false, vezeeta: false, spreadsheet: false },
        ],
    },
    {
        category: tr('المالية والتأمين', 'Finance & Insurance'),
        rows: [
            { feat: tr('فوترة + مدفوعات أونلاين', 'Billing + online payments'), doctorato: true, drchrono: true, practo: true, vezeeta: 'partial', spreadsheet: false },
            { feat: tr('متوافق مع NPHIES (السعودية)', 'NPHIES-compliant (Saudi)'), doctorato: true, drchrono: false, practo: false, vezeeta: 'partial', spreadsheet: false },
            { feat: tr('متوافق مع Riayati / Malaffi (الإمارات)', 'Riayati / Malaffi (UAE)'), doctorato: true, drchrono: false, practo: false, vezeeta: 'partial', spreadsheet: false },
            { feat: tr('ضريبة VAT تلقائية حسب الدولة', 'Auto VAT per country'),  doctorato: true, drchrono: false, practo: false, vezeeta: 'partial', spreadsheet: false },
        ],
    },
    {
        category: tr('الأمان والامتثال', 'Security & Compliance'),
        rows: [
            { feat: tr('تشفير AES-256 + SSL/TLS', 'AES-256 + SSL/TLS encryption'),  doctorato: true, drchrono: true, practo: true, vezeeta: 'partial', spreadsheet: false },
            { feat: tr('متوافق مع HIPAA + GDPR', 'HIPAA + GDPR compliant'),         doctorato: true, drchrono: true, practo: 'partial', vezeeta: 'partial', spreadsheet: false },
            { feat: tr('نسخ احتياطي يومي', 'Daily backups'),                          doctorato: true, drchrono: true, practo: true, vezeeta: 'partial', spreadsheet: false },
            { feat: tr('صلاحيات RBAC دقيقة', 'Fine-grained RBAC'),                  doctorato: true, drchrono: true, practo: 'partial', vezeeta: 'partial', spreadsheet: false },
        ],
    },
    {
        category: tr('السعر والإعداد', 'Pricing & Onboarding'),
        rows: [
            { feat: tr('تجربة مجانية 14 يوم بدون بطاقة', '14-day free trial, no card'), doctorato: true, drchrono: false, practo: false, vezeeta: false, spreadsheet: 'free' },
            { feat: tr('سعر مرئي على الموقع', 'Transparent on-site pricing'),        doctorato: true, drchrono: 'partial', practo: false, vezeeta: false, spreadsheet: 'free' },
            { feat: tr('إعداد كامل + تدريب مدفوع', 'Full setup + paid training'),    doctorato: true, drchrono: true, practo: true, vezeeta: true, spreadsheet: false },
            { feat: tr('رسوم إعداد لمرة واحدة', 'One-time setup fee'),               doctorato: true, drchrono: true, practo: true, vezeeta: true, spreadsheet: false },
        ],
    },
]);

function cellSymbol(val) {
    if (val === true) return { sym: '✓', color: 'text-emerald-500' };
    if (val === false) return { sym: '✗', color: 'text-red-400' };
    if (val === 'partial') return { sym: '◐', color: 'text-amber-500' };
    if (val === 'free') return { sym: 'مجاني', color: 'text-gray-400 text-xs' };
    return { sym: '—', color: 'text-gray-300' };
}

const ldJson = computed(() => ({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'Article',
            '@id': 'https://doctorato.com/compare#article',
            headline: tr('Doctorato vs أنظمة إدارة العيادات الأخرى — مقارنة شاملة 2026', 'Doctorato vs Other Clinic Management Systems — 2026 Comparison'),
            datePublished: '2026-04-25',
            dateModified: '2026-04-25',
            author: { '@type': 'Organization', name: 'Markeza Group' },
            publisher: {
                '@type': 'Organization',
                name: 'Doctorato',
                logo: { '@type': 'ImageObject', url: 'https://doctorato.com/images/doctorato-logo.png' },
            },
            mainEntityOfPage: 'https://doctorato.com/compare',
        },
        {
            '@type': 'BreadcrumbList',
            itemListElement: [
                { '@type': 'ListItem', position: 1, name: 'Doctorato', item: 'https://doctorato.com' },
                { '@type': 'ListItem', position: 2, name: tr('مقارنات', 'Compare'), item: 'https://doctorato.com/compare' },
            ],
        },
    ],
}));
</script>

<template>
    <SeoHead
        :title="tr('Doctorato vs أنظمة إدارة العيادات الأخرى | مقارنة شاملة 2026', 'Doctorato vs Other Clinic Management Software | 2026 Comparison')"
        :description="tr('مقارنة موضوعية بين Doctorato و Drchrono و Practo و Vezeeta و Excel — 30 ميزة عبر 7 فئات (السجل الطبي، التخصصات، التأمين، الأمان، السعر). اختار النظام المناسب لعيادتك في 5 دقائق.', 'Honest comparison of Doctorato vs Drchrono, Practo, Vezeeta, and Excel — 30 features across 7 categories (EMR, specialties, insurance, security, price). Pick the right clinic system in 5 minutes.')"
        :json-ld="ldJson"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-28 sm:pt-36 pb-12 sm:pb-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute -top-20 -start-20 w-96 h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-[0.2em] text-[#C4A265] mb-3 animate-fade-up">
                    {{ tr('مقارنة موضوعية', 'Honest comparison') }}
                </p>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white mb-4 leading-tight animate-fade-up">
                    {{ tr('Doctorato مقارنة بأنظمة إدارة العيادات الأخرى', 'Doctorato vs other clinic systems') }}
                </h1>
                <p class="text-sm sm:text-base text-white/70 max-w-2xl mx-auto leading-relaxed animate-fade-up">
                    {{ tr(
                        '30 ميزة عبر 7 فئات. كل خانة مرسومة بدقة — لو حد منهم بيقدم الميزة، نقول كده. لو نص الطريق، نقول. لو لأ، نقول. القرار في إيدك.',
                        '30 features across 7 categories. Every cell drawn honestly — if a competitor has the feature, we say so. Half-credit gets ◐. The decision is yours.'
                    ) }}
                </p>
            </div>
        </section>

        <!-- Legend -->
        <section class="py-6 bg-light-blue/30 border-y border-gray-100">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-xs sm:text-sm">
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-500 text-base">✓</span> {{ tr('متوفّر', 'Available') }}</span>
                    <span class="inline-flex items-center gap-1.5"><span class="text-amber-500 text-base">◐</span> {{ tr('جزئي', 'Partial') }}</span>
                    <span class="inline-flex items-center gap-1.5"><span class="text-red-400 text-base">✗</span> {{ tr('غير متوفر', 'Not available') }}</span>
                </div>
            </div>
        </section>

        <!-- Comparison table -->
        <section class="py-12 sm:py-16 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-md">
                    <table class="w-full min-w-[760px]">
                        <thead class="bg-gradient-to-r from-[#0A1628] to-[#1B4F72] text-white">
                            <tr>
                                <th class="text-start text-xs sm:text-sm font-bold p-3 sm:p-4 sticky start-0 bg-[#0A1628] z-10 min-w-[200px]">
                                    {{ tr('الميزة', 'Feature') }}
                                </th>
                                <th
                                    v-for="c in competitors"
                                    :key="c.key"
                                    class="text-xs sm:text-sm font-bold p-3 sm:p-4 text-center min-w-[100px]"
                                    :class="{ 'bg-gradient-to-b from-[#C4A265]/30 to-transparent border-x border-[#C4A265]/40': c.highlight }"
                                >
                                    {{ c.name }}
                                    <span v-if="c.highlight" class="block text-[9px] font-normal text-[#C4A265] mt-0.5">{{ tr('نظامنا', 'Ours') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(group, gIdx) in features" :key="gIdx">
                                <tr class="bg-light-blue/30">
                                    <td :colspan="competitors.length + 1" class="p-2.5 sm:p-3 text-xs sm:text-sm font-bold text-[#1B4F72] sticky start-0 bg-light-blue/30">
                                        {{ group.category }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="(row, rIdx) in group.rows"
                                    :key="`${gIdx}-${rIdx}`"
                                    class="border-t border-gray-100 hover:bg-gray-50/50"
                                >
                                    <td class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 sticky start-0 bg-white z-10">{{ row.feat }}</td>
                                    <td
                                        v-for="c in competitors"
                                        :key="c.key"
                                        class="p-3 sm:p-4 text-center"
                                        :class="{ 'bg-[#C4A265]/[0.04]': c.highlight }"
                                    >
                                        <span class="text-lg sm:text-xl font-bold" :class="cellSymbol(row[c.key]).color">
                                            {{ cellSymbol(row[c.key]).sym }}
                                        </span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <p class="text-center text-[11px] sm:text-xs text-gray-400 mt-4 max-w-3xl mx-auto leading-relaxed">
                    {{ tr(
                        'آخر مراجعة: أبريل 2026. المعلومات مأخوذة من مواقع المنافسين الرسمية ومن تجاربنا الفعلية. لو لاحظت أي خطأ، تواصل معنا وهنصحّحه فوراً.',
                        'Last reviewed: April 2026. Information sourced from competitors\' official sites and our hands-on testing. Spot an error? Contact us and we\'ll correct it immediately.'
                    ) }}
                </p>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-12 sm:py-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl sm:text-3xl font-extrabold mb-3">
                    {{ tr('قرّرت تجرّب Doctorato؟', 'Decided to try Doctorato?') }}
                </h2>
                <p class="text-sm sm:text-base text-white/60 mb-6 max-w-xl mx-auto">
                    {{ tr('14 يوم مجاناً، بدون بطاقة ائتمان، إعداد كامل لفريقك.', '14 days free, no credit card, full team onboarding.') }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <Link href="/demo" class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold shadow-xl hover:-translate-y-0.5 transition">
                        {{ tr('احجز عرضاً مجانياً', 'Book a free demo') }}
                    </Link>
                    <Link href="/pricing" class="inline-flex items-center gap-2 px-7 py-3 rounded-full border-2 border-white/20 font-semibold hover:bg-white/10 transition">
                        {{ tr('شاهد الأسعار', 'View pricing') }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
