<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import PricingCards from '@/Components/PricingCards.vue';
import FaqAccordion from '@/Components/FaqAccordion.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import { useCurrency } from '@/composables/useCurrency';
import { Head, Link } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import { ref, computed } from 'vue';

const { t, locale } = useI18n();
const { localizedField } = useLocale();
const { formatPrice, currentCurrencyCode } = useCurrency();
useScrollAnimation();

const props = defineProps({
    plans: { type: Array, default: () => [] },
    faqs: { type: Array, default: () => [] },
    currencies: Array,
    currentCurrency: String,
});

const billingCycle = ref('monthly');
const isYearly = computed(() => billingCycle.value === 'yearly');
const isApproximate = computed(() => currentCurrencyCode.value !== 'EGP');

function toggleBilling() {
    billingCycle.value = billingCycle.value === 'monthly' ? 'yearly' : 'monthly';
}

function getPlanPrice(plan) {
    if (plan.is_custom) return null;
    return isYearly.value ? plan.yearly_price : plan.monthly_price;
}

function getFormattedPrice(plan) {
    const price = getPlanPrice(plan);
    if (price === null || price === undefined) return null;
    return formatPrice(price);
}

// Comparison table data
const comparisonCategories = computed(() => [
    {
        name: t('pricing.compare_general'),
        features: [
            { label: t('pricing.compare_users'), values: getCompareValues('max_users') },
            { label: t('pricing.compare_patients'), values: getCompareValues('max_patients') },
            { label: t('pricing.compare_support'), values: getCompareValues('support_level') },
            { label: t('pricing.compare_storage'), values: ['5 GB', '25 GB', '100 GB', t('pricing.compare_unlimited')] },
        ]
    },
    {
        name: t('pricing.compare_clinical'),
        features: [
            { label: t('pricing.compare_patient_records'), values: [true, true, true, true] },
            { label: t('pricing.compare_appointments'), values: [true, true, true, true] },
            { label: t('pricing.compare_prescriptions'), values: [false, true, true, true] },
            { label: t('pricing.compare_dental'), values: [false, false, true, true] },
            { label: t('pricing.compare_lab_results'), values: [false, true, true, true] },
        ]
    },
    {
        name: t('pricing.compare_financial'),
        features: [
            { label: t('pricing.compare_invoicing'), values: [true, true, true, true] },
            { label: t('pricing.compare_payments'), values: [true, true, true, true] },
            { label: t('pricing.compare_insurance'), values: [false, false, true, true] },
            { label: t('pricing.compare_expenses'), values: [false, true, true, true] },
            { label: t('pricing.compare_wallet'), values: [false, true, true, true] },
        ]
    },
    {
        name: t('pricing.compare_management'),
        features: [
            { label: t('pricing.compare_crm'), values: [false, true, true, true] },
            { label: t('pricing.compare_hr'), values: [false, false, true, true] },
            { label: t('pricing.compare_inventory'), values: [false, false, true, true] },
            { label: t('pricing.compare_reports'), values: [t('pricing.compare_basic'), t('pricing.compare_advanced'), t('pricing.compare_full'), t('pricing.compare_full')] },
            { label: t('pricing.compare_rbac'), values: [false, false, true, true] },
            { label: t('pricing.compare_audit'), values: [false, false, true, true] },
        ]
    },
    {
        name: t('pricing.compare_extras'),
        features: [
            { label: t('pricing.compare_website'), values: [true, true, true, true] },
            { label: t('pricing.compare_sms'), values: [false, true, true, true] },
            { label: t('pricing.compare_whatsapp'), values: [false, false, true, true] },
            { label: t('pricing.compare_api'), values: [false, false, true, true] },
            { label: t('pricing.compare_custom_domain'), values: [false, false, true, true] },
            { label: t('pricing.compare_white_label'), values: [false, false, false, true] },
        ]
    },
]);

