<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import { useCurrency } from '@/composables/useCurrency';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import CurrencySwitcher from '@/Components/CurrencySwitcher.vue';

const props = defineProps({
    plans: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { localizedField, isRtl } = useLocale();
const { formatPrice, currentCurrencyCode } = useCurrency();

useScrollAnimation();

const billingCycle = ref('monthly');
const isYearly = computed(() => billingCycle.value === 'yearly');
const isApproximate = computed(() => currentCurrencyCode.value !== 'SAR');

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
</script>

<template>
    <section id="pricing" class="py-20 bg-light-blue">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12 animate-fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    {{ $t('pricing.title') }}
                </h2>
                <p class="text-lg text-gray max-w-2xl mx-auto">
                    {{ $t('pricing.subtitle') }}
                </p>
                <div class="w-20 h-1 bg-secondary mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Currency Switcher -->
            <div class="flex justify-center mb-8">
                <CurrencySwitcher />
            </div>

            <!-- Billing Toggle -->
            <div class="flex items-center justify-center gap-4 mb-10 animate-fade-up">
                <span
                    class="text-sm font-medium transition-colors"
                    :class="!isYearly ? 'text-dark' : 'text-gray'"
                >
                    {{ $t('pricing.monthly') }}
                </span>

                <button
                    @click="toggleBilling"
                    class="relative w-16 h-8 rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-secondary/50"
                    :class="isYearly ? 'bg-secondary' : 'bg-gray-light'"
                    :aria-label="$t('pricing.toggle_billing')"
                >
                    <span
                        class="absolute top-1 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300"
                        :class="isYearly ? (isRtl ? 'start-1' : 'start-9') : (isRtl ? 'start-9' : 'start-1')"
                        :style="{ insetInlineStart: isYearly ? '2.25rem' : '0.25rem' }"
                    />
                </button>

                <span
                    class="text-sm font-medium transition-colors"
                    :class="isYearly ? 'text-dark' : 'text-gray'"
                >
                    {{ $t('pricing.yearly') }}
                </span>

                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 scale-90"
                    enter-to-class="opacity-100 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-90"
                >
                    <span
                        v-if="isYearly"
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-success/10 text-success"
                    >
                        {{ $t('pricing.save_20') }}
                    </span>
                </Transition>
            </div>

            <!-- Approximate Price Note -->
            <p v-if="isApproximate" class="text-center text-sm text-gray mb-6 animate-fade-up">
                {{ $t('pricing.approximate_note') }}
            </p>

            <!-- Pricing Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-stagger">
                <div
                    v-for="plan in plans"
                    :key="plan.id"
                    class="relative bg-white rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl flex flex-col"
                    :class="{
                        'ring-2 ring-secondary scale-105 z-10': plan.is_popular,
                        'border-2 border-dashed border-primary/30': plan.is_custom,
                    }"
                >
                    <!-- Popular Badge -->
                    <div
                        v-if="plan.is_popular"
                        class="absolute -top-4 inset-x-0 flex justify-center"
                    >
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-secondary text-white shadow-lg">
                            {{ $t('pricing.most_popular') }}
                        </span>
                    </div>

                    <div class="p-6 sm:p-8 flex flex-col flex-1">
                        <!-- Plan Name -->
                        <h3 class="text-xl font-bold text-dark mb-2">
                            {{ localizedField(plan, 'name') }}
                        </h3>

                        <!-- Price -->
                        <div class="mb-4">
                            <template v-if="!plan.is_custom">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold text-primary">
                                        {{ getFormattedPrice(plan) }}
                                    </span>
                                    <span class="text-sm text-gray">
                                        / {{ isYearly ? $t('pricing.year') : $t('pricing.month') }}
                                    </span>
                                </div>
                            </template>
                            <template v-else>
                                <span class="text-2xl font-bold text-primary">
                                    {{ $t('pricing.contact_us') }}
                                </span>
                            </template>
                        </div>

                        <!-- Description -->
                        <p class="text-sm text-gray mb-6">
                            {{ localizedField(plan, 'description') }}
                        </p>

                        <!-- Divider -->
                        <div class="border-t border-gray-light/30 mb-6"></div>

                        <!-- Features List -->
                        <ul class="space-y-3 mb-8 flex-1">
                            <li
                                v-for="(feature, idx) in plan.features"
                                :key="idx"
                                class="flex items-start gap-3"
                            >
                                <svg class="w-5 h-5 text-success shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-dark">
                                    {{ typeof feature === 'string' ? feature : localizedField(feature, 'text') }}
                                </span>
                            </li>
                        </ul>

                        <!-- CTA Button -->
                        <Link
                            v-if="!plan.is_custom"
                            href="/demo"
                            class="block w-full text-center py-3 px-6 rounded-full font-semibold transition-all duration-300 hover:-translate-y-0.5"
                            :class="plan.is_popular
                                ? 'bg-secondary hover:bg-secondary-dark text-white shadow-lg hover:shadow-xl hover:shadow-secondary/25'
                                : 'border-2 border-primary text-primary hover:bg-primary hover:text-white'"
                        >
                            {{ $t('pricing.get_started') }}
                        </Link>
                        <Link
                            v-else
                            href="/demo"
                            class="block w-full text-center py-3 px-6 rounded-full font-semibold bg-primary hover:bg-primary-dark text-white transition-all duration-300 hover:-translate-y-0.5"
                        >
                            {{ $t('pricing.contact_for_offer') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
