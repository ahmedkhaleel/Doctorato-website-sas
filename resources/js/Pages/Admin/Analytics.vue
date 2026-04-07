<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    kpis: { type: Object, required: true },
    daily_series: { type: Array, default: () => [] },
    plan_distribution: { type: Array, default: () => [] },
});

function fmt(v) {
    return new Intl.NumberFormat('ar-EG').format(Math.round(v || 0));
}

// Build a simple inline SVG sparkline for daily revenue
const sparkPaths = computed(() => {
    const series = props.daily_series || [];
    if (!series.length) return { revenue: '', subs: '', demos: '' };

    const w = 600;
    const h = 100;
    const make = (key) => {
        const values = series.map(d => Number(d[key]) || 0);
        const max = Math.max(...values, 1);
        const step = w / Math.max(values.length - 1, 1);
        return values.map((v, i) => `${i === 0 ? 'M' : 'L'}${(i * step).toFixed(1)},${(h - (v / max) * h).toFixed(1)}`).join(' ');
    };

    return {
        revenue: make('revenue'),
        subs: make('subscriptions'),
        demos: make('demos'),
    };
});

const planTotal = computed(() => props.plan_distribution.reduce((s, p) => s + Number(p.count || 0), 0));
</script>

<template>
    <Head title="التحليلات — لوحة التحكم" />

    <AdminLayout page-title="تحليلات الموقع والإيرادات">
        <!-- KPIs -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white rounded-2xl p-5">
                <p class="text-xs text-white/70 mb-1">الإيراد (آخر 30 يوم)</p>
                <p class="text-2xl font-bold">{{ fmt(kpis.month_revenue) }} <span class="text-sm font-normal">ج.م</span></p>
                <p v-if="kpis.revenue_delta !== null" class="text-xs mt-2"
                   :class="kpis.revenue_delta >= 0 ? 'text-emerald-300' : 'text-red-300'">
                    {{ kpis.revenue_delta >= 0 ? '▲' : '▼' }} {{ Math.abs(kpis.revenue_delta) }}%
                    <span class="text-white/50">vs. الشهر الماضي</span>
                </p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 mb-1">MRR (الإيراد الشهري المتكرر)</p>
                <p class="text-2xl font-bold text-gray-800">{{ fmt(kpis.mrr) }} <span class="text-sm font-normal text-gray-500">ج.م</span></p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 mb-1">معدل التحويل</p>
                <p class="text-2xl font-bold text-emerald-600">{{ kpis.conversion_rate }}%</p>
                <p class="text-xs text-gray-400 mt-1">طلبات → مدفوع</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 mb-1">طلبات تجربة (30 يوم)</p>
                <p class="text-2xl font-bold text-gray-800">{{ fmt(kpis.demos) }}</p>
            </div>
            <div class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 mb-1">اشتراكات جديدة (30 يوم)</p>
                <p class="text-2xl font-bold text-gray-800">{{ fmt(kpis.new_subscriptions) }}</p>
                <p class="text-xs text-emerald-600 mt-1">منها {{ fmt(kpis.paid_subscriptions) }} مدفوع</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid lg:grid-cols-3 gap-6 mb-6">
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6">
                <h3 class="font-bold text-gray-800 mb-4">آخر 30 يوم — الإيراد والاشتراكات والطلبات</h3>
                <svg viewBox="0 0 600 100" class="w-full h-32 mb-2">
                    <path :d="sparkPaths.revenue" stroke="#1B4F72" stroke-width="2" fill="none" />
                </svg>
                <div class="flex gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-1 bg-[#1B4F72]"></span> الإيراد</span>
                </div>
                <svg viewBox="0 0 600 100" class="w-full h-24 mt-3">
                    <path :d="sparkPaths.subs" stroke="#C4A265" stroke-width="2" fill="none" />
                    <path :d="sparkPaths.demos" stroke="#10B981" stroke-width="2" fill="none" />
                </svg>
                <div class="flex gap-4 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-1 bg-[#C4A265]"></span> اشتراكات</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-1 bg-emerald-500"></span> طلبات تجربة</span>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6">
                <h3 class="font-bold text-gray-800 mb-4">توزيع الباقات النشطة</h3>
                <div v-if="plan_distribution.length === 0" class="text-center text-gray-400 py-8 text-sm">
                    لا توجد اشتراكات نشطة بعد
                </div>
                <div v-else class="space-y-3">
                    <div v-for="p in plan_distribution" :key="p.plan">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ p.plan }}</span>
                            <span class="font-semibold text-gray-800">{{ p.count }}</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-gradient-to-l from-[#C4A265] to-[#1B4F72]"
                                :style="{ width: planTotal > 0 ? `${(p.count / planTotal) * 100}%` : '0%' }"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Funnel -->
        <div class="bg-white border border-gray-100 rounded-2xl p-6">
            <h3 class="font-bold text-gray-800 mb-4">قمع التحويل (آخر 30 يوم)</h3>
            <div class="space-y-3">
                <div v-for="(stage, i) in [
                    { label: 'طلبات تجربة', value: kpis.demos, color: 'bg-blue-500' },
                    { label: 'رسائل تواصل', value: kpis.contacts, color: 'bg-cyan-500' },
                    { label: 'اشتراكات جديدة', value: kpis.new_subscriptions, color: 'bg-amber-500' },
                    { label: 'اشتراكات مدفوعة', value: kpis.paid_subscriptions, color: 'bg-emerald-500' },
                ]" :key="i" class="flex items-center gap-3">
                    <span class="w-32 text-sm text-gray-700">{{ stage.label }}</span>
                    <div class="flex-1 h-8 bg-gray-100 rounded-lg overflow-hidden">
                        <div
                            :class="stage.color"
                            class="h-full flex items-center px-3 text-white text-xs font-semibold transition-all"
                            :style="{ width: kpis.demos > 0 ? `${Math.max((stage.value / kpis.demos) * 100, 5)}%` : '0%' }"
                        >
                            {{ fmt(stage.value) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
