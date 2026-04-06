<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import AnimatedCounter from '@/Components/AnimatedCounter.vue';
import TestimonialsCarousel from '@/Components/TestimonialsCarousel.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const { t } = useI18n();
useScrollAnimation();

defineProps({
    testimonials: { type: Array, default: () => [] },
});

const stats = computed(() => [
    { value: 500, suffix: '+', label: t('about.stat_clinics'), icon: 'building' },
    { value: 15, suffix: '+', label: t('about.stat_countries'), icon: 'globe' },
    { value: 50000, suffix: '+', label: t('about.stat_patients'), icon: 'users' },
    { value: 99.9, suffix: '%', label: t('about.stat_uptime'), icon: 'server' },
]);

const values = computed(() => [
    {
        icon: 'bulb',
        color: 'from-amber-500 to-orange-500',
        bgColor: 'bg-amber-50',
        title: t('about.value_innovation_title'),
        description: t('about.value_innovation_desc'),
    },
    {
        icon: 'shield',
        color: 'from-blue-500 to-cyan-500',
        bgColor: 'bg-blue-50',
        title: t('about.value_trust_title'),
        description: t('about.value_trust_desc'),
    },
    {
        icon: 'heart',
        color: 'from-rose-500 to-pink-500',
        bgColor: 'bg-rose-50',
        title: t('about.value_customer_title'),
        description: t('about.value_customer_desc'),
    },
    {
        icon: 'star',
        color: 'from-emerald-500 to-teal-500',
        bgColor: 'bg-emerald-50',
        title: t('about.value_excellence_title'),
        description: t('about.value_excellence_desc'),
    },
]);

const timeline = computed(() => [
    { year: '2020', title: t('about.timeline_2020_title'), description: t('about.timeline_2020_desc') },
    { year: '2021', title: t('about.timeline_2021_title'), description: t('about.timeline_2021_desc') },
    { year: '2022', title: t('about.timeline_2022_title'), description: t('about.timeline_2022_desc') },
    { year: '2023', title: t('about.timeline_2023_title'), description: t('about.timeline_2023_desc') },
    { year: '2024', title: t('about.timeline_2024_title'), description: t('about.timeline_2024_desc') },
]);
</script>

