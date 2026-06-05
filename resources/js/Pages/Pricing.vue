<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import PricingCardV2 from '@/Components/PricingCardV2.vue';
import FaqAccordion from '@/Components/FaqAccordion.vue';
import RoiPreview from '@/Components/RoiPreview.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import { Head, Link, usePage } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import { ref, computed } from 'vue';

const page = usePage();
const { t, locale } = useI18n();
const { localizedField } = useLocale();
useScrollAnimation();

const props = defineProps({
    plans: { type: Array, default: () => [] },
    faqs: { type: Array, default: () => [] },
    addons: { type: Array, default: () => [] },
    testimonials: { type: Array, default: () => [] },
    // Currency active for the visitor's country (resolved server-side).
    // All non-plan amounts (add-ons) are converted from their EGP base
    // using rate_from_egp, so the whole page speaks one currency.
    activeCurrency: {
        type: Object,
        default: () => ({ code: 'EGP', symbol: 'ج.م', rate_from_egp: 1, decimal_places: 2, symbol_position: 'after' }),
    },
});

// Format an EGP-denominated amount in the active country's currency.
function formatLocal(amountInEgp) {
    const cur = props.activeCurrency;
    const converted = Number(amountInEgp) * (cur.rate_from_egp || 1);
    const rounded = Number(converted.toFixed(cur.decimal_places || 0));
    const formatted = new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US', {
        minimumFractionDigits: cur.decimal_places || 0,
        maximumFractionDigits: cur.decimal_places || 0,
    }).format(rounded);
    return cur.symbol_position === 'before' ? `${cur.symbol}${formatted}` : `${formatted} ${cur.symbol}`;
}

const billingCycle = ref('monthly');
const isYearly = computed(() => billingCycle.value === 'yearly');

// Pricing is now per-country. Each plan row already carries the correct
// numeric price + currency for the active country, so we render it as-is
// (no more EGP→X conversion, no more "~approximate" hedge).
const isApproximate = computed(() => false);

function toggleBilling() {
    billingCycle.value = billingCycle.value === 'monthly' ? 'yearly' : 'monthly';
}

function getPlanPrice(plan) {
    if (plan.is_custom) return null;
    return isYearly.value ? plan.yearly_price : plan.monthly_price;
}

function localeNumber(value) {
    if (value === null || value === undefined) return '';
    return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US').format(
        Math.round(Number(value))
    );
}

function getFormattedPrice(plan) {
    const price = getPlanPrice(plan);
    if (price === null || price === undefined) return null;
    return localeNumber(price);
}

function getCurrencySymbol(plan) {
    // Per-country currency symbol from the backend (e.g. ج.م / ر.س / د.إ).
    // Falls back to the plan's currency code if no symbol was seeded.
    return plan?.currency_symbol || plan?.currency || '';
}

// Setup fee helpers — yearly subscribers see the 50%-off price with a
// strikethrough on the full price. Monthly subscribers pay the full fee.
function getSetupFee(plan) {
    if (!plan || plan.is_custom) return 0;
    return Number(isYearly.value ? plan.setup_fee_yearly : plan.setup_fee) || 0;
}
function getSetupFeeFull(plan) {
    if (!plan || plan.is_custom) return 0;
    return Number(plan.setup_fee) || 0;
}

