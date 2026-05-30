<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    logs: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    stats: { type: Object, required: true },
    actions: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
});

const filter = ref({
    q: props.filters?.q ?? '',
    action: props.filters?.action ?? '',
    user: props.filters?.user ?? '',
    from: props.filters?.from ?? '',
    to: props.filters?.to ?? '',
});

const actionColors = {
    created: { bg: 'bg-emerald-50', text: 'text-emerald-700', icon: '+' },
    updated: { bg: 'bg-blue-50', text: 'text-blue-700', icon: '✎' },
    deleted: { bg: 'bg-red-50', text: 'text-red-700', icon: '✕' },
    logged_in: { bg: 'bg-purple-50', text: 'text-purple-700', icon: '→' },
    logged_out: { bg: 'bg-gray-50', text: 'text-gray-700', icon: '←' },
    cancelled: { bg: 'bg-orange-50', text: 'text-orange-700', icon: '⊘' },
    paid: { bg: 'bg-emerald-50', text: 'text-emerald-700', icon: '✓' },
};

function colorFor(action) {
    return actionColors[action] || { bg: 'bg-gray-50', text: 'text-gray-700', icon: '•' };
}

function subjectShort(type) {
    if (!type) return '';
    const parts = type.split('\\');
    return parts[parts.length - 1];
}

// Track which rows have their diff expanded. We keep this client-side
// so the user can pop several rows open without round-tripping.
const expandedRows = ref(new Set());
function toggleRow(id) {
    if (expandedRows.value.has(id)) {
        expandedRows.value.delete(id);
    } else {
        expandedRows.value.add(id);
    }
    // Force reactivity since we're mutating a Set in place
    expandedRows.value = new Set(expandedRows.value);
}

// Convert the changes JSON written by the LogsActivity trait into a
// flat array we can iterate in the template:
//   updated: { field: { from, to } } → [{ field, from, to }]
//   created/deleted: { field: value }      → [{ field, value }]
function changeRows(log) {
    const c = log.changes;
    if (!c || typeof c !== 'object') return [];
    if (log.action === 'updated') {
        return Object.entries(c)
            .filter(([, v]) => v && typeof v === 'object' && ('from' in v || 'to' in v))
            .map(([field, v]) => ({ field, from: v.from, to: v.to, kind: 'diff' }));
    }
    return Object.entries(c).map(([field, value]) => ({ field, value, kind: 'snapshot' }));
}

// Stringify a value for display. Long strings get truncated; nulls
// and booleans get a special render so a "0" doesn't read as null.
function fmtValue(v) {
    if (v === null || v === undefined) return '∅';
    if (typeof v === 'boolean') return v ? 'true' : 'false';
    if (typeof v === 'object') return JSON.stringify(v).slice(0, 200);
    const s = String(v);
    return s.length > 200 ? s.slice(0, 200) + '…' : s;
}

function exportCsv() {
    // Walk through the current filter state and hit the export endpoint
    // in a new tab. The server streams the CSV so the user's browser
    // gets a download dialog without us holding bytes in memory here.
    const params = new URLSearchParams(
        Object.entries(filter.value).filter(([, v]) => Boolean(v))
    );
    window.open('/admin/activity-logs/export?' + params.toString(), '_blank');
}

