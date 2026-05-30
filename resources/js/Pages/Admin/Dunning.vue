<script setup>
/**
 * /admin/dunning — billing-ops recovery console.
 *
 * Header strip = stage counts (0..5) clickable as filter chips.
 * Table = failed invoices with per-row action menu:
 *   Advance → next stage
 *   Reset   → back to stage 0
 *   Resolve → set stage 5 (cron stops)
 *
 * Paused subs surface a small amber badge — RunDunning skips them
 * (Phase 20) so the table also reflects "this isn't escalating".
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    rows: Object,
    filters: Object,
    stageLabels: Object,  // { 0: 'Untouched', ... }
    byStage: Object,      // { 0: 12, 1: 5, ... }
});

const filter = ref({
    stage: props.filters?.stage ?? '',
    q: props.filters?.q ?? '',
});

const advanceForm = useForm({});
const resetForm = useForm({});
const resolveForm = useForm({});

function applyFilters() {
    router.get('/admin/dunning', {
        stage: filter.value.stage === '' ? undefined : filter.value.stage,
        q: filter.value.q || undefined,
    }, { preserveState: true, preserveScroll: true });
}

function pickStage(stage) {
    filter.value.stage = stage;
    applyFilters();
}

function clearFilters() {
    filter.value = { stage: '', q: '' };
    applyFilters();
}

function advance(id) {
    if (!confirm('نقل الفاتورة إلى المرحلة التالية؟')) return;
    advanceForm.post(`/admin/dunning/${id}/advance`, { preserveScroll: true });
}
function reset(id) {
    if (!confirm('إعادة الفاتورة إلى المرحلة 0؟')) return;
    resetForm.post(`/admin/dunning/${id}/reset`, { preserveScroll: true });
}
function resolve(id) {
    if (!confirm('وضع علامة "تم الحل" — التذكيرات ستتوقف نهائياً.')) return;
    resolveForm.post(`/admin/dunning/${id}/resolve`, { preserveScroll: true });
}

function stageBadge(s) {
    if (s === 0) return 'bg-blue-100 text-blue-800';
    if (s === 5) return 'bg-emerald-100 text-emerald-800';
    if (s === 4) return 'bg-rose-100 text-rose-800';
    return 'bg-amber-100 text-amber-800';
}

function formatMoney(amt, ccy) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency', currency: ccy || 'USD',
        minimumFractionDigits: 2,
    }).format(amt);
}
function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
    });
}
</script>

<template>
    <Head title="التحصيل (Dunning) — لوحة التحكم" />
    <AdminLayout page-title="التحصيل (Dunning)">
        <!-- Stage strip — click to filter -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3 mb-6">
            <button
                v-for="(label, s) in stageLabels"
                :key="s"
                @click="pickStage(Number(s))"
                :class="[
                    'rounded-2xl p-4 border text-start transition',
                    Number(filter.stage) === Number(s) && filter.stage !== ''
                        ? 'bg-[#0A1628] border-[#0A1628] text-white'
                        : 'bg-white border-gray-100 hover:border-gray-300',
                ]"
            >
                <p class="text-[10px] uppercase tracking-wider opacity-70 mb-1">المرحلة {{ s }}</p>
                <p class="text-2xl font-bold leading-none">{{ byStage[s] || 0 }}</p>
                <p class="text-xs mt-2 opacity-80">{{ label }}</p>
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input
                v-model="filter.q"
                @keydown.enter="applyFilters"
                placeholder="ابحث بالبريد أو اسم العيادة"
                class="flex-1 min-w-[240px] px-4 py-2 border border-gray-200 rounded-lg text-sm"
            />
            <button @click="applyFilters" class="px-3 py-2 text-sm font-semibold text-white bg-[#0A1628] hover:bg-[#1C2833] rounded-lg">
                تطبيق
            </button>
            <button @click="clearFilters" class="px-3 py-2 text-sm text-gray-500 hover:text-[#1B4F72]">إعادة تعيين</button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div v-if="!rows.data.length" class="p-16 text-center text-gray-400">
                لا توجد فواتير فاشلة في هذه المرحلة. 🎉
            </div>
            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-start text-xs font-semibold uppercase tracking-wider text-gray-600">
                        <th class="px-5 py-3 text-start">الفاتورة</th>
                        <th class="px-5 py-3 text-start">العميل</th>
                        <th class="px-5 py-3 text-start">المبلغ</th>
                        <th class="px-5 py-3 text-start">تاريخ الفشل</th>
                        <th class="px-5 py-3 text-start">المرحلة</th>
                        <th class="px-5 py-3 text-end">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="row in rows.data" :key="row.id" class="hover:bg-gray-50">
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs text-gray-700">{{ row.number || `#${row.id}` }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">Sub #{{ row.subscription_id }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <p class="text-gray-800 font-medium">{{ row.clinic_name || '—' }}</p>
                            <p class="text-xs text-gray-500">{{ row.customer_email || '—' }}</p>
                            <span v-if="row.subscription_paused" class="inline-block mt-1 text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                                paused — cron skips
                            </span>
                        </td>
                        <td class="px-5 py-3 font-semibold text-gray-800 whitespace-nowrap">
                            {{ formatMoney(row.total, row.currency) }}
                        </td>
                        <td class="px-5 py-3 text-gray-700 whitespace-nowrap">
                            {{ formatDate(row.failed_at) }}
                            <span v-if="row.days_failed !== null" class="block text-[10px] text-gray-400 mt-0.5">
                                {{ Math.abs(row.days_failed) }} يوم
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span :class="stageBadge(row.dunning_stage)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ row.dunning_stage }} · {{ row.stage_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-end space-x-1 whitespace-nowrap">
                            <button
                                v-if="row.dunning_stage < 5"
                                @click="advance(row.id)"
                                class="text-xs font-semibold text-amber-700 hover:text-amber-900 px-2 py-1"
                            >
                                Advance
                            </button>
                            <button
                                v-if="row.dunning_stage > 0 && row.dunning_stage < 5"
                                @click="reset(row.id)"
                                class="text-xs font-semibold text-gray-500 hover:text-gray-800 px-2 py-1"
                            >
                                Reset
                            </button>
                            <button
                                v-if="row.dunning_stage < 5"
                                @click="resolve(row.id)"
                                class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 px-2 py-1"
                            >
                                Resolve
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="rows.last_page > 1" class="border-t border-gray-100 p-4 flex items-center justify-between text-sm text-gray-500">
                <span>صفحة {{ rows.current_page }} من {{ rows.last_page }} — {{ rows.total }} فاتورة</span>
                <div class="flex gap-2">
                    <Link
                        v-for="link in rows.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-sm',
                            link.active ? 'bg-[#0A1628] text-white' : 'text-gray-600 hover:bg-gray-100',
                            !link.url ? 'opacity-30 pointer-events-none' : '',
                        ]"
                        preserve-scroll
                    />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
