<script setup>
/**
 * /admin/webhooks — inbound webhook events list.
 *
 * Recent events (most recent first), filterable by status + free-text
 * (matches order_id / gateway_id). Click a row to drill into the
 * payload + replay UI on /admin/webhooks/{id}.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    events: Object,           // paginated payload
    filters: Object,
    stats: Object,
});

const filter = ref({
    status: props.filters?.status ?? '',
    q: props.filters?.q ?? '',
});

function applyFilters() {
    router.get('/admin/webhooks', filter.value, { preserveState: true, preserveScroll: true });
}
function clearFilters() {
    filter.value = { status: '', q: '' };
    applyFilters();
}

function statusBadge(s) {
    switch (s) {
        case 'received':  return 'bg-blue-100 text-blue-800';
        case 'processed': return 'bg-emerald-100 text-emerald-800';
        case 'failed':    return 'bg-rose-100 text-rose-800';
        case 'replayed':  return 'bg-amber-100 text-amber-800';
        default:          return 'bg-gray-100 text-gray-600';
    }
}

function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
    });
}
</script>

<template>
    <Head title="أحداث Webhooks — لوحة التحكم" />
    <AdminLayout page-title="أحداث Webhooks">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي الأحداث</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                <p class="text-xs text-blue-700">مستقبلة</p>
                <p class="text-3xl font-bold text-blue-700 mt-1">{{ stats.received }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                <p class="text-xs text-emerald-700">معالَجة</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">{{ stats.processed }}</p>
            </div>
            <div class="bg-rose-50 rounded-2xl p-5 border border-rose-100">
                <p class="text-xs text-rose-700">فاشلة</p>
                <p class="text-3xl font-bold text-rose-700 mt-1">{{ stats.failed }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input
                v-model="filter.q"
                @keydown.enter="applyFilters"
                placeholder="ابحث بـ order_id / transaction_id"
                class="flex-1 min-w-[240px] px-4 py-2 border border-gray-200 rounded-lg text-sm"
            />
            <select v-model="filter.status" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل الحالات</option>
                <option value="received">received</option>
                <option value="processed">processed</option>
                <option value="failed">failed</option>
                <option value="replayed">replayed</option>
            </select>
            <button @click="clearFilters" class="px-3 py-2 text-sm text-gray-500 hover:text-[#1B4F72]">إعادة تعيين</button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div v-if="!events.data.length" class="p-16 text-center text-gray-400">
                لا توجد أحداث مطابقة
            </div>
            <table v-else class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-start text-xs font-semibold uppercase tracking-wider text-gray-600">
                        <th class="px-5 py-3 text-start">#</th>
                        <th class="px-5 py-3 text-start">الوقت</th>
                        <th class="px-5 py-3 text-start">النوع</th>
                        <th class="px-5 py-3 text-start">Order ID</th>
                        <th class="px-5 py-3 text-start">Transaction ID</th>
                        <th class="px-5 py-3 text-start">HMAC</th>
                        <th class="px-5 py-3 text-start">الحالة</th>
                        <th class="px-5 py-3 text-start">الكود</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="row in events.data"
                        :key="row.id"
                        class="hover:bg-gray-50 cursor-pointer"
                        @click="router.visit(`/admin/webhooks/${row.id}`)"
                    >
                        <td class="px-5 py-3 font-mono text-gray-500">#{{ row.id }}</td>
                        <td class="px-5 py-3 text-gray-700 whitespace-nowrap">{{ formatDate(row.received_at) }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ row.event_type || '—' }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ row.order_id || '—' }}</td>
                        <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ row.gateway_id || '—' }}</td>
                        <td class="px-5 py-3">
                            <span :class="row.hmac_valid ? 'text-emerald-600' : 'text-rose-600'" class="font-bold">
                                {{ row.hmac_valid ? '✓' : '✗' }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span :class="statusBadge(row.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                {{ row.status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 font-mono text-xs">{{ row.response_code || '—' }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="events.last_page > 1" class="border-t border-gray-100 p-4 flex items-center justify-between text-sm text-gray-500">
                <span>صفحة {{ events.current_page }} من {{ events.last_page }} — {{ events.total }} حدث</span>
                <div class="flex gap-2">
                    <Link
                        v-for="link in events.links"
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