function fmtDateTime(d) {
    if (!d) return '—';
    return new Date(d).toLocaleString('ar-EG', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function relativeTime(d) {
    if (!d) return '';
    const diff = (Date.now() - new Date(d).getTime()) / 1000;
    if (diff < 60) return 'الآن';
    if (diff < 3600) return `منذ ${Math.floor(diff / 60)} دقيقة`;
    if (diff < 86400) return `منذ ${Math.floor(diff / 3600)} ساعة`;
    if (diff < 604800) return `منذ ${Math.floor(diff / 86400)} يوم`;
    return new Date(d).toLocaleDateString('ar-EG');
}

function applyFilters() {
    router.get('/admin/activity-logs', {
        q: filter.value.q || undefined,
        action: filter.value.action || undefined,
        user: filter.value.user || undefined,
        from: filter.value.from || undefined,
        to: filter.value.to || undefined,
    }, { preserveState: true, preserveScroll: true });
}

function clearFilters() {
    filter.value = { q: '', action: '', user: '', from: '', to: '' };
    applyFilters();
}

/**
 * One-click "security view" chips. Each chip sets the action filter
 * to a specific event family so the admin can answer:
 *   - 'Has anything suspicious happened today?'      → portal.abuse_signal
 *   - 'What was erased under GDPR this quarter?'     → gdpr_delete + gdpr_export
 *   - 'Did anyone replay a webhook recently?'        → webhook_replay
 * without remembering the action string.
 */
const securityChips = [
    { label: 'إشارات إساءة (Portal)', action: 'portal.abuse_signal', color: 'rose' },
    { label: 'مسح بيانات GDPR', action: 'gdpr_delete', color: 'rose' },
    { label: 'تصدير بيانات GDPR', action: 'gdpr_export', color: 'amber' },
    { label: 'إعادة تشغيل Webhook', action: 'webhook_replay', color: 'amber' },
];

function applyChip(action) {
    filter.value = { q: '', action, user: '', from: '', to: '' };
    applyFilters();
}
</script>

<template>
    <Head title="سجل النشاط — لوحة التحكم" />

    <AdminLayout page-title="سجل النشاط (Audit Log)">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي السجلات</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                <p class="text-xs text-emerald-700">اليوم</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">{{ stats.today }}</p>
            </div>
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                <p class="text-xs text-blue-700">آخر 7 أيام</p>
                <p class="text-3xl font-bold text-blue-700 mt-1">{{ stats.week }}</p>
            </div>
            <div class="bg-gradient-to-br from-[#C4A265] to-[#D4B876] text-white rounded-2xl p-5">
                <p class="text-xs text-white/80">مستخدمون نشطون</p>
                <p class="text-3xl font-bold mt-1">{{ stats.users_active }}</p>
            </div>
        </div>

        <!-- Security quick-filter chips -->
        <div class="flex flex-wrap gap-2 mb-3">
            <span class="text-xs text-gray-500 self-center me-1">عرض سريع:</span>
            <button
                v-for="chip in securityChips"
                :key="chip.action"
                @click="applyChip(chip.action)"
                :class="[
                    'px-3 py-1.5 rounded-full text-xs font-semibold transition border',
                    filter.action === chip.action
                        ? (chip.color === 'rose'
                            ? 'bg-rose-600 border-rose-600 text-white'
                            : 'bg-amber-500 border-amber-500 text-white')
                        : (chip.color === 'rose'
                            ? 'bg-rose-50 border-rose-200 text-rose-800 hover:bg-rose-100'
                            : 'bg-amber-50 border-amber-200 text-amber-800 hover:bg-amber-100'),
                ]"
            >
                {{ chip.label }}
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input v-model="filter.q" @keydown.enter="applyFilters" placeholder="بحث في الوصف / المستخدم / العنصر" class="flex-1 min-w-[240px] px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <select v-model="filter.action" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل الإجراءات</option>
                <option v-for="a in actions" :key="a" :value="a">{{ a }}</option>
            </select>
            <select v-model="filter.user" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل المستخدمين</option>
                <option v-for="u in users" :key="u.id" :value="u.id">{{ u.name }}</option>
            </select>
            <input v-model="filter.from" @change="applyFilters" type="date" class="px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <input v-model="filter.to" @change="applyFilters" type="date" class="px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <button @click="clearFilters" class="px-3 py-2 text-sm text-gray-500 hover:text-[#1B4F72]">إعادة تعيين</button>
            <button @click="exportCsv" class="px-3 py-2 text-sm font-semibold text-[#1B4F72] hover:text-[#0A1628] flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                تصدير CSV
            </button>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div v-if="!logs.data.length" class="p-16 text-center text-gray-400">لا توجد سجلات مطابقة</div>
            <div v-else>
                <div
                    v-for="log in logs.data"
                    :key="log.id"
                    class="border-b border-gray-100 last:border-b-0"
                >
                    <!-- Summary row (always visible) -->
                    <div
                        class="flex items-start gap-4 px-5 py-4 hover:bg-gray-50/50 transition-colors cursor-pointer"
                        @click="toggleRow(log.id)"
                    >
                        <div
                            :class="[colorFor(log.action).bg, colorFor(log.action).text]"
                            class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg"
                        >
                            {{ colorFor(log.action).icon }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <span :class="[colorFor(log.action).bg, colorFor(log.action).text]" class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full">
                                    {{ log.action }}
                                </span>
                                <span v-if="log.subject_type" class="text-[10px] font-mono text-gray-400 px-2 py-0.5 rounded bg-gray-50">
                                    {{ subjectShort(log.subject_type) }}{{ log.subject_id ? ' #' + log.subject_id : '' }}
                                </span>
                                <span v-if="log.subject_label" class="text-xs text-gray-700 font-medium">
                                    {{ log.subject_label }}
                                </span>
                            </div>
                            <p v-if="log.description" class="text-sm text-gray-800 font-medium">
                                {{ log.description }}
                            </p>
                            <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <div class="w-5 h-5 rounded-full bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white flex items-center justify-center font-bold text-[10px]">
                                        {{ (log.user_name || '?')[0] }}
                                    </div>
                                    {{ log.user_name || 'النظام' }}
                                    <span v-if="log.user_role" class="text-gray-400">({{ log.user_role }})</span>
                                </span>
                                <span class="text-gray-300">•</span>
                                <span :title="fmtDateTime(log.created_at)">{{ relativeTime(log.created_at) }}</span>
                                <span v-if="log.ip_address" class="text-gray-300">•</span>
                                <span v-if="log.ip_address" class="font-mono text-[10px]">{{ log.ip_address }}</span>
                                <span v-if="changeRows(log).length" class="text-gray-300">•</span>
                                <span v-if="changeRows(log).length" class="text-[#1B4F72] font-semibold flex items-center gap-1">
                                    <svg class="w-3 h-3 transition-transform" :class="expandedRows.has(log.id) ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                    {{ changeRows(log).length }} {{ log.action === 'updated' ? 'تعديل' : 'حقل' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Expanded diff (lazy-rendered only when toggled) -->
                    <div v-if="expandedRows.has(log.id) && changeRows(log).length" class="px-5 pb-4 ms-14">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-gray-100 border-b border-gray-200">
                                        <th class="text-start px-3 py-2 text-[10px] uppercase tracking-wider text-gray-500 font-bold w-32">الحقل</th>
                                        <th v-if="log.action === 'updated'" class="text-start px-3 py-2 text-[10px] uppercase tracking-wider text-gray-500 font-bold">السابق</th>
                                        <th class="text-start px-3 py-2 text-[10px] uppercase tracking-wider text-gray-500 font-bold">{{ log.action === 'updated' ? 'الجديد' : 'القيمة' }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="change in changeRows(log)" :key="change.field" class="border-b border-gray-100 last:border-0">
                                        <td class="px-3 py-2 font-mono text-[11px] text-gray-700 align-top">{{ change.field }}</td>
                                        <td v-if="log.action === 'updated'" class="px-3 py-2 text-rose-700 line-through align-top break-all">
                                            {{ fmtValue(change.from) }}
                                        </td>
                                        <td class="px-3 py-2 text-emerald-700 align-top break-all">
                                            {{ fmtValue(change.kind === 'diff' ? change.to : change.value) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="logs.links && logs.links.length > 3" class="flex justify-center gap-1 p-4 border-t border-gray-100">
                <Link
                    v-for="link in logs.links"
                    :key="link.label"
                    :href="link.url || ''"
                    v-html="link.label"
                    preserve-scroll
                    class="px-3 py-1.5 rounded text-sm"
                    :class="[
                        link.active ? 'bg-[#1B4F72] text-white' : 'bg-gray-50 text-gray-600 hover:bg-gray-100',
                        !link.url ? 'opacity-40 pointer-events-none' : '',
                    ]"
                />
            </div>
        </div>
    </AdminLayout>
</template>
