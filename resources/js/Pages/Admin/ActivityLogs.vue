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
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div v-if="!logs.data.length" class="p-16 text-center text-gray-400">لا توجد سجلات مطابقة</div>
            <div v-else>
                <div
                    v-for="log in logs.data"
                    :key="log.id"
                    class="flex items-start gap-4 px-5 py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50/50 transition-colors"
                >
                    <!-- Action icon -->
                    <div
                        :class="[colorFor(log.action).bg, colorFor(log.action).text]"
                        class="shrink-0 w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg"
                    >
                        {{ colorFor(log.action).icon }}
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span :class="[colorFor(log.action).bg, colorFor(log.action).text]" class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full">
                                {{ log.action }}
                            </span>
                            <span v-if="log.subject_type" class="text-[10px] font-mono text-gray-400 px-2 py-0.5 rounded bg-gray-50">
                                {{ subjectShort(log.subject_type) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-800 font-medium">
                            {{ log.description || '—' }}
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