// Comparison table — rebuilt for the 4-tier launch lineup
// (Starter / Growth / Professional / Enterprise + Custom).
//
// Values are READ FROM THE PLAN DATA where possible (max_doctors,
// max_patients, storage_gb, included_specialties_count) instead of
// being hardcoded. Module presence rows still need hardcoded truth
// tables because the modules_included JSON arrays carry shipping
// names not localized labels.
//
// Truth tables order matches displayOrder: Starter, Growth,
// Professional, Enterprise, Custom — 5 columns. If a plan is
// missing the row falls back to all-false so the table doesn't
// crash before the seeder runs.
const comparisonCategories = computed(() => [
    {
        name: locale.value === 'ar' ? 'الحدود والقياس' : 'Limits & scale',
        features: [
            { label: locale.value === 'ar' ? 'عدد التخصصات الطبية' : 'Medical specialties', values: getCompareValues('specialties') },
            { label: locale.value === 'ar' ? 'عدد الأطباء' : 'Doctors', values: getCompareValues('max_doctors') },
            { label: locale.value === 'ar' ? 'عدد الموظفين' : 'Staff users', values: getCompareValues('max_staff') },
            { label: locale.value === 'ar' ? 'عدد المرضى' : 'Patients', values: getCompareValues('max_patients') },
            { label: locale.value === 'ar' ? 'عدد الفروع' : 'Branches', values: getCompareValues('max_branches') },
            { label: locale.value === 'ar' ? 'التخزين' : 'Storage', values: getCompareValues('storage_gb') },
            { label: locale.value === 'ar' ? 'مستوى الدعم' : 'Support', values: getCompareValues('support_level') },
        ]
    },
    {
        name: locale.value === 'ar' ? 'العيادة والإكلينيكي' : 'Clinical',
        features: [
            { label: locale.value === 'ar' ? 'ملفات المرضى الإلكترونية (EMR)' : 'Patient EMR', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'المواعيد والحجز أونلاين' : 'Appointments + online booking', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'الوصفات الإلكترونية' : 'E-prescriptions', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'تكامل صيدليات' : 'Pharmacy integration', values: [false, true, true, true] },
            { label: locale.value === 'ar' ? 'تصوير وأرشيف طبي (PACS)' : 'Medical imaging (PACS)', values: [false, false, true, true] },
            { label: locale.value === 'ar' ? 'تكامل المختبرات' : 'Lab integration', values: ['add-on', 'add-on', true, true] },
        ]
    },
    {
        name: locale.value === 'ar' ? 'الإدارة والمالية' : 'Operations & finance',
        features: [
            { label: locale.value === 'ar' ? 'الفواتير والمدفوعات' : 'Invoicing + payments', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'المحفظة ونقاط الولاء' : 'Wallet + loyalty', values: [false, true, true, true] },
            { label: locale.value === 'ar' ? 'مخزون ومنتجات' : 'Inventory', values: [false, true, true, true] },
            { label: locale.value === 'ar' ? 'المحاسبة المالية' : 'Financial accounting', values: [false, 'basic', true, true] },
            { label: locale.value === 'ar' ? 'الموارد البشرية والرواتب' : 'HR + payroll', values: [false, false, true, true] },
            { label: locale.value === 'ar' ? 'تكامل التأمين (Bupa, GIG, ELAJI)' : 'Insurance integration', values: [false, 'basic', true, true] },
        ]
    },
    {
        name: locale.value === 'ar' ? 'التواصل والمشاركة' : 'Engagement & communication',
        features: [
            { label: locale.value === 'ar' ? 'تذكيرات SMS' : 'SMS reminders', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'تذكيرات WhatsApp' : 'WhatsApp reminders', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'WhatsApp Business API كامل' : 'WhatsApp Business API (full)', values: [false, true, true, true] },
            { label: locale.value === 'ar' ? 'CRM طبي' : 'Medical CRM', values: [false, true, true, true] },
            { label: locale.value === 'ar' ? 'تقييم رضا المريض (NPS)' : 'Patient satisfaction (NPS)', values: [false, true, true, true] },
            { label: locale.value === 'ar' ? 'بوابة المريض' : 'Patient portal', values: ['basic', true, true, true] },
        ]
    },
    {
        name: locale.value === 'ar' ? 'التحليل والذكاء' : 'Analytics & insights',
        features: [
            { label: locale.value === 'ar' ? 'تقارير أساسية' : 'Basic reports', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'Analytics متقدمة' : 'Advanced analytics', values: [false, true, true, true] },
            { label: locale.value === 'ar' ? 'AI insights' : 'AI insights', values: [false, false, true, true] },
            { label: locale.value === 'ar' ? 'تقارير ضريبية' : 'Tax reports', values: [false, false, true, true] },
        ]
    },
    {
        name: locale.value === 'ar' ? 'الأمان والإدارة الإدارية' : 'Security & admin',
        features: [
            { label: locale.value === 'ar' ? '6 بوابات منفصلة' : '6 separate portals', values: ['3', '5', true, true] },
            { label: locale.value === 'ar' ? 'RBAC (80+ صلاحية)' : 'RBAC (80+ permissions)', values: ['basic', 'basic', true, true] },
            { label: locale.value === 'ar' ? 'Audit Log' : 'Audit log', values: [false, false, true, true] },
            { label: locale.value === 'ar' ? '2FA للأدمن' : '2FA admin', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'GDPR / PDPL متوافق' : 'GDPR / PDPL', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'SLA 99.9% مكتوب' : 'Signed 99.9% SLA', values: [false, false, false, true] },
            { label: locale.value === 'ar' ? 'Backup' : 'Backup', values: ['يومي', 'يومي', 'ساعي', 'مخصّص'] },
        ]
    },
    {
        name: locale.value === 'ar' ? 'API و White-label' : 'API & White-label',
        features: [
            { label: locale.value === 'ar' ? 'موقع عيادتك' : 'Clinic website', values: [true, true, true, true] },
            { label: locale.value === 'ar' ? 'Webmaster Mode' : 'Webmaster mode', values: [false, false, false, true] },
            { label: locale.value === 'ar' ? 'API + Webhooks' : 'API + Webhooks', values: [false, false, false, true] },
            { label: locale.value === 'ar' ? 'Custom integrations' : 'Custom integrations', values: [false, false, false, true] },
            { label: locale.value === 'ar' ? 'White-label (تطبيق بشعارك)' : 'White-label (your branding)', values: [false, false, 'add-on', true] },
            { label: locale.value === 'ar' ? 'On-premise' : 'On-premise', values: [false, false, false, true] },
        ]
    },
]);

