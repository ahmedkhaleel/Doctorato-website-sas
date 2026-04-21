<script setup>
/**
 * Admin · Invoice details view.
 *
 * Renders a single invoice with its subscription + payment history.
 * Actions available here: print/PDF (server renders an HTML view the
 * browser can save), and refund from the attached payment row via
 * the Subscriptions flow.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    invoice: { type: Object, required: true },
});

const statusColors = {
    draft: 'bg-gray-100 text-gray-600',
    pending: 'bg-amber-100 text-amber-700',
    paid: 'bg-emerald-100 text-emerald-700',
    failed: 'bg-red-100 text-red-700',
    refunded: 'bg-purple-100 text-purple-700',
    cancelled: 'bg-gray-200 text-gray-500',
};

const statusLabels = {
    draft: 'مسودة', pending: 'قيد الانتظار', paid: 'مدفوعة',
    failed: 'فشل', refunded: 'مستردّة', cancelled: 'ملغاة',
};

function fmt(v) {
    return new Intl.NumberFormat('ar-EG', { minimumFractionDigits: 2 }).format(Number(v || 0));
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleString('ar-EG', {
        dateStyle: 'long', timeStyle: 'short',
    });
}

const lineItems = computed(() => Array.isArray(props.invoice.line_items) ? props.invoice.line_items : []);
</script>

<template>
    <Head :title="`فاتورة ${invoice.number} — لوحة التحكم`" />

    <AdminLayout :page-title="`فاتورة ${invoice.number}`">
        <div class="max-w-4xl space-y-6">
            <!-- Back link + actions -->
            <div class="flex items-center justify-between flex-wrap gap-3">
                <Link href="/admin/invoices" class="text-sm text-gray-500 hover:text-[#1B4F72] transition">
                    ← العودة للفواتير
                </Link>
                <div class="flex items-center gap-2">
                    <a
                        :href="`/admin/invoices/${invoice.id}/print`"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-50 transition"
                    >
                        🖨️ طباعة / PDF
                    </a>
                    <Link
                        v-if="invoice.subscription"
                        :href="`/admin/subscriptions/${invoice.subscription.id}`"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1B4F72] text-white rounded-lg text-sm font-semibold hover:bg-[#0A1628] transition"
                    >
                        عرض الاشتراك
                    </Link>
                </div>
            </div>

            <!-- Header card -->
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                <div class="flex items-start justify-between flex-wrap gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">رقم الفاتورة</p>
                        <p class="text-2xl font-extrabold text-gray-900 font-mono" dir="ltr">{{ invoice.number }}</p>
                        <p class="text-xs text-gray-500 mt-2">أُنشئت: {{ fmtDate(invoice.created_at) }}</p>
                        <p v-if="invoice.paid_at" class="text-xs text-emerald-600 mt-0.5">دُفعت: {{ fmtDate(invoice.paid_at) }}</p>
                    </div>
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold"
                        :class="statusColors[invoice.status] || statusColors.draft"
                    >
                        {{ statusLabels[invoice.status] || invoice.status }}
                    </span>
                </div>

                <!-- Customer block -->
                <div v-if="invoice.subscription" class="grid md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-gray-100">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">العميل</p>
                        <p class="font-semibold text-gray-800">{{ invoice.subscription.customer_name }}</p>
                        <p class="text-sm text-gray-500" dir="ltr">{{ invoice.subscription.customer_email }}</p>
                        <p v-if="invoice.subscription.customer_phone" class="text-sm text-gray-500" dir="ltr">{{ invoice.subscription.customer_phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">الاشتراك</p>
                        <p class="font-semibold text-gray-800">{{ invoice.subscription.plan?.name_ar || '—' }}</p>
                        <p class="text-sm text-gray-500 font-mono" dir="ltr">{{ invoice.subscription.reference }}</p>
                        <p class="text-sm text-gray-500">دورة: {{ invoice.subscription.billing_cycle === 'yearly' ? 'سنوي' : 'شهري' }}</p>
                    </div>
                </div>

                <!-- Line items -->
                <div class="mb-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">البنود</p>
                    <div v-if="lineItems.length === 0" class="text-center py-6 text-sm text-gray-400">
                        لا توجد بنود لهذه الفاتورة
                    </div>
                    <table v-else class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                <th class="text-start py-2.5">البند</th>
                                <th class="text-center py-2.5 w-20">الكمية</th>
                                <th class="text-end py-2.5 w-32">السعر</th>
                                <th class="text-end py-2.5 w-32">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, i) in lineItems" :key="i" class="border-b border-gray-50">
                                <td class="py-3 text-gray-700">{{ item.name }}</td>
                                <td class="py-3 text-center text-gray-500 tabular-nums">{{ item.qty || 1 }}</td>
                                <td class="py-3 text-end text-gray-500 tabular-nums">{{ fmt(item.unit_price) }}</td>
                                <td class="py-3 text-end font-semibold text-gray-800 tabular-nums">{{ fmt(item.total) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="space-y-1.5 text-sm max-w-sm ms-auto">
                    <div class="flex justify-between">
                        <span class="text-gray-500">الإجمالي الفرعي</span>
                        <span class="font-semibold text-gray-700 tabular-nums">{{ fmt(invoice.subtotal) }} {{ invoice.currency }}</span>
                    </div>
                    <div v-if="Number(invoice.setup_fee_amount) > 0" class="flex justify-between text-[#C4A265]">
                        <span>رسوم إعداد</span>
                        <span class="font-semibold tabular-nums">{{ fmt(invoice.setup_fee_amount) }} {{ invoice.currency }}</span>
                    </div>
                    <div v-if="Number(invoice.discount) > 0" class="flex justify-between text-emerald-600">
                        <span>خصم</span>
                        <span class="font-semibold tabular-nums">-{{ fmt(invoice.discount) }} {{ invoice.currency }}</span>
                    </div>
                    <div v-if="Number(invoice.tax) > 0" class="flex justify-between">
                        <span class="text-gray-500">ضريبة</span>
                        <span class="font-semibold text-gray-700 tabular-nums">{{ fmt(invoice.tax) }} {{ invoice.currency }}</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-gray-200 text-lg">
                        <span class="font-bold text-gray-900">الإجمالي</span>
                        <span class="font-extrabold text-[#1B4F72] tabular-nums">{{ fmt(invoice.total) }} {{ invoice.currency }}</span>
                    </div>
                </div>
            </section>

            <!-- Payments -->
            <section v-if="invoice.payments && invoice.payments.length" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                <h2 class="font-bold text-gray-800 mb-4">سجل المدفوعات</h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="text-start py-2.5">البوابة</th>
                            <th class="text-start py-2.5">المرجع</th>
                            <th class="text-start py-2.5">التاريخ</th>
                            <th class="text-end py-2.5">المبلغ</th>
                            <th class="text-center py-2.5">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in invoice.payments" :key="p.id" class="border-b border-gray-50">
                            <td class="py-3 text-gray-700 capitalize">{{ p.gateway }}</td>
                            <td class="py-3 text-gray-500 font-mono text-xs" dir="ltr">{{ p.gateway_reference || '—' }}</td>
                            <td class="py-3 text-gray-500">{{ fmtDate(p.processed_at) }}</td>
                            <td class="py-3 text-end font-semibold tabular-nums">{{ fmt(p.amount) }} {{ p.currency }}</td>
                            <td class="py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold"
                                      :class="p.status === 'succeeded' ? 'bg-emerald-100 text-emerald-700' :
                                              p.status === 'refunded' ? 'bg-purple-100 text-purple-700' :
                                              'bg-red-100 text-red-700'">
                                    {{ p.status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </div>
    </AdminLayout>
</template>
