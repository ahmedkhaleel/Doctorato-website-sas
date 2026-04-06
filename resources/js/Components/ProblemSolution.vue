<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const { t } = useI18n();
useScrollAnimation();

const problems = computed(() => [
    {
        icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        text: t('problem_solution.problem_1'),
    },
    {
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        text: t('problem_solution.problem_2'),
    },
    {
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        text: t('problem_solution.problem_3'),
    },
    {
        icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
        text: t('problem_solution.problem_4'),
    },
    {
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
        text: t('problem_solution.problem_5'),
    },
]);

const solutions = computed(() => [
    {
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        text: t('problem_solution.solution_1'),
    },
    {
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        text: t('problem_solution.solution_2'),
    },
    {
        icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
        text: t('problem_solution.solution_3'),
    },
    {
        icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        text: t('problem_solution.solution_4'),
    },
    {
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
        text: t('problem_solution.solution_5'),
    },
]);
</script>

<template>
    <section class="py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-16 animate-fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    {{ $t('problem_solution.title') }}
                </h2>
                <p class="text-lg text-gray max-w-2xl mx-auto">
                    {{ $t('problem_solution.subtitle') }}
                </p>
                <div class="w-20 h-1 bg-secondary mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                <!-- Problem Side -->
                <div class="animate-slide-right">
                    <div class="bg-danger/5 border border-danger/20 rounded-2xl p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-danger/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-danger">{{ $t('problem_solution.problem_title') }}</h3>
                        </div>

                        <ul class="space-y-4">
                            <li
                                v-for="(item, idx) in problems"
                                :key="idx"
                                class="flex items-start gap-3"
                            >
                                <div class="w-8 h-8 rounded-lg bg-danger/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                    </svg>
                                </div>
                                <p class="text-dark text-sm leading-relaxed">{{ item.text }}</p>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Solution Side -->
                <div class="animate-slide-left">
                    <div class="bg-success/5 border border-success/20 rounded-2xl p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-success">{{ $t('problem_solution.solution_title') }}</h3>
                        </div>

                        <ul class="space-y-4">
                            <li
                                v-for="(item, idx) in solutions"
                                :key="idx"
                                class="flex items-start gap-3"
                            >
                                <div class="w-8 h-8 rounded-lg bg-success/10 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                                    </svg>
                                </div>
                                <p class="text-dark text-sm leading-relaxed">{{ item.text }}</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Connecting Arrow (center) -->
            <div class="hidden lg:flex justify-center -mt-6 mb-0">
                <div class="w-16 h-16 rounded-full bg-secondary flex items-center justify-center shadow-lg shadow-secondary/25">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
            </div>
        </div>
    </section>
</template>
