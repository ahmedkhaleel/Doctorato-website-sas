<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import AnimatedCounter from '@/Components/AnimatedCounter.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import { computed, ref } from 'vue';

const { t } = useI18n();
useScrollAnimation();

/* ───────── Key Highlights (6 cards) ───────── */
const highlights = computed(() => [
    {
        icon: 'shield',
        color: 'from-blue-500/10 to-blue-600/5',
        iconColor: 'text-blue-600',
        title: t('features.highlight_security_title'),
        description: t('features.highlight_security_desc'),
    },
    {
        icon: 'bolt',
        color: 'from-amber-500/10 to-amber-600/5',
        iconColor: 'text-amber-600',
        title: t('features.highlight_performance_title'),
        description: t('features.highlight_performance_desc'),
    },
    {
        icon: 'cloud',
        color: 'from-emerald-500/10 to-emerald-600/5',
        iconColor: 'text-emerald-600',
        title: t('features.highlight_cloud_title'),
        description: t('features.highlight_cloud_desc'),
    },
    {
        icon: 'mobile',
        color: 'from-purple-500/10 to-purple-600/5',
        iconColor: 'text-purple-600',
        title: t('features.highlight_mobile_title'),
        description: t('features.highlight_mobile_desc'),
    },
    {
        icon: 'customize',
        color: 'from-rose-500/10 to-rose-600/5',
        iconColor: 'text-rose-600',
        title: t('features.highlight_customizable_title'),
        description: t('features.highlight_customizable_desc'),
    },
    {
        icon: 'users',
        color: 'from-cyan-500/10 to-cyan-600/5',
        iconColor: 'text-cyan-600',
        title: t('features.highlight_multiuser_title'),
        description: t('features.highlight_multiuser_desc'),
    },
]);

/* ───────── System Overview Cards ───────── */
const overviewCards = computed(() => [
    {
        route: 'portals',
        color: 'from-[#1B4F72]',
        iconBg: 'bg-[#1B4F72]',
        title: t('features_page.overview_portals_title'),
        description: t('features_page.overview_portals_desc'),
        bullets: [
            t('features_page.overview_portals_bullet1'),
            t('features_page.overview_portals_bullet2'),
            t('features_page.overview_portals_bullet3'),
            t('features_page.overview_portals_bullet4'),
        ],
    },
    {
        route: 'dental',
        color: 'from-[#C4A265]',
        iconBg: 'bg-[#C4A265]',
        title: t('features_page.overview_dental_title'),
        description: t('features_page.overview_dental_desc'),
        bullets: [
            t('features_page.overview_dental_bullet1'),
            t('features_page.overview_dental_bullet2'),
            t('features_page.overview_dental_bullet3'),
            t('features_page.overview_dental_bullet4'),
        ],
    },
    {
        route: 'dermatology',
        color: 'from-[#EC4899]',
        iconBg: 'bg-gradient-to-br from-[#EC4899] to-[#A855F7]',
        title: t('features_page.overview_dermatology_title'),
        description: t('features_page.overview_dermatology_desc'),
        bullets: [
            t('features_page.overview_dermatology_bullet1'),
            t('features_page.overview_dermatology_bullet2'),
            t('features_page.overview_dermatology_bullet3'),
            t('features_page.overview_dermatology_bullet4'),
        ],
    },
    {
        route: 'pediatrics',
        color: 'from-sky-500',
        iconBg: 'bg-gradient-to-br from-sky-500 to-cyan-400',
        title: t('features_page.overview_pediatrics_title'),
        description: t('features_page.overview_pediatrics_desc'),
        bullets: [
            t('features_page.overview_pediatrics_bullet1'),
            t('features_page.overview_pediatrics_bullet2'),
            t('features_page.overview_pediatrics_bullet3'),
            t('features_page.overview_pediatrics_bullet4'),
        ],
    },
    {
        route: 'telemedicine',
        color: 'from-teal-500',
        iconBg: 'bg-gradient-to-br from-teal-500 to-emerald-500',
        title: t('features_page.overview_telemedicine_title'),
        description: t('features_page.overview_telemedicine_desc'),
        bullets: [
            t('features_page.overview_telemedicine_bullet1'),
            t('features_page.overview_telemedicine_bullet2'),
            t('features_page.overview_telemedicine_bullet3'),
            t('features_page.overview_telemedicine_bullet4'),
        ],
    },
    {
        route: 'solutions',
        color: 'from-emerald-600',
        iconBg: 'bg-emerald-600',
        title: t('features_page.overview_solutions_title'),
        description: t('features_page.overview_solutions_desc'),
        bullets: [
            t('features_page.overview_solutions_bullet1'),
            t('features_page.overview_solutions_bullet2'),
            t('features_page.overview_solutions_bullet3'),
            t('features_page.overview_solutions_bullet4'),
        ],
    },
    {
        route: 'technology',
        color: 'from-violet-600',
        iconBg: 'bg-violet-600',
        title: t('features_page.overview_technology_title'),
        description: t('features_page.overview_technology_desc'),
        bullets: [
            t('features_page.overview_technology_bullet1'),
            t('features_page.overview_technology_bullet2'),
            t('features_page.overview_technology_bullet3'),
        ],
    },
    {
        route: 'reports',
        color: 'from-orange-500',
        iconBg: 'bg-orange-500',
        title: t('features_page.overview_reports_title'),
        description: t('features_page.overview_reports_desc'),
        bullets: [
            t('features_page.overview_reports_bullet1'),
            t('features_page.overview_reports_bullet2'),
            t('features_page.overview_reports_bullet3'),
        ],
    },
    {
        route: 'pricing',
        color: 'from-[#1B4F72]',
        iconBg: 'bg-[#1B4F72]',
        title: t('features_page.overview_pricing_title'),
        description: t('features_page.overview_pricing_desc'),
        bullets: [
            t('features_page.overview_pricing_bullet1'),
            t('features_page.overview_pricing_bullet2'),
            t('features_page.overview_pricing_bullet3'),
        ],
    },
]);

