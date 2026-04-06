<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const { t } = useI18n();
useScrollAnimation();

const features = computed(() => [
    { icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', text: t('dental.feature_1') },
    { icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', text: t('dental.feature_2') },
    { icon: 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z', text: t('dental.feature_3') },
    { icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', text: t('dental.feature_4') },
    { icon: 'M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122', text: t('dental.feature_5') },
    { icon: 'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z', text: t('dental.feature_6') },
]);
</script>

<template>
    <section class="py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Interactive Dental Chart Placeholder -->
                <div class="animate-slide-right">
                    <div class="relative bg-gradient-to-br from-primary/5 to-secondary/5 rounded-3xl p-8 sm:p-12 border border-gray-light/20">
                        <!-- SVG Dental Chart Placeholder -->
                        <svg viewBox="0 0 400 300" class="w-full h-auto" fill="none">
                            <!-- Upper Jaw Arc -->
                            <path d="M80 120 Q200 40 320 120" stroke="var(--color-primary)" stroke-width="2" fill="none" stroke-dasharray="4 4" />
                            <!-- Lower Jaw Arc -->
                            <path d="M80 180 Q200 260 320 180" stroke="var(--color-primary)" stroke-width="2" fill="none" stroke-dasharray="4 4" />

                            <!-- Upper teeth (simplified) -->
                            <g v-for="(x, i) in [100, 130, 160, 185, 215, 240, 270, 300]" :key="'u'+i">
                                <rect
                                    :x="x - 10" :y="90 + Math.abs(i - 3.5) * 5" width="18" height="22" rx="4"
                                    fill="white" stroke="var(--color-primary)" stroke-width="1.5"
                                    class="hover:fill-[var(--color-secondary)] transition-colors cursor-pointer"
                                    opacity="0.9"
                                />
                                <text :x="x - 1" :y="105 + Math.abs(i - 3.5) * 5" class="text-[8px] fill-gray" text-anchor="middle">{{ 18 - i }}</text>
                            </g>

                            <!-- Lower teeth (simplified) -->
                            <g v-for="(x, i) in [100, 130, 160, 185, 215, 240, 270, 300]" :key="'l'+i">
                                <rect
                                    :x="x - 10" :y="185 - Math.abs(i - 3.5) * 5" width="18" height="22" rx="4"
                                    fill="white" stroke="var(--color-primary)" stroke-width="1.5"
                                    class="hover:fill-[var(--color-secondary)] transition-colors cursor-pointer"
                                    opacity="0.9"
                                />
                                <text :x="x - 1" :y="200 - Math.abs(i - 3.5) * 5" class="text-[8px] fill-gray" text-anchor="middle">{{ 31 + i }}</text>
                            </g>

                            <!-- Center label -->
                            <text x="200" y="155" text-anchor="middle" class="text-xs font-semibold" fill="var(--color-secondary)">
                                {{ $t('dental.chart_label') }}
                            </text>
                        </svg>

                        <!-- Overlay Badge -->
                        <div class="absolute top-4 end-4 bg-secondary text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            {{ $t('dental.interactive') }}
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="animate-slide-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-secondary/10 rounded-full mb-4">
                        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        <span class="text-sm font-semibold text-secondary">{{ $t('dental.spotlight_badge') }}</span>
                    </div>

                    <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                        {{ $t('dental.title') }}
                    </h2>
                    <p class="text-gray leading-relaxed mb-8">
                        {{ $t('dental.description') }}
                    </p>

                    <!-- Features -->
                    <ul class="space-y-4 mb-8">
                        <li
                            v-for="(feature, idx) in features"
                            :key="idx"
                            class="flex items-start gap-3"
                        >
                            <div class="w-8 h-8 rounded-lg bg-success/10 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
                                </svg>
                            </div>
                            <span class="text-sm text-dark leading-relaxed">{{ feature.text }}</span>
                        </li>
                    </ul>

                    <!-- CTA -->
                    <Link
                        href="/demo"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-secondary hover:bg-secondary-dark text-white font-semibold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25 hover:-translate-y-0.5"
                    >
                        {{ $t('dental.cta') }}
                        <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>
