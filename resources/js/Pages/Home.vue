<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Hero from '@/Components/Hero.vue';
import TrustBar from '@/Components/TrustBar.vue';
import TestimonialsCarousel from '@/Components/TestimonialsCarousel.vue';
import PartnersMarquee from '@/Components/PartnersMarquee.vue';
import NewsletterSignup from '@/Components/NewsletterSignup.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import AnimatedCounter from '@/Components/AnimatedCounter.vue';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

useScrollAnimation();

const { t, locale } = useI18n();

const props = defineProps({
    plans: Array,
    testimonials: Array,
    faqs: Array,
    currencies: Array,
    currentCurrency: String,
});

const overviewItems = computed(() => [
    {
        number: 6,
        label: t('home.overview.portals'),
        description: t('home.overview.portals_desc'),
        href: '/portals',
        icon: 'portal',
        color: '#1B4F72',
    },
    {
        number: 95,
        suffix: '+',
        label: t('home.overview.modules'),
        description: t('home.overview.modules_desc'),
        href: '/features',
        icon: 'module',
        color: '#C4A265',
    },
    {
        number: 157,
        suffix: '+',
        label: t('home.overview.tables'),
        description: t('home.overview.tables_desc'),
        href: '/technology',
        icon: 'database',
        color: '#2471A3',
    },
    {
        number: 800,
        suffix: '+',
        label: t('home.overview.features'),
        description: t('home.overview.features_desc'),
        href: '/features',
        icon: 'features',
        color: '#C4A265',
    },
    {
        number: 3000,
        suffix: '+',
        label: t('home.overview.translations'),
        description: t('home.overview.translations_desc'),
        href: '/technology',
        icon: 'globe',
        color: '#1B4F72',
    },
    {
        number: 80,
        suffix: '+',
        label: t('home.overview.permissions'),
        description: t('home.overview.permissions_desc'),
        href: '/technology',
        icon: 'shield',
        color: '#1C2833',
    },
]);

const featureCards = computed(() => [
    {
        title: t('home.cards.features.title'),
        description: t('home.cards.features.description'),
        href: '/features',
        icon: 'features',
        color: 'from-[#1B4F72] to-[#0D2B45]',
        iconBg: 'from-[#1B4F72]/10 to-[#2471A3]/10',
        stat: '800+ ' + t('home.cards.stat_features'),
    },
    {
        title: t('home.cards.portals.title'),
        description: t('home.cards.portals.description'),
        href: '/portals',
        icon: 'portals',
        color: 'from-[#C4A265] to-[#a1864e]',
        iconBg: 'from-[#C4A265]/15 to-[#D4B876]/15',
    },
    {
        title: t('home.cards.dental.title'),
        description: t('home.cards.dental.description'),
        href: '/dental',
        icon: 'dental',
        color: 'from-[#1C2833] to-[#0D2B45]',
        iconBg: 'from-[#1C2833]/10 to-[#2C3E50]/10',
    },
    {
        title: t('home.cards.solutions.title'),
        description: t('home.cards.solutions.description'),
        href: '/solutions',
        icon: 'solutions',
        color: 'from-[#1B4F72] to-[#0D2B45]',
        iconBg: 'from-[#1B4F72]/10 to-[#2471A3]/10',
    },
    {
        title: t('home.cards.technology.title'),
        description: t('home.cards.technology.description'),
        href: '/technology',
        icon: 'technology',
        color: 'from-[#C4A265] to-[#8b6f3d]',
        iconBg: 'from-[#C4A265]/15 to-[#D4B876]/15',
    },
    {
        title: t('home.cards.reports.title'),
        description: t('home.cards.reports.description'),
        href: '/reports',
        icon: 'reports',
        color: 'from-[#1C2833] to-[#0D2B45]',
        iconBg: 'from-[#1C2833]/10 to-[#34495E]/10',
    },
]);

const stats = computed(() => [
    {
        target: 800, suffix: '+', label: t('home.stats.features'),
        accent: '#C4A265', accentSoft: 'rgba(196,162,101,0.15)',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    },
    {
        target: 6, suffix: '', label: t('home.stats.portals'),
        accent: '#2E86C1', accentSoft: 'rgba(46,134,193,0.15)',
        icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    },
    {
        target: 3000, suffix: '+', label: t('home.stats.translations'),
        accent: '#5DADE2', accentSoft: 'rgba(93,173,226,0.15)',
        icon: 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
    },
    {
        target: 80, suffix: '+', label: t('home.stats.permissions'),
        accent: '#27AE60', accentSoft: 'rgba(39,174,96,0.15)',
        icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
    },
    {
        target: 157, suffix: '+', label: t('home.stats.tables'),
        accent: '#D4B87A', accentSoft: 'rgba(212,184,122,0.15)',
        icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4',
    },
]);

const howItWorksSteps = computed(() => [
    {
        number: '01',
        title: t('home.how_it_works.step1_title'),
        description: t('home.how_it_works.step1_desc'),
        icon: 'register',
    },
    {
        number: '02',
        title: t('home.how_it_works.step2_title'),
        description: t('home.how_it_works.step2_desc'),
        icon: 'setup',
    },
    {
        number: '03',
        title: t('home.how_it_works.step3_title'),
        description: t('home.how_it_works.step3_desc'),
        icon: 'manage',
    },
]);
</script>

