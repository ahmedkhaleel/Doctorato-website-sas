<script setup>
/**
 * /admin/metrics — owner KPI dashboard.
 *
 * Layout:
 *   Row 1: MRR / ARR / Active subs / ARPU (the big four)
 *   Row 2: Paused / Past-due / New 30d / Cancelled 30d
 *   Row 3: Churn rate 30d + 90d, billing-cycle split
 *   Row 4: Recent cancellations table (last 10)
 *
 * All values come from a 15-min cached snapshot. The Refresh button
 * busts the cache.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Sparkline from '@/Components/Admin/Sparkline.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    snapshot: Object,
    trend: { type: Array, default: () => [] },
});

const trendMrr = computed(() => props.trend.map((p) => p.mrr));
const trendActive = computed(() => props.trend.map((p) => p.active));
const trendChurn = computed(() => props.trend.map((p) => p.churn));
const trendLabels = computed(() => props.trend.map((p) => p.date));
const hasTrend = computed(() => props.trend.length >= 2);

const page = usePage();
const flash = computed(() => page.props.flash?.success ?? null);

const refreshForm = useForm({});
function refresh() { refreshForm.post('/admin/metrics/refresh', { preserveScroll: true }); }

function formatSAR(n) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency', currency: 'SAR',
        minimumFractionDigits: 0, maximumFractionDigits: 0,
    }).format(n || 0);
}

function formatMoneyCcy(n, ccy) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency', currency: ccy || 'USD',
        minimumFractionDigits: 0, maximumFractionDigits: 0,
    }).format(n || 0);
}

function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
    });
}

function asOfRelative(iso) {
    if (!iso) return '';
    const mins = Math.floor((Date.now() - new Date(iso).getTime()) / 60000);
    if (mins < 1) return 'الآن';
    if (mins < 60) return `منذ ${mins} دقيقة`;
    return `منذ ${Math.floor(mins / 60)} ساعة`;
}
</script>

<template>
    <Head title="إحصائيات الاشتراكات — لوحة التحكم" />
    <AdminLayout page-title="إحصائيات الاشتراكات">

        <div v-if="flash" class="mb-4 bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-sm text-emerald-900">
            {{ flash }}
        </div>

        <!-- Header: as-of + refresh -->
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <p class="text-xs text-gray-500">
                مُحدَّثة <strong>{{ asOfRelative(snapshot.as_of) }}</strong> · مخزَّن مؤقتاً لمدة 15 دقيقة
            </p>
            <button @click="refresh" :disabled="refreshForm.processing"
                class="inline-flex items-center gap-2 bg-[#0A1628] hover:bg-[#1C2833] disabled:bg-gray-300 text-white text-sm font-semibold rounded-lg px-4 py-2 transition">
                <svg class="w-4 h-4" :class="{ 'animate-spin': refreshForm.processing }" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                تحديث
            </button>
        </div>

        <!-- Row 1: the big four -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <article class="bg-gradient-to-br from-[#0A1628] to-[#1C2833] text-white rounded-2xl p-5">
                <p class="text-xs text-white/70 uppercase tracking-widest mb-1">MRR</p>
                <p class="text-3xl font-bold leading-none">{{ formatSAR(snapshot.mrr_sar) }}</p>
                <p class="text-xs text-white/60 mt-2">شهرياً، يعادل SAR</p>
            </article>
            <article class="bg-gradient-to-br from-[#C4A265] to-[#D4B876] text-white rounded-2xl p-5">
                <p class="text-xs text-white/80 uppercase tracking-widest mb-1">ARR</p>
                <p class="text-3xl font-bold leading-none">{{ formatSAR(snapshot.arr_sar) }}</p>
                <p class="text-xs text-white/70 mt-2">MRR × 12</p>
            </article>
            <article class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">اشتراكات نشطة</p>
                <p class="text-3xl font-bold leading-none text-gray-800">{{ snapshot.active_subs }}</p>
                <p class="text-xs text-gray-500 mt-2">شهري {{ snapshot.by_cycle?.monthly ?? 0 }} · سنوي {{ snapshot.by_cycle?.yearly ?? 0 }}</p>
            </article>
            <article class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">ARPU</p>
                <p class="text-3xl font-bold leading-none text-gray-800">{{ formatSAR(snapshot.arpu_sar) }}</p>
                <p class="text-xs text-gray-500 mt-2">متوسط الإيراد لكل اشتراك</p>
            </article>
        </div>

        <!-- Row 2: secondary counts -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <article class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                <p class="text-xs text-amber-700 uppercase tracking-widest mb-1">متوقفة مؤقتاً</p>
                <p class="text-2xl font-bold leading-none text-amber-900">{{ snapshot.paused_subs }}</p>
            </article>
            <article class="bg-rose-50 border border-rose-100 rounded-2xl p-5">
                <p class="text-xs text-rose-700 uppercase tracking-widest mb-1">Past Due</p>
                <p class="text-2xl font-bold leading-none text-rose-900">{{ snapshot.past_due_subs }}</p>
            </article>
            <article class="bg-emerald-50 border border-emerald-100 rounded-2xl p-5">
                <p class="text-xs text-emerald-700 uppercase tracking-widest mb-1">جديدة (30 يوم)</p>
                <p class="text-2xl font-bold leading-none text-emerald-900">{{ snapshot.new_30d }}</p>
                <p class="text-[10px] text-emerald-700 mt-1">شهر حتى الآن: {{ snapshot.new_mtd }}</p>
            </article>
            <article class="bg-gray-50 border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">إلغاءات (30 يوم)</p>
                <p class="text-2xl font-bold leading-none text-gray-800">{{ snapshot.cancelled_30d }}</p>
            </article>
        </div>

        <!-- Trend row — 90-day sparklines (hidden when no snapshots yet) -->
        <div v-if="hasTrend" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <article class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-3">اتجاه MRR (90 يوم)</p>
                <Sparkline :points="trendMrr" :labels="trendLabels" :width="260" :height="50"
                           stroke="#0A1628" fill="rgba(10, 22, 40, 0.08)"
                           :format="(v) => formatSAR(v)" />
            </article>
            <article class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-3">اتجاه الاشتراكات النشطة</p>
                <Sparkline :points="trendActive" :labels="trendLabels" :width="260" :height="50"
                           stroke="#C4A265" fill="rgba(196, 162, 101, 0.18)"
                           :format="(v) => `${v} sub`" />
            </article>
            <article class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-3">اتجاه الـ Churn (%)</p>
                <Sparkline :points="trendChurn" :labels="trendLabels" :width="260" :height="50"
                           stroke="#b91c1c" fill="rgba(185, 28, 28, 0.10)"
                           :format="(v) => `${v.toFixed(1)}%`" />
            </article>
        </div>
        <div v-else class="bg-amber-50 border border-amber-100 rounded-2xl p-5 mb-6 text-sm text-amber-900">
            لا توجد لقطات تاريخية بعد. شغّل
            <code class="bg-white px-1.5 py-0.5 rounded font-mono text-xs">php artisan metrics:snapshot</code>
            أو انتظر التشغيل التلقائي عند الساعة 23:55.
        </div>

        <!-- Row 3: churn -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <article class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">معدّل الـ Churn (30 يوم)</p>
                <p class="text-3xl font-bold leading-none" :class="snapshot.churn_30d > 5 ? 'text-rose-700' : 'text-emerald-700'">
                    {{ snapshot.churn_30d }}%
                </p>
                <p class="text-xs text-gray-500 mt-2">المُلغاة / النشطة في بداية الفترة</p>
            </article>
            <article class="bg-white border border-gray-100 rounded-2xl p-5">
                <p class="text-xs text-gray-500 uppercase tracking-widest mb-2">معدّل الـ Churn (90 يوم)</p>
                <p class="text-3xl font-bold leading-none" :class="snapshot.churn_90d > 10 ? 'text-rose-700' : 'text-emerald-700'">
                    {{ snapshot.churn_90d }}%
                </p>
                <p class="text-xs text-gray-500 mt-2">للمقارنة الفصلية</p>
            </article>
        </div>

        <!-- Row 4: recent cancellations -->
        <article class="bg-white border border-gray-100 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265]">آخر الإلغاءات</h2>
            </div>
            <div v-if="!snapshot.recent_cancellations?.length" class="p-8 text-center text-gray-400 text-sm">
                لا إلغاءات مسجَّلة. 🎉
            </div>
            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-start text-xs font-semibold uppercase tracking-wider text-gray-600">
                        <th class="px-5 py-3 text-start">العيادة</th>
                        <th class="px-5 py-3 text-start">الدورة</th>
                        <th class="px-5 py-3 text-start">القيمة</th>
                        <th class="px-5 py-3 text-start">مدة العمر</th>
                        <th class="px-5 py-3 text-start">تاريخ الإلغاء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="row in snapshot.recent_cancellations" :key="row.id" class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-gray-800 font-medium">{{ row.clinic }}</td>
                        <td class="px-5 py-3 text-gray-700 capitalize">{{ row.billing_cycle }}</td>
                        <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ formatMoneyCcy(row.amount, row.currency) }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ row.days_active !== null ? `${Math.abs(row.days_active)} يوم` : '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ formatDate(row.cancelled_at) }}</td>
                    </tr>
                </tbody>
            </table>
        </article>
    </AdminLayout>
</template>
