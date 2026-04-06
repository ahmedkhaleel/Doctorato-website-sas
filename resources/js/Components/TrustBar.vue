<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { useIntersectionObserver } from '@vueuse/core';

const { t } = useI18n();

const sectionRef = ref(null);
const isVisible = ref(false);

const { stop } = useIntersectionObserver(sectionRef, ([{ isIntersecting }]) => {
    if (isIntersecting) {
        isVisible.value = true;
        stop();
    }
}, { threshold: 0.2 });

const trustBadges = [
    {
        labelKey: 'trust.hipaa',
        icon: 'shield',
        color: '#1B4F72',
    },
    {
        labelKey: 'trust.ssl',
        icon: 'lock',
        color: '#C4A265',
    },
    {
        labelKey: 'trust.cloud',
        icon: 'cloud',
        color: '#2471A3',
    },
    {
        labelKey: 'trust.support',
        icon: 'headset',
        color: '#1C2833',
    },
    {
        labelKey: 'trust.uptime',
        icon: 'clock',
        color: '#C4A265',
    },
    {
        labelKey: 'trust.updates',
        icon: 'refresh',
        color: '#1B4F72',
    },
];
</script>

<template>
    <section ref="sectionRef" class="relative py-20 bg-gradient-to-b from-white via-[#EBF5FB]/60 to-white overflow-hidden">
        <!-- Decorative background elements -->
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, #1B4F72 1px, transparent 0); background-size: 40px 40px;"></div>
        <div class="absolute top-0 start-0 w-96 h-96 bg-[#1B4F72]/[0.03] rounded-full blur-3xl -translate-y-1/2 -translate-x-1/2"></div>
        <div class="absolute bottom-0 end-0 w-80 h-80 bg-[#C4A265]/[0.03] rounded-full blur-3xl translate-y-1/2 translate-x-1/2"></div>

        <!-- Top gradient line -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#1B4F72]/20 to-transparent" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Heading with decorative elements -->
            <div
                class="text-center mb-14 transition-all duration-700"
                :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
            >
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#1B4F72]/5 border border-[#1B4F72]/10 mb-4">
                    <span class="w-2 h-2 rounded-full bg-[#C4A265] animate-pulse"></span>
                    <span class="text-sm font-medium text-[#1B4F72]">{{ $t('trust.headline') }}</span>
                </div>
            </div>

            <!-- Trust Badges - Premium Horizontal Layout -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-5">
                <div
                    v-for="(badge, index) in trustBadges"
                    :key="index"
                    class="group relative flex flex-col items-center gap-4 p-6 rounded-2xl transition-all duration-700 bg-white/70 backdrop-blur-sm border border-gray-100 hover:border-[#1B4F72]/20 hover:shadow-xl hover:shadow-[#1B4F72]/5 hover:-translate-y-1"
                    :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'"
                    :style="{ transitionDelay: isVisible ? `${index * 80}ms` : '0ms' }"
                >
                    <!-- Hover gradient overlay -->
                    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-[#1B4F72]/[0.02] to-[#C4A265]/[0.02] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

                    <!-- Icon Container with ring animation -->
                    <div class="relative">
                        <!-- Animated ring on hover -->
                        <div class="absolute inset-0 rounded-2xl scale-0 group-hover:scale-110 transition-transform duration-500 border-2 border-dashed opacity-20" :style="{ borderColor: badge.color }"></div>

                        <div
                            class="relative w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 group-hover:shadow-lg"
                            :style="{
                                backgroundColor: badge.color + '08',
                                boxShadow: `0 0 0 1px ${badge.color}15`
                            }"
                        >
                            <!-- Shield -->
                            <svg v-if="badge.icon === 'shield'" class="w-7 h-7 transition-colors duration-300" :style="{ color: badge.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <!-- Lock -->
                            <svg v-else-if="badge.icon === 'lock'" class="w-7 h-7 transition-colors duration-300" :style="{ color: badge.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <!-- Cloud -->
                            <svg v-else-if="badge.icon === 'cloud'" class="w-7 h-7 transition-colors duration-300" :style="{ color: badge.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                            <!-- Headset -->
                            <svg v-else-if="badge.icon === 'headset'" class="w-7 h-7 transition-colors duration-300" :style="{ color: badge.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636a9 9 0 010 12.728M15.536 8.464a5 5 0 010 7.072M12 12h.01M8.464 8.464a5 5 0 000 7.072M5.636 5.636a9 9 0 000 12.728" />
                            </svg>
                            <!-- Clock -->
                            <svg v-else-if="badge.icon === 'clock'" class="w-7 h-7 transition-colors duration-300" :style="{ color: badge.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Refresh -->
                            <svg v-else-if="badge.icon === 'refresh'" class="w-7 h-7 transition-colors duration-300" :style="{ color: badge.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>

                    <!-- Label -->
                    <span class="relative text-sm font-semibold text-[#1C2833] text-center leading-snug group-hover:text-[#1B4F72] transition-colors duration-300">
                        {{ $t(badge.labelKey) }}
                    </span>

                    <!-- Bottom accent line on hover -->
                    <div
                        class="absolute bottom-0 inset-x-4 h-0.5 rounded-full scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-center"
                        :style="{ background: `linear-gradient(90deg, transparent, ${badge.color}, transparent)` }"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Bottom gradient line -->
        <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/20 to-transparent" />
    </section>
</template>
