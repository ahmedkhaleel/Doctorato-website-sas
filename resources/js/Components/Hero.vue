<script setup>
import { ref, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { gsap } from 'gsap';
import AnimatedCounter from '@/Components/AnimatedCounter.vue';

const { t } = useI18n();

const heroRef = ref(null);
const headlineRef = ref(null);
const subheadlineRef = ref(null);
const ctaRef = ref(null);
const statsRef = ref(null);
const brandRef = ref(null);
const mockupRef = ref(null);

const stats = [
    {
        target: 800, suffix: '+', labelKey: 'hero.stats.features',
        accent: '#C4A265', accentSoft: 'rgba(196,162,101,0.18)',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    },
    {
        target: 6, suffix: '', labelKey: 'hero.stats.portals',
        accent: '#5DADE2', accentSoft: 'rgba(93,173,226,0.18)',
        icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
    },
    {
        target: 3000, suffix: '+', labelKey: 'hero.stats.translations',
        accent: '#2E86C1', accentSoft: 'rgba(46,134,193,0.18)',
        icon: 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129',
    },
    {
        target: 80, suffix: '+', labelKey: 'hero.stats.permissions',
        accent: '#27AE60', accentSoft: 'rgba(39,174,96,0.18)',
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    },
    {
        target: 39, suffix: '', labelKey: 'hero.stats.webmaster_pages',
        accent: '#D4B87A', accentSoft: 'rgba(212,184,122,0.18)',
        icon: 'M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2',
    },
];

onMounted(() => {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    tl.fromTo(
        brandRef.value,
        { opacity: 0, y: -30 },
        { opacity: 1, y: 0, duration: 0.8 }
    )
    .fromTo(
        headlineRef.value,
        { opacity: 0, y: 40, clipPath: 'inset(0 0 100% 0)' },
        { opacity: 1, y: 0, clipPath: 'inset(0 0 0% 0)', duration: 1 },
        '-=0.3'
    )
    .fromTo(
        subheadlineRef.value,
        { opacity: 0, y: 30 },
        { opacity: 1, y: 0, duration: 0.8 },
        '-=0.5'
    )
    .fromTo(
        ctaRef.value,
        { opacity: 0, y: 20 },
        { opacity: 1, y: 0, duration: 0.6 },
        '-=0.3'
    )
    .fromTo(
        mockupRef.value,
        { opacity: 0, x: 60, rotateY: -15 },
        { opacity: 1, x: 0, rotateY: 0, duration: 1.2, ease: 'power2.out' },
        '-=1.0'
    )
    .fromTo(
        statsRef.value,
        { opacity: 0, y: 30 },
        { opacity: 1, y: 0, duration: 0.8 },
        '-=0.4'
    );
});
</script>

<template>
    <section
        ref="heroRef"
        class="relative min-h-screen flex flex-col overflow-hidden bg-[#0A1628]"
    >
        <!-- Layer 1: Gradient Mesh Background -->
        <div class="absolute inset-0 hero-gradient-mesh" />

        <!-- Layer 2: Static rectangular grid -->
        <div class="absolute inset-0 hero-grid-overlay opacity-[0.04] pointer-events-none" />

        <!-- Layer 3: Animated diagonal grid (drifts slowly) -->
        <div
            class="absolute inset-0 opacity-[0.05] pointer-events-none animate-grid-drift"
            style="background-image: linear-gradient(45deg, rgba(196,162,101,0.5) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.5) 1px, transparent 1px); background-size: 80px 80px;"
        />

        <!-- Layer 4: Hexagon medical pattern -->
        <svg class="absolute inset-0 w-full h-full opacity-[0.035] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="hero-hex-pattern" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                    <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#hero-hex-pattern)"/>
        </svg>

        <!-- Layer 5: Noise / grain overlay -->
        <div class="absolute inset-0 opacity-[0.04] mix-blend-overlay pointer-events-none" style="background-image: url(&quot;data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='1'/%3E%3C/svg%3E&quot;);"></div>

        <!-- Layer 6: Animated Gradient Orbs (auroras) -->
        <div class="absolute top-[-10%] start-[-5%] w-[500px] h-[500px] bg-[#1B4F72]/40 rounded-full blur-[120px] pointer-events-none animate-aurora" />
        <div class="absolute bottom-[10%] end-[-10%] w-[600px] h-[600px] bg-[#C4A265]/18 rounded-full blur-[150px] pointer-events-none animate-aurora" style="animation-delay: -6s" />
        <div class="absolute top-[40%] start-[30%] w-[400px] h-[400px] bg-[#2E86C1]/20 rounded-full blur-[100px] pointer-events-none animate-aurora" style="animation-delay: -12s" />

        <!-- Layer 7: Glowing accent dots -->
        <div class="absolute top-[18%] end-[12%] w-2 h-2 rounded-full bg-[#C4A265] shadow-[0_0_20px_#C4A265] animate-pulse-slow opacity-60 pointer-events-none"></div>
        <div class="absolute top-[35%] start-[10%] w-1.5 h-1.5 rounded-full bg-[#5DADE2] shadow-[0_0_15px_#5DADE2] animate-pulse-slow opacity-50 pointer-events-none" style="animation-delay: -3s"></div>
        <div class="absolute bottom-[22%] end-[20%] w-1.5 h-1.5 rounded-full bg-[#27AE60] shadow-[0_0_15px_#27AE60] animate-pulse-slow opacity-50 pointer-events-none" style="animation-delay: -5s"></div>

        <!-- Floating Medical Icons -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <!-- Stethoscope -->
            <svg class="hero-floating-icon absolute top-[15%] start-[8%] w-8 h-8 text-white/[0.04]" fill="currentColor" viewBox="0 0 24 24" style="animation-delay: 0s;">
                <path d="M19 8c0-3.87-3.13-7-7-7S5 4.13 5 8v5c0 2.76 2.24 5 5 5h1v-2h-1c-1.66 0-3-1.34-3-3V8c0-2.76 2.24-5 5-5s5 2.24 5 5v5c0 2.21-1.79 4-4 4h-1v2h1c3.31 0 6-2.69 6-6V8z"/>
                <circle cx="19" cy="16" r="2"/>
            </svg>
            <!-- Tooth -->
            <svg class="hero-floating-icon absolute top-[25%] end-[15%] w-7 h-7 text-white/[0.04]" fill="currentColor" viewBox="0 0 24 24" style="animation-delay: 2s;">
                <path d="M12 2C9.24 2 7 4.24 7 7c0 1.1.45 2.1 1.17 2.83L12 22l3.83-12.17A3.98 3.98 0 0017 7c0-2.76-2.24-5-5-5z"/>
            </svg>
            <!-- Calendar -->
            <svg class="hero-floating-icon absolute top-[55%] start-[5%] w-9 h-9 text-white/[0.03]" fill="currentColor" viewBox="0 0 24 24" style="animation-delay: 4s;">
                <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM9 10H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2z"/>
            </svg>
            <!-- Chart -->
            <svg class="hero-floating-icon absolute bottom-[30%] end-[8%] w-8 h-8 text-white/[0.04]" fill="currentColor" viewBox="0 0 24 24" style="animation-delay: 1.5s;">
                <path d="M3 13h2v8H3v-8zm4-6h2v14H7V7zm4-4h2v18h-2V3zm4 7h2v11h-2V10zm4-3h2v14h-2V7z"/>
            </svg>
            <!-- Heart/Pulse -->
            <svg class="hero-floating-icon absolute top-[70%] start-[25%] w-7 h-7 text-white/[0.03]" fill="currentColor" viewBox="0 0 24 24" style="animation-delay: 3s;">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
            </svg>
            <!-- Pill -->
            <svg class="hero-floating-icon absolute top-[40%] end-[25%] w-6 h-6 text-white/[0.03]" fill="currentColor" viewBox="0 0 24 24" style="animation-delay: 5s;">
                <path d="M4.22 11.29l5.07-5.07c1.95-1.95 5.12-1.95 7.07 0s1.95 5.12 0 7.07l-5.07 5.07c-1.95 1.95-5.12 1.95-7.07 0s-1.95-5.12 0-7.07zm1.41 1.41c-1.17 1.17-1.17 3.07 0 4.24 1.17 1.17 3.07 1.17 4.24 0l2.12-2.12-4.24-4.24-2.12 2.12z"/>
            </svg>
        </div>

        <!-- Floating Particles -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div
                v-for="i in 20"
                :key="i"
                class="hero-particle"
                :style="{
                    left: `${Math.random() * 100}%`,
                    top: `${Math.random() * 100}%`,
                    animationDelay: `${Math.random() * 8}s`,
                    animationDuration: `${6 + Math.random() * 6}s`,
                    width: `${1.5 + Math.random() * 2}px`,
                    height: `${1.5 + Math.random() * 2}px`,
                }"
            />
        </div>

        <!-- Main Content: Split Layout -->
        <div class="relative z-10 flex-1 flex items-center w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 md:pt-32 pb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16 items-center w-full">

                <!-- Left Side: Text Content -->
                <div class="text-start">
                    <!-- Brand Badge -->
                    <div ref="brandRef" class="mb-8" style="opacity: 0">
                        <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white/[0.05] backdrop-blur-sm border border-white/[0.08]">
                            <span class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#C4A265]"></span>
                            </span>
                            <span class="text-sm font-medium text-white/70 tracking-wide">{{ $t('hero.brand') }}</span>
                            <!-- Golden accent line -->
                            <div class="h-px w-8 bg-gradient-to-r from-[#C4A265]/60 to-transparent" />
                        </div>
                    </div>

                    <!-- Main Headline -->
                    <h1
                        ref="headlineRef"
                        class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-white leading-[1.1] mb-6"
                        style="opacity: 0"
                    >
                        <span class="hero-headline-gradient">{{ $t('hero.headline') }}</span>
                    </h1>

                    <!-- Subheadline -->
                    <p
                        ref="subheadlineRef"
                        class="text-base sm:text-lg text-white/50 max-w-xl leading-relaxed mb-10"
                        style="opacity: 0"
                    >
                        {{ $t('hero.subheadline') }}
                    </p>

                    <!-- CTA Buttons -->
                    <div ref="ctaRef" class="flex flex-col sm:flex-row items-start gap-4" style="opacity: 0">
                        <!-- Primary CTA with animated gradient border -->
                        <Link
                            href="/demo"
                            class="hero-cta-primary group relative inline-flex items-center gap-2 px-7 py-3.5 text-white text-base font-bold rounded-xl transition-all duration-300 hover:-translate-y-0.5"
                        >
                            <span class="relative z-10 flex items-center gap-2">
                                {{ $t('hero.cta_primary') }}
                                <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 rtl:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </span>
                            <!-- Shine effect -->
                            <div class="hero-cta-shine absolute inset-0 rounded-xl overflow-hidden pointer-events-none" />
                        </Link>

                        <a
                            href="#features"
                            class="group inline-flex items-center gap-2 px-7 py-3.5 text-white/70 text-base font-semibold rounded-xl border border-white/10 transition-all duration-300 hover:bg-white/[0.05] hover:border-white/20 hover:text-white hover:-translate-y-0.5"
                        >
                            {{ $t('hero.cta_secondary') }}
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-y-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Right Side: Floating Dashboard Mockup -->
                <div ref="mockupRef" class="hidden md:flex justify-center items-center" style="opacity: 0">
                    <div class="hero-mockup-wrapper relative" style="perspective: 1200px;">
                        <div class="hero-mockup-float relative w-full max-w-[440px] rounded-2xl bg-white/[0.05] backdrop-blur-xl border border-white/[0.1] shadow-2xl shadow-black/30 overflow-hidden"
                             style="transform: rotateY(-5deg) rotateX(3deg);">

                            <!-- Top Bar -->
                            <div class="flex items-center justify-between px-5 py-3 border-b border-white/[0.08]">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-[#C4A265] to-[#1B4F72] flex items-center justify-center">
                                        <span class="text-white text-[10px] font-bold">D</span>
                                    </div>
                                    <span class="text-white/80 text-sm font-semibold">Doctorato</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-white/20" />
                                    <div class="w-2 h-2 rounded-full bg-white/20" />
                                    <div class="w-2 h-2 rounded-full bg-[#C4A265]/60" />
                                </div>
                            </div>

                            <!-- Dashboard Content -->
                            <div class="p-4 space-y-4">

                                <!-- Mini Stat Cards Row -->
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="hero-stat-card rounded-xl bg-white/[0.06] border border-white/[0.08] p-3 transition-all duration-300">
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <div class="w-5 h-5 rounded-md bg-[#1B4F72]/40 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-[#5DADE2]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-white text-base font-bold leading-none">24</div>
                                        <div class="text-white/30 text-[10px] mt-1">Patients</div>
                                    </div>
                                    <div class="hero-stat-card rounded-xl bg-white/[0.06] border border-white/[0.08] p-3 transition-all duration-300">
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <div class="w-5 h-5 rounded-md bg-[#C4A265]/20 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-[#C4A265]" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-white text-base font-bold leading-none">18</div>
                                        <div class="text-white/30 text-[10px] mt-1">Appointments</div>
                                    </div>
                                    <div class="hero-stat-card rounded-xl bg-white/[0.06] border border-white/[0.08] p-3 transition-all duration-300">
                                        <div class="flex items-center gap-1.5 mb-1.5">
                                            <div class="w-5 h-5 rounded-md bg-emerald-500/20 flex items-center justify-center">
                                                <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="text-emerald-400 text-base font-bold leading-none flex items-center gap-0.5">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                            12%
                                        </div>
                                        <div class="text-white/30 text-[10px] mt-1">Revenue</div>
                                    </div>
                                </div>

                                <!-- Mini Bar Chart -->
                                <div class="rounded-xl bg-white/[0.04] border border-white/[0.06] p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-white/50 text-[11px] font-medium">Weekly Overview</span>
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center gap-1">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#1B4F72]" />
                                                <span class="text-white/30 text-[9px]">Visits</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <div class="w-1.5 h-1.5 rounded-full bg-[#C4A265]" />
                                                <span class="text-white/30 text-[9px]">New</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-end gap-2 h-20">
                                        <div class="flex-1 flex flex-col items-center gap-1">
                                            <div class="w-full rounded-t-sm bg-[#1B4F72]/70 hero-bar" style="height: 55%;" />
                                            <span class="text-white/20 text-[8px]">M</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1">
                                            <div class="w-full rounded-t-sm bg-[#C4A265]/70 hero-bar" style="height: 80%;" />
                                            <span class="text-white/20 text-[8px]">T</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1">
                                            <div class="w-full rounded-t-sm bg-[#1B4F72]/70 hero-bar" style="height: 45%;" />
                                            <span class="text-white/20 text-[8px]">W</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1">
                                            <div class="w-full rounded-t-sm bg-[#C4A265]/70 hero-bar" style="height: 95%;" />
                                            <span class="text-white/20 text-[8px]">T</span>
                                        </div>
                                        <div class="flex-1 flex flex-col items-center gap-1">
                                            <div class="w-full rounded-t-sm bg-[#1B4F72]/70 hero-bar" style="height: 65%;" />
                                            <span class="text-white/20 text-[8px]">F</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Mini Appointment List -->
                                <div class="rounded-xl bg-white/[0.04] border border-white/[0.06] p-3 space-y-2.5">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-white/50 text-[11px] font-medium">Upcoming</span>
                                        <span class="text-[#C4A265]/60 text-[10px]">View all</span>
                                    </div>
                                    <!-- Row 1 -->
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-400 shrink-0" />
                                        <div class="flex-1 min-w-0">
                                            <div class="h-2 bg-white/10 rounded-full w-3/4" />
                                        </div>
                                        <div class="h-2 bg-white/[0.06] rounded-full w-12" />
                                    </div>
                                    <!-- Row 2 -->
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-[#C4A265] shrink-0" />
                                        <div class="flex-1 min-w-0">
                                            <div class="h-2 bg-white/10 rounded-full w-2/3" />
                                        </div>
                                        <div class="h-2 bg-white/[0.06] rounded-full w-10" />
                                    </div>
                                    <!-- Row 3 -->
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-[#5DADE2] shrink-0" />
                                        <div class="flex-1 min-w-0">
                                            <div class="h-2 bg-white/10 rounded-full w-4/5" />
                                        </div>
                                        <div class="h-2 bg-white/[0.06] rounded-full w-14" />
                                    </div>
                                </div>
                            </div>

                            <!-- Glow effect behind mockup -->
                            <div class="absolute -inset-4 bg-gradient-to-br from-[#1B4F72]/20 via-transparent to-[#C4A265]/10 rounded-3xl blur-xl -z-10 pointer-events-none" />
                        </div>

                        <!-- Floating accent cards around mockup -->
                        <div class="absolute -top-4 -end-4 w-20 h-12 rounded-lg bg-white/[0.05] backdrop-blur-sm border border-white/[0.08] flex items-center justify-center hero-mockup-float-accent" style="animation-delay: -2s;">
                            <svg class="w-5 h-5 text-[#C4A265]/60" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="absolute -bottom-3 -start-6 w-24 h-10 rounded-lg bg-white/[0.05] backdrop-blur-sm border border-white/[0.08] flex items-center justify-center gap-1.5 hero-mockup-float-accent" style="animation-delay: -4s;">
                            <div class="w-4 h-4 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                            <span class="text-white/40 text-[10px] font-medium">Synced</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div
            ref="statsRef"
            class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24"
            style="opacity: 0"
        >
            <!-- Golden accent line -->
            <div class="w-full h-px bg-gradient-to-r from-transparent via-[#C4A265]/20 to-transparent mb-8" />

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                <div
                    v-for="(stat, index) in stats"
                    :key="index"
                    class="hero-stat-glass group relative"
                    :style="{ '--accent': stat.accent }"
                >
                    <!-- Animated conic-gradient border on hover -->
                    <div class="absolute -inset-px rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 overflow-hidden">
                        <div class="absolute inset-[-100%] animate-spin-slow" :style="{ background: `conic-gradient(from 0deg, transparent 0deg, ${stat.accent} 60deg, transparent 120deg, transparent 360deg)` }"></div>
                    </div>

                    <!-- Card body -->
                    <div class="relative h-full rounded-2xl bg-[#0A1628]/85 backdrop-blur-md border border-white/[0.07] p-5 transition-all duration-500 group-hover:-translate-y-1 group-hover:shadow-2xl overflow-hidden">
                        <!-- Radial hover glow -->
                        <div
                            class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                            :style="{ background: `radial-gradient(circle at 50% 0%, ${stat.accentSoft} 0%, transparent 70%)` }"
                        ></div>

                        <!-- Top accent line that grows on hover -->
                        <div
                            class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] transition-all duration-700"
                            :style="{ background: `linear-gradient(90deg, transparent, ${stat.accent}, transparent)`, boxShadow: `0 0 12px ${stat.accent}` }"
                        ></div>

                        <!-- Glowing accent dot -->
                        <div
                            class="absolute top-3 end-3 w-1.5 h-1.5 rounded-full opacity-60 group-hover:opacity-100 group-hover:scale-150 transition-all duration-500"
                            :style="{ background: stat.accent, boxShadow: `0 0 12px ${stat.accent}` }"
                        ></div>

                        <!-- Subtle dotted corner -->
                        <svg class="absolute -bottom-2 -end-2 w-12 h-12 text-white/[0.04] pointer-events-none" viewBox="0 0 48 48" fill="none">
                            <pattern :id="`hero-dots-${index}`" x="0" y="0" width="6" height="6" patternUnits="userSpaceOnUse">
                                <circle cx="1.5" cy="1.5" r="1" fill="currentColor" />
                            </pattern>
                            <rect width="48" height="48" :fill="`url(#hero-dots-${index})`" />
                        </svg>

                        <div class="relative">
                            <!-- Icon -->
                            <div
                                class="w-9 h-9 rounded-lg flex items-center justify-center mb-3 transition-all duration-500 group-hover:scale-110 group-hover:rotate-6"
                                :style="{ background: stat.accentSoft, color: stat.accent, boxShadow: `inset 0 0 0 1px ${stat.accent}30` }"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="stat.icon" />
                                </svg>
                            </div>

                            <!-- Number with gradient -->
                            <div class="text-3xl sm:text-4xl font-black leading-none mb-1.5 bg-gradient-to-br from-white to-white/70 bg-clip-text text-transparent group-hover:from-white group-hover:to-[var(--accent)] transition-all duration-500">
                                <AnimatedCounter
                                    :target="stat.target"
                                    :suffix="stat.suffix"
                                    :duration="2000"
                                />
                            </div>

                            <!-- Label -->
                            <div class="text-[11px] sm:text-xs font-bold uppercase tracking-widest transition-colors" :style="{ color: stat.accent }">
                                {{ $t(stat.labelKey) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom edge line -->
        <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>
    </section>
</template>

<style scoped>
/* Gradient Mesh Background */
.hero-gradient-mesh {
    background:
        radial-gradient(ellipse at 20% 50%, rgba(27, 79, 114, 0.4) 0%, transparent 50%),
        radial-gradient(ellipse at 80% 20%, rgba(27, 79, 114, 0.2) 0%, transparent 40%),
        radial-gradient(ellipse at 60% 80%, rgba(196, 162, 101, 0.08) 0%, transparent 40%),
        linear-gradient(180deg, #0A1628 0%, #0F1D2F 50%, #0A1628 100%);
}

/* Grid Overlay */
.hero-grid-overlay {
    background-image:
        linear-gradient(rgba(255, 255, 255, 1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 1) 1px, transparent 1px);
    background-size: 60px 60px;
}

/* Headline gradient text */
.hero-headline-gradient {
    background: linear-gradient(135deg, #ffffff 0%, #ffffff 50%, rgba(196, 162, 101, 0.8) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Primary CTA Button */
.hero-cta-primary {
    background: linear-gradient(135deg, #C4A265 0%, #A8893E 100%);
    box-shadow:
        0 0 0 1px rgba(196, 162, 101, 0.3),
        0 4px 16px rgba(196, 162, 101, 0.2),
        0 1px 2px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
}

.hero-cta-primary::before {
    content: '';
    position: absolute;
    inset: -1px;
    border-radius: 0.75rem;
    padding: 1px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(196, 162, 101, 0.1), rgba(255, 255, 255, 0.2));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    pointer-events: none;
}

.hero-cta-primary:hover {
    box-shadow:
        0 0 0 1px rgba(196, 162, 101, 0.5),
        0 8px 32px rgba(196, 162, 101, 0.3),
        0 2px 4px rgba(0, 0, 0, 0.2);
}

/* CTA Shine Effect */
.hero-cta-shine::after {
    content: '';
    position: absolute;
    top: -50%;
    left: -75%;
    width: 50%;
    height: 200%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.15),
        transparent
    );
    transform: skewX(-20deg);
    transition: none;
}

.hero-cta-primary:hover .hero-cta-shine::after {
    animation: heroShine 0.6s ease-out;
}

@keyframes heroShine {
    0% {
        left: -75%;
    }
    100% {
        left: 125%;
    }
}

/* Floating Particles */
.hero-particle {
    position: absolute;
    background: rgba(196, 162, 101, 0.4);
    border-radius: 50%;
    animation: heroParticleFloat linear infinite;
}

@keyframes heroParticleFloat {
    0% {
        transform: translateY(0) scale(1);
        opacity: 0;
    }
    10% {
        opacity: 0.8;
    }
    90% {
        opacity: 0.8;
    }
    100% {
        transform: translateY(-180px) scale(0.3);
        opacity: 0;
    }
}

/* Floating Medical Icons */
.hero-floating-icon {
    animation: heroIconDrift 12s ease-in-out infinite;
}

@keyframes heroIconDrift {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    25% {
        transform: translateY(-15px) rotate(3deg);
    }
    50% {
        transform: translateY(-8px) rotate(-2deg);
    }
    75% {
        transform: translateY(-20px) rotate(1deg);
    }
}

/* Dashboard Mockup Float Animation */
.hero-mockup-float {
    animation: heroMockupFloat 6s ease-in-out infinite;
}

@keyframes heroMockupFloat {
    0%, 100% {
        transform: rotateY(-5deg) rotateX(3deg) translateY(0);
    }
    50% {
        transform: rotateY(-5deg) rotateX(3deg) translateY(-12px);
    }
}

/* Floating accent cards around mockup */
.hero-mockup-float-accent {
    animation: heroAccentFloat 5s ease-in-out infinite;
}

@keyframes heroAccentFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-8px);
    }
}

/* Stat card hover glow in mockup */
.hero-stat-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.15);
    box-shadow: 0 0 20px rgba(196, 162, 101, 0.05);
}