<template>
    <Head :title="t('home.title')" />
    <MainLayout>
        <!-- 1. Hero -->
        <Hero />

        <!-- 2. Trust Bar -->
        <TrustBar />

        <!-- 3. System Overview - Refined 6-stat grid matching TrustBar -->
        <section class="relative py-20 bg-[#F8FAFC] overflow-hidden">
            <!-- Decorative background elements -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, #1B4F72 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/[0.04] rounded-full blur-3xl -translate-y-1/3 translate-x-1/3"></div>
            <div class="absolute bottom-0 start-0 w-80 h-80 bg-[#1B4F72]/[0.04] rounded-full blur-3xl translate-y-1/3 -translate-x-1/3"></div>

            <!-- Top gradient line -->
            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/20 to-transparent" />

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Heading badge + title -->
                <div class="text-center mb-12 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#1B4F72]/5 border border-[#1B4F72]/10 mb-4">
                        <span class="w-2 h-2 rounded-full bg-[#C4A265] animate-pulse"></span>
                        <span class="text-sm font-medium text-[#1B4F72]">{{ t('home.overview.badge') }}</span>
                    </div>
                    <h2 class="text-2xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ t('home.overview.title') }}
                    </h2>
                    <p class="text-sm md:text-base leading-relaxed max-w-2xl mx-auto text-gray-500">
                        {{ t('home.overview.subtitle') }}
                    </p>
                    <div class="flex items-center justify-center gap-3 mt-5">
                        <div class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></div>
                        <div class="w-14 h-0.5 bg-[#C4A265] rounded-full"></div>
                        <div class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></div>
                    </div>
                </div>

                <!-- 6-stat grid matching TrustBar card style -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-5 animate-stagger">
                    <a
                        v-for="(item, index) in overviewItems"
                        :key="index"
                        :href="item.href"
                        class="group relative flex flex-col items-center gap-3 p-5 rounded-2xl bg-white/70 backdrop-blur-sm border border-gray-100 hover:border-[#1B4F72]/20 hover:shadow-xl hover:shadow-[#1B4F72]/5 hover:-translate-y-1 transition-all duration-500 overflow-hidden"
                    >
                        <!-- Hover gradient overlay -->
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-[#1B4F72]/[0.02] to-[#C4A265]/[0.02] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                        <!-- Icon container -->
                        <div class="relative">
                            <div class="absolute inset-0 rounded-xl scale-0 group-hover:scale-110 transition-transform duration-500 border-2 border-dashed opacity-20" :style="{ borderColor: item.color }"></div>
                            <div
                                class="relative w-12 h-12 rounded-xl flex items-center justify-center transition-all duration-500 group-hover:scale-110 group-hover:rotate-3"
                                :style="{ backgroundColor: item.color + '0D', boxShadow: `0 0 0 1px ${item.color}15` }"
                            >
                                <!-- Portal icon -->
                                <svg v-if="item.icon === 'portal'" class="w-6 h-6" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <!-- Module icon -->
                                <svg v-else-if="item.icon === 'module'" class="w-6 h-6" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <!-- Database icon -->
                                <svg v-else-if="item.icon === 'database'" class="w-6 h-6" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                </svg>
                                <!-- Features icon -->
                                <svg v-else-if="item.icon === 'features'" class="w-6 h-6" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                <!-- Globe icon -->
                                <svg v-else-if="item.icon === 'globe'" class="w-6 h-6" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <!-- Shield icon -->
                                <svg v-else class="w-6 h-6" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Animated counter -->
                        <div class="relative text-2xl md:text-[1.75rem] font-extrabold leading-none" :style="{ color: item.color }">
                            <AnimatedCounter :target="item.number" :suffix="item.suffix || ''" />
                        </div>

                        <!-- Label -->
                        <div class="relative text-center">
                            <h3 class="text-xs md:text-sm font-bold text-[#1C2833] group-hover:text-[#1B4F72] transition-colors leading-snug">
                                {{ item.label }}
                            </h3>
                            <p class="hidden sm:block text-[11px] text-gray-500 mt-1 leading-relaxed">
                                {{ item.description }}
                            </p>
                        </div>

                        <!-- Bottom accent line -->
                        <div
                            class="absolute bottom-0 inset-x-4 h-0.5 rounded-full scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-center"
                            :style="{ background: `linear-gradient(90deg, transparent, ${item.color}, transparent)` }"
                        ></div>
                    </a>
                </div>
            </div>

            <!-- Bottom gradient line -->
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#1B4F72]/20 to-transparent" />
        </section>

        <!-- 4. Explore System — Premium Bento Grid -->
        <section class="py-20 lg:py-24 bg-white relative overflow-hidden">
            <!-- Decorative orbs -->
            <div class="absolute top-10 start-0 w-[500px] h-[500px] bg-[#1B4F72]/[0.035] rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 end-0 w-[450px] h-[450px] bg-[#C4A265]/[0.04] rounded-full blur-3xl"></div>
            <!-- Subtle dot grid -->
            <div class="absolute inset-0 opacity-[0.025]" style="background-image: radial-gradient(#1B4F72 1px, transparent 1px); background-size: 28px 28px;"></div>

            <div class="container mx-auto px-4 relative z-10">
                <!-- Compact refined header -->
                <div class="text-center mb-12 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#1B4F72]/5 border border-[#1B4F72]/10 mb-4">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></span>
                        <span class="text-[11px] font-semibold text-[#1B4F72] tracking-wide uppercase">{{ t('home.cards.badge') }}</span>
                    </div>
                    <h2 class="text-2xl md:text-4xl font-extrabold text-[#0D2B45] mb-3 leading-tight">
                        {{ t('home.cards.title') }}
                    </h2>
                    <p class="text-sm md:text-base text-gray-500 max-w-xl mx-auto leading-relaxed">
                        {{ t('home.cards.subtitle') }}
                    </p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <div class="w-1 h-1 rounded-full bg-[#C4A265]"></div>
                        <div class="w-12 h-0.5 bg-gradient-to-r from-transparent via-[#C4A265] to-transparent rounded-full"></div>
                        <div class="w-1 h-1 rounded-full bg-[#C4A265]"></div>
                    </div>
                </div>

                <!-- Premium asymmetric bento -->
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4 md:gap-5 animate-stagger">
                    <a
                        v-for="(card, index) in featureCards"
                        :key="index"
                        :href="card.href"
                        class="feature-card group relative rounded-2xl overflow-hidden transition-all duration-500 hover:-translate-y-1.5"
                        :class="[
                            index === 0 ? 'md:col-span-4 md:row-span-2 min-h-[260px] md:min-h-[340px]' : '',
                            index === 1 ? 'md:col-span-2 min-h-[200px] md:min-h-[165px]' : '',
                            index === 2 ? 'md:col-span-2 min-h-[200px] md:min-h-[165px]' : '',
                            index > 2 ? 'md:col-span-2 min-h-[180px]' : '',
                        ]"
                    >
                        <!-- Base card -->
                        <div class="absolute inset-0 rounded-2xl bg-white border border-gray-100 shadow-sm group-hover:shadow-2xl group-hover:shadow-[#1B4F72]/10 transition-all duration-500"></div>

                        <!-- Hover gradient layer -->
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500" :class="card.color"></div>

                        <!-- Decorative corner accent -->
                        <div class="absolute top-0 end-0 w-24 h-24 opacity-[0.04] group-hover:opacity-[0.12] transition-opacity duration-500" :class="card.color.replace('from-', 'bg-gradient-to-br from-')">
                            <div class="absolute inset-0 rounded-bl-[100%]" :class="card.color"></div>
                        </div>

                        <!-- Number badge -->
                        <div class="absolute top-4 end-4 text-[10px] font-bold tracking-widest text-gray-300 group-hover:text-white/40 transition-colors duration-500">
                            {{ String(index + 1).padStart(2, '0') }} / 06
                        </div>

                        <!-- Content -->
                        <div class="relative h-full p-5 md:p-6 flex flex-col">
                            <!-- Icon -->
                            <div class="mb-auto">
                                <div class="relative inline-flex items-center justify-center rounded-xl bg-gradient-to-br transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 group-hover:bg-white/10 group-hover:backdrop-blur-sm"
                                    :class="[
                                        index === 0 ? 'w-16 h-16 md:w-[72px] md:h-[72px]' : 'w-12 h-12',
                                        card.iconBg
                                    ]">
                                    <svg v-if="card.icon === 'features'" class="text-[#1B4F72] group-hover:text-white transition-colors duration-500" :class="index === 0 ? 'w-8 h-8 md:w-10 md:h-10' : 'w-6 h-6'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                                    </svg>
                                    <svg v-else-if="card.icon === 'portals'" class="text-[#C4A265] group-hover:text-white transition-colors duration-500 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                                    </svg>
                                    <svg v-else-if="card.icon === 'dental'" class="text-[#1C2833] group-hover:text-white transition-colors duration-500 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/>
                                    </svg>
                                    <svg v-else-if="card.icon === 'solutions'" class="text-[#1B4F72] group-hover:text-white transition-colors duration-500 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18"/>
                                    </svg>
                                    <svg v-else-if="card.icon === 'technology'" class="text-[#C4A265] group-hover:text-white transition-colors duration-500 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/>
                                    </svg>
                                    <svg v-else class="text-[#1C2833] group-hover:text-white transition-colors duration-500 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Text + stat pill for large card -->
                            <div class="mt-5 md:mt-8">
                                <div v-if="index === 0 && card.stat" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 group-hover:bg-white/15 group-hover:border-white/30 transition-all duration-500 mb-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] group-hover:bg-white transition-colors duration-500"></span>
                                    <span class="text-[10px] font-bold text-[#C4A265] group-hover:text-white transition-colors duration-500 tracking-wide">{{ card.stat }}</span>
                                </div>
                                <h3 class="font-bold text-[#0D2B45] group-hover:text-white transition-colors duration-500 mb-2 leading-snug"
                                    :class="index === 0 ? 'text-lg md:text-2xl' : 'text-base md:text-[17px]'">
                                    {{ card.title }}
                                </h3>
                                <p class="text-gray-500 group-hover:text-white/80 transition-colors duration-500 leading-relaxed"
                                    :class="index === 0 ? 'text-sm md:text-[15px] line-clamp-3' : 'text-xs md:text-[13px] line-clamp-2'">
                                    {{ card.description }}
                                </p>
                            </div>

                            <!-- Footer arrow -->
                            <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100 group-hover:border-white/20 transition-colors duration-500">
                                <span class="text-[11px] md:text-xs font-semibold text-[#C4A265] group-hover:text-white transition-colors duration-500">
                                    {{ t('home.learn_more') }}
                                </span>
                                <div class="w-7 h-7 rounded-lg bg-[#C4A265]/10 group-hover:bg-white/20 flex items-center justify-center transition-all duration-500 group-hover:translate-x-0 rtl:group-hover:-translate-x-0">
                                    <svg class="w-3.5 h-3.5 text-[#C4A265] group-hover:text-white rtl:rotate-180 transition-all duration-500 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- 5. How It Works - Horizontal Timeline -->
        <section class="py-20 lg:py-28 bg-[#F8FAFC] relative overflow-hidden">
            <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(circle at 1px 1px, #1B4F72 1px, transparent 0); background-size: 48px 48px;"></div>

            <div class="container mx-auto px-4 relative z-10">
                <SectionTitle
                    :title="t('home.how_it_works.title')"
                    :subtitle="t('home.how_it_works.subtitle')"
                />

                <div class="relative mt-16 max-w-5xl mx-auto">
                    <!-- Connecting dashed line (horizontal on desktop) -->
                    <div class="hidden md:block absolute top-24 start-[16.67%] end-[16.67%] h-[2px]">
                        <div class="w-full h-full border-t-2 border-dashed border-[#C4A265]/30 animate-fade-up"></div>
                    </div>
                    <!-- Connecting dashed line (vertical on mobile) -->
                    <div class="md:hidden absolute top-0 bottom-0 start-8 w-[2px]">
                        <div class="w-full h-full border-s-2 border-dashed border-[#C4A265]/30"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 md:gap-8">
                        <div
                            v-for="(step, index) in howItWorksSteps"
                            :key="index"
                            class="relative text-center animate-fade-up"
                        >
                            <!-- Step number bubble -->
                            <div class="relative mx-auto md:mx-auto ms-8 md:ms-auto w-20 h-20 mb-6">
                                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-[#1B4F72] to-[#2471A3] opacity-10 animate-pulse"></div>
                                <div class="relative w-full h-full rounded-full bg-white border-2 border-[#1B4F72]/20 flex items-center justify-center shadow-lg shadow-[#1B4F72]/5">
                                    <!-- Register icon -->
                                    <svg v-if="step.icon === 'register'" class="w-9 h-9 text-[#1B4F72]" viewBox="0 0 36 36" fill="none">
                                        <circle cx="18" cy="12" r="6" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="0.1"/>
                                        <path d="M6 30C6 24.477 10.477 20 16 20H20C25.523 20 30 24.477 30 30" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>
                                        <path d="M26 10L28 12L32 8" stroke="#C4A265" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <!-- Setup icon -->
                                    <svg v-else-if="step.icon === 'setup'" class="w-9 h-9 text-[#1B4F72]" viewBox="0 0 36 36" fill="none">
                                        <circle cx="18" cy="18" r="7" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="0.1"/>
                                        <circle cx="18" cy="18" r="3" fill="currentColor" fill-opacity="0.3"/>
                                        <path d="M18 7V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M18 26V29" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M7 18H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M26 18H29" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M10.2 10.2L12.3 12.3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M23.7 23.7L25.8 25.8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <!-- Manage icon -->
                                    <svg v-else class="w-9 h-9 text-[#1B4F72]" viewBox="0 0 36 36" fill="none">
                                        <rect x="4" y="6" width="28" height="20" rx="3" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="0.05"/>
                                        <line x1="4" y1="12" x2="32" y2="12" stroke="currentColor" stroke-width="1.5" opacity="0.3"/>
                                        <rect x="8" y="16" width="8" height="4" rx="1" fill="currentColor" fill-opacity="0.15"/>
                                        <rect x="20" y="16" width="8" height="4" rx="1" fill="#C4A265" fill-opacity="0.2"/>
                                        <path d="M12 30H24" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M18 26V30" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <!-- Step number badge -->
                                <div class="absolute -top-1 -end-1 w-7 h-7 rounded-full bg-gradient-to-br from-[#C4A265] to-[#D4B876] text-white text-xs font-bold flex items-center justify-center shadow-md">
                                    {{ step.number }}
                                </div>
                            </div>

                            <h3 class="text-lg font-bold text-[#1C2833] mb-3 md:text-center text-start ps-8 md:ps-0">{{ step.title }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto md:text-center text-start ps-8 md:ps-0">{{ step.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Stats Section - Premium redesign with bento layout -->
        <section class="py-20 lg:py-28 relative overflow-hidden bg-[#0A1F33]">
            <!-- Animated mesh gradient background -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-[#1B4F72]/40 rounded-full blur-[120px] animate-pulse-slow"></div>
                <div class="absolute bottom-0 right-1/4 w-[600px] h-[600px] bg-[#C4A265]/15 rounded-full blur-[140px] animate-pulse-slow" style="animation-delay: 2s"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-[#2E86C1]/20 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: 4s"></div>
            </div>

            <!-- Diagonal lines pattern -->
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(45deg, white 1px, transparent 1px), linear-gradient(-45deg, white 1px, transparent 1px); background-size: 60px 60px;"></div>

            <!-- Top + bottom border glow -->
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/40 to-transparent"></div>

            <div class="container mx-auto px-4 relative z-10">
                <!-- Header -->
                <div class="text-center max-w-2xl mx-auto mb-16 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 mb-5">
                        <span class="relative flex w-1.5 h-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#C4A265]"></span>
                        </span>
                        <span class="text-[#C4A265] text-xs font-semibold tracking-wider uppercase">{{ locale === 'ar' ? 'بالأرقام' : 'By the numbers' }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
                        {{ t('home.stats.title') }}
                    </h2>
                    <p class="text-base md:text-lg text-white/60 max-w-xl mx-auto">
                        {{ t('home.stats.subtitle') }}
                    </p>
                </div>

                <!-- Bento Grid Stats -->
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4 md:gap-5 max-w-6xl mx-auto">
                    <!-- Featured stat (Big) -->
                    <div class="col-span-2 md:col-span-3 md:row-span-2 group relative animate-fade-up">
                        <div
                            class="relative h-full min-h-[280px] md:min-h-[360px] rounded-3xl p-8 md:p-10 overflow-hidden border border-white/10 transition-all duration-700 hover:border-[#C4A265]/30"
                            style="background: linear-gradient(135deg, rgba(196,162,101,0.12) 0%, rgba(27,79,114,0.2) 100%);"
                        >
                            <!-- Animated gradient orbs inside -->
                            <div class="absolute -top-20 -end-20 w-64 h-64 bg-[#C4A265]/20 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-1000"></div>
                            <div class="absolute -bottom-10 -start-10 w-48 h-48 bg-[#1B4F72]/30 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-1000"></div>

                            <!-- Decorative grid -->
                            <svg class="absolute top-4 end-4 w-20 h-20 text-white/5" viewBox="0 0 80 80" fill="none">
                                <pattern id="dots-bg" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="1" fill="currentColor" />
                                </pattern>
                                <rect width="80" height="80" fill="url(#dots-bg)" />
                            </svg>

                            <div class="relative h-full flex flex-col">
                                <!-- Icon -->
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B87A] flex items-center justify-center mb-6 shadow-2xl shadow-[#C4A265]/30 group-hover:rotate-12 group-hover:scale-110 transition-all duration-500">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="stats[0].icon" />
                                    </svg>
                                </div>

                                <!-- Number -->
                                <div class="flex items-baseline gap-2 mb-2">
                                    <div class="text-6xl md:text-7xl lg:text-8xl font-black bg-gradient-to-br from-white via-[#C4A265] to-[#D4B87A] bg-clip-text text-transparent leading-none">
                                        <AnimatedCounter :target="stats[0].target" :suffix="stats[0].suffix" />
                                    </div>
                                </div>

                                <!-- Label -->
                                <div class="mt-auto">
                                    <div class="h-px w-12 bg-gradient-to-r from-[#C4A265] to-transparent mb-3"></div>
                                    <p class="text-[#C4A265] font-bold text-base md:text-lg uppercase tracking-wider">{{ stats[0].label }}</p>
                                    <p class="text-white/50 text-xs md:text-sm mt-1.5 max-w-xs">{{ locale === 'ar' ? 'خواص شاملة لإدارة كل تفاصيل عيادتك' : 'Comprehensive features for every clinic detail' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Smaller stats -->
                    <div
                        v-for="(stat, idx) in stats.slice(1)"
                        :key="idx"
                        class="col-span-1 md:col-span-3 lg:col-span-1 group relative animate-fade-up"
                        :class="{ 'md:col-span-3 lg:col-span-1': idx === 0, 'md:col-span-3 lg:col-span-1': idx === 1 }"
                        :style="{ animationDelay: `${(idx + 1) * 100}ms` }"
                    >
                        <div
                            class="relative h-full min-h-[140px] md:min-h-[170px] rounded-2xl p-5 md:p-6 overflow-hidden border border-white/10 backdrop-blur-md bg-white/[0.03] transition-all duration-500 hover:bg-white/[0.06] hover:-translate-y-1"
                            :style="{ '--accent': stat.accent }"
                        >
                            <!-- Hover glow -->
                            <div
                                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"
                                :style="{ background: `radial-gradient(circle at 50% 0%, ${stat.accentSoft} 0%, transparent 70%)` }"
                            ></div>

                            <!-- Top accent line -->
                            <div
                                class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-px transition-all duration-700"
                                :style="{ background: `linear-gradient(90deg, transparent, ${stat.accent}, transparent)` }"
                            ></div>

                            <!-- Floating accent dot -->
                            <div class="absolute top-4 end-4 w-1.5 h-1.5 rounded-full opacity-50 group-hover:opacity-100 group-hover:scale-150 transition-all duration-500" :style="{ background: stat.accent, boxShadow: `0 0 12px ${stat.accent}` }"></div>

                            <div class="relative">
                                <!-- Icon -->
                                <div
                                    class="w-10 h-10 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500"
                                    :style="{ background: stat.accentSoft, color: stat.accent }"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="stat.icon" />
                                    </svg>
                                </div>

                                <!-- Number -->
                                <div class="text-3xl md:text-4xl font-black text-white leading-none mb-2">
                                    <AnimatedCounter :target="stat.target" :suffix="stat.suffix" />
                                </div>

                                <!-- Label -->
                                <p class="font-semibold text-[10px] sm:text-[11px] uppercase tracking-widest" :style="{ color: stat.accent }">
                                    {{ stat.label }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom trust bar -->
                <div class="mt-12 max-w-3xl mx-auto flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-white/40 text-xs animate-fade-up">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <span>{{ locale === 'ar' ? 'بيانات محدّثة باستمرار' : 'Continuously updated' }}</span>
                    </div>
                    <div class="w-1 h-1 rounded-full bg-white/20"></div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        <span>{{ locale === 'ar' ? 'أداء عالي وسرعة استجابة' : 'High performance' }}</span>
                    </div>
                    <div class="w-1 h-1 rounded-full bg-white/20"></div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        <span>{{ locale === 'ar' ? 'أمان على مستوى المؤسسات' : 'Enterprise-grade security' }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 7. Dashboard Preview -->
        <section class="py-20 lg:py-28 bg-white relative overflow-hidden">
            <!-- Background gradient orbs -->
            <div class="absolute top-0 start-1/3 w-[600px] h-[600px] bg-[#1B4F72]/[0.03] rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 end-1/4 w-[400px] h-[400px] bg-[#C4A265]/[0.03] rounded-full blur-[100px]"></div>

            <div class="container mx-auto px-4 relative z-10">
                <SectionTitle
                    :title="t('home.preview.title')"
                    :subtitle="t('home.preview.subtitle')"
                />

                <div class="relative mt-14 max-w-6xl mx-auto animate-fade-up">
                    <!-- Dashboard mockup container -->
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-[#1C2833]/20 border border-gray-200/60">
                        <!-- Title bar -->
                        <div class="bg-[#1C2833] px-6 py-3.5 flex items-center gap-3">
                            <div class="flex gap-2">
                                <div class="w-3 h-3 rounded-full bg-red-400/80"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400/80"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400/80"></div>
                            </div>
                            <div class="flex-1 flex justify-center">
                                <div class="bg-white/10 rounded-lg px-4 py-1 text-white/40 text-xs font-mono">app.doctorato.com/dashboard</div>
                            </div>
                        </div>

                        <!-- Dashboard body -->
                        <div class="flex bg-[#0F1923] min-h-[420px]">
                            <!-- Sidebar -->
                            <div class="hidden md:flex flex-col w-56 bg-[#162230] border-e border-white/5 p-4 gap-1 shrink-0">
                                <!-- Logo area -->
                                <div class="flex items-center gap-2 px-3 py-3 mb-4">
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center">
                                        <span class="text-white font-bold text-xs">D</span>
                                    </div>
                                    <span class="text-white/80 font-semibold text-sm">Doctorato</span>
                                </div>
                                <!-- Menu items -->
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#1B4F72]/40 text-white/90 text-sm">
                                    <div class="w-4 h-4 rounded bg-[#C4A265]/30"></div>
                                    <span>Dashboard</span>
                                </div>
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/40 text-sm hover:text-white/60 transition-colors">
                                    <div class="w-4 h-4 rounded bg-white/10"></div>
                                    <span>Patients</span>
                                </div>
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/40 text-sm hover:text-white/60 transition-colors">
                                    <div class="w-4 h-4 rounded bg-white/10"></div>
                                    <span>Appointments</span>
                                </div>
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/40 text-sm hover:text-white/60 transition-colors">
                                    <div class="w-4 h-4 rounded bg-white/10"></div>
                                    <span>Billing</span>
                                </div>
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/40 text-sm hover:text-white/60 transition-colors">
                                    <div class="w-4 h-4 rounded bg-white/10"></div>
                                    <span>Reports</span>
                                </div>
                                <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-white/40 text-sm hover:text-white/60 transition-colors">
                                    <div class="w-4 h-4 rounded bg-white/10"></div>
                                    <span>Settings</span>
                                </div>
                            </div>

                            <!-- Main content area -->
                            <div class="flex-1 p-6">
                                <!-- Stat cards row -->
                                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                    <div class="bg-white/[0.04] border border-white/[0.06] rounded-xl p-4 backdrop-blur-sm">
                                        <div class="text-white/40 text-xs mb-1">Total Patients</div>
                                        <div class="text-white text-xl font-bold">1,284</div>
                                        <div class="text-green-400 text-xs mt-1">+12.5%</div>
                                    </div>
                                    <div class="bg-white/[0.04] border border-white/[0.06] rounded-xl p-4 backdrop-blur-sm">
                                        <div class="text-white/40 text-xs mb-1">Appointments</div>
                                        <div class="text-white text-xl font-bold">48</div>
                                        <div class="text-[#C4A265] text-xs mt-1">Today</div>
                                    </div>
                                    <div class="bg-white/[0.04] border border-white/[0.06] rounded-xl p-4 backdrop-blur-sm">
                                        <div class="text-white/40 text-xs mb-1">Revenue</div>
                                        <div class="text-white text-xl font-bold">$24.8K</div>
                                        <div class="text-green-400 text-xs mt-1">+8.2%</div>
                                    </div>
                                    <div class="bg-white/[0.04] border border-white/[0.06] rounded-xl p-4 backdrop-blur-sm">
                                        <div class="text-white/40 text-xs mb-1">Pending</div>
                                        <div class="text-white text-xl font-bold">7</div>
                                        <div class="text-yellow-400 text-xs mt-1">Actions</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                                    <!-- Chart area -->
                                    <div class="lg:col-span-3 bg-white/[0.04] border border-white/[0.06] rounded-xl p-5 backdrop-blur-sm">
                                        <div class="flex items-center justify-between mb-4">
                                            <span class="text-white/70 text-sm font-medium">Revenue Overview</span>
                                            <div class="flex gap-2">
                                                <span class="text-xs text-white/30 px-2 py-1 rounded bg-white/5">Weekly</span>
                                                <span class="text-xs text-[#C4A265] px-2 py-1 rounded bg-[#C4A265]/10">Monthly</span>
                                            </div>
                                        </div>
                                        <!-- Chart bars -->
                                        <div class="flex items-end gap-2 h-28">
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 45%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 65%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 50%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 80%"></div>
                                            <div class="flex-1 bg-[#C4A265]/50 rounded-t" style="height: 95%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 70%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 55%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 75%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 60%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 85%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/40 rounded-t" style="height: 72%"></div>
                                            <div class="flex-1 bg-[#1B4F72]/30 rounded-t border border-dashed border-[#1B4F72]/30" style="height: 90%"></div>
                                        </div>
                                    </div>

                                    <!-- Table preview -->
                                    <div class="lg:col-span-2 bg-white/[0.04] border border-white/[0.06] rounded-xl p-5 backdrop-blur-sm">
                                        <div class="text-white/70 text-sm font-medium mb-4">Recent Patients</div>
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#1B4F72]/30 flex items-center justify-center text-white/60 text-xs font-bold">A</div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-white/70 text-xs truncate">Ahmed K.</div>
                                                    <div class="text-white/30 text-[10px]">Checkup</div>
                                                </div>
                                                <div class="text-green-400/70 text-[10px]">Done</div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#C4A265]/20 flex items-center justify-center text-white/60 text-xs font-bold">S</div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-white/70 text-xs truncate">Sara M.</div>
                                                    <div class="text-white/30 text-[10px]">Dental</div>
                                                </div>
                                                <div class="text-[#C4A265]/70 text-[10px]">In Progress</div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#1B4F72]/30 flex items-center justify-center text-white/60 text-xs font-bold">M</div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-white/70 text-xs truncate">Mohammed R.</div>
                                                    <div class="text-white/30 text-[10px]">Surgery</div>
                                                </div>
                                                <div class="text-yellow-400/70 text-[10px]">Pending</div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-[#C4A265]/20 flex items-center justify-center text-white/60 text-xs font-bold">F</div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="text-white/70 text-xs truncate">Fatima L.</div>
                                                    <div class="text-white/30 text-[10px]">Follow-up</div>
                                                </div>
                                                <div class="text-green-400/70 text-[10px]">Done</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating annotation callouts -->
                    <div class="hidden lg:block absolute -top-4 -end-4 bg-white rounded-xl shadow-lg shadow-[#1B4F72]/10 border border-gray-100 px-4 py-2.5 text-xs font-medium text-[#1C2833] animate-scale-in">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-green-400"></div>
                            Real-time Analytics
                        </div>
                    </div>
                    <div class="hidden lg:block absolute top-1/3 -start-6 bg-white rounded-xl shadow-lg shadow-[#C4A265]/10 border border-gray-100 px-4 py-2.5 text-xs font-medium text-[#1C2833] animate-scale-in">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-[#C4A265]"></div>
                            6 Portals
                        </div>
                    </div>
                    <div class="hidden lg:block absolute -bottom-4 start-1/3 bg-white rounded-xl shadow-lg shadow-[#1B4F72]/10 border border-gray-100 px-4 py-2.5 text-xs font-medium text-[#1C2833] animate-scale-in">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-[#1B4F72]"></div>
                            95+ Modules
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Testimonials Carousel - with decorative background -->
        <section id="testimonials" class="py-20 lg:py-28 bg-gradient-to-b from-[#FAF7F0] via-[#FAF7F0] to-white relative overflow-hidden">
            <!-- Decorative elements -->
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#1B4F72]/[0.02] rounded-full blur-[100px] translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 start-0 w-80 h-80 bg-[#C4A265]/[0.03] rounded-full blur-[100px] -translate-x-1/3 translate-y-1/3"></div>
            <!-- Large quote marks decoration -->
            <div class="absolute top-16 start-8 text-[#1B4F72]/[0.03] pointer-events-none">
                <svg class="w-40 h-40" viewBox="0 0 100 100" fill="currentColor">
                    <path d="M20 60C20 46.745 30.745 36 44 36V28C26.327 28 12 42.327 12 60V80H40V60H20ZM60 60C60 46.745 70.745 36 84 36V28C66.327 28 52 42.327 52 60V80H80V60H60Z"/>
                </svg>
            </div>

            <div class="container mx-auto px-4 relative z-10">
                <SectionTitle
                    :title="t('testimonials.title')"
                    :subtitle="t('testimonials.subtitle')"
                />
                <TestimonialsCarousel :testimonials="testimonials" />
            </div>
        </section>

        <!-- Partners Marquee -->
        <PartnersMarquee />

        <!-- 9. CTA Section - Redesigned with floating medical icons -->
        <section class="py-24 lg:py-32 relative overflow-hidden">
            <!-- Large gradient background -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#1C2833] via-[#1B4F72] to-[#1C2833]"></div>
            <!-- Extra gradient layer -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#1C2833]/50 to-transparent"></div>

            <!-- Decorative glow orbs -->
            <div class="absolute top-1/4 start-1/4 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-1/4 end-1/4 w-80 h-80 bg-[#1B4F72]/20 rounded-full blur-[100px]"></div>
            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/5 rounded-full blur-[80px]"></div>

            <!-- Floating medical icons -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <!-- Stethoscope -->
                <div class="absolute top-[15%] start-[10%] opacity-[0.06] animate-pulse" style="animation-delay: 0s; animation-duration: 4s;">
                    <svg class="w-16 h-16 text-white" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 8C14 8 10 14 10 20V38C10 44 14 48 20 48H28" stroke-linecap="round"/>
                        <circle cx="40" cy="40" r="10"/>
                        <circle cx="40" cy="40" r="4" fill="currentColor" fill-opacity="0.2"/>
                        <path d="M28 8C34 8 38 14 38 20" stroke-linecap="round"/>
                    </svg>
                </div>
                <!-- Heart pulse -->
                <div class="absolute top-[20%] end-[15%] opacity-[0.06] animate-pulse" style="animation-delay: 1s; animation-duration: 5s;">
                    <svg class="w-14 h-14 text-white" viewBox="0 0 56 56" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M28 48S8 36 8 22C8 14 14 8 22 8C26 8 28 12 28 12S30 8 34 8C42 8 48 14 48 22C48 36 28 48 28 48Z" fill="currentColor" fill-opacity="0.1"/>
                        <path d="M8 28H18L22 20L28 36L32 24L36 28H48" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <!-- Medical cross -->
                <div class="absolute bottom-[20%] start-[20%] opacity-[0.06] animate-pulse" style="animation-delay: 2s; animation-duration: 3.5s;">
                    <svg class="w-12 h-12 text-white" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="16" y="4" width="16" height="40" rx="3" fill="currentColor" fill-opacity="0.1"/>
                        <rect x="4" y="16" width="40" height="16" rx="3" fill="currentColor" fill-opacity="0.1"/>
                    </svg>
                </div>
                <!-- DNA helix -->
                <div class="absolute bottom-[25%] end-[10%] opacity-[0.06] animate-pulse" style="animation-delay: 0.5s; animation-duration: 4.5s;">
                    <svg class="w-12 h-12 text-white" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 4C16 4 32 12 32 24C32 36 16 44 16 44" stroke-linecap="round"/>
                        <path d="M32 4C32 4 16 12 16 24C16 36 32 44 32 44" stroke-linecap="round"/>
                        <line x1="18" y1="14" x2="30" y2="14" stroke-linecap="round" opacity="0.5"/>
                        <line x1="16" y1="24" x2="32" y2="24" stroke-linecap="round" opacity="0.5"/>
                        <line x1="18" y1="34" x2="30" y2="34" stroke-linecap="round" opacity="0.5"/>
                    </svg>
                </div>
                <!-- Pill -->
                <div class="absolute top-[50%] start-[5%] opacity-[0.05] animate-pulse" style="animation-delay: 1.5s; animation-duration: 5s;">
                    <svg class="w-10 h-10 text-white" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="8" y="4" width="24" height="32" rx="12" fill="currentColor" fill-opacity="0.1" transform="rotate(30 20 20)"/>
                        <line x1="6" y1="20" x2="34" y2="20" transform="rotate(30 20 20)" stroke-linecap="round" opacity="0.5"/>
                    </svg>
                </div>
            </div>

            <div class="container mx-auto px-4 relative z-10 text-center">
                <div class="max-w-4xl mx-auto animate-fade-up">
                    <!-- Trusted badge -->
                    <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/10 rounded-full px-5 py-2 mb-8">
                        <div class="flex -space-x-1.5 rtl:space-x-reverse">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-[#C4A265] to-[#D4B876] border-2 border-[#1C2833] flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-[#1B4F72] to-[#2471A3] border-2 border-[#1C2833] flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-[#C4A265] to-[#D4B876] border-2 border-[#1C2833] flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>
                        <span class="text-white/70 text-sm font-medium">{{ t('home.cta.trusted_badge') }}</span>
                    </div>

                    <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-8 leading-tight">
                        {{ t('home.cta.title') }}
                    </h2>
                    <p class="text-lg text-white/60 mb-12 leading-relaxed max-w-2xl mx-auto">
                        {{ t('home.cta.description') }}
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-5">
                        <a
                            href="/demo"
                            class="group inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold text-base rounded-2xl shadow-xl shadow-[#C4A265]/25 hover:shadow-2xl hover:shadow-[#C4A265]/30 hover:-translate-y-1 transition-all duration-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z" />
                            </svg>
                            {{ t('home.cta.demo_button') }}
                            <svg class="w-5 h-5 opacity-0 -ms-2 group-hover:opacity-100 group-hover:ms-0 transition-all duration-300 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                        <a
                            href="/contact"
                            class="inline-flex items-center gap-3 px-8 py-4 bg-white/10 text-white font-bold text-base rounded-2xl border border-white/20 backdrop-blur-sm hover:bg-white/20 hover:border-white/30 hover:-translate-y-1 transition-all duration-300"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            {{ t('home.cta.contact_button') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- 12. Newsletter -->
        <NewsletterSignup />
    </MainLayout>
</template>

<style scoped>
@keyframes pulse-slow {
    0%, 100% {
        opacity: 0.5;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

.animate-pulse-slow {
    animation: pulse-slow 8s ease-in-out infinite;
}
</style>
