<script setup>
/**
 * PricingCardV2 — post-reset (May 2026).
 *
 * Replaces LaunchPricingCard. Removed: launch/anchor strikethrough,
 * yearly setup discount, 3-installment block.
 *
 * Adds: per-doctor pricing line, Contact-Sales mode, free-trial badge,
 * free-setup chip, "save 2 months" annual signal.
 */
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    plan: { type: Object, required: true },
    isYearly: { type: Boolean, default: false },
});

const { t, locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');

const name = computed(() => isAr.value ? props.plan.name_ar : props.plan.name_en);
const description = computed(() => isAr.value ? props.plan.description_ar : props.plan.description_en);
const features = computed(() => isAr.value ? (props.plan.features_ar || []) : (props.plan.features_en || []));

const isContactSales = computed(() => !!props.plan.is_contact_sales);
const isPopular = computed(() => !!props.plan.is_popular);
const currencySymbol = computed(() => props.plan.currency_symbol || 'ج.م');

// Effective price for the selected cycle
const headlinePrice = computed(() => {
    if (isContactSales.value) return null;
    if (props.isYearly) {
        // Yearly cycle → show monthly-equivalent (yearly / 12) with badge
        return Math.round((props.plan.yearly_monthly_equivalent || (props.plan.yearly_price / 12)) || 0);
    }
    return Math.round(props.plan.monthly_price || 0);
});

const yearlyTotal = computed(() => Math.round(props.plan.yearly_price || 0));
const setupFee = computed(() => Math.round(props.plan.setup_fee || 0));
const perExtraDoctor = computed(() => props.plan.per_extra_doctor_price
    ? Math.round(props.plan.per_extra_doctor_price)
    : null);

function fmt(n) {
    return new Intl.NumberFormat(isAr.value ? 'ar-EG' : 'en-US').format(n);
}

const ctaHref = computed(() => isContactSales.value
    ? '/contact?topic=enterprise'
    : `/demo?plan=${props.plan.slug}`);

const ctaLabel = computed(() => {
    if (isContactSales.value) return isAr.value ? 'تواصل مع المبيعات' : 'Contact sales';
    return isAr.value ? 'ابدأ التجربة المجانية' : 'Start free trial';
});
</script>

<template>
    <div
        class="relative flex flex-col rounded-2xl border bg-white p-7 transition-shadow"
        :class="isPopular
            ? 'border-[var(--color-secondary)] shadow-[0_8px_30px_rgba(196,162,101,0.18)] ring-1 ring-[var(--color-secondary)]/40'
            : 'border-gray-200 hover:shadow-lg'"
    >
        <!-- Most Popular ribbon -->
        <div
            v-if="isPopular"
            class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-[var(--color-secondary)] px-4 py-1 text-xs font-semibold text-white shadow-md"
        >
            {{ isAr ? 'الأكثر اختيارًا' : 'Most popular' }}
        </div>

        <!-- Header -->
        <div class="mb-5">
            <h3 class="text-xl font-bold text-[var(--color-primary)]">{{ name }}</h3>
            <p class="mt-1 text-sm text-gray-600 leading-relaxed min-h-[2.5rem]">{{ description }}</p>
        </div>

        <!-- Price block -->
        <div class="mb-5 pb-5 border-b border-gray-100">
            <template v-if="isContactSales">
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-[var(--color-primary)]">
                        {{ isAr ? 'تسعير مخصص' : 'Custom pricing' }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    {{ isAr
                        ? 'مفصّل حسب حجم المؤسسة وعدد الفروع — تواصل معنا لعرض سعر'
                        : 'Tailored to organization size & branches — talk to sales' }}
                </p>
            </template>

            <template v-else>
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl font-bold text-[var(--color-primary)]">{{ fmt(headlinePrice) }}</span>
                    <span class="text-base text-gray-600">{{ currencySymbol }}</span>
                    <span class="text-sm text-gray-500">/ {{ isAr ? 'شهريًا' : 'mo' }}</span>
                </div>

                <!-- Yearly savings hint -->
                <div v-if="isYearly" class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ isAr ? `وفّر شهرين — ${fmt(yearlyTotal)} ${currencySymbol} سنويًا` : `Save 2 months — ${fmt(yearlyTotal)} ${currencySymbol}/yr` }}
                </div>

                <!-- Per-extra-doctor line -->
                <div v-if="perExtraDoctor" class="mt-2 text-xs text-gray-600">
                    + {{ fmt(perExtraDoctor) }} {{ currencySymbol }} {{ isAr ? 'لكل طبيب إضافي / شهر' : 'per extra doctor / mo' }}
                </div>

                <!-- Setup fee row -->
                <div class="mt-3 flex items-center gap-1.5 text-xs">
                    <template v-if="setupFee === 0">
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 font-semibold text-emerald-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3a1 1 0 00-1.4-1.4L9 10.6 7.7 9.3a1 1 0 00-1.4 1.4l2 2a1 1 0 001.4 0l4-4z"/></svg>
                            {{ isAr ? 'تركيب مجاني' : 'Free setup' }}
                        </span>
                        <span class="text-gray-500">·</span>
                        <span class="text-gray-600">{{ isAr ? 'ترحيل بيانات مجاني' : 'Free data migration' }}</span>
                    </template>
                    <template v-else>
                        <span class="text-gray-700">
                            <span class="font-semibold">{{ fmt(setupFee) }} {{ currencySymbol }}</span>
                            <span class="text-gray-500"> — {{ isAr ? 'تركيب اختياري لمرة واحدة (White-glove)' : 'optional one-time white-glove setup' }}</span>
                        </span>
                    </template>
                </div>
            </template>
        </div>

        <!-- CTA -->
        <Link
            :href="ctaHref"
            class="block w-full rounded-xl px-5 py-3 text-center text-sm font-semibold transition-colors"
            :class="isPopular
                ? 'bg-[var(--color-primary)] text-white hover:bg-[var(--color-primary)]/90'
                : 'border-2 border-[var(--color-primary)] text-[var(--color-primary)] hover:bg-[var(--color-primary)] hover:text-white'"
        >
            {{ ctaLabel }}
        </Link>

        <!-- Trial line -->
        <div v-if="!isContactSales" class="mt-2 text-center text-[11px] text-gray-500">
            {{ isAr ? `تجربة مجانية ${plan.trial_days || 30} يوم — بدون بطاقة ائتمان` : `${plan.trial_days || 30}-day free trial — no credit card` }}
        </div>

        <!-- Features list -->
        <ul class="mt-6 space-y-2.5 text-sm">
            <li v-for="(feature, i) in features" :key="i" class="flex items-start gap-2 text-gray-700 leading-relaxed">
                <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-[var(--color-secondary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ feature }}</span>
            </li>
        </ul>
    </div>
</template>
