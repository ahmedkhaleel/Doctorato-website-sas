<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import RelatedModules from '@/Components/RelatedModules.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import { computed } from 'vue';

const { t } = useI18n();
useScrollAnimation();

/* ------------------------------------------------------------------ */
/*  Upper & lower tooth positions for the interactive SVG chart        */
/* ------------------------------------------------------------------ */
const upperRight = [18, 17, 16, 15, 14, 13, 12, 11];
const upperLeft  = [21, 22, 23, 24, 25, 26, 27, 28];
const lowerLeft  = [31, 32, 33, 34, 35, 36, 37, 38];
const lowerRight = [48, 47, 46, 45, 44, 43, 42, 41];

const toothX = (i) => 60 + i * 35;
const upperY = (i) => 80 + Math.abs(i - 7.5) * 3;
const lowerY = (i) => 195 - Math.abs(i - 7.5) * 3;

/* ------------------------------------------------------------------ */
/*  Tooth status legend                                                */
/* ------------------------------------------------------------------ */
const toothStatuses = computed(() => [
    { color: '#10B981', label: t('dental_page.status_healthy') },
    { color: '#EF4444', label: t('dental_page.status_decayed') },
    { color: '#6B7280', label: t('dental_page.status_missing') },
    { color: '#3B82F6', label: t('dental_page.status_treated') },
    { color: '#8B5CF6', label: t('dental_page.status_implanted') },
]);

/* ------------------------------------------------------------------ */
/*  Dental Chart features                                              */
/* ------------------------------------------------------------------ */
const chartFeatures = computed(() => [
    t('dental_page.chart_feature_fdi'),
    t('dental_page.chart_feature_status'),
    t('dental_page.chart_feature_surfaces'),
    t('dental_page.chart_feature_history'),
]);

