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
import SeoHead from '@/Components/SeoHead.vue';
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

const homeJsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: 'Doctorato',
    alternateName: 'دكتوراتو',
    description: t('home.title'),
    applicationCategory: 'HealthApplication',
    operatingSystem: 'Web, iOS, Android',
    offers: {
        '@type': 'AggregateOffer',
        priceCurrency: 'EGP',
        lowPrice: '799',
        highPrice: '2999',
        offerCount: 3,
    },
    aggregateRating: {
        '@type': 'AggregateRating',
        ratingValue: '4.9',
        reviewCount: '127',
        bestRating: '5',
    },
}));
</script>

<template>
    <SeoHead
        :title="t('home.title')"
        :description="t('home.hero_subtitle') || t('home.title')"
        :json-ld="homeJsonLd"
    />
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

        <!-- 3b. Specialized Solutions — 3 dedicated services spotlight -->
        <section class="py-20 lg:py-24 bg-white relative overflow-hidden">
            <!-- Subtle background -->
            <div class="absolute top-0 end-0 w-[500px] h-[500px] bg-[#1B4F72]/[0.025] rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 start-0 w-[400px] h-[400px] bg-[#C4A265]/[0.035] rounded-full blur-3xl"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <!-- Header -->
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 mb-5">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        <span class="text-sm font-semibold text-[#1B4F72]">
                            {{ locale === 'ar' ? 'حلول متخصّصة' : 'Specialized solutions' }}
                        </span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4 leading-tight">
                        {{ locale === 'ar' ? 'نظام واحد، 3 تخصّصات جاهزة' : 'One platform, 3 ready-made specialties' }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ locale === 'ar'
                            ? 'اختر الحل الأقرب لعيادتك — كل واحد مدمج بالكامل داخل دكتوراتو'
                            : 'Pick the solution closest to your clinic — each one is fully integrated inside Doctorato' }}
                    </p>
                </div>

                <!-- 3 spotlight cards -->
                <div class="grid md:grid-cols-3 gap-6 animate-stagger">
                    <!-- Dental System -->
                    <Link
                        href="/dental"
                        class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#0A1628] text-white p-8 border border-white/10 hover:border-[#C4A265]/40 hover:-translate-y-1 transition-all duration-500 shadow-lg"
                    >
                        <!-- Animated orb -->
                        <div class="absolute -top-24 -end-24 w-64 h-64 bg-[#C4A265]/15 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                        <!-- Hex pattern -->
                        <svg class="absolute inset-0 w-full h-full opacity-[0.05] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="hx-dental" x="0" y="0" width="48" height="56" patternUnits="userSpaceOnUse">
                                    <polygon points="24,2 44,14 44,42 24,54 4,42 4,14" fill="none" stroke="white" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#hx-dental)"/>
                        </svg>

                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B87A] flex items-center justify-center mb-5 shadow-lg shadow-[#C4A265]/30 group-hover:rotate-6 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-extrabold mb-2">
                                {{ locale === 'ar' ? 'نظام عيادات الأسنان' : 'Dental Clinic System' }}
                            </h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-5">
                                {{ locale === 'ar'
                                    ? 'نظام كامل لإدارة عيادات الأسنان: مخطط FDI تفاعلي، خطط علاج، أشعة، لاب، موافقات رقمية.'
                                    : 'Full dental clinic system: interactive FDI chart, treatment plans, imaging, lab orders, digital consent.' }}
                            </p>
                            <span class="inline-flex items-center gap-2 text-[#C4A265] text-sm font-bold group-hover:gap-3 transition-all">
                                {{ locale === 'ar' ? 'تعرّف أكثر' : 'Learn more' }}
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                        </div>
                    </Link>

                    <!-- Pediatrics -->
                    <Link
                        href="/pediatrics"
                        class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#0A1628] text-white p-8 border border-white/10 hover:border-[#EC4899]/40 hover:-translate-y-1 transition-all duration-500 shadow-lg"
                    >
                        <div class="absolute -top-24 -end-24 w-64 h-64 bg-[#EC4899]/15 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                        <svg class="absolute inset-0 w-full h-full opacity-[0.05] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="hx-ped" x="0" y="0" width="48" height="56" patternUnits="userSpaceOnUse">
                                    <polygon points="24,2 44,14 44,42 24,54 4,42 4,14" fill="none" stroke="white" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#hx-ped)"/>
                        </svg>

                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#EC4899] to-[#DB2777] flex items-center justify-center mb-5 shadow-lg shadow-[#EC4899]/30 group-hover:rotate-6 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-extrabold mb-2">
                                {{ locale === 'ar' ? 'طب الأطفال' : 'Pediatrics' }}
                            </h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-5">
                                {{ locale === 'ar'
                                    ? 'وحدة متكاملة لعيادات الأطفال: مخططات نمو WHO، تطعيمات، بروتوكولات، ملف عائلي.'
                                    : 'Complete pediatric module: WHO growth charts, vaccinations, protocols, family profiles.' }}
                            </p>
                            <span class="inline-flex items-center gap-2 text-[#EC4899] text-sm font-bold group-hover:gap-3 transition-all">
                                {{ locale === 'ar' ? 'تعرّف أكثر' : 'Learn more' }}
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                        </div>
                    </Link>

                    <!-- Telemedicine -->
                    <Link
                        href="/telemedicine"
                        class="group relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#0A1628] text-white p-8 border border-white/10 hover:border-[#5DADE2]/40 hover:-translate-y-1 transition-all duration-500 shadow-lg"
                    >
                        <div class="absolute -top-24 -end-24 w-64 h-64 bg-[#5DADE2]/15 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
                        <svg class="absolute inset-0 w-full h-full opacity-[0.05] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <pattern id="hx-tel" x="0" y="0" width="48" height="56" patternUnits="userSpaceOnUse">
                                    <polygon points="24,2 44,14 44,42 24,54 4,42 4,14" fill="none" stroke="white" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="100%" height="100%" fill="url(#hx-tel)"/>
                        </svg>

                        <!-- NEW badge -->
                        <div class="absolute top-5 end-5 px-2.5 py-1 rounded-full bg-[#5DADE2]/20 border border-[#5DADE2]/30 text-[#5DADE2] text-[10px] font-bold uppercase tracking-widest">
                            {{ locale === 'ar' ? 'جديد' : 'New' }}
                        </div>

                        <div class="relative">
                            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#2E86C1] to-[#1B4F72] flex items-center justify-center mb-5 shadow-lg shadow-[#2E86C1]/30 group-hover:rotate-6 transition-transform duration-500">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-extrabold mb-2">
                                {{ locale === 'ar' ? 'الاستشارات الأون لاين' : 'Online Consultations' }}
                            </h3>
                            <p class="text-white/60 text-sm leading-relaxed mb-5">
                                {{ locale === 'ar'
                                    ? 'مكالمات فيديو HD، وصفات إلكترونية، دفع فوري، سجل طبي متزامن — عيادة افتراضية كاملة.'
                                    : 'HD video, digital prescriptions, instant payment, live-synced records — a complete virtual clinic.' }}
                            </p>
                            <span class="inline-flex items-center gap-2 text-[#5DADE2] text-sm font-bold group-hover:gap-3 transition-all">
                                {{ locale === 'ar' ? 'تعرّف أكثر' : 'Learn more' }}
                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </span>
                        </div>
                    </Link>
                </div>
            </div>
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
                        class="feature-card group relative rounded-2xl overflow-hidden transition-all duration-500 hover:-translate-y-1.5 bg-white border border-gray-100 shadow-sm hover:shadow-2xl hover:shadow-[#1B4F72]/10 hover:border-transparent"
                        :class="[
                            index === 0 ? 'md:col-span-4 md:row-span-2 min-h-[320px] md:min-h-[380px]' : '',
                            index === 1 ? 'md:col-span-2 min-h-[200px] md:min-h-[185px]' : '',
                            index === 2 ? 'md:col-span-2 min-h-[200px] md:min-h-[185px]' : '',
                            index > 2 ? 'md:col-span-2 min-h-[200px]' : '',
                        ]"
                    >
                        <!-- Hover gradient layer -->
                        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500" :class="card.color"></div>

                        <!-- ===== BACKGROUND ILLUSTRATIONS (per card) ===== -->

                        <!-- FEATURES (large card) - Rich constellation illustration -->
                        <template v-if="card.icon === 'features'">
                            <!-- Huge "800+" background number -->
                            <div class="absolute top-1/2 end-8 -translate-y-1/2 pointer-events-none select-none hidden md:block">
                                <div class="text-[200px] font-black leading-none text-[#1B4F72]/[0.04] group-hover:text-white/[0.08] transition-colors duration-500" style="font-variant-numeric: tabular-nums;">
                                    800
                                </div>
                            </div>
                            <!-- Floating feature dots/sparkles -->
                            <svg class="absolute top-8 end-8 w-48 h-48 md:w-56 md:h-56 opacity-[0.35] group-hover:opacity-60 transition-opacity duration-700" viewBox="0 0 240 240" fill="none">
                                <!-- Connecting lines -->
                                <g class="text-[#1B4F72] group-hover:text-white transition-colors duration-500" stroke="currentColor" stroke-width="1" stroke-dasharray="3 4" opacity="0.5">
                                    <line x1="60" y1="40" x2="140" y2="80"/>
                                    <line x1="140" y1="80" x2="200" y2="50"/>
                                    <line x1="140" y1="80" x2="180" y2="150"/>
                                    <line x1="140" y1="80" x2="80" y2="160"/>
                                    <line x1="180" y1="150" x2="80" y2="160"/>
                                    <line x1="60" y1="40" x2="80" y2="160"/>
                                </g>
                                <!-- Feature cards (small boxes) -->
                                <g class="text-[#1B4F72] group-hover:text-white transition-all duration-500">
                                    <rect x="48" y="28" width="28" height="24" rx="4" fill="currentColor" fill-opacity="0.1" stroke="currentColor" stroke-width="1.5"/>
                                    <rect x="52" y="34" width="14" height="2" rx="1" fill="currentColor" opacity="0.5"/>
                                    <rect x="52" y="40" width="10" height="2" rx="1" fill="currentColor" opacity="0.3"/>

                                    <rect x="128" y="68" width="28" height="24" rx="4" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="1.5"/>
                                    <circle cx="136" cy="78" r="3" fill="currentColor" opacity="0.5"/>
                                    <rect x="143" y="76" width="10" height="2" rx="1" fill="currentColor" opacity="0.4"/>
                                    <rect x="143" y="80" width="8" height="2" rx="1" fill="currentColor" opacity="0.3"/>

                                    <rect x="188" y="38" width="28" height="24" rx="4" fill="currentColor" fill-opacity="0.1" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="M194 50 L200 44 L206 50 L212 42" stroke="currentColor" stroke-width="1.5" fill="none" opacity="0.6"/>

                                    <rect x="168" y="138" width="28" height="24" rx="4" fill="currentColor" fill-opacity="0.12" stroke="currentColor" stroke-width="1.5"/>
                                    <circle cx="182" cy="150" r="5" stroke="currentColor" stroke-width="1.5" fill="none" opacity="0.5"/>

                                    <rect x="68" y="148" width="28" height="24" rx="4" fill="currentColor" fill-opacity="0.1" stroke="currentColor" stroke-width="1.5"/>
                                    <rect x="73" y="154" width="18" height="2" rx="1" fill="currentColor" opacity="0.4"/>
                                    <rect x="73" y="158" width="12" height="2" rx="1" fill="currentColor" opacity="0.3"/>
                                    <rect x="73" y="162" width="8" height="2" rx="1" fill="currentColor" opacity="0.3"/>
                                </g>
                                <!-- Glowing dots -->
                                <g class="text-[#C4A265] group-hover:text-white transition-colors duration-500">
                                    <circle cx="62" cy="40" r="3" fill="currentColor"/>
                                    <circle cx="142" cy="80" r="3" fill="currentColor"/>
                                    <circle cx="200" cy="50" r="3" fill="currentColor"/>
                                    <circle cx="180" cy="150" r="3" fill="currentColor"/>
                                    <circle cx="80" cy="160" r="3" fill="currentColor"/>
                                </g>
                            </svg>
                            <!-- Sparkle accents -->
                            <div class="absolute top-16 start-20 w-3 h-3 text-[#C4A265] group-hover:text-white opacity-60">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l1.5 9L24 12l-10.5 3L12 24l-1.5-9L0 12l10.5-3L12 0z"/></svg>
                            </div>
                            <div class="absolute bottom-24 end-32 w-2 h-2 text-[#1B4F72] group-hover:text-white opacity-40">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 0l1.5 9L24 12l-10.5 3L12 24l-1.5-9L0 12l10.5-3L12 0z"/></svg>
                            </div>
                        </template>

                        <!-- PORTALS - 6 grid boxes illustration -->
                        <template v-else-if="card.icon === 'portals'">
                            <svg class="absolute -top-4 -end-4 w-40 h-40 opacity-[0.12] group-hover:opacity-30 transition-opacity duration-500" viewBox="0 0 160 160" fill="none">
                                <g class="text-[#C4A265] group-hover:text-white transition-colors duration-500">
                                    <rect x="16" y="16" width="36" height="36" rx="6" fill="currentColor" fill-opacity="0.3"/>
                                    <rect x="62" y="16" width="36" height="36" rx="6" fill="currentColor" fill-opacity="0.5"/>
                                    <rect x="108" y="16" width="36" height="36" rx="6" fill="currentColor" fill-opacity="0.3"/>
                                    <rect x="16" y="62" width="36" height="36" rx="6" fill="currentColor" fill-opacity="0.5"/>
                                    <rect x="62" y="62" width="36" height="36" rx="6" fill="currentColor" fill-opacity="0.7"/>
                                    <rect x="108" y="62" width="36" height="36" rx="6" fill="currentColor" fill-opacity="0.3"/>
                                </g>
                            </svg>
                        </template>

                        <!-- DENTAL - Tooth + pattern -->
                        <template v-else-if="card.icon === 'dental'">
                            <svg class="absolute -top-6 -end-6 w-40 h-40 opacity-[0.12] group-hover:opacity-25 transition-opacity duration-500" viewBox="0 0 160 160" fill="none">
                                <g class="text-[#1C2833] group-hover:text-white transition-colors duration-500">
                                    <path d="M80 20C60 20 36 32 36 60C36 100 62 132 68 132C74 132 76 108 76 108C76 108 78 132 84 132C90 132 116 100 116 60C116 32 100 20 80 20Z" fill="currentColor" fill-opacity="0.4"/>
                                    <circle cx="60" cy="48" r="3" fill="currentColor" opacity="0.6"/>
                                    <circle cx="100" cy="48" r="3" fill="currentColor" opacity="0.6"/>
                                    <path d="M52 72 Q80 90 108 72" stroke="currentColor" stroke-width="2" fill="none" opacity="0.5" stroke-dasharray="3 3"/>
                                </g>
                            </svg>
                        </template>

                        <!-- SOLUTIONS - Lightbulb + rays -->
                        <template v-else-if="card.icon === 'solutions'">
                            <svg class="absolute -top-4 -end-4 w-40 h-40 opacity-[0.12] group-hover:opacity-25 transition-opacity duration-500" viewBox="0 0 160 160" fill="none">
                                <g class="text-[#1B4F72] group-hover:text-white transition-colors duration-500">
                                    <!-- Rays -->
                                    <line x1="80" y1="20" x2="80" y2="32" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.6"/>
                                    <line x1="40" y1="60" x2="52" y2="60" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.6"/>
                                    <line x1="108" y1="60" x2="120" y2="60" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.6"/>
                                    <line x1="50" y1="30" x2="58" y2="38" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.6"/>
                                    <line x1="110" y1="30" x2="102" y2="38" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" opacity="0.6"/>
                                    <!-- Bulb -->
                                    <circle cx="80" cy="70" r="24" fill="currentColor" fill-opacity="0.4"/>
                                    <path d="M68 88 L92 88 M70 96 L90 96 M74 104 L86 104" stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.6"/>
                                </g>
                            </svg>
                        </template>

                        <!-- TECHNOLOGY - Circuit lines -->
                        <template v-else-if="card.icon === 'technology'">
                            <svg class="absolute -top-4 -end-4 w-44 h-44 opacity-[0.14] group-hover:opacity-30 transition-opacity duration-500" viewBox="0 0 160 160" fill="none">
                                <g class="text-[#C4A265] group-hover:text-white transition-colors duration-500" stroke="currentColor" stroke-width="2" fill="none">
                                    <path d="M20 40 L60 40 L60 80 L100 80 L100 40 L140 40"/>
                                    <path d="M20 100 L40 100 L40 120 L80 120 L80 60"/>
                                    <path d="M140 100 L110 100 L110 140 L60 140"/>
                                </g>
                                <g class="text-[#C4A265] group-hover:text-white transition-colors duration-500" fill="currentColor">
                                    <circle cx="20" cy="40" r="4"/>
                                    <circle cx="60" cy="40" r="4"/>
                                    <circle cx="60" cy="80" r="4"/>
                                    <circle cx="100" cy="80" r="4"/>
                                    <circle cx="100" cy="40" r="4"/>
                                    <circle cx="140" cy="40" r="4"/>
                                    <circle cx="20" cy="100" r="4"/>
                                    <circle cx="40" cy="120" r="4"/>
                                    <circle cx="80" cy="60" r="4"/>
                                    <circle cx="140" cy="100" r="4"/>
                                    <circle cx="110" cy="140" r="4"/>
                                </g>
                            </svg>
                        </template>

                        <!-- REPORTS - Bar chart + line -->
                        <template v-else>
                            <svg class="absolute -top-4 -end-4 w-44 h-44 opacity-[0.12] group-hover:opacity-25 transition-opacity duration-500" viewBox="0 0 160 160" fill="none">
                                <g class="text-[#1C2833] group-hover:text-white transition-colors duration-500">
                                    <rect x="24" y="80" width="18" height="60" rx="3" fill="currentColor" fill-opacity="0.3"/>
                                    <rect x="52" y="50" width="18" height="90" rx="3" fill="currentColor" fill-opacity="0.5"/>
                                    <rect x="80" y="70" width="18" height="70" rx="3" fill="currentColor" fill-opacity="0.4"/>
                                    <rect x="108" y="30" width="18" height="110" rx="3" fill="currentColor" fill-opacity="0.6"/>
                                    <path d="M24 100 L52 70 L80 85 L108 45 L136 60" stroke="currentColor" stroke-width="2.5" fill="none" opacity="0.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="24" cy="100" r="3" fill="currentColor"/>
                                    <circle cx="52" cy="70" r="3" fill="currentColor"/>
                                    <circle cx="80" cy="85" r="3" fill="currentColor"/>
                                    <circle cx="108" cy="45" r="3" fill="currentColor"/>
                                </g>
                            </svg>
                        </template>

                        <!-- Subtle grid pattern overlay -->
                        <div class="absolute inset-0 opacity-[0.02] group-hover:opacity-[0.06] transition-opacity duration-500" style="background-image: linear-gradient(#1B4F72 1px, transparent 1px), linear-gradient(90deg, #1B4F72 1px, transparent 1px); background-size: 32px 32px;"></div>

                        <!-- Number badge -->
                        <div class="absolute top-4 end-4 text-[10px] font-bold tracking-widest text-gray-300 group-hover:text-white/40 transition-colors duration-500 z-10">
                            {{ String(index + 1).padStart(2, '0') }} / 06
                        </div>

                        <!-- Content -->
                        <div class="relative h-full p-5 md:p-6 flex flex-col z-[1]">
                            <!-- Icon -->
                            <div class="mb-auto">
                                <div class="relative inline-flex items-center justify-center rounded-xl bg-gradient-to-br transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 group-hover:bg-white/15 group-hover:backdrop-blur-sm group-hover:border-white/20 border border-transparent shadow-sm"
                                    :class="[
                                        index === 0 ? 'w-14 h-14 md:w-16 md:h-16' : 'w-12 h-12',
                                        card.iconBg
                                    ]">
                                    <svg v-if="card.icon === 'features'" class="text-[#1B4F72] group-hover:text-white transition-colors duration-500" :class="index === 0 ? 'w-7 h-7 md:w-8 md:h-8' : 'w-6 h-6'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
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
                            <div class="mt-5 md:mt-6 max-w-[80%]">
                                <div v-if="index === 0 && card.stat" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/25 group-hover:bg-white/15 group-hover:border-white/30 transition-all duration-500 mb-3">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] group-hover:bg-white transition-colors duration-500 animate-pulse"></span>
                                    <span class="text-[10px] font-bold text-[#C4A265] group-hover:text-white transition-colors duration-500 tracking-wide">{{ card.stat }}</span>
                                </div>
                                <h3 class="font-bold text-[#0D2B45] group-hover:text-white transition-colors duration-500 mb-2 leading-snug"
                                    :class="index === 0 ? 'text-xl md:text-2xl' : 'text-base md:text-[17px]'">
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
                                <div class="w-7 h-7 rounded-lg bg-[#C4A265]/10 group-hover:bg-white/20 flex items-center justify-center transition-all duration-500">
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

        <!-- 5. How It Works - Premium Step Cards -->
        <section class="py-20 lg:py-28 bg-[#F8FAFC] relative overflow-hidden">
            <!-- Ambient background effects -->
            <div class="absolute inset-0 opacity-[0.025]" style="background-image: radial-gradient(circle at 1px 1px, #1B4F72 1px, transparent 0); background-size: 48px 48px;"></div>
            <div class="absolute top-0 start-1/4 w-[500px] h-[500px] bg-[#1B4F72]/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 end-1/4 w-[450px] h-[450px] bg-[#C4A265]/5 rounded-full blur-3xl"></div>

            <div class="container mx-auto px-4 relative z-10">
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto mb-16 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 mb-5">
                        <span class="relative flex w-1.5 h-1.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-[#C4A265]"></span>
                        </span>
                        <span class="text-[#8B6B2F] text-xs font-bold tracking-wider uppercase">
                            {{ locale === 'ar' ? 'ثلاث خطوات فقط' : 'Only 3 Steps' }}
                        </span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#0D2B45] mb-4 leading-tight">
                        {{ t('home.how_it_works.title') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500">
                        {{ t('home.how_it_works.subtitle') }}
                    </p>
                </div>

                <!-- Steps Grid -->
                <div class="relative max-w-6xl mx-auto">
                    <!-- Connection Line (desktop only) -->
                    <svg class="hidden lg:block absolute top-[88px] start-[12%] end-[12%] w-[76%] h-4 pointer-events-none" viewBox="0 0 800 16" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#1B4F72" stop-opacity="0"/>
                                <stop offset="20%" stop-color="#1B4F72" stop-opacity="0.4"/>
                                <stop offset="50%" stop-color="#C4A265" stop-opacity="0.6"/>
                                <stop offset="80%" stop-color="#1B4F72" stop-opacity="0.4"/>
                                <stop offset="100%" stop-color="#1B4F72" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <path d="M0 8 Q200 -8, 400 8 T800 8" stroke="url(#lineGradient)" stroke-width="2" stroke-dasharray="6 6" fill="none" class="animate-dash"/>
                    </svg>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                        <div
                            v-for="(step, index) in howItWorksSteps"
                            :key="index"
                            class="how-step-card group animate-fade-up"
                            :style="{ animationDelay: (index * 150) + 'ms' }"
                        >
                            <!-- Gradient border wrapper -->
                            <div class="relative h-full rounded-3xl p-[1.5px] bg-gradient-to-br from-[#1B4F72]/10 via-[#C4A265]/20 to-[#1B4F72]/10 group-hover:from-[#1B4F72]/30 group-hover:via-[#C4A265]/50 group-hover:to-[#1B4F72]/30 transition-all duration-500">
                                <div class="relative h-full rounded-3xl bg-white p-8 overflow-hidden">
                                    <!-- Decorative corner SVG -->
                                    <svg class="absolute -top-8 -end-8 w-48 h-48 text-[#1B4F72]/[0.03] group-hover:text-[#C4A265]/[0.07] transition-colors duration-700" viewBox="0 0 200 200" fill="none">
                                        <circle cx="100" cy="100" r="80" stroke="currentColor" stroke-width="1" stroke-dasharray="4 4"/>
                                        <circle cx="100" cy="100" r="60" stroke="currentColor" stroke-width="1"/>
                                        <circle cx="100" cy="100" r="40" stroke="currentColor" stroke-width="1" stroke-dasharray="2 2"/>
                                    </svg>

                                    <!-- Huge background number -->
                                    <div class="absolute -top-4 end-6 text-[140px] font-black leading-none text-[#1B4F72]/[0.04] group-hover:text-[#C4A265]/[0.08] transition-colors duration-500 select-none pointer-events-none">
                                        {{ step.number }}
                                    </div>

                                    <div class="relative">
                                        <!-- Icon Container -->
                                        <div class="relative inline-flex mb-6">
                                            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] blur-lg opacity-20 group-hover:opacity-40 transition-opacity duration-500"></div>
                                            <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] flex items-center justify-center shadow-xl shadow-[#1B4F72]/20 group-hover:-translate-y-1 group-hover:rotate-3 transition-all duration-500">
                                                <!-- Register icon -->
                                                <svg v-if="step.icon === 'register'" class="w-10 h-10 text-white" viewBox="0 0 36 36" fill="none">
                                                    <circle cx="18" cy="12" r="6" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="0.2"/>
                                                    <path d="M6 30C6 24.477 10.477 20 16 20H20C25.523 20 30 24.477 30 30" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>
                                                    <path d="M26 10L28 12L32 8" stroke="#C4A265" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                <!-- Setup icon -->
                                                <svg v-else-if="step.icon === 'setup'" class="w-10 h-10 text-white" viewBox="0 0 36 36" fill="none">
                                                    <circle cx="18" cy="18" r="7" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="0.2"/>
                                                    <circle cx="18" cy="18" r="3" fill="#C4A265"/>
                                                    <path d="M18 7V10M18 26V29M7 18H10M26 18H29M10.2 10.2L12.3 12.3M23.7 23.7L25.8 25.8M10.2 25.8L12.3 23.7M23.7 12.3L25.8 10.2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                </svg>
                                                <!-- Manage icon -->
                                                <svg v-else class="w-10 h-10 text-white" viewBox="0 0 36 36" fill="none">
                                                    <rect x="4" y="6" width="28" height="22" rx="3" stroke="currentColor" stroke-width="2" fill="currentColor" fill-opacity="0.1"/>
                                                    <line x1="4" y1="12" x2="32" y2="12" stroke="currentColor" stroke-width="1.5" opacity="0.5"/>
                                                    <circle cx="7" cy="9" r="0.8" fill="currentColor"/>
                                                    <circle cx="10" cy="9" r="0.8" fill="currentColor"/>
                                                    <rect x="8" y="16" width="8" height="3" rx="1" fill="currentColor" fill-opacity="0.4"/>
                                                    <rect x="8" y="21" width="20" height="2" rx="1" fill="#C4A265"/>
                                                    <rect x="8" y="25" width="14" height="2" rx="1" fill="currentColor" fill-opacity="0.3"/>
                                                    <rect x="20" y="16" width="8" height="3" rx="1" fill="#C4A265" fill-opacity="0.6"/>
                                                </svg>
                                            </div>
                                            <!-- Step badge -->
                                            <div class="absolute -top-2 -end-2 px-2.5 py-1 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white text-[10px] font-black shadow-lg shadow-[#C4A265]/30 ring-2 ring-white">
                                                {{ locale === 'ar' ? `خطوة ${step.number}` : `Step ${step.number}` }}
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <h3 class="text-xl lg:text-2xl font-extrabold text-[#0D2B45] mb-3 leading-tight">
                                            {{ step.title }}
                                        </h3>
                                        <p class="text-gray-500 text-sm leading-relaxed mb-6">
                                            {{ step.description }}
                                        </p>

                                        <!-- Bottom row: arrow + duration hint -->
                                        <div class="flex items-center justify-between pt-5 border-t border-dashed border-gray-200">
                                            <div class="inline-flex items-center gap-1.5 text-[11px] text-gray-400">
                                                <svg class="w-3.5 h-3.5 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-semibold">
                                                    {{ index === 0 ? (locale === 'ar' ? '3 دقائق' : '3 min') : index === 1 ? (locale === 'ar' ? '5 دقائق' : '5 min') : (locale === 'ar' ? 'فوراً' : 'Instant') }}
                                                </span>
                                            </div>
                                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#FAF7F0] text-[#C4A265] group-hover:bg-[#C4A265] group-hover:text-white transition-all duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1">
                                                <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA below steps -->
                    <div class="mt-14 text-center animate-fade-up" style="animation-delay: 500ms">
                        <Link
                            href="/demo"
                            class="group inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white font-bold text-sm shadow-xl shadow-[#1B4F72]/20 hover:shadow-2xl hover:shadow-[#1B4F72]/30 hover:-translate-y-0.5 transition-all duration-300"
                        >
                            <span>{{ locale === 'ar' ? 'ابدأ الآن مجاناً' : 'Start Now for Free' }}</span>
                            <svg class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                        <p class="text-xs text-gray-400 mt-3">
                            {{ locale === 'ar' ? 'لا حاجة لبطاقة ائتمانية • تجربة 15 يوم مجاناً' : 'No credit card needed • 15-day free trial' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Stats Section — Premium redesign with cinematic background -->
        <section class="stats-section py-24 lg:py-32 relative overflow-hidden bg-[#070F1B]">
            <!-- Layer 1: Base radial gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#070F1B] to-[#0A1F33]"></div>

            <!-- Layer 2: Animated aurora orbs (drifting mesh gradient) -->
            <div class="absolute inset-0">
                <div class="absolute top-[-10%] left-[15%] w-[600px] h-[600px] bg-[#1B4F72]/40 rounded-full blur-[140px] animate-aurora"></div>
                <div class="absolute bottom-[-15%] right-[10%] w-[700px] h-[700px] bg-[#C4A265]/20 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#2E86C1]/25 rounded-full blur-[120px] animate-aurora" style="animation-delay: -12s"></div>
            </div>

            <!-- Layer 3: Animated diagonal grid texture -->
            <div
                class="absolute inset-0 opacity-[0.05] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.6) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.6) 1px, transparent 1px); background-size: 60px 60px;"
            ></div>

            <!-- Layer 4: Subtle noise/grain via SVG -->
            <div class="absolute inset-0 opacity-[0.035] mix-blend-overlay pointer-events-none" style="background-image: url(&quot;data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E&quot;);"></div>

            <!-- Layer 5: Floating accent dots -->
            <div class="absolute top-[15%] left-[8%] w-2 h-2 rounded-full bg-[#C4A265] shadow-[0_0_20px_#C4A265] animate-float opacity-60"></div>
            <div class="absolute top-[30%] right-[12%] w-1.5 h-1.5 rounded-full bg-[#5DADE2] shadow-[0_0_15px_#5DADE2] animate-float opacity-50" style="animation-delay: -2s"></div>
            <div class="absolute bottom-[25%] left-[18%] w-1.5 h-1.5 rounded-full bg-[#C4A265] shadow-[0_0_15px_#C4A265] animate-float opacity-40" style="animation-delay: -4s"></div>
            <div class="absolute bottom-[15%] right-[20%] w-2 h-2 rounded-full bg-[#27AE60] shadow-[0_0_20px_#27AE60] animate-float opacity-50" style="animation-delay: -1s"></div>

            <!-- Top + bottom border glow -->
            <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>

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

                <!-- Bento Grid Stats — premium edition -->
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4 md:gap-5 max-w-6xl mx-auto animate-stagger">
                    <!-- Featured stat (Big) -->
                    <div class="col-span-2 md:col-span-3 md:row-span-2 group relative">
                        <!-- Animated conic-gradient border -->
                        <div class="absolute -inset-px rounded-3xl opacity-60 group-hover:opacity-100 transition-opacity duration-700 overflow-hidden">
                            <div class="absolute inset-[-100%] animate-spin-slow" style="background: conic-gradient(from 0deg, transparent 0deg, #C4A265 90deg, transparent 180deg, #1B4F72 270deg, transparent 360deg);"></div>
                        </div>

                        <div
                            class="relative h-full min-h-[280px] md:min-h-[380px] rounded-3xl p-8 md:p-10 overflow-hidden bg-[#0A1628]/90 backdrop-blur-xl transition-all duration-700"
                        >
                            <!-- Inner gradient layer -->
                            <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(196,162,101,0.10) 0%, rgba(27,79,114,0.18) 60%, rgba(10,22,40,0) 100%);"></div>

                            <!-- Pulsing orb -->
                            <div class="absolute -top-32 -end-32 w-80 h-80 bg-[#C4A265]/25 rounded-full blur-[100px] animate-pulse-slow"></div>
                            <div class="absolute -bottom-20 -start-20 w-64 h-64 bg-[#1B4F72]/40 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: -3s"></div>

                            <!-- Shimmer sweep on hover -->
                            <div class="absolute inset-0 overflow-hidden rounded-3xl pointer-events-none">
                                <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/[0.08] to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"></div>
                            </div>

                            <!-- Decorative dots grid -->
                            <svg class="absolute top-6 end-6 w-24 h-24 text-[#C4A265]/15" viewBox="0 0 80 80" fill="none">
                                <pattern id="dots-featured" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="1.2" fill="currentColor" />
                                </pattern>
                                <rect width="80" height="80" fill="url(#dots-featured)" />
                            </svg>

                            <div class="relative h-full flex flex-col">
                                <!-- Icon with gradient ring -->
                                <div class="relative w-20 h-20 mb-6">
                                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B87A] blur-xl opacity-50 group-hover:opacity-80 transition-opacity duration-700"></div>
                                    <div class="relative w-20 h-20 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B87A] flex items-center justify-center shadow-2xl shadow-[#C4A265]/40 group-hover:rotate-[-6deg] group-hover:scale-110 transition-all duration-500">
                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="stats[0].icon" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Number with gradient + glow -->
                                <div class="flex items-baseline gap-2 mb-3">
                                    <div class="text-7xl md:text-8xl lg:text-9xl font-black leading-none bg-gradient-to-br from-white via-[#F5E6C8] to-[#C4A265] bg-clip-text text-transparent group-hover:drop-shadow-[0_0_30px_rgba(196,162,101,0.4)] transition-all duration-700">
                                        <AnimatedCounter :target="stats[0].target" :suffix="stats[0].suffix" />
                                    </div>
                                </div>

                                <!-- Label -->
                                <div class="mt-auto">
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="h-px w-12 bg-gradient-to-r from-[#C4A265] to-transparent"></div>
                                        <div class="w-1 h-1 rounded-full bg-[#C4A265] animate-pulse"></div>
                                    </div>
                                    <p class="text-[#C4A265] font-bold text-lg md:text-xl uppercase tracking-wider">{{ stats[0].label }}</p>
                                    <p class="text-white/55 text-xs md:text-sm mt-2 max-w-xs leading-relaxed">{{ locale === 'ar' ? 'خواص شاملة لإدارة كل تفاصيل عيادتك بكفاءة' : 'Comprehensive features for every clinic detail' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Smaller stats -->
                    <div
                        v-for="(stat, idx) in stats.slice(1)"
                        :key="idx"
                        class="col-span-1 md:col-span-3 lg:col-span-1 group relative"
                    >
                        <!-- Animated gradient border on hover -->
                        <div class="absolute -inset-px rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 overflow-hidden">
                            <div class="absolute inset-[-100%] animate-spin-slow" :style="{ background: `conic-gradient(from 0deg, transparent 0deg, ${stat.accent} 60deg, transparent 120deg, transparent 360deg)` }"></div>
                        </div>

                        <div
                            class="relative h-full min-h-[160px] md:min-h-[180px] rounded-2xl p-5 md:p-6 overflow-hidden bg-[#0A1628]/80 backdrop-blur-xl border border-white/[0.06] transition-all duration-500 group-hover:bg-[#0A1628]/95 group-hover:-translate-y-1.5 group-hover:shadow-2xl"
                            :style="{ '--accent': stat.accent }"
                        >
                            <!-- Radial hover glow -->
                            <div
                                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500"
                                :style="{ background: `radial-gradient(circle at 50% 0%, ${stat.accentSoft} 0%, transparent 65%)` }"
                            ></div>

                            <!-- Top accent line that grows -->
                            <div
                                class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] transition-all duration-700"
                                :style="{ background: `linear-gradient(90deg, transparent, ${stat.accent}, transparent)`, boxShadow: `0 0 12px ${stat.accent}` }"
                            ></div>

                            <!-- Glowing accent dot -->
                            <div class="absolute top-4 end-4 w-2 h-2 rounded-full opacity-60 group-hover:opacity-100 group-hover:scale-150 transition-all duration-500" :style="{ background: stat.accent, boxShadow: `0 0 16px ${stat.accent}` }"></div>

                            <!-- Subtle corner texture -->
                            <svg class="absolute -bottom-2 -end-2 w-16 h-16 text-white/[0.04]" viewBox="0 0 64 64" fill="none">
                                <pattern :id="`dots-stat-${idx}`" x="0" y="0" width="8" height="8" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="1" fill="currentColor" />
                                </pattern>
                                <rect width="64" height="64" :fill="`url(#dots-stat-${idx})`" />
                            </svg>

                            <div class="relative">
                                <!-- Icon -->
                                <div
                                    class="relative w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6"
                                    :style="{ background: stat.accentSoft, color: stat.accent, boxShadow: `inset 0 0 0 1px ${stat.accent}30` }"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="stat.icon" />
                                    </svg>
                                </div>

                                <!-- Number with gradient -->
                                <div class="text-4xl md:text-5xl font-black leading-none mb-2 bg-gradient-to-br from-white to-white/70 bg-clip-text text-transparent group-hover:from-white group-hover:to-[var(--accent)] transition-all duration-500">
                                    <AnimatedCounter :target="stat.target" :suffix="stat.suffix" />
                                </div>

                                <!-- Label -->
                                <p class="font-bold text-[10px] sm:text-[11px] uppercase tracking-widest transition-colors" :style="{ color: stat.accent }">
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

@keyframes dash-flow {
    to { stroke-dashoffset: -24; }
}
.animate-dash {
    animation: dash-flow 1.5s linear infinite;
}

.how-step-card {
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}
.how-step-card:hover {
    transform: translateY(-6px);
}
</style>