<template>
    <Head :title="t('about.page_title')" />
    <MainLayout>
        <!-- ═══════ HERO ═══════ -->
        <section class="relative pt-32 pb-24 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>
            <div class="absolute bottom-0 start-0 w-80 h-80 bg-[#2471A3]/15 rounded-full blur-[100px]"></div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/[0.06] border border-white/[0.1] mb-6 animate-fade-up">
                    <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-sm text-white/70 font-medium">{{ t('about.hero_badge') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight animate-fade-up">
                    {{ t('about.hero_title') }}
                </h1>
                <p class="text-lg text-white/60 max-w-2xl mx-auto leading-relaxed animate-fade-up">
                    {{ t('about.hero_subtitle') }}
                </p>
            </div>
        </section>

        <!-- ═══════ STATS BAR ═══════ -->
        <section class="relative -mt-10 z-20 pb-10">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8 animate-fade-up">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                        <div v-for="(stat, idx) in stats" :key="idx" class="text-center">
                            <div class="text-3xl md:text-4xl font-extrabold text-[#1B4F72] mb-1">
                                <AnimatedCounter :target="stat.value" :suffix="stat.suffix" />
                            </div>
                            <p class="text-xs md:text-sm text-gray-500 font-medium">{{ stat.label }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ STORY ═══════ -->
        <section class="py-20 lg:py-28 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
                    <!-- Text -->
                    <div class="animate-fade-up">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#1B4F72]/5 border border-[#1B4F72]/10 mb-5">
                            <span class="text-sm text-[#1B4F72] font-medium">{{ t('about.story_badge') }}</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-6 leading-tight">
                            {{ t('about.story_title') }}
                        </h2>
                        <p class="text-gray-500 leading-relaxed mb-4">{{ t('about.story_p1') }}</p>
                        <p class="text-gray-500 leading-relaxed mb-4">{{ t('about.story_p2') }}</p>
                        <p class="text-gray-500 leading-relaxed">{{ t('about.story_p3') }}</p>
                    </div>

                    <!-- Visual -->
                    <div class="animate-fade-up">
                        <div class="relative">
                            <div class="bg-gradient-to-br from-[#1B4F72]/5 to-[#C4A265]/5 rounded-3xl p-10 aspect-square flex items-center justify-center border border-gray-100">
                                <div class="text-center">
                                    <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-[#1B4F72] to-[#0A1628] flex items-center justify-center mx-auto mb-6 shadow-xl">
                                        <span class="text-4xl font-extrabold text-white">D</span>
                                    </div>
                                    <p class="text-2xl font-extrabold text-[#1C2833]">Doctorato</p>
                                    <p class="text-sm text-gray-400 mt-1">{{ t('about.since') }} 2020</p>
                                </div>
                            </div>
                            <!-- Floating badge -->
                            <div class="absolute -bottom-5 -end-5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white px-6 py-3 rounded-2xl shadow-lg shadow-[#C4A265]/20">
                                <span class="text-2xl font-extrabold">+800</span>
                                <span class="block text-xs opacity-80">{{ t('about.features_count') }}</span>
                            </div>
                            <!-- Second floating badge -->
                            <div class="absolute -top-3 -start-3 bg-white text-[#1B4F72] px-5 py-2.5 rounded-xl shadow-lg border border-gray-100">
                                <span class="text-xl font-extrabold">15+</span>
                                <span class="block text-xs text-gray-400">{{ t('about.stat_countries') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ MISSION & VISION ═══════ -->
        <section class="py-20 lg:py-28 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-stagger">
                    <!-- Mission -->
                    <div class="group bg-white rounded-3xl border border-gray-100 p-8 md:p-10 hover:shadow-2xl hover:shadow-[#1B4F72]/5 transition-all duration-500 relative overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#1B4F72] to-[#2471A3] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-center"></div>
                        <div class="w-14 h-14 rounded-2xl bg-[#1B4F72]/5 group-hover:bg-[#1B4F72] flex items-center justify-center mb-6 transition-all duration-300">
                            <svg class="w-7 h-7 text-[#1B4F72] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-[#1C2833] mb-4">{{ t('about.mission_title') }}</h3>
                        <p class="text-gray-500 leading-relaxed">{{ t('about.mission_text') }}</p>
                    </div>

                    <!-- Vision -->
                    <div class="group bg-white rounded-3xl border border-gray-100 p-8 md:p-10 hover:shadow-2xl hover:shadow-[#C4A265]/5 transition-all duration-500 relative overflow-hidden">
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#C4A265] to-[#D4B876] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-center"></div>
                        <div class="w-14 h-14 rounded-2xl bg-[#C4A265]/5 group-hover:bg-[#C4A265] flex items-center justify-center mb-6 transition-all duration-300">
                            <svg class="w-7 h-7 text-[#C4A265] group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-[#1C2833] mb-4">{{ t('about.vision_title') }}</h3>
                        <p class="text-gray-500 leading-relaxed">{{ t('about.vision_text') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ VALUES ═══════ -->
        <section class="py-20 lg:py-28 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#C4A265]/5 border border-[#C4A265]/10 mb-5">
                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <span class="text-sm text-[#C4A265] font-medium">{{ t('about.values_badge') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">{{ t('about.values_title') }}</h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">{{ t('about.values_subtitle') }}</p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                        <span class="w-8 h-1 rounded-full bg-[#C4A265]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-stagger">
                    <div
                        v-for="(value, idx) in values"
                        :key="idx"
                        class="group flex items-start gap-5 bg-white rounded-2xl border border-gray-100 p-7 hover:shadow-xl hover:border-transparent transition-all duration-500 relative overflow-hidden"
                    >
                        <div class="absolute bottom-0 inset-x-0 h-0.5 bg-gradient-to-r transition-transform duration-500 transform scale-x-0 group-hover:scale-x-100 origin-center" :class="value.color"></div>

                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 transition-all duration-300" :class="value.bgColor">
                            <svg class="w-7 h-7" :class="value.icon === 'bulb' ? 'text-amber-500' : value.icon === 'shield' ? 'text-blue-500' : value.icon === 'heart' ? 'text-rose-500' : 'text-emerald-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="value.icon === 'bulb'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                <path v-if="value.icon === 'shield'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                <path v-if="value.icon === 'heart'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                <path v-if="value.icon === 'star'" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-[#1C2833] mb-2">{{ value.title }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ value.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ TIMELINE ═══════ -->
        <section class="py-20 lg:py-28 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#1B4F72]/5 border border-[#1B4F72]/10 mb-5">
                        <svg class="w-4 h-4 text-[#1B4F72]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm text-[#1B4F72] font-medium">{{ t('about.timeline_badge') }}</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">{{ t('about.timeline_title') }}</h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">{{ t('about.timeline_subtitle') }}</p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                        <span class="w-8 h-1 rounded-full bg-[#C4A265]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="relative">
                    <!-- Vertical line -->
                    <div class="absolute start-6 md:start-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-[#C4A265] via-[#1B4F72] to-[#C4A265] md:-translate-x-1/2"></div>

                    <div
                        v-for="(item, idx) in timeline"
                        :key="idx"
                        class="relative mb-10 last:mb-0 animate-fade-up"
                        :class="idx % 2 === 0 ? 'md:pe-[50%] md:text-end' : 'md:ps-[50%]'"
                    >
                        <!-- Dot -->
                        <div class="absolute start-6 md:start-1/2 w-5 h-5 -translate-x-1/2 top-2 z-10">
                            <div class="w-5 h-5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] rounded-full border-4 border-white shadow-md"></div>
                        </div>

                        <!-- Card -->
                        <div
                            class="ms-14 md:ms-0 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-lg transition-all duration-300"
                            :class="idx % 2 === 0 ? 'md:me-10' : 'md:ms-10'"
                        >
                            <span class="inline-flex items-center px-3 py-1 bg-[#1B4F72]/5 text-[#1B4F72] text-sm font-bold rounded-full mb-3">
                                {{ item.year }}
                            </span>
                            <h3 class="text-lg font-bold text-[#1C2833] mb-2">{{ item.title }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ═══════ TESTIMONIALS ═══════ -->
        <section v-if="testimonials && testimonials.length > 0" class="py-20 lg:py-28 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14 animate-fade-up">
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#1C2833] mb-4">{{ t('about.testimonials_title') }}</h2>
                    <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">{{ t('about.testimonials_subtitle') }}</p>
                    <div class="flex items-center justify-center gap-2 mt-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                        <span class="w-8 h-1 rounded-full bg-[#C4A265]"></span>
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265]"></span>
                    </div>
                </div>
                <TestimonialsCarousel :testimonials="testimonials" />
            </div>
        </section>

        <!-- ═══════ CTA ═══════ -->
        <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative bg-gradient-to-br from-[#1B4F72] via-[#1B4F72] to-[#0A1628] rounded-3xl p-10 md:p-14 overflow-hidden animate-fade-up">
                    <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/10 rounded-full blur-[80px]"></div>
                    <div class="absolute bottom-0 start-0 w-48 h-48 bg-white/5 rounded-full blur-[60px]"></div>
                    <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 30px 30px;"></div>

                    <div class="relative z-10 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-[#C4A265]/10 flex items-center justify-center mx-auto mb-6">
                            <svg class="w-7 h-7 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl md:text-4xl font-extrabold text-white mb-4">{{ t('about.cta_title') }}</h2>
                        <p class="text-white/60 text-base md:text-lg mb-8 max-w-2xl mx-auto leading-relaxed">{{ t('about.cta_subtitle') }}</p>
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <Link
                                href="/demo"
                                class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-semibold rounded-xl shadow-lg shadow-[#C4A265]/20 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                            >
                                {{ t('about.cta_demo') }}
                                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </Link>
                            <Link
                                href="/contact"
                                class="inline-flex items-center gap-2 px-8 py-3.5 border-2 border-white/20 text-white font-semibold rounded-xl hover:bg-white/10 transition-all duration-300"
                            >
                                {{ t('about.cta_contact') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
