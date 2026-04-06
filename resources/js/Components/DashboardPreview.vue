<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const { t } = useI18n();
useScrollAnimation();

const metrics = computed(() => [
    {
        label: t('dashboard.metric_patients'),
        value: '2,847',
        change: '+12%',
        positive: true,
        icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
    },
    {
        label: t('dashboard.metric_appointments'),
        value: '156',
        change: '+8%',
        positive: true,
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
    },
    {
        label: t('dashboard.metric_revenue'),
        value: '84,500',
        change: '+23%',
        positive: true,
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    },
    {
        label: t('dashboard.metric_satisfaction'),
        value: '98%',
        change: '+2%',
        positive: true,
        icon: 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    },
]);
</script>

<template>
    <section class="py-20 bg-gradient-to-br from-primary via-primary-dark to-[#0A1628] overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12 animate-fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    {{ $t('dashboard.title') }}
                </h2>
                <p class="text-lg text-white/60 max-w-2xl mx-auto">
                    {{ $t('dashboard.subtitle') }}
                </p>
                <div class="w-20 h-1 bg-secondary mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Dashboard Mockup -->
            <div class="animate-scale-in">
                <div class="max-w-5xl mx-auto">
                    <!-- Browser Chrome -->
                    <div class="bg-dark/80 rounded-t-2xl px-4 py-3 flex items-center gap-2">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-danger"></div>
                            <div class="w-3 h-3 rounded-full bg-warning"></div>
                            <div class="w-3 h-3 rounded-full bg-success"></div>
                        </div>
                        <div class="flex-1 mx-8">
                            <div class="bg-white/10 rounded-lg px-4 py-1.5 text-xs text-white/40 text-center">
                                app.doctorato.co/admin/dashboard
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Content -->
                    <div class="bg-gradient-to-br from-[#f8fafc] to-[#f1f5f9] rounded-b-2xl p-6 sm:p-8 border-x border-b border-white/10">
                        <!-- Top Bar -->
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-dark">{{ $t('dashboard.welcome') }}</h3>
                                <p class="text-xs text-gray">{{ $t('dashboard.overview') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-secondary/20 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-bold">
                                    D
                                </div>
                            </div>
                        </div>

                        <!-- Metric Cards -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div
                                v-for="(metric, idx) in metrics"
                                :key="idx"
                                class="bg-white rounded-xl p-4 shadow-sm border border-gray-100"
                            >
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="metric.icon" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full" :class="metric.positive ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger'">
                                        {{ metric.change }}
                                    </span>
                                </div>
                                <p class="text-2xl font-bold text-dark">{{ metric.value }}</p>
                                <p class="text-xs text-gray mt-1">{{ metric.label }}</p>
                            </div>
                        </div>

                        <!-- Chart Placeholder -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="lg:col-span-2 bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-semibold text-dark">{{ $t('dashboard.chart_title') }}</h4>
                                    <div class="flex gap-1">
                                        <span class="px-2 py-1 rounded text-xs bg-primary/10 text-primary font-medium">{{ $t('dashboard.weekly') }}</span>
                                    </div>
                                </div>
                                <!-- Placeholder bars -->
                                <div class="flex items-end gap-2 h-32">
                                    <div v-for="h in [40, 65, 55, 80, 70, 90, 75]" :key="h" class="flex-1 bg-gradient-to-t from-primary to-primary-light rounded-t-md" :style="{ height: h + '%' }"></div>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100">
                                <h4 class="text-sm font-semibold text-dark mb-4">{{ $t('dashboard.upcoming') }}</h4>
                                <div class="space-y-3">
                                    <div v-for="i in 4" :key="i" class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gray-light/20"></div>
                                        <div class="flex-1">
                                            <div class="h-3 bg-gray-light/30 rounded w-3/4 mb-1"></div>
                                            <div class="h-2 bg-gray-light/20 rounded w-1/2"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
