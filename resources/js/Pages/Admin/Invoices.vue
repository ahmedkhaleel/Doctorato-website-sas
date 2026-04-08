<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    invoices: { type: Object, required: true },
    stats: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const filter = ref({
    q: props.filters?.q ?? '',
    status: props.filters?.status ?? '',
    from: props.filters?.from ?? '',
    to: props.filters?.to ?? '',
});

const statusLabels = {
    draft: 'مسودة',
    pending: 'قيد الانتظار',
    paid: 'مدفوعة',
    failed: 'فشلت',
    refunded: 'مستردة',
    cancelled: 'ملغاة',
};

const statusColors = {
    draft: 'bg-gray-100 text-gray-600',
    pending: 'bg-amber-100 text-amber-700',
    paid: 'bg-emerald-100 text-emerald-700',
    failed: 'bg-red-100 text-red-700',
    refunded: 'bg-purple-100 text-purple-700',
    cancelled: 'bg-gray-100 text-gray-600',
};

function fmtMoney(v) {
    return new Intl.NumberFormat('ar-EG').format(Math.round(v || 0));
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('ar-EG', { day: '2-digit', month: 'short', year: 'numeric' });
}

function applyFilters() {
    router.get(
        '/admin/invoices',
        {
            q: filter.value.q || undefined,
            status: filter.value.status || undefined,
            from: filter.value.from || undefined,
            to: filter.value.to || undefined,
        },
        { preserveState: true, preserveScroll: true }
    );
}

function clearFilters() {
    filter.value = { q: '', status: '', from: '', to: '' };
    applyFilters();
}

function printInvoice(id) {
    window.open(`/admin/invoices/${id}/print`, '_blank', 'width=900,height=700');
}
</script>

<template>
    <Head title="الفواتير — لوحة التحكم" />

    <AdminLayout page-title="الفواتير">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
            <div class="bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white rounded-2xl p-5 col-span-2">
                <p class="text-xs text-white/70">إجمالي المدفوع</p>
                <p class="text-3xl font-black mt-1 tabular-nums">{{ fmtMoney(stats.total_revenue) }} <span class="text-sm font-normal">ج.م</span></p>
            </div>
            <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 col-span-2">
                <p class="text-xs text-amber-700">بانتظار الدفع</p>
                <p class="text-3xl font-black text-amber-700 mt-1 tabular-nums">{{ fmtMoney(stats.pending_amount) }} <span class="text-sm font-normal">ج.م</span></p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                <p class="text-xs text-emerald-700">مدفوعة</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">{{ stats.paid }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input v-model="filter.q" @keydown.enter="applyFilters" placeholder="بحث برقم الفاتورة / العميل / مرجع الاشتراك" class="flex-1 min-w-[240px] px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <select v-model="filter.status" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل الحالات</option>
                <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
            </select>
            <input v-model="filter.from" @change="applyFilters" type="date" class="px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <input v-model="filter.to" @change="applyFilters" type="date" class="px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <button @click="clearFilters" class="px-3 py-2 text-sm text-gray-500 hover:text-[#1B4F72]">إعادة تعيين</button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-right">رقم الفاتورة</th>
                            <th class="px-4 py-3 text-right">العميل</th>
                            <th class="px-4 py-3 text-right">المبلغ</th>
                            <th class="px-4 py-3 text-right">الحالة</th>
                            <th class="px-4 py-3 text-right">التاريخ</th>
                            <th class="px-4 py-3 text-right">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="invoice in invoices.data" :key="invoice.id" class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs font-bold text-[#1B4F72]">{{ invoice.number }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ invoice.subscription?.customer_name || '—' }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ invoice.subscription?.reference }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800 tabular-nums">{{ fmtMoney(invoice.total) }} {{ invoice.currency }}</div>
                                <div v-if="invoice.discount > 0" class="text-[10px] text-emerald-600">خصم {{ fmtMoney(invoice.discount) }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="statusColors[invoice.status]" class="inline-block px-2 py-1 rounded-full text-xs font-bold">
                                    {{ statusLabels[invoice.status] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ fmtDate(invoice.paid_at || invoice.created_at) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button
                                        @click="printInvoice(invoice.id)"
                                        title="طباعة / حفظ كـ PDF"
                                        class="inline-flex items-center gap-1 text-xs text-[#1B4F72] hover:underline"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        PDF
                                    </button>
                                    <Link v-if="invoice.subscription" :href="`/admin/subscriptions/${invoice.subscription_id}`" class="text-xs text-emerald-600 hover:underline">عرض الاشتراك</Link>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!invoices.data.length">
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">لا توجد فواتير مطابقة</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="invoices.links && invoices.links.length > 3" class="flex justify-center gap-1 p-4 border-t border-gray-100">
                <Link
                    v-for="link in invoices.links"
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
