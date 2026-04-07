<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DemoRequestForm from '@/Components/DemoRequestForm.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
useScrollAnimation();

const featureCategories = computed(() => [
    {
        key: 'clinic',
        title: t('demo.cat_clinic_title'),
        subtitle: t('demo.cat_clinic_subtitle'),
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />`,
        features: [
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />`,
                title: t('demo.feature_ehr_title'),
                description: t('demo.feature_ehr_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />`,
                title: t('demo.feature_scheduling_title'),
                description: t('demo.feature_scheduling_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />`,
                title: t('demo.feature_crm_title'),
                description: t('demo.feature_crm_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />`,
                title: t('demo.feature_dental_title'),
                description: t('demo.feature_dental_desc'),
            },
        ],
    },
    {
        key: 'operations',
        title: t('demo.cat_ops_title'),
        subtitle: t('demo.cat_ops_subtitle'),
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />`,
        features: [
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />`,
                title: t('demo.feature_billing_title'),
                description: t('demo.feature_billing_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />`,
                title: t('demo.feature_hr_title'),
                description: t('demo.feature_hr_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />`,
                title: t('demo.feature_inventory_title'),
                description: t('demo.feature_inventory_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />`,
                title: t('demo.feature_insurance_title'),
                description: t('demo.feature_insurance_desc'),
            },
        ],
    },
    {
        key: 'tech',
        title: t('demo.cat_tech_title'),
        subtitle: t('demo.cat_tech_subtitle'),
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />`,
        features: [
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />`,
                title: t('demo.feature_security_title'),
                description: t('demo.feature_security_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />`,
                title: t('demo.feature_cloud_title'),
                description: t('demo.feature_cloud_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />`,
                title: t('demo.feature_mobile_title'),
                description: t('demo.feature_mobile_desc'),
            },
            {
                icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />`,
                title: t('demo.feature_reports_title'),
                description: t('demo.feature_reports_desc'),
            },
        ],
    },
]);
</script>

<template>
    <Head :title="t('demo.page_title')" />
    <MainLayout>
        <!-- Hero Section -->
        <section class="relative py-24 bg-gradient-to-br from-primary via-primary-dark to-primary overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 start-20 w-72 h-72 bg-secondary rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="relative container mx-auto px-4 text-center">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 animate-fade-up">
                    {{ t('demo.hero_title') }}
                </h1>
                <p class="text-xl text-white/80 max-w-3xl mx-auto animate-fade-up">
                    {{ t('demo.hero_subtitle') }}
                </p>
            </div>
        </section>

        <!-- Features Overview -->
        <section class="relative py-20 bg-gradient-to-b from-light-blue via-white to-light-blue overflow-hidden">
            <!-- Decorative background -->
            <div class="absolute inset-0 pointer-events-none opacity-40">
                <div class="absolute top-10 start-1/4 w-72 h-72 bg-primary/5 rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-1/4 w-96 h-96 bg-secondary/5 rounded-full blur-3xl"></div>
            </div>

            <div class="relative container mx-auto px-4">
                <div class="max-w-7xl mx-auto">
                    <!-- Section heading -->
                    <div class="text-center mb-14 animate-fade-up">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-semibold uppercase tracking-wider mb-4">
                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                            {{ t('demo.features_badge') }}
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-dark mb-3">
                            {{ t('demo.features_title') }}
                        </h2>
                        <p class="text-gray max-w-2xl mx-auto">
                            {{ t('demo.features_subtitle') }}
                        </p>
                    </div>

                    <!-- Categories -->
                    <div class="space-y-14">
                        <div
                            v-for="(category, cIdx) in featureCategories"
                            :key="category.key"
                            class="animate-fade-up"
                        >
                            <!-- Category header -->
                            <div class="flex items-center gap-4 mb-6">
                                <div class="relative shrink-0">
                                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center shadow-lg shadow-primary/20">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="category.icon"></svg>
                                    </div>
                                    <div class="absolute -top-1 -end-1 w-5 h-5 rounded-full bg-secondary text-white text-[10px] font-bold flex items-center justify-center shadow-md">
                                        {{ cIdx + 1 }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl md:text-2xl font-bold text-dark">{{ category.title }}</h3>
                                    <p class="text-sm text-gray">{{ category.subtitle }}</p>
                                </div>
                                <div class="hidden md:block flex-1 h-px bg-gradient-to-r from-primary/20 via-secondary/20 to-transparent"></div>
                            </div>

                            <!-- Features grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                                <div
                                    v-for="(feature, fIdx) in category.features"
                                    :key="fIdx"
                                    class="group relative bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-primary/20 transition-all duration-500 overflow-hidden"
                                >
                                    <!-- Gold accent line -->
                                    <div class="absolute top-0 inset-x-0 h-0.5 bg-gradient-to-r from-transparent via-secondary to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                                    <!-- Icon -->
                                    <div class="relative mb-4">
                                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-transform duration-500">
                                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="feature.icon"></svg>
                                        </div>
                                    </div>

                                    <h4 class="font-bold text-dark mb-2 group-hover:text-primary transition-colors">{{ feature.title }}</h4>
                                    <p class="text-gray text-sm leading-relaxed">{{ feature.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats bar -->
                    <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 p-6 md:p-8 rounded-3xl bg-gradient-to-br from-primary to-primary-dark text-white shadow-2xl shadow-primary/20 animate-fade-up">
                        <div class="text-center">
                            <div class="text-3xl md:text-4xl font-bold text-secondary mb-1">800+</div>
                            <div class="text-xs md:text-sm text-white/70 uppercase tracking-wide">{{ t('demo.stat_features') }}</div>
                        </div>
                        <div class="text-center border-s border-white/10">
                            <div class="text-3xl md:text-4xl font-bold text-secondary mb-1">6</div>
                            <div class="text-xs md:text-sm text-white/70 uppercase tracking-wide">{{ t('demo.stat_portals') }}</div>
                        </div>
                        <div class="text-center border-s border-white/10">
                            <div class="text-3xl md:text-4xl font-bold text-secondary mb-1">95+</div>
                            <div class="text-xs md:text-sm text-white/70 uppercase tracking-wide">{{ t('demo.stat_modules') }}</div>
                        </div>
                        <div class="text-center border-s border-white/10">
                            <div class="text-3xl md:text-4xl font-bold text-secondary mb-1">24/7</div>
                            <div class="text-xs md:text-sm text-white/70 uppercase tracking-wide">{{ t('demo.stat_support') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Demo Request Form (full-page usage) -->
        <DemoRequestForm />

        <!-- Bottom CTA -->
        <section class="py-16 bg-light-blue">
            <div class="container mx-auto px-4 text-center animate-fade-up">
                <div class="max-w-2xl mx-auto">
                    <h2 class="text-2xl font-bold text-dark mb-4">{{ t('demo.bottom_title') }}</h2>
                    <p class="text-gray mb-4">{{ t('demo.bottom_subtitle') }}</p>
                    <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-3 text-sm text-gray">
                        <a href="tel:+201012967285" class="flex items-center gap-2 hover:text-primary transition-colors">
                            <span class="text-lg leading-none">🇪🇬</span>
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span dir="ltr">+20 101 296 7285</span>
                        </a>
                        <span class="text-gray-light hidden sm:inline">|</span>
                        <a href="tel:+971557961688" class="flex items-center gap-2 hover:text-primary transition-colors">
                            <span class="text-lg leading-none">🇦🇪</span>
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span dir="ltr">+971 55 796 1688</span>
                        </a>
                        <span class="text-gray-light hidden sm:inline">|</span>
                        <a href="mailto:info@markeza-group.com" class="flex items-center gap-2 hover:text-primary transition-colors">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span dir="ltr">info@markeza-group.com</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