/* Stats glass cards bottom */
.hero-stat-glass::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 0.75rem;
    opacity: 0;
    background: radial-gradient(circle at 50% 0%, rgba(196, 162, 101, 0.1), transparent 70%);
    transition: opacity 0.5s;
    pointer-events: none;
}

.hero-stat-glass:hover::before {
    opacity: 1;
}

/* Bar chart animation */
.hero-bar {
    animation: heroBarGrow 1.5s ease-out forwards;
    transform-origin: bottom;
}

@keyframes heroBarGrow {
    from {
        transform: scaleY(0);
    }
    to {
        transform: scaleY(1);
    }
}

/* Orb animations */
.hero-orb-1 {
    animation: heroOrb1 20s ease-in-out infinite;
}

.hero-orb-2 {
    animation: heroOrb2 25s ease-in-out infinite;
}

.hero-orb-3 {
    animation: heroOrb3 18s ease-in-out infinite;
}

@keyframes heroOrb1 {
    0%, 100% { transform: translate(0, 0); }
    33% { transform: translate(30px, 20px); }
    66% { transform: translate(-20px, 10px); }
}

@keyframes heroOrb2 {
    0%, 100% { transform: translate(0, 0); }
    33% { transform: translate(-40px, -20px); }
    66% { transform: translate(20px, -30px); }
}

@keyframes heroOrb3 {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(25px, -15px); }
}
</style>