function getCompareValues(field) {
    return props.plans.map(plan => {
        if (field === 'max_users') {
            return plan.max_users ? `${plan.max_users} ${t('pricing.compare_users_count')}` : t('pricing.compare_unlimited');
        }
        if (field === 'max_patients') {
            return plan.max_patients ? `${plan.max_patients}` : t('pricing.compare_unlimited');
        }
        if (field === 'support_level') {
            const levels = {
                'email': t('pricing.support_email'),
                'phone': t('pricing.support_phone'),
                'priority': t('pricing.support_priority'),
                'dedicated': t('pricing.support_dedicated'),
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

// Add-ons — prices stored natively in EGP (the base currency)
const addons = ref([
    {
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        name_ar: 'مستخدم إضافي',
        name_en: 'Extra user',
        desc_ar: 'أضف مستخدماً جديداً فوق حد الخطة',
        desc_en: 'Add a user above your plan limit',
        price_egp: 99,
        period_ar: 'شهرياً',
        period_en: 'month',
    },
    {
        icon: 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
        name_ar: '1000 رسالة SMS',
        name_en: '1,000 SMS messages',
        desc_ar: 'رسائل تذكير ومواعيد للمرضى',
        desc_en: 'Reminders & appointment SMS',
        price_egp: 249,
        period_ar: 'الباقة',
        period_en: 'bundle',
    },
    {
        icon: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z',
        name_ar: 'واتساب بزنس API',
        name_en: 'WhatsApp Business API',
        desc_ar: 'إرسال تذكيرات ومواعيد عبر واتساب',
        desc_en: 'Send reminders & notifications via WhatsApp',
        price_egp: 399,
        period_ar: 'شهرياً',
        period_en: 'month',
    },
    {
        icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
        name_ar: 'دومين وبريد احترافي',
        name_en: 'Custom domain + email',
        desc_ar: 'نطاق خاص وبريد باسم عيادتك',
        desc_en: 'Your own domain & branded email',
        price_egp: 199,
        period_ar: 'شهرياً',
        period_en: 'month',
    },
    {
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        name_ar: 'نسخ احتياطي كل ساعة',
        name_en: 'Hourly backup',
        desc_ar: 'نسخ احتياطي متكرر وحماية قصوى',
        desc_en: 'Frequent backups, maximum protection',
        price_egp: 299,
        period_ar: 'شهرياً',
        period_en: 'month',
    },
    {
        icon: 'M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z',
        name_ar: 'White-Label كامل',
        name_en: 'Full White-Label',
        desc_ar: 'شعارك وهويتك بدلاً من دكتوراتو',
        desc_en: 'Your branding instead of Doctorato',
        price_egp: 1999,
        period_ar: 'شهرياً',
        period_en: 'month',
    },
]);

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
        :title="t('pricing.page_title') || t('pricing.title')"
        :description="t('pricing.subtitle') || 'خطط مرنة بالجنيه المصري — ابدأ من 799 ج.م شهرياً'"
        :json-ld="pricingJsonLd"
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

                <!-- Launch Offer Banner -->
                <div class="max-w-4xl mx-auto mb-10 animate-fade-up">
                    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#C4A265] via-[#D4B876] to-[#C4A265] p-[1.5px] shadow-xl shadow-[#C4A265]/20">
                        <div class="relative rounded-3xl bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] px-6 py-5 md:px-8 md:py-6 overflow-hidden">
                            <!-- Decorative glows -->
                            <div class="absolute -top-10 end-0 w-40 h-40 bg-[#C4A265]/20 rounded-full blur-3xl"></div>
                            <div class="absolute -bottom-10 start-0 w-40 h-40 bg-[#2471A3]/20 rounded-full blur-3xl"></div>

                            <div class="relative flex flex-col md:flex-row items-center gap-5 md:gap-6">
                                <!-- Fire icon -->
                                <div class="shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center shadow-lg shadow-[#C4A265]/30 animate-pulse">
                                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M13.5 0.67s.74 2.65.74 4.8c0 2.06-1.35 3.73-3.41 3.73-2.07 0-3.63-1.67-3.63-3.73l.03-.36C5.21 7.51 4 10.62 4 14c0 4.42 3.58 8 8 8s8-3.58 8-8C20 8.61 17.41 3.8 13.5.67zM11.71 19c-1.78 0-3.22-1.4-3.22-3.14 0-1.62 1.05-2.76 2.81-3.12 1.77-.36 3.6-1.21 4.62-2.58.39 1.29.59 2.65.59 4.04 0 2.65-2.15 4.8-4.8 4.8z"/>
                                    </svg>
                                </div>

                                <!-- Text -->
                                <div class="flex-1 text-center md:text-start">
                                    <div class="flex items-center justify-center md:justify-start gap-2 mb-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#C4A265] text-white uppercase tracking-wide">{{ locale === 'ar' ? 'عرض الإطلاق' : 'Launch Offer' }}</span>
                                        <span class="text-xs text-white/50">{{ locale === 'ar' ? 'لفترة محدودة' : 'Limited time' }}</span>
                                    </div>
                                    <h3 class="text-lg md:text-xl font-extrabold text-white mb-1">
                                        {{ locale === 'ar' ? 'خصم 30% لأول 50 عيادة' : '30% off for first 50 clinics' }}
                                    </h3>
                                    <p class="text-sm text-white/60 leading-relaxed">
                                        {{ locale === 'ar' ? 'اشترك الآن واحصل على خصم 30% لمدة 6 أشهر + تجهيز ونقل بيانات مجاني + تدريب كامل لفريقك' : 'Subscribe now, get 30% off for 6 months + free onboarding, data migration & team training' }}
                                    </p>
                                </div>

                                <!-- CTA -->
                                <Link
                                    href="/demo"
                                    class="shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-sm bg-white text-[#1B4F72] hover:bg-[#C4A265] hover:text-white transition-all duration-300 shadow-lg hover:-translate-y-0.5"
                                >
                                    {{ locale === 'ar' ? 'احجز الآن' : 'Claim now' }}
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 lg:gap-5 animate-stagger">
                    <div
                        v-for="(plan, planIdx) in plans"
                        :key="plan.id"
                        class="relative group rounded-3xl transition-all duration-500 flex flex-col"
                        :class="{
                            'bg-gradient-to-b from-[#1B4F72] to-[#0A1628] text-white shadow-2xl shadow-[#1B4F72]/20 scale-[1.03] z-10 ring-1 ring-white/10': plan.is_popular,
                            'bg-white border border-gray-100 shadow-lg hover:shadow-xl hover:border-[#C4A265]/20': !plan.is_popular && !plan.is_custom,
                            'bg-gradient-to-b from-[#C4A265]/5 to-white border-2 border-dashed border-[#C4A265]/30 shadow-lg': plan.is_custom,
                        }"
                    >
                        <!-- Popular Badge -->
                        <div v-if="plan.is_popular" class="absolute -top-4 inset-x-0 flex justify-center">
                            <span class="inline-flex items-center gap-1.5 px-5 py-1.5 rounded-full text-xs font-bold bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-lg shadow-[#C4A265]/30">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                {{ t('pricing.most_popular') }}
                            </span>
                        </div>

                        <div class="p-7 sm:p-8 flex flex-col flex-1">
                            <!-- Plan Name -->
                            <h3 class="text-lg font-bold mb-1" :class="plan.is_popular ? 'text-white' : 'text-[#1C2833]'">
                                {{ localizedField(plan, 'name') }}
                            </h3>
                            <p class="text-sm mb-5 leading-relaxed" :class="plan.is_popular ? 'text-white/60' : 'text-gray-400'">
                                {{ localizedField(plan, 'description') }}
                            </p>

                            <!-- Price -->
                            <div class="mb-6">
                                <template v-if="!plan.is_custom">
                                    <div class="flex items-baseline gap-1 flex-wrap">
                                        <span class="text-2xl md:text-3xl font-extrabold whitespace-nowrap" :class="plan.is_popular ? 'text-white' : 'text-[#1B4F72]'">
                                            {{ getFormattedPrice(plan) }}
                                        </span>
                                        <span class="text-xs whitespace-nowrap" :class="plan.is_popular ? 'text-white/50' : 'text-gray-400'">
                                            / {{ isYearly ? t('pricing.year') : t('pricing.month') }}
                                        </span>
                                    </div>
                                    <p v-if="isYearly" class="text-xs mt-1" :class="plan.is_popular ? 'text-emerald-300' : 'text-emerald-500'">
                                        {{ t('pricing.save_20') }}
                                    </p>
                                </template>
                                <template v-else>
                                    <span class="text-xl md:text-2xl font-extrabold text-[#C4A265]">
                                        {{ t('pricing.custom_quote') }}
                                    </span>
                                </template>
                            </div>

                            <!-- Key Specs -->
                            <div class="grid grid-cols-2 gap-3 mb-6">
                                <div class="rounded-xl p-3" :class="plan.is_popular ? 'bg-white/[0.06]' : 'bg-gray-50'">
                                    <div class="text-xs mb-0.5" :class="plan.is_popular ? 'text-white/50' : 'text-gray-400'">{{ t('pricing.compare_users') }}</div>
                                    <div class="text-sm font-bold" :class="plan.is_popular ? 'text-white' : 'text-[#1C2833]'">
                                        {{ plan.max_users ? plan.max_users : t('pricing.compare_unlimited') }}
                                    </div>
                                </div>
                                <div class="rounded-xl p-3" :class="plan.is_popular ? 'bg-white/[0.06]' : 'bg-gray-50'">
                                    <div class="text-xs mb-0.5" :class="plan.is_popular ? 'text-white/50' : 'text-gray-400'">{{ t('pricing.compare_patients') }}</div>
                                    <div class="text-sm font-bold" :class="plan.is_popular ? 'text-white' : 'text-[#1C2833]'">
                                        {{ plan.max_patients ? plan.max_patients : t('pricing.compare_unlimited') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Divider -->
                            <div class="h-px mb-6" :class="plan.is_popular ? 'bg-white/10' : 'bg-gray-100'"></div>

                            <!-- Features -->
                            <ul class="space-y-3 mb-8 flex-1">
                                <li
                                    v-for="(feature, idx) in (localizedField(plan, 'features') || []).slice(0, 8)"
                                    :key="idx"
                                    class="flex items-start gap-2.5"
                                >
                                    <div class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5"
                                         :class="plan.is_popular ? 'bg-[#C4A265]/20' : 'bg-emerald-50'">
                                        <svg class="w-3 h-3" :class="plan.is_popular ? 'text-[#C4A265]' : 'text-emerald-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-sm leading-relaxed" :class="plan.is_popular ? 'text-white/80' : 'text-gray-600'">
                                        {{ typeof feature === 'string' ? feature : localizedField(feature, 'text') }}
                                    </span>
                                </li>
                                <li v-if="(localizedField(plan, 'features') || []).length > 8"
                                    class="text-xs font-medium pt-1"
                                    :class="plan.is_popular ? 'text-[#C4A265]' : 'text-[#1B4F72]'"
                                >
                                    + {{ (localizedField(plan, 'features') || []).length - 8 }} {{ t('pricing.more_features') }}
                                </li>
                            </ul>

                            <!-- CTA Button -->
                            <Link
                                v-if="!plan.is_custom"
                                :href="`/checkout/${plan.slug}?cycle=${billingCycle}`"
                                class="block w-full text-center py-3.5 px-6 rounded-xl font-semibold text-sm transition-all duration-300 hover:-translate-y-0.5"
                                :class="plan.is_popular
                                    ? 'bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-lg shadow-[#C4A265]/25 hover:shadow-xl'
                                    : 'border-2 border-[#1B4F72] text-[#1B4F72] hover:bg-[#1B4F72] hover:text-white'"
                            >
                                {{ t('pricing.get_started') }}
                            </Link>
                            <Link
                                v-else
                                href="/contact"
                                class="block w-full text-center py-3.5 px-6 rounded-xl font-semibold text-sm bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-lg shadow-[#C4A265]/20 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-xl"
                            >
                                {{ t('pricing.contact_for_offer') }}
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- VAT Note -->
                <p class="text-center text-xs text-gray-400 mt-8">{{ t('pricing.vat_note') }}</p>
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
                        v-for="(addon, idx) in addons"
                        :key="idx"
                        class="group relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:border-[#C4A265]/30 hover:-translate-y-1 transition-all duration-300 animate-fade-up"
                    >
                        <div class="flex items-start gap-4">
                            <div class="shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[#1B4F72]/10 to-[#C4A265]/10 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-[#1B4F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="addon.icon"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-[#1C2833] mb-1">
                                    {{ locale === 'ar' ? addon.name_ar : addon.name_en }}
                                </h3>
                                <p class="text-xs text-gray-500 leading-relaxed mb-3">
                                    {{ locale === 'ar' ? addon.desc_ar : addon.desc_en }}
                                </p>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl font-extrabold text-[#1B4F72]">{{ formatPrice(addon.price_egp) }}</span>
                                    <span class="text-xs text-gray-400">/ {{ locale === 'ar' ? addon.period_ar : addon.period_en }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-center text-sm text-gray-400 mt-10">
                    {{ locale === 'ar' ? '* جميع الإضافات يمكن تفعيلها أو إلغاؤها في أي وقت من لوحة التحكم' : '* All add-ons can be enabled or disabled anytime from your dashboard' }}
                </p>
            </div>
        </section>

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
                                        <div v-if="!plan.is_custom" class="text-xs text-gray-400">
                                            {{ getFormattedPrice(plan) }} / {{ isYearly ? t('pricing.year') : t('pricing.month') }}
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
                                            :href="plan.is_custom ? '/contact' : `/checkout/${plan.slug}?cycle=${billingCycle}`"
                                            class="inline-block px-5 py-2.5 rounded-lg text-xs font-bold transition-all duration-300 hover:-translate-y-0.5"
                                            :class="plan.is_popular
                                                ? 'bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-md hover:shadow-lg'
                                                : 'border border-[#1B4F72]/20 text-[#1B4F72] hover:bg-[#1B4F72] hover:text-white hover:border-[#1B4F72]'"
                                        >
                                            {{ plan.is_custom ? t('pricing.contact_for_offer') : t('pricing.get_started') }}
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
