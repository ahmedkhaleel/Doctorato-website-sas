<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DemoRequestForm from '@/Components/DemoRequestForm.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
useScrollAnimation();

const features = computed(() => [
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
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />`,
        title: t('demo.feature_analytics_title'),
        description: t('demo.feature_analytics_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />`,
        title: t('demo.feature_billing_title'),
        description: t('demo.feature_billing_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />`,
        title: t('demo.feature_security_title'),
        description: t('demo.feature_security_desc'),
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />`,
        title: t('demo.feature_multilang_title'),
        description: t('demo.feature_multilang_desc'),
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
        <section class="py-16 bg-light-blue">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <h2 class="text-2xl md:text-3xl font-bold text-dark text-center mb-10 animate-fade-up">
                        {{ t('demo.features_title') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div
                            v-for="(feature, idx) in features"
                            :key="idx"
                            class="flex items-start gap-4 bg-white rounded-xl p-5 shadow-sm animate-fade-up"
                        >
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="feature.icon"></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-dark mb-1">{{ feature.title }}</h3>
                                <p class="text-gray text-sm leading-relaxed">{{ feature.description }}</p>
                            </div>
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
                    <div class="flex items-center justify-center gap-4 text-sm text-gray">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>+966 50 000 0000</span>
                        </div>
                        <span class="text-gray-light">|</span>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>info@doctorato.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