/* ------------------------------------------------------------------ */
/*  Treatment Plans features                                           */
/* ------------------------------------------------------------------ */
const treatmentFeatures = computed(() => [
    { icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', title: t('dental_page.treatment_procedures'), desc: t('dental_page.treatment_procedures_desc') },
    { icon: 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z', title: t('dental_page.treatment_templates'), desc: t('dental_page.treatment_templates_desc') },
    { icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', title: t('dental_page.treatment_sessions'), desc: t('dental_page.treatment_sessions_desc') },
    { icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', title: t('dental_page.treatment_cost'), desc: t('dental_page.treatment_cost_desc') },
    { icon: 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', title: t('dental_page.treatment_approval'), desc: t('dental_page.treatment_approval_desc') },
    { icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', title: t('dental_page.treatment_tracking'), desc: t('dental_page.treatment_tracking_desc') },
]);

/* ------------------------------------------------------------------ */
/*  X-Ray types                                                        */
/* ------------------------------------------------------------------ */
const xrayTypes = computed(() => [
    { name: t('dental_page.xray_panoramic'), icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { name: t('dental_page.xray_periapical'), icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7' },
    { name: t('dental_page.xray_bitewing'), icon: 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2' },
    { name: t('dental_page.xray_cbct'), icon: 'M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5' },
    { name: t('dental_page.xray_cephalometric'), icon: 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
]);

/* ------------------------------------------------------------------ */
/*  Lab order materials                                                */
/* ------------------------------------------------------------------ */
const labMaterials = computed(() => [
    { name: t('dental_page.lab_zirconia'), color: '#F0F0F0' },
    { name: t('dental_page.lab_porcelain'), color: '#FFF8E1' },
    { name: t('dental_page.lab_gold'), color: '#C4A265' },
    { name: t('dental_page.lab_emax'), color: '#E8EAF6' },
    { name: t('dental_page.lab_composite'), color: '#FBE9E7' },
]);

const labStatuses = computed(() => [
    { label: t('dental_page.lab_ordered'), icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', active: true },
    { label: t('dental_page.lab_manufacturing'), icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', active: false },
    { label: t('dental_page.lab_ready'), icon: 'M5 13l4 4L19 7', active: false },
    { label: t('dental_page.lab_delivered'), icon: 'M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4', active: false },
]);

/* ------------------------------------------------------------------ */
/*  Periodontal chart features                                         */
/* ------------------------------------------------------------------ */
const perioFeatures = computed(() => [
    { icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', title: t('dental_page.perio_pocket'), desc: t('dental_page.perio_pocket_desc') },
    { icon: 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6', title: t('dental_page.perio_recession'), desc: t('dental_page.perio_recession_desc') },
    { icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', title: t('dental_page.perio_bleeding'), desc: t('dental_page.perio_bleeding_desc') },
    { icon: 'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z', title: t('dental_page.perio_plaque'), desc: t('dental_page.perio_plaque_desc') },
    { icon: 'M8 9l4-4 4 4m0 6l-4 4-4-4', title: t('dental_page.perio_mobility'), desc: t('dental_page.perio_mobility_desc') },
]);

/* ------------------------------------------------------------------ */
/*  Smart Notifications features                                       */
/* ------------------------------------------------------------------ */
const notificationFeatures = computed(() => [
    { icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', title: t('dental_page.notif_followup'), desc: t('dental_page.notif_followup_desc') },
    { icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', title: t('dental_page.notif_risk'), desc: t('dental_page.notif_risk_desc') },
    { icon: 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', title: t('dental_page.notif_completion'), desc: t('dental_page.notif_completion_desc') },
    { icon: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z', title: t('dental_page.notif_photos'), desc: t('dental_page.notif_photos_desc') },
]);
</script>

<template>
    <SeoHead
        :title="t('dental_page.page_title') || 'نظام إدارة عيادات الأسنان | برنامج عيادة أسنان احترافي — دكتوراتو'"
        :description="t('dental_page.hero_subtitle') || 'أفضل نظام إدارة عيادات الأسنان في السعودية ومصر والإمارات: مخطط أسنان تفاعلي FDI، خطط علاج، أشعة، مختبرات، تقويم، تركيبات، فوترة وتأمين. جرّب Doctorato مجاناً 14 يوم.'"
    />
    <MainLayout>
        <!-- ============================================================ -->
        <!-- HERO SECTION — Cinematic Premium Edition                      -->
        <!-- ============================================================ -->
        <section class="relative py-32 lg:py-40 overflow-hidden bg-[#070F1B]">
            <!-- Layer 1: Base radial gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#070F1B]"></div>

            <!-- Layer 2: Animated aurora orbs -->
            <div class="absolute inset-0">
                <div class="absolute top-[-15%] start-[10%] w-[600px] h-[600px] bg-[#1B4F72]/50 rounded-full blur-[140px] animate-aurora"></div>
                <div class="absolute bottom-[-20%] end-[5%] w-[700px] h-[700px] bg-[#C4A265]/25 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>
                <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[#2E86C1]/20 rounded-full blur-[120px] animate-aurora" style="animation-delay: -12s"></div>
            </div>

            <!-- Layer 3: Animated diagonal grid -->
            <div
                class="absolute inset-0 opacity-[0.06] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.6) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.6) 1px, transparent 1px); background-size: 60px 60px;"
            ></div>

            <!-- Layer 4: Hexagon medical pattern (DNA/molecule feel) -->
            <svg class="absolute inset-0 w-full h-full opacity-[0.035] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="hex-pattern" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#hex-pattern)"/>
            </svg>

            <!-- Layer 5: Noise / grain overlay -->
            <div class="absolute inset-0 opacity-[0.04] mix-blend-overlay pointer-events-none" style="background-image: url(&quot;data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E&quot;);"></div>

            <!-- Layer 6: Floating tooth icons with motion -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <svg class="absolute top-[12%] start-[8%] w-20 h-20 text-[#C4A265]/20 animate-float" viewBox="0 0 64 64" fill="currentColor">
                    <path d="M32 4c-6 0-11 2-14 6s-4 9-3 15c1 5 3 11 5 16 2 4 4 9 5 13 1 3 3 6 7 6s6-3 7-6c1-4 3-9 5-13 2-5 4-11 5-16 1-6 0-11-3-15S38 4 32 4z"/>
                </svg>
                <svg class="absolute top-[20%] end-[10%] w-14 h-14 text-white/15 animate-float" style="animation-delay: -2s" viewBox="0 0 64 64" fill="currentColor">
                    <path d="M32 4c-6 0-11 2-14 6s-4 9-3 15c1 5 3 11 5 16 2 4 4 9 5 13 1 3 3 6 7 6s6-3 7-6c1-4 3-9 5-13 2-5 4-11 5-16 1-6 0-11-3-15S38 4 32 4z"/>
                </svg>
                <svg class="absolute bottom-[18%] start-[18%] w-12 h-12 text-[#5DADE2]/20 animate-float" style="animation-delay: -4s" viewBox="0 0 64 64" fill="currentColor">
                    <path d="M32 4c-6 0-11 2-14 6s-4 9-3 15c1 5 3 11 5 16 2 4 4 9 5 13 1 3 3 6 7 6s6-3 7-6c1-4 3-9 5-13 2-5 4-11 5-16 1-6 0-11-3-15S38 4 32 4z"/>
                </svg>
                <svg class="absolute bottom-[15%] end-[20%] w-16 h-16 text-[#C4A265]/15 animate-float" style="animation-delay: -1s" viewBox="0 0 64 64" fill="currentColor">
                    <path d="M32 4c-6 0-11 2-14 6s-4 9-3 15c1 5 3 11 5 16 2 4 4 9 5 13 1 3 3 6 7 6s6-3 7-6c1-4 3-9 5-13 2-5 4-11 5-16 1-6 0-11-3-15S38 4 32 4z"/>
                </svg>

                <!-- Glowing accent dots -->
                <div class="absolute top-[30%] start-[25%] w-2 h-2 rounded-full bg-[#C4A265] shadow-[0_0_20px_#C4A265] animate-pulse-slow"></div>
                <div class="absolute top-[60%] end-[28%] w-1.5 h-1.5 rounded-full bg-[#5DADE2] shadow-[0_0_15px_#5DADE2] animate-pulse-slow" style="animation-delay: -3s"></div>
                <div class="absolute bottom-[35%] start-[35%] w-1.5 h-1.5 rounded-full bg-[#C4A265] shadow-[0_0_15px_#C4A265] animate-pulse-slow" style="animation-delay: -5s"></div>
                <div class="absolute top-[45%] end-[35%] w-2 h-2 rounded-full bg-[#27AE60] shadow-[0_0_20px_#27AE60] animate-pulse-slow" style="animation-delay: -2s"></div>
            </div>

            <!-- Top + bottom border glow -->
            <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>
            <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>

            <div class="relative container mx-auto px-4 text-center">
                <!-- Badge with glow -->
                <div class="relative inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-8 border border-white/10 animate-fade-up overflow-hidden group">
                    <!-- Shimmer sweep on hover -->
                    <div class="absolute inset-0 overflow-hidden rounded-full pointer-events-none">
                        <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                    <span class="relative flex w-2 h-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#C4A265]"></span>
                    </span>
                    <svg class="relative w-5 h-5 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <span class="relative text-sm font-semibold text-white tracking-wide">{{ t('dental_page.hero_badge') }}</span>
                </div>

                <!-- Title with gradient glow -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white via-white to-[#C4A265] bg-clip-text text-transparent drop-shadow-[0_0_30px_rgba(196,162,101,0.3)]">
                        {{ t('dental_page.hero_title') }}
                    </span>
                </h1>

                <!-- Decorative divider -->
                <div class="flex items-center justify-center gap-3 mb-6 animate-fade-up">
                    <div class="h-px w-12 bg-gradient-to-r from-transparent to-[#C4A265]"></div>
                    <div class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></div>
                    <div class="h-px w-12 bg-gradient-to-l from-transparent to-[#C4A265]"></div>
                </div>

                <p class="text-lg md:text-xl text-white/70 max-w-3xl mx-auto mb-10 animate-fade-up leading-relaxed">
                    {{ t('dental_page.hero_subtitle') }}
                </p>

                <!-- CTAs -->
                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link
                        href="/demo"
                        class="group relative px-8 py-4 bg-gradient-to-br from-[#C4A265] to-[#D4B87A] text-white font-bold rounded-full transition-all duration-500 hover:shadow-2xl hover:shadow-[#C4A265]/40 hover:-translate-y-1 overflow-hidden"
                    >
                        <!-- Shimmer sweep -->
                        <div class="absolute inset-0 overflow-hidden rounded-full pointer-events-none">
                            <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/30 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"></div>
                        </div>
                        <span class="relative flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ t('dental_page.hero_cta_demo') }}
                        </span>
                    </Link>
                    <a
                        href="#dental-chart"
                        class="group px-8 py-4 bg-white/[0.06] hover:bg-white/[0.12] text-white font-bold rounded-full backdrop-blur-md transition-all duration-500 border border-white/15 hover:border-white/30 hover:-translate-y-0.5"
                    >
                        <span class="flex items-center gap-2">
                            {{ t('dental_page.hero_cta_explore') }}
                            <svg class="w-5 h-5 transition-transform group-hover:translate-y-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </span>
                    </a>
                </div>

                <!-- Trust indicators row -->
                <div class="mt-12 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-white/40 text-xs animate-fade-up">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>FDI معايير دولية</span>
                    </div>
                    <div class="w-1 h-1 rounded-full bg-white/20"></div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>مخطط أسنان تفاعلي</span>
                    </div>
                    <div class="w-1 h-1 rounded-full bg-white/20"></div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>علاجات + أشعة + مختبرات</span>
                    </div>
                </div>
            </div>

            <!-- Bottom scroll cue -->
            <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-float pointer-events-none">
                <div class="w-px h-12 bg-gradient-to-b from-transparent to-[#C4A265]/40"></div>
                <svg class="w-4 h-4 text-[#C4A265]/60" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- INTERACTIVE DENTAL CHART SECTION                              -->
        <!-- ============================================================ -->
        <section id="dental-chart" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('dental_page.chart_title')"
                    :subtitle="t('dental_page.chart_subtitle')"
                />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center max-w-6xl mx-auto">
                    <!-- SVG Dental Chart -->
                    <div class="animate-fade-up">
                        <div class="relative bg-gradient-to-br from-primary/5 to-secondary/5 rounded-3xl p-6 sm:p-10 border border-gray-light/20">
                            <svg viewBox="0 0 620 340" class="w-full h-auto" fill="none">
                                <!-- Upper Jaw Label -->
                                <text x="310" y="25" text-anchor="middle" class="text-[11px] font-bold" fill="var(--color-primary)">{{ t('dental_page.chart_upper') }}</text>
                                <!-- Upper Jaw Arc -->
                                <path d="M40 70 Q310 15 580 70" stroke="var(--color-primary)" stroke-width="1.5" fill="none" stroke-dasharray="4 4" opacity="0.3" />

                                <!-- Upper Right Teeth (18-11) -->
                                <g v-for="(num, i) in upperRight" :key="'ur'+num">
                                    <rect
                                        :x="toothX(i) - 12" :y="upperY(i)" width="22" height="26" rx="5"
                                        fill="white" stroke="var(--color-primary)" stroke-width="1.5"
                                        class="hover:fill-[var(--color-secondary)]/20 transition-colors cursor-pointer"
                                    />
                                    <text :x="toothX(i) - 1" :y="upperY(i) + 16" class="text-[9px] fill-primary font-medium" text-anchor="middle">{{ num }}</text>
                                </g>
                                <!-- Upper Left Teeth (21-28) -->
                                <g v-for="(num, i) in upperLeft" :key="'ul'+num">
                                    <rect
                                        :x="toothX(i + 8) - 12" :y="upperY(i + 8)" width="22" height="26" rx="5"
                                        fill="white" stroke="var(--color-primary)" stroke-width="1.5"
                                        class="hover:fill-[var(--color-secondary)]/20 transition-colors cursor-pointer"
                                    />
                                    <text :x="toothX(i + 8) - 1" :y="upperY(i + 8) + 16" class="text-[9px] fill-primary font-medium" text-anchor="middle">{{ num }}</text>
                                </g>

                                <!-- Midline -->
                                <line x1="310" y1="55" x2="310" y2="285" stroke="var(--color-secondary)" stroke-width="1" stroke-dasharray="6 3" opacity="0.4" />

                                <!-- Center Label -->
                                <text x="310" y="170" text-anchor="middle" class="text-[11px] font-semibold" fill="var(--color-secondary)">{{ t('dental_page.chart_label_fdi') }}</text>

                                <!-- Lower Jaw Arc -->
                                <path d="M40 260 Q310 315 580 260" stroke="var(--color-primary)" stroke-width="1.5" fill="none" stroke-dasharray="4 4" opacity="0.3" />

                                <!-- Lower Left Teeth (31-38) -->
                                <g v-for="(num, i) in lowerLeft" :key="'ll'+num">
                                    <rect
                                        :x="toothX(i + 8) - 12" :y="lowerY(i + 8)" width="22" height="26" rx="5"
                                        fill="white" stroke="var(--color-primary)" stroke-width="1.5"
                                        class="hover:fill-[var(--color-secondary)]/20 transition-colors cursor-pointer"
                                    />
                                    <text :x="toothX(i + 8) - 1" :y="lowerY(i + 8) + 16" class="text-[9px] fill-primary font-medium" text-anchor="middle">{{ num }}</text>
                                </g>
                                <!-- Lower Right Teeth (48-41) -->
                                <g v-for="(num, i) in lowerRight" :key="'lr'+num">
                                    <rect
                                        :x="toothX(i) - 12" :y="lowerY(i)" width="22" height="26" rx="5"
                                        fill="white" stroke="var(--color-primary)" stroke-width="1.5"
                                        class="hover:fill-[var(--color-secondary)]/20 transition-colors cursor-pointer"
                                    />
                                    <text :x="toothX(i) - 1" :y="lowerY(i) + 16" class="text-[9px] fill-primary font-medium" text-anchor="middle">{{ num }}</text>
                                </g>

                                <!-- Lower Jaw Label -->
                                <text x="310" y="335" text-anchor="middle" class="text-[11px] font-bold" fill="var(--color-primary)">{{ t('dental_page.chart_lower') }}</text>
                            </svg>

                            <!-- Tooth Status Legend -->
                            <div class="flex flex-wrap justify-center gap-3 mt-6">
                                <div
                                    v-for="status in toothStatuses"
                                    :key="status.label"
                                    class="flex items-center gap-1.5 text-xs text-dark"
                                >
                                    <span class="w-3 h-3 rounded-full inline-block" :style="{ backgroundColor: status.color }"></span>
                                    {{ status.label }}
                                </div>
                            </div>

                            <!-- Interactive badge -->
                            <div class="absolute top-4 end-4 bg-secondary text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                                {{ t('dental_page.chart_interactive') }}
                            </div>
                        </div>
                    </div>

                    <!-- Chart Features -->
                    <div class="animate-fade-up">
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-full mb-4">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="text-sm font-semibold text-primary">{{ t('dental_page.chart_badge') }}</span>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-dark mb-4">{{ t('dental_page.chart_heading') }}</h3>
                        <p class="text-gray leading-relaxed mb-8">{{ t('dental_page.chart_description') }}</p>

                        <ul class="space-y-4">
                            <li
                                v-for="(feature, idx) in chartFeatures"
                                :key="idx"
                                class="flex items-start gap-3"
                            >
                                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <span class="text-sm text-dark leading-relaxed">{{ feature }}</span>
                            </li>
                        </ul>

                        <!-- Surface diagram -->
                        <div class="mt-8 p-4 bg-light-blue rounded-xl">
                            <p class="text-xs font-semibold text-primary mb-3 uppercase tracking-wide">{{ t('dental_page.chart_surfaces_label') }}</p>
                            <div class="flex justify-center gap-3">
                                <span v-for="surface in ['M', 'D', 'B', 'L', 'O']" :key="surface" class="w-10 h-10 rounded-lg bg-white border border-primary/20 flex items-center justify-center text-sm font-bold text-primary shadow-sm">
                                    {{ surface }}
                                </span>
                            </div>
                            <p class="text-[11px] text-gray text-center mt-2">{{ t('dental_page.chart_surfaces_hint') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- TREATMENT PLANS SECTION                                       -->
        <!-- ============================================================ -->
        <section class="py-20 bg-light-blue">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('dental_page.treatment_title')"
                    :subtitle="t('dental_page.treatment_subtitle')"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    <div
                        v-for="(item, idx) in treatmentFeatures"
                        :key="idx"
                        class="group p-6 bg-white rounded-2xl hover:shadow-lg transition-all duration-300 animate-fade-up border border-gray-light/10"
                    >
                        <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-4 group-hover:bg-secondary/20 transition-colors">
                            <svg class="w-7 h-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-dark mb-2">{{ item.title }}</h3>
                        <p class="text-sm text-gray leading-relaxed">{{ item.desc }}</p>
                    </div>
                </div>

                <!-- Status flow -->
                <div class="mt-16 max-w-4xl mx-auto animate-fade-up">
                    <p class="text-center text-sm font-semibold text-primary mb-6 uppercase tracking-wide">{{ t('dental_page.treatment_status_flow') }}</p>
                    <div class="flex flex-wrap items-center justify-center gap-3">
                        <span v-for="(status, idx) in ['draft', 'approved', 'in_progress', 'completed']" :key="status" class="flex items-center gap-2">
                            <span class="px-4 py-2 rounded-full text-sm font-semibold"
                                :class="idx === 3 ? 'bg-primary text-white' : 'bg-white text-primary border border-primary/20'">
                                {{ t('dental_page.treatment_status_' + status) }}
                            </span>
                            <svg v-if="idx < 3" class="w-5 h-5 text-primary/30 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- X-RAY & IMAGING SECTION                                       -->
        <!-- ============================================================ -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('dental_page.xray_title')"
                    :subtitle="t('dental_page.xray_subtitle')"
                />

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 max-w-4xl mx-auto mb-12">
                    <div
                        v-for="(xray, idx) in xrayTypes"
                        :key="idx"
                        class="group text-center p-5 bg-light-blue rounded-2xl hover:shadow-md transition-all duration-300 animate-fade-up"
                    >
                        <div class="w-14 h-14 mx-auto bg-primary/10 rounded-xl flex items-center justify-center mb-3 group-hover:bg-primary/20 transition-colors">
                            <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="xray.icon" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-dark">{{ xray.name }}</p>
                    </div>
                </div>

                <!-- Imaging features -->
                <div class="max-w-3xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-up">
                    <div class="p-5 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/10 text-center">
                        <svg class="w-8 h-8 text-primary mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                        <p class="text-sm font-semibold text-dark">{{ t('dental_page.xray_linked') }}</p>
                    </div>
                    <div class="p-5 rounded-xl bg-gradient-to-br from-secondary/5 to-transparent border border-secondary/10 text-center">
                        <svg class="w-8 h-8 text-secondary mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-sm font-semibold text-dark">{{ t('dental_page.xray_diagnosis') }}</p>
                    </div>
                    <div class="p-5 rounded-xl bg-gradient-to-br from-primary/5 to-transparent border border-primary/10 text-center">
                        <svg class="w-8 h-8 text-primary mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-semibold text-dark">{{ t('dental_page.xray_management') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- LAB ORDERS SECTION                                            -->
        <!-- ============================================================ -->
        <section class="py-20 bg-light-blue">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('dental_page.lab_title')"
                    :subtitle="t('dental_page.lab_subtitle')"
                />

                <div class="max-w-5xl mx-auto">
                    <!-- Status pipeline -->
                    <div class="mb-12 animate-fade-up">
                        <div class="flex flex-wrap items-center justify-center gap-4">
                            <div
                                v-for="(step, idx) in labStatuses"
                                :key="idx"
                                class="flex items-center gap-3"
                            >
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center transition-all"
                                        :class="step.active ? 'bg-primary text-white shadow-lg shadow-primary/25' : 'bg-white text-primary border border-primary/20'">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="step.icon" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-semibold" :class="step.active ? 'text-primary' : 'text-gray'">{{ step.label }}</span>
                                </div>
                                <svg v-if="idx < labStatuses.length - 1" class="w-6 h-6 text-primary/20 rtl:rotate-180 -mt-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Materials -->
                        <div class="bg-white rounded-2xl p-6 border border-gray-light/10 animate-fade-up">
                            <h4 class="text-lg font-bold text-dark mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                                {{ t('dental_page.lab_materials_title') }}
                            </h4>
                            <div class="space-y-3">
                                <div
                                    v-for="material in labMaterials"
                                    :key="material.name"
                                    class="flex items-center gap-3 p-3 rounded-lg hover:bg-light-blue transition-colors"
                                >
                                    <span class="w-8 h-8 rounded-full border-2 border-gray-light/30 shadow-inner" :style="{ backgroundColor: material.color }"></span>
                                    <span class="text-sm font-medium text-dark">{{ material.name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Lab features -->
                        <div class="bg-white rounded-2xl p-6 border border-gray-light/10 animate-fade-up">
                            <h4 class="text-lg font-bold text-dark mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                {{ t('dental_page.lab_features_title') }}
                            </h4>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-3 text-sm text-dark">
                                    <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ t('dental_page.lab_feature_crowns') }}
                                </li>
                                <li class="flex items-start gap-3 text-sm text-dark">
                                    <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ t('dental_page.lab_feature_tracking') }}
                                </li>
                                <li class="flex items-start gap-3 text-sm text-dark">
                                    <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ t('dental_page.lab_feature_shade') }}
                                </li>
                                <li class="flex items-start gap-3 text-sm text-dark">
                                    <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ t('dental_page.lab_feature_profitability') }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- PERIODONTAL CHART SECTION                                     -->
        <!-- ============================================================ -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('dental_page.perio_title')"
                    :subtitle="t('dental_page.perio_subtitle')"
                />

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center max-w-6xl mx-auto">
                    <!-- Perio features -->
                    <div class="space-y-6 animate-fade-up">
                        <div
                            v-for="(item, idx) in perioFeatures"
                            :key="idx"
                            class="flex items-start gap-4 p-4 rounded-xl hover:bg-light-blue transition-colors"
                        >
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-dark mb-1">{{ item.title }}</h4>
                                <p class="text-sm text-gray leading-relaxed">{{ item.desc }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Perio Chart Visual -->
                    <div class="animate-fade-up">
                        <div class="bg-gradient-to-br from-primary/5 to-secondary/5 rounded-3xl p-6 sm:p-8 border border-gray-light/20">
                            <svg viewBox="0 0 400 250" class="w-full h-auto">
                                <!-- Pocket depth visualization -->
                                <text x="200" y="22" text-anchor="middle" class="text-[11px] font-bold" fill="var(--color-primary)">{{ t('dental_page.perio_pocket_depth_visual') }}</text>

                                <!-- Sample teeth with pocket depths -->
                                <g v-for="(tooth, i) in 6" :key="'perio-'+i">
                                    <!-- Tooth body -->
                                    <rect
                                        :x="45 + i * 55" y="80" width="35" height="55" rx="6"
                                        fill="white" stroke="var(--color-primary)" stroke-width="1.5"
                                    />
                                    <!-- Gum line -->
                                    <line
                                        :x1="42 + i * 55" y1="85"
                                        :x2="83 + i * 55" y2="85"
                                        stroke="#EF4444" stroke-width="2" opacity="0.6"
                                    />
                                    <!-- Pocket depth bars (6 per tooth, showing 3 buccal) -->
                                    <rect
                                        v-for="j in 3" :key="'pd-'+i+'-'+j"
                                        :x="45 + i * 55 + (j - 1) * 12" y="60"
                                        width="8" :height="10 + Math.random() * 20" rx="2"
                                        :fill="(10 + Math.random() * 20) > 20 ? '#EF4444' : '#10B981'" opacity="0.6"
                                    />
                                    <!-- Tooth number -->
                                    <text :x="62 + i * 55" y="115" text-anchor="middle" class="text-[9px] font-medium" fill="var(--color-primary)">{{ [31, 32, 33, 34, 35, 36][i] }}</text>
                                </g>

                                <!-- Scale reference -->
                                <g>
                                    <text x="15" y="65" class="text-[8px]" fill="var(--color-gray)">mm</text>
                                    <line x1="30" y1="60" x2="30" y2="85" stroke="var(--color-gray)" stroke-width="0.5" />
                                    <text x="10" y="63" class="text-[7px]" fill="var(--color-gray)">0</text>
                                    <text x="10" y="76" class="text-[7px]" fill="var(--color-gray)">3</text>
                                    <text x="10" y="88" class="text-[7px]" fill="var(--color-gray)">6</text>
                                </g>

                                <!-- Legend -->
                                <g transform="translate(80, 170)">
                                    <rect x="0" y="0" width="12" height="12" rx="2" fill="#10B981" opacity="0.6" />
                                    <text x="18" y="10" class="text-[9px]" fill="var(--color-dark)">{{ t('dental_page.perio_normal') }}</text>
                                    <rect x="120" y="0" width="12" height="12" rx="2" fill="#EF4444" opacity="0.6" />
                                    <text x="138" y="10" class="text-[9px]" fill="var(--color-dark)">{{ t('dental_page.perio_deep') }}</text>
                                </g>

                                <!-- BOP dots -->
                                <g transform="translate(80, 200)">
                                    <circle cx="6" cy="6" r="4" fill="#EF4444" />
                                    <text x="18" y="10" class="text-[9px]" fill="var(--color-dark)">{{ t('dental_page.perio_bop') }}</text>
                                    <circle cx="126" cy="6" r="4" fill="var(--color-secondary)" />
                                    <text x="138" y="10" class="text-[9px]" fill="var(--color-dark)">{{ t('dental_page.perio_plaque_indicator') }}</text>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- SMART NOTIFICATIONS SECTION                                   -->
        <!-- ============================================================ -->
        <section class="py-20 bg-gradient-to-br from-primary/[0.03] to-secondary/[0.03]">
            <div class="container mx-auto px-4">
                <SectionTitle
                    :title="t('dental_page.notif_title')"
                    :subtitle="t('dental_page.notif_subtitle')"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <div
                        v-for="(item, idx) in notificationFeatures"
                        :key="idx"
                        class="group flex items-start gap-4 p-6 bg-white rounded-2xl border border-gray-light/10 hover:shadow-lg transition-all duration-300 animate-fade-up"
                    >
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 transition-colors"
                            :class="idx % 2 === 0 ? 'bg-primary/10 group-hover:bg-primary/20' : 'bg-secondary/10 group-hover:bg-secondary/20'">
                            <svg class="w-6 h-6" :class="idx % 2 === 0 ? 'text-primary' : 'text-secondary'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-dark mb-1">{{ item.title }}</h4>
                            <p class="text-sm text-gray leading-relaxed">{{ item.desc }}</p>
                        </div>
                    </div>
                </div>

                <!-- Before/After Photo Slider Illustration -->
                <div class="mt-16 max-w-md mx-auto animate-fade-up">
                    <div class="bg-white rounded-2xl p-6 border border-gray-light/10 shadow-sm">
                        <p class="text-sm font-semibold text-center text-dark mb-4">{{ t('dental_page.notif_photo_compare') }}</p>
                        <div class="relative h-40 rounded-xl overflow-hidden bg-gradient-to-r from-primary/10 to-secondary/10">
                            <!-- Before side -->
                            <div class="absolute inset-y-0 start-0 w-1/2 bg-primary/10 flex items-center justify-center border-e-2 border-white">
                                <span class="text-xs font-bold text-primary bg-white/80 px-3 py-1 rounded-full">{{ t('dental_page.notif_before') }}</span>
                            </div>
                            <!-- After side -->
                            <div class="absolute inset-y-0 end-0 w-1/2 bg-secondary/10 flex items-center justify-center">
                                <span class="text-xs font-bold text-secondary bg-white/80 px-3 py-1 rounded-full">{{ t('dental_page.notif_after') }}</span>
                            </div>
                            <!-- Slider handle -->
                            <div class="absolute top-1/2 start-1/2 -translate-x-1/2 -translate-y-1/2 w-8 h-8 bg-white rounded-full shadow-lg border-2 border-secondary flex items-center justify-center z-10">
                                <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ============================================================ -->
        <!-- CTA SECTION                                                   -->
        <!-- ============================================================ -->
        <section class="py-20 bg-gradient-to-r from-primary to-primary-dark">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6 animate-fade-up">
                    {{ t('dental_page.cta_title') }}
                </h2>
                <p class="text-xl text-white/80 max-w-2xl mx-auto mb-8 animate-fade-up">
                    {{ t('dental_page.cta_subtitle') }}
                </p>
                <div class="flex flex-wrap justify-center gap-4 animate-fade-up">
                    <Link
                        href="/demo"
                        class="px-8 py-4 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25 hover:-translate-y-0.5"
                    >
                        {{ t('dental_page.cta_request_demo') }}
                    </Link>
                    <Link
                        href="/pricing"
                        class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm transition-all duration-300 border border-white/20"
                    >
                        {{ t('dental_page.cta_view_pricing') }}
                    </Link>
                </div>
            </div>
        </section>
            <RelatedModules current="dental" />
    </MainLayout>
</template>
