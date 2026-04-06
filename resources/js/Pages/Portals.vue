<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
useScrollAnimation();

const portals = computed(() => [
    {
        id: 'admin',
        iconPath: 'M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75',
        color: 'from-primary to-primary-dark',
        accentBg: 'bg-primary/10',
        accentText: 'text-primary',
        featureBg: 'bg-primary/5',
        featureBgHover: 'hover:bg-primary/10',
        badge: '95+',
        badgeLabel: t('portals_page.sub_modules'),
        title: t('portals_page.admin_title'),
        subtitle: t('portals_page.admin_subtitle'),
        description: t('portals_page.admin_description'),
        features: [
            t('portals_page.admin_feature_1'),
            t('portals_page.admin_feature_2'),
            t('portals_page.admin_feature_3'),
            t('portals_page.admin_feature_4'),
            t('portals_page.admin_feature_5'),
            t('portals_page.admin_feature_6'),
        ],
    },
    {
        id: 'doctor',
        iconPath: 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z',
        color: 'from-emerald-600 to-emerald-800',
        accentBg: 'bg-emerald-500/10',
        accentText: 'text-emerald-600',
        featureBg: 'bg-emerald-500/5',
        featureBgHover: 'hover:bg-emerald-500/10',
        badge: null,
        badgeLabel: null,
        title: t('portals_page.doctor_title'),
        subtitle: t('portals_page.doctor_subtitle'),
        description: t('portals_page.doctor_description'),
        features: [
            t('portals_page.doctor_feature_1'),
            t('portals_page.doctor_feature_2'),
            t('portals_page.doctor_feature_3'),
            t('portals_page.doctor_feature_4'),
            t('portals_page.doctor_feature_5'),
            t('portals_page.doctor_feature_6'),
        ],
    },
    {
        id: 'patient',
        iconPath: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        color: 'from-sky-500 to-sky-700',
        accentBg: 'bg-sky-500/10',
        accentText: 'text-sky-600',
        featureBg: 'bg-sky-500/5',
        featureBgHover: 'hover:bg-sky-500/10',
        badge: null,
        badgeLabel: null,
        title: t('portals_page.patient_title'),
        subtitle: t('portals_page.patient_subtitle'),
        description: t('portals_page.patient_description'),
        features: [
            t('portals_page.patient_feature_1'),
            t('portals_page.patient_feature_2'),
            t('portals_page.patient_feature_3'),
            t('portals_page.patient_feature_4'),
            t('portals_page.patient_feature_5'),
            t('portals_page.patient_feature_6'),
        ],
    },
    {
        id: 'secretary',
        iconPath: 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        color: 'from-amber-500 to-amber-700',
        accentBg: 'bg-amber-500/10',
        accentText: 'text-amber-600',
        featureBg: 'bg-amber-500/5',
        featureBgHover: 'hover:bg-amber-500/10',
        badge: null,
        badgeLabel: null,
        title: t('portals_page.secretary_title'),
        subtitle: t('portals_page.secretary_subtitle'),
        description: t('portals_page.secretary_description'),
        features: [
            t('portals_page.secretary_feature_1'),
            t('portals_page.secretary_feature_2'),
            t('portals_page.secretary_feature_3'),
            t('portals_page.secretary_feature_4'),
            t('portals_page.secretary_feature_5'),
            t('portals_page.secretary_feature_6'),
        ],
    },
    {
        id: 'webmaster',
        iconPath: 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
        color: 'from-violet-600 to-violet-800',
        accentBg: 'bg-violet-500/10',
        accentText: 'text-violet-600',
        featureBg: 'bg-violet-500/5',
        featureBgHover: 'hover:bg-violet-500/10',
        badge: null,
        badgeLabel: null,
        title: t('portals_page.webmaster_title'),
        subtitle: t('portals_page.webmaster_subtitle'),
        description: t('portals_page.webmaster_description'),
        features: [
            t('portals_page.webmaster_feature_1'),
            t('portals_page.webmaster_feature_2'),
            t('portals_page.webmaster_feature_3'),
            t('portals_page.webmaster_feature_4'),
            t('portals_page.webmaster_feature_5'),
            t('portals_page.webmaster_feature_6'),
        ],
    },
    {
        id: 'website',
        iconPath: 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418',
        color: 'from-rose-500 to-rose-700',
        accentBg: 'bg-rose-500/10',
        accentText: 'text-rose-600',
        featureBg: 'bg-rose-500/5',
        featureBgHover: 'hover:bg-rose-500/10',
        badge: null,
        badgeLabel: null,
        title: t('portals_page.website_title'),
        subtitle: t('portals_page.website_subtitle'),
        description: t('portals_page.website_description'),
        features: [
            t('portals_page.website_feature_1'),
            t('portals_page.website_feature_2'),
            t('portals_page.website_feature_3'),
            t('portals_page.website_feature_4'),
            t('portals_page.website_feature_5'),
            t('portals_page.website_feature_6'),
        ],
    },
]);
</script>

