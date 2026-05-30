<script setup>
/**
 * /admin/email-logs — outbound email visibility.
 *
 * Read-only. Recipient emails are stored hashed in the DB; the
 * admin can search by full address (we hash on submit and look up
 * by hashed_recipient) but the dashboard only ever displays the
 * 'j***@example.com' redacted form.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    logs: Object,
    filters: Object,
    stats: Object,
    classes: Array,
});

const filter = ref({
    status: props.filters?.status ?? '',
    class: props.filters?.class ?? '',
    email: props.filters?.email ?? '',
});

function applyFilters() {
    router.get('/admin/email-logs', {
        status: filter.value.status || undefined,
        class: filter.value.class || undefined,
        email: filter.value.email || undefined,
    }, { preserveState: true, preserveScroll: true });
}

function clearFilters() {
    filter.value = { status: '', class: '', email: '' };
    applyFilters();
}

function statusBadge(s) {
    switch (s) {
        case 'queued':  return 'bg-blue-100 text-blue-800';
        case 'sending': return 'bg-amber-100 text-amber-800';
        case 'sent':    return 'bg-emerald-100 text-emerald-800';
        case 'failed':  return 'bg-rose-100 text-rose-800';
        default:        return 'bg-gray-100 text-gray-600';
    }
}

function classBasename(fqcn) {
    if (!fqcn) return '—';
    const parts = fqcn.split('\\');
    return parts[parts.length - 1];
}

function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleString('en-GB', {
        day: 'numeric', month: 'short',
        hour: '2-digit', minute: '2-digit',
    });
}
</script>

<template>
    <Head title="سجل البريد المُرسَل — لوحة التحكم" />
    <AdminLayout page-title="سجل البريد المُرسَل">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي السجلات</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                <p class="text-xs text-blue-700">اليوم</p>
                <p class="text-3xl font-bold text-blue-700 mt-1">{{ stats.today }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                <p class="text-xs text-emerald-700">مُرسَلة (7 أيام)</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">{{ stats.sent_7d }}</p>
            </div>
            <div class="bg-rose-50 rounded-2xl p-5 border border-rose-100">
                <p class="text-xs text-rose-700">فاشلة (7 أيام)</p>
                <p class="text-3xl font-bold text-rose-700 mt-1">{{ stats.failed_7d }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input
                v-model="filter.email"
                @keydown.enter="applyFilters"
                type="email"
                placeholder="ابحث بالبريد الكامل (يُجزَّأ خادمياً)"
                class="flex-1 min-w-[240px] px-4 py-2 border border-gray-200 rounded-lg text-sm"
            />
            <select v-model="filter.status" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل الحالات</option>
                <option value="queued">queued</option>
                <option value="sending">sending</option>
                <option value="sent">sent</option>
                <option value="failed">failed</option>
            </select>
            <select v-model="filter.class" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل الأنواع</option>
                <option v-for="c in classes" :key="c" :value="c">{{ classBasename(c) }}</option>
            </select>
            <button @click="applyFilters" class="px-3 py-2 text-sm font-semibold text-white bg-[#0A1628] hover:bg-[#1C2833] rounded-lg">
                تطبيق
            </button>
            <button @click="clearFilters" class="px-3 py-2 text-sm text-gray-500 hover:text-[#1B4F72]">إعادة تعيين</button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div v-if="!logs.data.length" class="p-16 text-center text-gray-400">
                لا توجد رسائل مطابقة
            </div>
            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-start text-xs font-semibold uppercase tracking-wider text-gray-600">
                        <th class="px-5 py-3 text-start">#</th>
                        <th class="px-5 py-3 text-start">الوقت</th>
                        <th class="px-5 py-3 text-start">المستلم</th>
                        <th class="px-5 py-3 text-start">النوع</th>
                        <th class="px-5 py-3 text-start">العنوان</th>
                        <th class="px-5 py-3 text-start">الحالة</th>
                        <th class="px-5 py-3 text-start">الإرسال</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="row in logs.data" :key="row.id" class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono text-gray-500">#{{ row.id }}</td>
                        <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ formatDate(row.queued_at) }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-700">{{ row.recipient_display || '—' }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ classBasename(row.mailable_class) }}</td>
                        <td class="px-5 py-3 text-gray-700 max-w-xs truncate" :title="row.subject">{{ row.subject || '—' }}</td>
                        <td class="px-5 py-3">
                            <span :class="statusBadge(row.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ row.status }}
                            </span>
                            <p v-if="row.error" class="text-[10px] text-rose-700 mt-1 max-w-xs truncate" :title="row.error">{{ row.error }}</p>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                            {{ row.sent_at ? formatDate(row.sent_at) : (row.failed_at ? formatDate(row.failed_at) : '—') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="logs.last_page > 1" class="border-t border-gray-100 p-4 flex items-center justify-between text-sm text-gray-500">
                <span>صفحة {{ logs.current_page }} من {{ logs.last_page }} — {{ logs.total }} رسالة</span>
                <div class="flex gap-2">
                    <Link
                        v-for="link in logs.links"
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