function getCompareValues(field) {
    return props.plans.map(plan => {
        if (field === 'specialties') {
            const map = {
                one: locale.value === 'ar' ? 'تخصص واحد' : '1 specialty',
                three: locale.value === 'ar' ? '3 تخصصات' : '3 specialties',
                all: locale.value === 'ar' ? 'الكل (7)' : 'All (7)',
                all_plus_early: locale.value === 'ar' ? 'الكل + Early access' : 'All + early access',
            };
            return map[plan.included_specialties_count] || '—';
        }
        if (field === 'max_doctors') {
            if (plan.is_contact_sales) return locale.value === 'ar' ? 'مخصّص' : 'Custom';
            return plan.max_doctors ? String(plan.max_doctors) : (locale.value === 'ar' ? 'بلا حدود' : 'Unlimited');
        }
        if (field === 'max_staff') {
            if (plan.is_contact_sales) return locale.value === 'ar' ? 'مخصّص' : 'Custom';
            return plan.max_staff ? String(plan.max_staff) : (locale.value === 'ar' ? 'بلا حدود' : 'Unlimited');
        }
        if (field === 'max_patients') {
            if (plan.is_contact_sales) return locale.value === 'ar' ? 'مخصّص' : 'Custom';
            return plan.max_patients ? new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US').format(plan.max_patients) : (locale.value === 'ar' ? 'بلا حدود' : 'Unlimited');
        }
        if (field === 'max_branches') {
            if (plan.is_contact_sales) return locale.value === 'ar' ? 'مخصّص' : 'Custom';
            return plan.max_branches ? String(plan.max_branches) : (locale.value === 'ar' ? 'بلا حدود' : 'Unlimited');
        }
        if (field === 'storage_gb') {
            if (plan.is_contact_sales) return locale.value === 'ar' ? 'مخصّص' : 'Custom';
            return plan.storage_gb ? `${plan.storage_gb} GB` : (locale.value === 'ar' ? 'بلا حدود' : 'Unlimited');
        }
        if (field === 'support_level') {
            const levels = {
                email:          locale.value === 'ar' ? 'بريد إلكتروني'      : 'Email',
                priority_email: locale.value === 'ar' ? 'بريد بأولوية'        : 'Priority email',
                priority_phone: locale.value === 'ar' ? 'بريد + هاتف بأولوية' : 'Email + phone',
                dedicated_24_7: locale.value === 'ar' ? 'دعم مخصص 24/7'       : 'Dedicated 24/7',
                // legacy fallback keys (kept in case any old plan still uses them)
                chat:           locale.value === 'ar' ? 'دردشة'              : 'Chat',
                phone:          locale.value === 'ar' ? 'هاتف'               : 'Phone',
                priority:       locale.value === 'ar' ? 'أولوية'             : 'Priority',
                dedicated:      locale.value === 'ar' ? 'مخصص'               : 'Dedicated',
            };
            return levels[plan.support_level] || plan.support_level;
        }
        return plan[field];
    });
}

const guarantees = computed(() => [
    { icon: 'shield', text: t('pricing.guarantee_trial') },
    { icon: 'card', text: t('pricing.guarantee_no_card') },
    { icon: 'cancel', text: t('pricing.guarantee_cancel') },
    { icon: 'support', text: t('pricing.guarantee_support') },
]);

const showComparison = ref(false);

// Add-ons come from the `addons` prop (fed from AddOn model). Keep a local
// alias so the existing template doesn't need to change.
const addons = computed(() => props.addons || []);

const pricingJsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'Product',
            name: 'Doctorato — نظام إدارة العيادات',
            description: t('pricing.subtitle') || 'نظام متكامل لإدارة العيادات الطبية',
            brand: { '@type': 'Brand', name: 'Doctorato' },
            offers: (props.plans || []).filter(p => !p.is_custom).map(plan => ({
                '@type': 'Offer',
                name: locale.value === 'ar' ? plan.name_ar : plan.name_en,
                price: String(plan.monthly_price),
                priceCurrency: plan.currency || 'EGP',
                availability: 'https://schema.org/InStock',
                priceValidUntil: '2026-12-31',
                url: `${typeof window !== 'undefined' ? window.location.origin : ''}/checkout/${plan.slug}`,
            })),
        },
        ...((props.faqs || []).length ? [{
            '@type': 'FAQPage',
            mainEntity: (props.faqs || []).map(f => ({
                '@type': 'Question',
                name: locale.value === 'ar' ? f.question_ar : f.question_en,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: locale.value === 'ar' ? f.answer_ar : f.answer_en,
                },
            })),
        }] : []),
    ],
}));
</script>

<template>
    <SeoHead
        :title="locale === 'ar'
            ? 'أسعار نظام إدارة عيادات | 4 باقات تنافسية بالجنيه المصري — دكتوراتو'
            : 'Clinic Management Pricing | 4 Competitive Plans in EGP — Doctorato'"
        :description="locale === 'ar'
            ? 'أسعار شفافة لنظام إدارة العيادات: المبتدئ 1,990 ج.م، النمو 3,990 ج.م، الاحترافي 6,990 ج.م، Enterprise. تركيب مجاني، تجربة 30 يوم بدون بطاقة ائتمان.'
            : 'Transparent clinic management pricing: Starter 1,990 EGP, Growth 3,990 EGP, Professional 6,990 EGP, Enterprise. Free setup, 30-day trial — no credit card required.'"
        :json-ld="pricingJsonLd"
        :breadcrumbs="[
            { name: locale === 'ar' ? 'الرئيسية' : 'Home', url: '/' },
            { name: locale === 'ar' ? 'الأسعار' : 'Pricing', url: '/pricing' },
        ]"
    />
    <MainLayout>
        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <!-- Glow orbs -->
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 start-0 w-80 h-80 bg-[#2471A3]/15 rounded-full blur-[100px]"></div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/[0.06] border border-white/[0.1] mb-6 animate-fade-up">
                    <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-white/70 font-medium">{{ t('pricing.hero_badge') }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6 animate-fade-up">
                    {{ t('pricing.hero_title') }}
                </h1>
                <p class="text-lg text-white/60 max-w-2xl mx-auto leading-relaxed mb-10 animate-fade-up">
                    {{ t('pricing.hero_subtitle') }}
                </p>

                <!-- Guarantee badges -->
                <div class="flex flex-wrap justify-center gap-3 animate-fade-up">
                    <div
                        v-for="(g, idx) in guarantees"
                        :key="idx"
                        class="flex items-center gap-2 bg-white/[0.06] backdrop-blur-sm px-4 py-2 rounded-full border border-white/[0.1]"
                    >
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="g.icon === 'shield'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            <path v-if="g.icon === 'card'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            <path v-if="g.icon === 'cancel'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            <path v-if="g.icon === 'support'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-white/80 font-medium">{{ g.text }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Cards Section -->
        <section class="py-20 lg:py-28 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Billing Toggle -->
                <div class="flex items-center justify-center gap-4 mb-12 animate-fade-up">
                    <span class="text-sm font-semibold transition-colors" :class="!isYearly ? 'text-[#1C2833]' : 'text-gray-400'">
                        {{ t('pricing.monthly') }}
                    </span>
                    <button
                        @click="toggleBilling"
                        class="relative w-16 h-8 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-[#C4A265]/50"
                        :class="isYearly ? 'bg-[#C4A265]' : 'bg-gray-300'"
                        :aria-label="t('pricing.toggle_billing')"
                    >
                        <span
                            class="absolute top-1 w-6 h-6 bg-white rounded-full shadow-md transition-all duration-300"
                            :style="{ insetInlineStart: isYearly ? '2.25rem' : '0.25rem' }"
                        />
                    </button>
                    <span class="text-sm font-semibold transition-colors" :class="isYearly ? 'text-[#1C2833]' : 'text-gray-400'">
                        {{ t('pricing.yearly') }}
                    </span>
                    <Transition
                        enter-active-class="transition duration-300 ease-out"
                        enter-from-class="opacity-0 scale-90"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition duration-200 ease-in"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-90"
                    >
                        <span v-if="isYearly" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                            {{ t('pricing.save_20') }}
                        </span>
                    </Transition>
                </div>

                <!-- Approximate Note -->
                <p v-if="isApproximate" class="text-center text-sm text-gray-400 mb-8 animate-fade-up">
                    {{ t('pricing.approximate_note') }}
                </p>

                <!-- Cards Grid — clean post-reset PricingCardV2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 animate-stagger">
                    <PricingCardV2
                        v-for="plan in plans"
                        :key="plan.id"
                        :plan="plan"
                        :is-yearly="isYearly"
                    />
                </div>

                <!-- VAT Note -->
                <p class="text-center text-xs text-gray-400 mt-8">{{ t('pricing.vat_note') }}</p>
            </div>
        </section>

        <!-- Payment Options — explains annual upfront vs 3-instalments
             so the visitor sees the cash-flow flexibility BEFORE the
             FAQ section. Light cream backdrop for a clean break from
             the plan grid above. -->
        <section class="py-16 lg:py-20 bg-[#FBFAF6]">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-10">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                        {{ locale === 'ar' ? 'خيارات الدفع' : 'Payment options' }}
                    </p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C2833]">
                        {{ locale === 'ar' ? 'ادفع بالطريقة التي تناسبك' : 'Pay the way that fits' }}
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 gap-5 max-w-4xl mx-auto">
                    <!-- Monthly -->
                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-[#1C2833] mb-1">{{ locale === 'ar' ? 'شهري' : 'Monthly' }}</h3>
                        <p class="text-xs text-[#5A6C7D] leading-relaxed">
                            {{ locale === 'ar' ? 'مرونة كاملة — ألغِ في أي وقت بدون التزام. مناسب للعيادات التي تبدأ رحلتها الرقمية.' : 'Full flexibility — cancel anytime, no commitment. Ideal for clinics starting digital.' }}
                        </p>
                    </div>

                    <!-- Annual upfront -->
                    <div class="relative bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white rounded-3xl shadow-xl shadow-[#1B4F72]/15 p-6">
                        <div class="absolute -top-3 inset-x-0 flex justify-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white text-[10px] font-bold uppercase tracking-widest shadow">
                                {{ locale === 'ar' ? 'الأفضل قيمة' : 'Best value' }}
                            </span>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[#C4A265]/20 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-base font-bold mb-1">{{ locale === 'ar' ? 'سنوي مقدمًا' : 'Annual Upfront' }}</h3>
                        <p class="text-xs text-white/70 leading-relaxed mb-3">
                            {{ locale === 'ar' ? 'ادفع 10 شهور بدل 12 — شهران مجانًا. الأكثر اقتصاداً للعيادات الراسخة.' : 'Pay 10 months, get 12 — 2 months on us. Most economical for established clinics.' }}
                        </p>
                        <ul class="space-y-1.5 text-xs">
                            <li class="flex items-center gap-2"><span class="text-[#C4A265]">✓</span> <span>{{ locale === 'ar' ? 'وفّر شهرين كاملين' : 'Save 2 full months' }}</span></li>
                            <li class="flex items-center gap-2"><span class="text-[#C4A265]">✓</span> <span>{{ locale === 'ar' ? 'سعر ثابت لمدة سنة' : 'Locked price for a year' }}</span></li>
                            <li class="flex items-center gap-2"><span class="text-[#C4A265]">✓</span> <span>{{ locale === 'ar' ? 'ضمان استرداد خلال 30 يوم' : '30-day money-back guarantee' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add-ons Section -->
        <section class="py-20 lg:py-28 bg-[#F8FAFC] relative overflow-hidden">
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 start-0 w-96 h-96 bg-[#1B4F72]/5 rounded-full blur-[120px]"></div>

            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 mb-5">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="2 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-sm text-[#C4A265] font-bold">{{ locale === 'ar' ? 'إضافات اختيارية' : 'Optional Add-ons' }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ locale === 'ar' ? 'خصّص خطتك كما تريد' : 'Customize your plan' }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ locale === 'ar' ? 'أضف المزايا التي تحتاجها فقط إلى أي خطة اشتراك' : 'Add only the features you need to any subscription plan' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 animate-stagger">
                    <div
                        v-for="addon in addons"
                        :key="addon.id"
                        class="group relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:border-[#C4A265]/30 hover:-translate-y-1 transition-all duration-300 animate-fade-up overflow-hidden"
                    >
                        <div v-if="addon.badge_ar" class="absolute top-3 end-3 px-2 py-0.5 rounded-full bg-[#C4A265]/15 text-[#C4A265] text-[10px] font-bold">
                            {{ locale === 'ar' ? addon.badge_ar : addon.badge_en }}
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[#1B4F72]/10 to-[#C4A265]/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300 text-[#1B4F72] font-bold text-xs uppercase">
                                {{ (addon.icon || addon.name_en || '?').substring(0, 2) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-[#1C2833] mb-1">
                                    {{ locale === 'ar' ? addon.name_ar : addon.name_en }}
                                </h3>
                                <p class="text-xs text-gray-500 leading-relaxed mb-3">
                                    {{ locale === 'ar' ? addon.description_ar : addon.description_en }}
                                </p>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl font-extrabold text-[#1B4F72]">
                                        {{ formatLocal(addon.active_price || addon.price_egp) }}
                                    </span>
                                    <span class="text-xs text-gray-400">
                                        /
                                        <template v-if="addon.period === 'yearly'">{{ locale === 'ar' ? 'سنوياً' : 'year' }}</template>
                                        <template v-else-if="addon.period === 'one_time'">{{ locale === 'ar' ? 'لمرة واحدة' : 'one-time' }}</template>
                                        <template v-else>{{ locale === 'ar' ? 'شهرياً' : 'month' }}</template>
                                    </span>
                                </div>
                                <div v-if="Array.isArray(addon.included_in_plans) && addon.included_in_plans.length" class="mt-1.5 inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                                    {{ locale === 'ar' ? 'مجاني مع ' + addon.included_in_plans.join('/') : 'Free with ' + addon.included_in_plans.join('/') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-10 space-y-2">
                    <Link href="/add-ons" class="inline-flex items-center gap-2 text-sm font-bold text-[#1B4F72] hover:text-[#0D2B45] transition">
                        {{ locale === 'ar' ? 'استكشف كل الإضافات' : 'Explore all add-ons' }}
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </Link>
                    <p class="text-xs text-gray-400">
                        {{ locale === 'ar' ? '* جميع الإضافات يمكن تفعيلها أو إلغاؤها في أي وقت من لوحة التحكم' : '* All add-ons can be enabled or disabled anytime from your dashboard' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- ROI Preview — plant a number in the visitor's head before the comparison table -->
        <RoiPreview />

        <!-- Feature Comparison Table -->
        <section class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#1B4F72]/5 border border-[#1B4F72]/10 mb-5">
                        <svg class="w-4 h-4 text-[#1B4F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="text-sm text-[#1B4F72] font-medium">{{ t('pricing.compare_badge') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ t('pricing.compare_title') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ t('pricing.compare_subtitle') }}
                    </p>
                </div>

                <!-- Toggle Button for Mobile -->
                <div class="lg:hidden text-center mb-8">
                    <button
                        @click="showComparison = !showComparison"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-[#1B4F72] text-[#1B4F72] font-semibold text-sm transition-all hover:bg-[#1B4F72] hover:text-white"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        {{ showComparison ? t('pricing.hide_comparison') : t('pricing.show_comparison') }}
                    </button>
                </div>

                <!-- Comparison Table -->
                <div class="animate-fade-up" :class="{ 'hidden lg:block': !showComparison }">
                    <div class="overflow-x-auto rounded-2xl border border-gray-100 shadow-lg">
                        <table class="w-full min-w-[800px]">
                            <!-- Sticky Header -->
                            <thead>
                                <tr class="bg-gradient-to-r from-gray-50 to-white">
                                    <th class="text-start p-5 w-[280px]">
                                        <span class="text-sm font-bold text-[#1C2833]">{{ t('pricing.compare_features') }}</span>
                                    </th>
                                    <th
                                        v-for="plan in plans"
                                        :key="plan.id"
                                        class="p-5 text-center"
                                        :class="{ 'bg-[#1B4F72]/5': plan.is_popular }"
                                    >
                                        <div class="text-sm font-bold mb-1" :class="plan.is_popular ? 'text-[#1B4F72]' : 'text-[#1C2833]'">
                                            {{ localizedField(plan, 'name') }}
                                        </div>
                                        <div v-if="!plan.is_contact_sales" class="text-xs text-gray-400">
                                            {{ getFormattedPrice(plan) }} {{ getCurrencySymbol(plan) }} / {{ isYearly ? t('pricing.year') : t('pricing.month') }}
                                        </div>
                                        <div v-else class="text-xs text-gray-400">
                                            {{ locale === 'ar' ? 'تسعير مخصص' : 'Custom' }}
                                        </div>
                                        <span v-if="plan.is_popular" class="inline-block mt-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#C4A265] text-white">
                                            {{ t('pricing.most_popular') }}
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(cat, catIdx) in comparisonCategories" :key="catIdx">
                                    <!-- Category Header -->
                                    <tr class="bg-gray-50/80">
                                        <td :colspan="plans.length + 1" class="px-5 py-3">
                                            <span class="text-sm font-bold text-[#1B4F72]">{{ cat.name }}</span>
                                        </td>
                                    </tr>
                                    <!-- Feature Rows -->
                                    <tr
                                        v-for="(feature, fIdx) in cat.features"
                                        :key="`${catIdx}-${fIdx}`"
                                        class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors"
                                    >
                                        <td class="px-5 py-3.5 text-sm text-gray-600">
                                            {{ feature.label }}
                                        </td>
                                        <td
                                            v-for="(val, vIdx) in feature.values"
                                            :key="vIdx"
                                            class="px-5 py-3.5 text-center"
                                            :class="{ 'bg-[#1B4F72]/[0.02]': plans[vIdx]?.is_popular }"
                                        >
                                            <!-- Boolean check/x -->
                                            <template v-if="typeof val === 'boolean'">
                                                <div class="flex justify-center">
                                                    <div v-if="val" class="w-6 h-6 rounded-full bg-emerald-50 flex items-center justify-center">
                                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    <div v-else class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </template>
                                            <!-- Add-on tag — feature available but as a paid add-on -->
                                            <template v-else-if="val === 'add-on'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-wider border border-amber-100">
                                                    {{ locale === 'ar' ? 'إضافة' : 'Add-on' }}
                                                </span>
                                            </template>
                                            <!-- 'basic' — feature included but limited -->
                                            <template v-else-if="val === 'basic'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider border border-blue-100">
                                                    {{ locale === 'ar' ? 'أساسي' : 'Basic' }}
                                                </span>
                                            </template>
                                            <!-- String value -->
                                            <template v-else>
                                                <span class="text-sm font-medium text-[#1C2833]">{{ val }}</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <!-- CTA Row -->
                                <tr class="border-t border-gray-100 bg-gray-50/50">
                                    <td class="p-5"></td>
                                    <td v-for="plan in plans" :key="plan.id" class="p-5 text-center" :class="{ 'bg-[#1B4F72]/[0.02]': plan.is_popular }">
                                        <Link
                                            :href="plan.is_contact_sales ? '/contact?topic=enterprise' : `/demo?plan=${plan.slug}&cycle=${billingCycle}`"
                                            class="inline-block px-5 py-2.5 rounded-lg text-xs font-bold transition-all duration-300 hover:-translate-y-0.5"
                                            :class="plan.is_popular
                                                ? 'bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-md hover:shadow-lg'
                                                : 'border border-[#1B4F72]/20 text-[#1B4F72] hover:bg-[#1B4F72] hover:text-white hover:border-[#1B4F72]'"
                                        >
                                            {{ plan.is_contact_sales ? (locale === 'ar' ? 'تواصل مع المبيعات' : 'Contact sales') : (locale === 'ar' ? 'ابدأ التجربة' : 'Start trial') }}
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enterprise CTA -->
        <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative bg-gradient-to-br from-[#1B4F72] via-[#1B4F72] to-[#0A1628] rounded-3xl p-10 md:p-14 overflow-hidden animate-fade-up">
                    <!-- Decorative -->
                    <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/10 rounded-full blur-[80px]"></div>
                    <div class="absolute bottom-0 start-0 w-48 h-48 bg-white/5 rounded-full blur-[60px]"></div>
                    <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 30px 30px;"></div>

                    <div class="relative z-10 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-[#C4A265]/10 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-7 h-7 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h2 class="text-2xl md:text-4xl font-extrabold text-white mb-4">
                            {{ t('pricing.enterprise_title') }}
                        </h2>
                        <p class="text-white/60 text-base md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                            {{ t('pricing.enterprise_desc') }}
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href="/contact"
                                class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-semibold rounded-xl shadow-lg shadow-[#C4A265]/20 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                {{ t('pricing.enterprise_cta') || t('pricing.contact_us') }}
                            </Link>
                            <Link
                                href="/demo"
                                class="inline-flex items-center gap-2 px-8 py-3.5 border-2 border-white/20 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300"
                            >
                                {{ t('pricing.request_demo') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social Proof: testimonials from actual doctors -->
        <section v-if="testimonials.length" class="py-20 lg:py-28 bg-gradient-to-b from-white to-light-blue/40 relative overflow-hidden">
            <div class="absolute top-20 start-10 w-80 h-80 bg-[#C4A265]/5 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-20 end-10 w-96 h-96 bg-[#1B4F72]/5 rounded-full blur-[120px]"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 mb-5">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="text-sm text-[#C4A265] font-bold">{{ locale === 'ar' ? 'آراء عملائنا' : 'Customer Stories' }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ locale === 'ar' ? 'ثقة يستحقها عملاؤنا' : 'Trusted by clinic owners' }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ locale === 'ar' ? 'أكثر من 200 عيادة اختارت دكتوراتو — إليك ما يقولونه' : 'Over 200 clinics chose Doctorato. Here is what they say.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-stagger">
                    <article
                        v-for="t in testimonials"
                        :key="t.id"
                        class="group relative bg-white rounded-3xl p-7 border border-gray-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-500 flex flex-col"
                    >
                        <!-- Quote mark -->
                        <svg class="absolute top-5 end-5 w-10 h-10 text-[#C4A265]/15" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/>
                        </svg>

                        <!-- Rating stars -->
                        <div class="flex items-center gap-0.5 mb-4">
                            <svg
                                v-for="n in 5" :key="n"
                                class="w-4 h-4"
                                :class="n <= t.rating ? 'text-[#C4A265]' : 'text-gray-200'"
                                fill="currentColor" viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>

                        <!-- Review -->
                        <p class="text-sm text-gray-600 leading-relaxed mb-6 flex-1">
                            "{{ locale === 'ar' ? t.review_ar : (t.review_en || t.review_ar) }}"
                        </p>

                        <!-- Author -->
                        <div class="flex items-center gap-3 pt-5 border-t border-gray-100">
                            <div
                                v-if="t.photo"
                                class="shrink-0 w-12 h-12 rounded-full bg-cover bg-center ring-2 ring-[#C4A265]/20"
                                :style="{ backgroundImage: `url(${t.photo})` }"
                            ></div>
                            <div
                                v-else
                                class="shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-[#1B4F72] to-[#0A1628] flex items-center justify-center text-white font-bold text-base ring-2 ring-[#C4A265]/20"
                            >
                                {{ ((locale === 'ar' ? (t.client_name_ar || t.client_name_en) : (t.client_name_en || t.client_name_ar)) || '?').trim().split(' ').pop().charAt(0) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-[#1C2833] truncate">
                                    {{ locale === 'ar' ? t.client_name_ar : (t.client_name_en || t.client_name_ar) }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ locale === 'ar' ? t.role_ar : (t.role_en || t.role_ar) }}
                                </p>
                                <p class="text-[11px] text-[#C4A265] font-semibold truncate mt-0.5">
                                    {{ locale === 'ar' ? t.clinic_name_ar : (t.clinic_name_en || t.clinic_name_ar) }}
                                </p>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- "See all" link -->
                <div class="text-center mt-10">
                    <Link
                        href="/testimonials"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-[#1B4F72] hover:text-[#C4A265] transition-colors"
                    >
                        {{ locale === 'ar' ? 'شاهد كل الآراء' : 'See all reviews' }}
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section v-if="faqs.length > 0" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ t('pricing.faq_title') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ t('pricing.faq_subtitle') }}
                    </p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                        <span class="w-8 h-1 rounded-full bg-[#C4A265]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                    </div>
                </div>
                <FaqAccordion :faqs="faqs" />
            </div>
        </section>

        <!-- Bottom CTA -->
        <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-3xl mx-auto px-4 text-center animate-fade-up">
                <h2 class="text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-4">
                    {{ t('pricing.bottom_cta_title') }}
                </h2>
                <p class="text-gray-500 text-lg mb-8 max-w-xl mx-auto leading-relaxed">
                    {{ t('pricing.bottom_cta_subtitle') }}
                </p>
                <Link
                    href="/demo"
                    class="inline-flex items-center gap-2 px-10 py-4 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold rounded-xl shadow-lg shadow-[#C4A265]/20 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                >
                    {{ t('pricing.bottom_cta_button') }}
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </Link>
            </div>
        </section>
    </MainLayout>
</template>
