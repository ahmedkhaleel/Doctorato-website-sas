<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    subscriptions: Object,
    stats: Object,
    filters: Object,
});

const q = ref(props.filters?.q ?? '');
const status = ref(props.filters?.status ?? '');

const statusLabels = {
    pending: 'بانتظار الدفع',
    active: 'نشط',
    past_due: 'متأخر',
    cancelled: 'ملغي',
    expired: 'منتهي',
};

const statusColors = {
    pending: 'bg-amber-100 text-amber-700',
    active: 'bg-emerald-100 text-emerald-700',
    past_due: 'bg-orange-100 text-orange-700',
    cancelled: 'bg-gray-100 text-gray-600',
    expired: 'bg-red-100 text-red-700',
};

function applyFilters() {
    router.get('/admin/subscriptions', { q: q.value || undefined, status: status.value || undefined }, {
        preserveState: true,
        preserveScroll: true,
    });
}

function cancel(sub) {
    if (!confirm(`إلغاء اشتراك ${sub.customer_name}?`)) return;
    router.post(`/admin/subscriptions/${sub.id}/cancel`, {}, { preserveScroll: true });
}

function fmtMoney(v) {
    return new Intl.NumberFormat('ar-EG').format(Math.round(v || 0));
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('ar-EG', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="الاشتراكات — لوحة التحكم" />

    <AdminLayout page-title="الاشتراكات والمدفوعات">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي الاشتراكات</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <p class="text-xs text-emerald-700">نشط</p>
                <p class="text-2xl font-bold text-emerald-700 mt-1">{{ stats.active }}</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                <p class="text-xs text-amber-700">بانتظار الدفع</p>
                <p class="text-2xl font-bold text-amber-700 mt-1">{{ stats.pending }}</p>
            </div>
            <div class="bg-orange-50 rounded-xl p-4 border border-orange-100">
                <p class="text-xs text-orange-700">متأخر</p>
                <p class="text-2xl font-bold text-orange-700 mt-1">{{ stats.past_due }}</p>
            </div>
            <div class="bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white rounded-xl p-4 col-span-2">
                <p class="text-xs text-white/70">الإيراد الشهري (MRR)</p>
                <p class="text-2xl font-bold mt-1">{{ fmtMoney(stats.mrr) }} <span class="text-sm font-normal">ج.م</span></p>
                <p class="text-xs text-white/60 mt-1">إجمالي مدفوع: {{ fmtMoney(stats.total_revenue) }} ج.م</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input
                v-model="q"
                @keydown.enter="applyFilters"
                type="text"
                placeholder="بحث بالاسم/البريد/رقم الاشتراك"
                class="flex-1 min-w-[220px] px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 outline-none"
            />
            <select v-model="status" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل الحالات</option>
                <option v-for="(label, value) in statusLabels" :key="value" :value="value">{{ label }}</option>
            </select>
            <button @click="applyFilters" class="px-4 py-2 bg-[#1B4F72] text-white rounded-lg text-sm font-medium">تصفية</button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-right">الرقم</th>
                            <th class="px-4 py-3 text-right">العميل</th>
                            <th class="px-4 py-3 text-right">الباقة</th>
                            <th class="px-4 py-3 text-right">الدورة</th>
                            <th class="px-4 py-3 text-right">المبلغ</th>
                            <th class="px-4 py-3 text-right">الحالة</th>
                            <th class="px-4 py-3 text-right">ينتهي في</th>
                            <th class="px-4 py-3 text-right">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sub in subscriptions.data" :key="sub.id" class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs">{{ sub.reference }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ sub.customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ sub.customer_email }}</div>
                            </td>
                            <td class="px-4 py-3">{{ sub.plan?.name_ar || sub.plan?.name_en || '—' }}</td>
                            <td class="px-4 py-3">{{ sub.billing_cycle === 'yearly' ? 'سنوي' : 'شهري' }}</td>
                            <td class="px-4 py-3 font-semibold">{{ fmtMoney(sub.amount) }} {{ sub.currency }}</td>
                            <td class="px-4 py-3">
                                <span :class="statusColors[sub.status]" class="inline-block px-2 py-1 rounded-full text-xs font-medium">
                                    {{ statusLabels[sub.status] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ fmtDate(sub.ends_at) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <Link :href="`/admin/subscriptions/${sub.id}`" class="text-xs text-[#1B4F72] hover:underline">عرض</Link>
                                    <button
                                        v-if="sub.status === 'active'"
                                        @click="cancel(sub)"
                                        class="text-xs text-red-600 hover:underline"
                                    >إلغاء</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!subscriptions.data.length">
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400">لا توجد اشتراكات بعد</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="subscriptions.links && subscriptions.links.length > 3" class="flex justify-center gap-1 p-4 border-t border-gray-100">
                <Link
                    v-for="link in subscriptions.links"
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