/* ───────── Numbers / Stats ───────── */
const stats = computed(() => [
    { target: 800, suffix: '+', label: t('features_page.stat_features'), icon: 'features' },
    { target: 6, suffix: '', label: t('features_page.stat_portals'), icon: 'portals' },
    { target: 95, suffix: '+', label: t('features_page.stat_modules'), icon: 'modules' },
    { target: 157, suffix: '+', label: t('features_page.stat_tables'), icon: 'tables' },
    { target: 3000, suffix: '+', label: t('features_page.stat_translations'), icon: 'translations' },
    { target: 80, suffix: '+', label: t('features_page.stat_permissions'), icon: 'permissions' },
]);

/* ───────── Competitive Advantages ───────── */
const advantages = computed(() => [
    { title: t('features_page.advantage_bilingual_title'), description: t('features_page.advantage_bilingual_desc'), icon: 'globe' },
    { title: t('features_page.advantage_multispecialty_title'), description: t('features_page.advantage_multispecialty_desc'), icon: 'building' },
    { title: t('features_page.advantage_portals_title'), description: t('features_page.advantage_portals_desc'), icon: 'grid' },
    { title: t('features_page.advantage_crm_title'), description: t('features_page.advantage_crm_desc'), icon: 'users' },
    { title: t('features_page.advantage_financials_title'), description: t('features_page.advantage_financials_desc'), icon: 'money' },
    { title: t('features_page.advantage_hr_title'), description: t('features_page.advantage_hr_desc'), icon: 'calendar' },
    { title: t('features_page.advantage_dental_title'), description: t('features_page.advantage_dental_desc'), icon: 'heart' },
    { title: t('features_page.advantage_insurance_title'), description: t('features_page.advantage_insurance_desc'), icon: 'document' },
    { title: t('features_page.advantage_inventory_title'), description: t('features_page.advantage_inventory_desc'), icon: 'box' },
    { title: t('features_page.advantage_security_title'), description: t('features_page.advantage_security_desc'), icon: 'shield' },
]);

const activeAdvantage = ref(0);
</script>