<template>
    <Head :title="t('portals_page.page_title')" />
    <MainLayout>
        <!-- Hero Section -->
        <section class="relative py-24 bg-gradient-to-br from-primary via-primary-dark to-primary overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 start-10 w-72 h-72 bg-secondary rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-secondary/30 rounded-full blur-3xl"></div>
            </div>

            <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 40px 40px;"></div>

            <div class="relative container mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full border border-white/20 mb-8 animate-fade-up">
                    <span class="w-2 h-2 bg-secondary rounded-full animate-pulse"></span>
                    <span class="text-sm font-medium text-white/90">{{ t('portals_page.hero_badge') }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 animate-fade-up">
                    {{ t('portals_page.hero_title') }}
                </h1>
                <p class="text-xl text-white/80 max-w-3xl mx-auto mb-10 animate-fade-up">
                    {{ t('portals_page.hero_subtitle') }}
                </p>

                <div class="flex flex-wrap justify-center gap-6 mb-10 animate-fade-up">
                    <div class="flex items-center gap-3 px-5 py-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                        <span class="text-3xl font-bold text-secondary">6</span>
                        <span class="text-sm text-white/80 text-start">{{ t('portals_page.stat_portals') }}</span>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                        <span class="text-3xl font-bold text-secondary">95+</span>
                        <span class="text-sm text-white/80 text-start">{{ t('portals_page.stat_modules') }}</span>
                    </div>
                    <div class="flex items-center gap-3 px-5 py-3 bg-white/10 backdrop-blur-sm rounded-xl border border-white/10">
                        <span class="text-3xl font-bold text-secondary">100%</span>
                        <span class="text-sm text-white/80 text-start">{{ t('portals_page.stat_coverage') }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link
                        href="/demo"
                        class="px-8 py-4 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25"
                    >
                        {{ t('portals_page.hero_cta_demo') }}
                    </Link>
                    <a
                        href="#portals"
                        class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm transition-all duration-300 border border-white/20"
                    >
                        {{ t('portals_page.hero_cta_explore') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- Portals Overview Grid -->
        <section id="portals" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('portals_page.overview_title')"
                    :subtitle="t('portals_page.overview_subtitle')"
                />
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                    <a
                        v-for="portal in portals"
                        :key="portal.id"
                        :href="'#' + portal.id"
                        class="group relative p-8 bg-light-blue rounded-2xl hover:shadow-xl transition-all duration-500 animate-fade-up overflow-hidden"
                    >
                        <div
                            class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            :class="portal.color"
                        ></div>

                        <div
                            class="w-16 h-16 rounded-2xl flex items-center justify-center mb-5 transition-all duration-300 group-hover:scale-110"
                            :class="portal.accentBg"
                        >
                            <svg class="w-8 h-8" :class="portal.accentText" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="portal.iconPath" />
                            </svg>
                        </div>

                        <span
                            v-if="portal.badge"
                            class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full mb-3 bg-secondary/10 text-secondary"
                        >
                            {{ portal.badge }} {{ portal.badgeLabel }}
                        </span>

                        <h3 class="text-xl font-bold text-dark mb-1">{{ portal.title }}</h3>
                        <p class="text-sm font-medium mb-3" :class="portal.accentText">{{ portal.subtitle }}</p>
                        <p class="text-gray leading-relaxed text-sm">{{ portal.description }}</p>

                        <div class="mt-5 flex items-center gap-2 text-sm font-medium opacity-0 group-hover:opacity-100 transition-all duration-300" :class="portal.accentText">
                            {{ t('portals_page.view_details') }}
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Detailed Portal Sections -->
        <section class="py-20 bg-light-blue">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('portals_page.details_title')"
                    :subtitle="t('portals_page.details_subtitle')"
                />

                <div class="max-w-6xl mx-auto space-y-16">
                    <div
                        v-for="portal in portals"
                        :key="portal.id"
                        :id="portal.id"
                        class="animate-fade-up"
                    >
                        <div class="relative bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-500">
                            <div class="relative p-8 pb-6 bg-gradient-to-r text-white" :class="portal.color">
                                <div class="absolute top-0 end-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                                <div class="absolute bottom-0 start-0 w-24 h-24 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/2"></div>

                                <div class="relative flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="w-14 h-14 bg-white/15 backdrop-blur-sm rounded-2xl flex items-center justify-center shrink-0">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="portal.iconPath" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3 flex-wrap">
                                            <h3 class="text-2xl font-bold">{{ portal.title }}</h3>
                                            <span
                                                v-if="portal.badge"
                                                class="inline-flex items-center px-3 py-1 text-xs font-bold rounded-full bg-white/20 backdrop-blur-sm"
                                            >
                                                {{ portal.badge }} {{ portal.badgeLabel }}
                                            </span>
                                        </div>
                                        <p class="text-white/80 mt-1">{{ portal.subtitle }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-8">
                                <p class="text-gray leading-relaxed mb-8 text-lg">{{ portal.description }}</p>

                                <h4 class="text-sm font-bold uppercase tracking-wider text-dark/50 mb-4">
                                    {{ t('portals_page.key_features') }}
                                </h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div
                                        v-for="(feature, fIdx) in portal.features"
                                        :key="fIdx"
                                        class="flex items-start gap-3 p-4 rounded-xl transition-colors duration-200"
                                        :class="[portal.featureBg, portal.featureBgHover]"
                                    >
                                        <div class="shrink-0 mt-0.5">
                                            <svg class="w-5 h-5" :class="portal.accentText" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <span class="text-dark font-medium text-sm">{{ feature }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-r from-primary to-primary-dark">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6 animate-fade-up">
                    {{ t('portals_page.cta_title') }}
                </h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto mb-8 animate-fade-up">
                    {{ t('portals_page.cta_subtitle') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link
                        href="/demo"
                        class="px-8 py-4 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25"
                    >
                        {{ t('portals_page.cta_request_demo') }}
                    </Link>
                    <Link
                        href="/pricing"
                        class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm transition-all duration-300 border border-white/20"
                    >
                        {{ t('portals_page.cta_view_pricing') }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
