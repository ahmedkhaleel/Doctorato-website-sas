<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const { t } = useI18n();
useScrollAnimation();

const activeTab = ref('patients');

const tabs = computed(() => [
    {
        key: 'patients',
        label: t('modules.patients'),
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        headline: t('modules.patients_headline'),
        description: t('modules.patients_desc'),
        features: [
            t('modules.patients_f1'), t('modules.patients_f2'),
            t('modules.patients_f3'), t('modules.patients_f4'),
            t('modules.patients_f5'), t('modules.patients_f6'),
        ],
    },
    {
        key: 'bookings',
        label: t('modules.bookings'),
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        headline: t('modules.bookings_headline'),
        description: t('modules.bookings_desc'),
        features: [
            t('modules.bookings_f1'), t('modules.bookings_f2'),
            t('modules.bookings_f3'), t('modules.bookings_f4'),
            t('modules.bookings_f5'), t('modules.bookings_f6'),
        ],
    },
    {
        key: 'invoicing',
        label: t('modules.invoicing'),
        icon: 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z',
        headline: t('modules.invoicing_headline'),
        description: t('modules.invoicing_desc'),
        features: [
            t('modules.invoicing_f1'), t('modules.invoicing_f2'),
            t('modules.invoicing_f3'), t('modules.invoicing_f4'),
            t('modules.invoicing_f5'), t('modules.invoicing_f6'),
        ],
    },
    {
        key: 'dental',
        label: t('modules.dental'),
        icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
        headline: t('modules.dental_headline'),
        description: t('modules.dental_desc'),
        features: [
            t('modules.dental_f1'), t('modules.dental_f2'),
            t('modules.dental_f3'), t('modules.dental_f4'),
            t('modules.dental_f5'), t('modules.dental_f6'),
        ],
    },
    {
        key: 'crm',
        label: t('modules.crm'),
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        headline: t('modules.crm_headline'),
        description: t('modules.crm_desc'),
        features: [
            t('modules.crm_f1'), t('modules.crm_f2'),
            t('modules.crm_f3'), t('modules.crm_f4'),
            t('modules.crm_f5'), t('modules.crm_f6'),
        ],
    },
    {
        key: 'hr',
        label: t('modules.hr'),
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        headline: t('modules.hr_headline'),
        description: t('modules.hr_desc'),
        features: [
            t('modules.hr_f1'), t('modules.hr_f2'),
            t('modules.hr_f3'), t('modules.hr_f4'),
            t('modules.hr_f5'), t('modules.hr_f6'),
        ],
    },
    {
        key: 'insurance',
        label: t('modules.insurance'),
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        headline: t('modules.insurance_headline'),
        description: t('modules.insurance_desc'),
        features: [
            t('modules.insurance_f1'), t('modules.insurance_f2'),
            t('modules.insurance_f3'), t('modules.insurance_f4'),
            t('modules.insurance_f5'), t('modules.insurance_f6'),
        ],
    },
    {
        key: 'inventory',
        label: t('modules.inventory'),
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        headline: t('modules.inventory_headline'),
        description: t('modules.inventory_desc'),
        features: [
            t('modules.inventory_f1'), t('modules.inventory_f2'),
            t('modules.inventory_f3'), t('modules.inventory_f4'),
            t('modules.inventory_f5'), t('modules.inventory_f6'),
        ],
    },
    {
        key: 'reports',
        label: t('modules.reports'),
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        headline: t('modules.reports_headline'),
        description: t('modules.reports_desc'),
        features: [
            t('modules.reports_f1'), t('modules.reports_f2'),
            t('modules.reports_f3'), t('modules.reports_f4'),
            t('modules.reports_f5'), t('modules.reports_f6'),
        ],
    },
    {
        key: 'website',
        label: t('modules.website'),
        icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
        headline: t('modules.website_headline'),
        description: t('modules.website_desc'),
        features: [
            t('modules.website_f1'), t('modules.website_f2'),
            t('modules.website_f3'), t('modules.website_f4'),
            t('modules.website_f5'), t('modules.website_f6'),
        ],
    },
]);

const activeModule = computed(() => tabs.value.find(tab => tab.key === activeTab.value) || tabs.value[0]);
</script>

<template>
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12 animate-fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    {{ $t('modules.title') }}
                </h2>
                <p class="text-lg text-gray max-w-2xl mx-auto">
                    {{ $t('modules.subtitle') }}
                </p>
                <div class="w-20 h-1 bg-secondary mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Tab Bar -->
            <div class="mb-10 animate-fade-up">
                <div class="flex overflow-x-auto gap-1 pb-2 scrollbar-hide border-b border-gray-light/30">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        class="flex items-center gap-2 px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-all duration-200 -mb-px"
                        :class="activeTab === tab.key
                            ? 'border-secondary text-secondary'
                            : 'border-transparent text-gray hover:text-dark hover:border-gray-light/50'"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="tab.icon" />
                        </svg>
                        <span>{{ tab.label }}</span>
                    </button>
                </div>
            </div>

            <!-- Tab Content -->
            <Transition
                mode="out-in"
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-4"
            >
                <div :key="activeTab" class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                    <!-- Content -->
                    <div>
                        <h3 class="text-2xl font-bold text-dark mb-4">{{ activeModule.headline }}</h3>
                        <p class="text-gray leading-relaxed mb-8">{{ activeModule.description }}</p>

                        <!-- Feature Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div
                                v-for="(feature, idx) in activeModule.features"
                                :key="idx"
                                class="flex items-start gap-2"
                            >
                                <svg class="w-5 h-5 text-success shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-sm text-dark">{{ feature }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Mockup Placeholder -->
                    <div class="bg-gradient-to-br from-primary/5 to-secondary/5 rounded-2xl border border-gray-light/20 p-8 flex items-center justify-center min-h-[320px]">
                        <div class="text-center">
                            <div class="w-20 h-20 rounded-2xl bg-secondary/10 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="activeModule.icon" />
                                </svg>
                            </div>
                            <p class="text-gray-light text-sm font-medium">{{ activeModule.label }} {{ $t('modules.preview') }}</p>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </section>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