<template>
    <SeoHead
        :title="t('features.page_title')"
        :description="t('features.hero_subtitle') || 'أكثر من 800 خاصية في نظام واحد لإدارة كل جانب من جوانب عيادتك'"
    />
    <MainLayout>
        <!-- ═══════ 1. HERO SECTION ═══════ -->
        <section class="relative pt-32 pb-24 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <!-- Background pattern -->
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <!-- Glow orbs -->
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 start-0 w-80 h-80 bg-[#2471A3]/15 rounded-full blur-[100px]"></div>
            <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#1B4F72]/20 rounded-full blur-[150px]"></div>

            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/[0.06] border border-white/[0.1] mb-6 animate-fade-up">
                    <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    <span class="text-sm text-white/70 font-medium">{{ t('features_page.hero_badge') }}</span>
                </div>

                <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6 leading-tight animate-fade-up">
                    {{ t('features_page.hero_title') }}
                </h1>
                <p class="text-lg md:text-xl text-white/60 max-w-3xl mx-auto leading-relaxed mb-10 animate-fade-up">
                    {{ t('features_page.hero_subtitle') }}
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-wrap justify-center gap-4 mb-14 animate-fade-up">
                    <Link
                        href="/demo"
                        class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-semibold rounded-xl shadow-lg shadow-[#C4A265]/20 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                    >
                        {{ t('features.cta_demo') }}
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                    <a
                        href="#overview"
                        class="inline-flex items-center gap-2 px-8 py-3.5 border-2 border-white/20 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300"
                    >
                        {{ t('features.cta_explore') }}
                    </a>
                </div>

                <!-- Mini Stats in Hero -->
                <div class="grid grid-cols-3 md:grid-cols-6 gap-4 max-w-4xl mx-auto animate-fade-up">
                    <div v-for="(stat, idx) in stats" :key="idx" class="text-center">
                        <div class="text-2xl md:text-3xl font-extrabold text-white mb-0.5">
                            <AnimatedCounter :target="stat.target" :suffix="stat.suffix" />
                        </div>
                        <p class="text-[11px] md:text-xs text-white/40 font-medium">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ 2. KEY HIGHLIGHTS (6-card grid) ═══════ -->
        <section class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#1B4F72]/5 border border-[#1B4F72]/10 mb-5">
                        <svg class="w-4 h-4 text-[#1B4F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-sm text-[#1B4F72] font-medium">{{ t('features_page.highlights_badge') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ t('features.highlights_title') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ t('features.highlights_subtitle') }}
                    </p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                        <span class="w-8 h-1 rounded-full bg-[#C4A265]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-stagger">
                    <div
                        v-for="(item, idx) in highlights"
                        :key="idx"
                        class="group relative bg-white rounded-2xl border border-gray-100 p-7 hover:shadow-xl hover:border-[#C4A265]/20 transition-all duration-500 overflow-hidden"
                    >
                        <!-- Subtle gradient bg on hover -->
                        <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity duration-500" :class="item.color"></div>

                        <div class="relative z-10">
                            <!-- Icon -->
                            <div class="w-14 h-14 rounded-2xl bg-gray-50 group-hover:bg-white flex items-center justify-center mb-5 transition-all duration-300 group-hover:shadow-lg">
                                <svg class="w-7 h-7 transition-colors duration-300" :class="item.iconColor" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path v-if="item.icon === 'shield'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    <path v-if="item.icon === 'bolt'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    <path v-if="item.icon === 'cloud'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    <path v-if="item.icon === 'mobile'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    <path v-if="item.icon === 'customize'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                    <path v-if="item.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>

                            <h3 class="text-lg font-bold text-[#1C2833] mb-2">{{ item.title }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ item.description }}</p>
                        </div>

                        <!-- Bottom accent on hover -->
                        <div class="absolute bottom-0 inset-x-0 h-1 bg-gradient-to-r from-[#C4A265] to-[#D4B876] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-center"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ 3. SYSTEM OVERVIEW CARDS ═══════ -->
        <section id="overview" class="py-20 lg:py-28 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/5 border border-[#C4A265]/10 mb-5">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="text-sm text-[#C4A265] font-medium">{{ t('features_page.overview_badge') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ t('features_page.overview_title') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ t('features_page.overview_subtitle') }}
                    </p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                        <span class="w-8 h-1 rounded-full bg-[#C4A265]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-stagger">
                    <div
                        v-for="(card, idx) in overviewCards"
                        :key="idx"
                        class="group bg-white rounded-3xl border border-gray-100 p-8 hover:shadow-2xl hover:shadow-gray-200/50 transition-all duration-500 flex flex-col relative overflow-hidden"
                    >
                        <!-- Top colored line -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity duration-500" :class="card.color + ' to-transparent'"></div>

                        <!-- Icon -->
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 text-white transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg" :class="card.iconBg">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="card.route === 'portals'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                <path v-if="card.route === 'dental'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                <path v-if="card.route === 'dermatology'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                <path v-if="card.route === 'pediatrics'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                <path v-if="card.route === 'telemedicine'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                <path v-if="card.route === 'solutions'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                <path v-if="card.route === 'technology'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                                <path v-if="card.route === 'reports'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                <path v-if="card.route === 'pricing'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <!-- Title -->
                        <h3 class="text-xl font-bold text-[#1C2833] mb-3">{{ card.title }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed mb-5">{{ card.description }}</p>

                        <!-- Bullet points -->
                        <ul class="space-y-2.5 mb-6 flex-grow">
                            <li v-for="(bullet, bIdx) in card.bullets" :key="bIdx" class="flex items-start gap-2.5">
                                <div class="w-5 h-5 rounded-full bg-emerald-50 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-600">{{ bullet }}</span>
                            </li>
                        </ul>

                        <!-- Learn More link -->
                        <Link
                            :href="'/' + card.route"
                            class="inline-flex items-center gap-2 text-[#1B4F72] font-semibold text-sm hover:text-[#C4A265] transition-colors mt-auto group/link"
                        >
                            {{ t('features_page.learn_more') }}
                            <svg class="w-4 h-4 rtl:rotate-180 transition-transform group-hover/link:translate-x-1 rtl:group-hover/link:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ 4. NUMBERS / ANIMATED COUNTERS ═══════ -->
        <section class="py-20 lg:py-28 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628]"></div>
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-0 start-0 w-96 h-96 bg-[#C4A265]/8 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 end-0 w-80 h-80 bg-[#2471A3]/10 rounded-full blur-[100px]"></div>

            <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-4">
                        {{ t('features_page.numbers_title') }}
                    </h2>
                    <p class="text-base md:text-lg text-white/50 max-w-2xl mx-auto leading-relaxed">
                        {{ t('features_page.numbers_subtitle') }}
                    </p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6 animate-stagger">
                    <div
                        v-for="(stat, idx) in stats"
                        :key="idx"
                        class="text-center bg-white/[0.04] backdrop-blur-sm rounded-2xl border border-white/[0.06] p-6 hover:bg-white/[0.08] transition-all duration-300"
                    >
                        <div class="text-3xl md:text-4xl font-extrabold text-[#C4A265] mb-2">
                            <AnimatedCounter :target="stat.target" :suffix="stat.suffix" />
                        </div>
                        <p class="text-white/60 text-xs font-medium">{{ stat.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ 5. COMPETITIVE ADVANTAGES ═══════ -->
        <section class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Section Header -->
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/5 border border-[#C4A265]/10 mb-5">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                        <span class="text-sm text-[#C4A265] font-medium">{{ t('features_page.advantages_badge') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">
                        {{ t('features_page.advantages_title') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                        {{ t('features_page.advantages_subtitle') }}
                    </p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                        <span class="w-8 h-1 rounded-full bg-[#C4A265]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 animate-stagger">
                    <div
                        v-for="(adv, idx) in advantages"
                        :key="idx"
                        class="group relative bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-xl hover:border-[#1B4F72]/10 transition-all duration-500 overflow-hidden cursor-default"
                        @mouseenter="activeAdvantage = idx"
                    >
                        <!-- Number badge -->
                        <span class="absolute top-4 end-4 text-3xl font-extrabold text-gray-100 group-hover:text-[#C4A265]/10 transition-colors duration-300">
                            {{ String(idx + 1).padStart(2, '0') }}
                        </span>

                        <!-- Icon -->
                        <div class="w-11 h-11 rounded-xl bg-[#1B4F72]/5 group-hover:bg-[#1B4F72] flex items-center justify-center mb-4 transition-all duration-300">
                            <svg class="w-5.5 h-5.5 text-[#1B4F72] group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="adv.icon === 'globe'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path v-if="adv.icon === 'building'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                <path v-if="adv.icon === 'grid'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                <path v-if="adv.icon === 'users'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path v-if="adv.icon === 'money'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path v-if="adv.icon === 'calendar'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                <path v-if="adv.icon === 'heart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                <path v-if="adv.icon === 'document'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                <path v-if="adv.icon === 'box'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                <path v-if="adv.icon === 'shield'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>

                        <h3 class="text-base font-bold text-[#1C2833] mb-2">{{ adv.title }}</h3>
                        <p class="text-xs text-gray-500 leading-relaxed">{{ adv.description }}</p>

                        <!-- Bottom accent -->
                        <div class="absolute bottom-0 inset-x-0 h-0.5 bg-gradient-to-r from-[#1B4F72] to-[#C4A265] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-center"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ 6. CTA SECTION ═══════ -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl md:text-4xl font-extrabold text-white mb-4">
                            {{ t('features.cta_title') }}
                        </h2>
                        <p class="text-white/60 text-base md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">
                            {{ t('features.cta_subtitle') }}
                        </p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href="/demo"
                                class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-semibold rounded-xl shadow-lg shadow-[#C4A265]/20 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                            >
                                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                                {{ t('features.cta_request_demo') }}
                            </Link>
                            <Link
                                href="/pricing"
                                class="inline-flex items-center gap-2 px-8 py-3.5 border-2 border-white/20 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300"
                            >
                                {{ t('features.cta_view_pricing') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
