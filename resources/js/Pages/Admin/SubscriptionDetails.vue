<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    subscription: { type: Object, required: true },
});

const showCancelConfirm = ref(false);

const statusLabels = {
    pending: 'بانتظار الدفع',
    active: 'نشط',
    past_due: 'متأخر',
    cancelled: 'ملغي',
    expired: 'منتهي',
};

const statusColors = {
    pending: 'bg-amber-100 text-amber-700 border-amber-200',
    active: 'bg-emerald-100 text-emerald-700 border-emerald-200',
    past_due: 'bg-orange-100 text-orange-700 border-orange-200',
    cancelled: 'bg-gray-100 text-gray-600 border-gray-200',
    expired: 'bg-red-100 text-red-700 border-red-200',
};

const invoiceStatusColors = {
    draft: 'bg-gray-100 text-gray-600',
    pending: 'bg-amber-100 text-amber-700',
    paid: 'bg-emerald-100 text-emerald-700',
    failed: 'bg-red-100 text-red-700',
    refunded: 'bg-purple-100 text-purple-700',
    cancelled: 'bg-gray-100 text-gray-600',
};

const paymentStatusColors = {
    initiated: 'bg-gray-100 text-gray-600',
    pending: 'bg-amber-100 text-amber-700',
    succeeded: 'bg-emerald-100 text-emerald-700',
    failed: 'bg-red-100 text-red-700',
    refunded: 'bg-purple-100 text-purple-700',
};

const totalPaid = computed(() =>
    (props.subscription.payments || [])
        .filter((p) => p.status === 'succeeded')
        .reduce((s, p) => s + Number(p.amount || 0), 0)
);

const daysRemaining = computed(() => {
    if (!props.subscription.ends_at) return null;
    const diff = Math.ceil((new Date(props.subscription.ends_at).getTime() - Date.now()) / (1000 * 60 * 60 * 24));
    return diff;
});

function fmtMoney(v) {
    return new Intl.NumberFormat('ar-EG').format(Math.round(v || 0));
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('ar-EG', { day: '2-digit', month: 'long', year: 'numeric' });
}

function fmtDateTime(d) {
    if (!d) return '—';
    return new Date(d).toLocaleString('ar-EG', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function cancelSubscription() {
    router.post(
        `/admin/subscriptions/${props.subscription.id}/cancel`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                showCancelConfirm.value = false;
            },
        }
    );
}

function copyReference() {
    navigator.clipboard?.writeText(props.subscription.reference);
}
</script>

<template>
    <Head :title="`${subscription.reference} — تفاصيل الاشتراك`" />

    <AdminLayout :page-title="`اشتراك ${subscription.reference}`">
        <!-- Back link -->
        <Link href="/admin/subscriptions" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#1B4F72] mb-4 transition-colors">
            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            العودة لكل الاشتراكات
        </Link>

        <!-- Hero -->
        <div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white p-6 lg:p-8 shadow-xl">
            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-pulse-slow"></div>
            <div class="absolute bottom-0 start-0 w-48 h-48 bg-[#1B4F72]/30 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 animate-pulse-slow" style="animation-delay: -3s"></div>
            <svg class="absolute inset-0 w-full h-full opacity-[0.04] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="sub-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#sub-hex)" />
            </svg>

            <div class="relative grid lg:grid-cols-3 gap-6">
                <!-- Customer info -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-2">
                        <span :class="statusColors[subscription.status]" class="px-3 py-1 rounded-full text-xs font-bold border">
                            {{ statusLabels[subscription.status] }}
                        </span>
                        <button @click="copyReference" class="text-xs text-[#C4A265] font-mono hover:underline flex items-center gap-1">
                            {{ subscription.reference }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        </button>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold mb-1">{{ subscription.customer_name }}</h1>
                    <p class="text-white/60 text-sm mb-4">{{ subscription.customer_email }} · {{ subscription.customer_phone }}</p>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div>
                            <p class="text-[10px] text-white/50 uppercase tracking-widest mb-1">الباقة</p>
                            <p class="font-bold text-sm">{{ subscription.plan?.name_ar || '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-white/50 uppercase tracking-widest mb-1">الدورة</p>
                            <p class="font-bold text-sm">{{ subscription.billing_cycle === 'yearly' ? 'سنوي' : 'شهري' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-white/50 uppercase tracking-widest mb-1">يبدأ في</p>
                            <p class="font-bold text-sm">{{ fmtDate(subscription.starts_at) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-white/50 uppercase tracking-widest mb-1">ينتهي في</p>
                            <p class="font-bold text-sm">{{ fmtDate(subscription.ends_at) }}</p>
                        </div>
                    </div>

                    <div v-if="daysRemaining !== null && subscription.status === 'active'" class="mt-4 inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs">
                        <span v-if="daysRemaining > 7" class="text-emerald-300">{{ daysRemaining }} يوم متبقي</span>
                        <span v-else-if="daysRemaining > 0" class="text-amber-300">⚠️ {{ daysRemaining }} يوم متبقي</span>
                        <span v-else class="text-red-300">⚠️ انتهى</span>
                    </div>
                </div>

                <!-- Amount card -->
                <div class="bg-white/[0.08] backdrop-blur-sm rounded-2xl border border-white/10 p-5">
                    <p class="text-[10px] text-white/50 uppercase tracking-widest mb-1">المبلغ</p>
                    <p class="text-3xl font-black text-white tabular-nums">{{ fmtMoney(subscription.amount) }}</p>
                    <p class="text-xs text-white/60 mt-1">{{ subscription.currency }} / {{ subscription.billing_cycle === 'yearly' ? 'سنة' : 'شهر' }}</p>
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <p class="text-[10px] text-white/50 uppercase tracking-widest mb-1">إجمالي مدفوع</p>
                        <p class="text-xl font-bold text-emerald-300 tabular-nums">{{ fmtMoney(totalPaid) }} <span class="text-xs font-normal">{{ subscription.currency }}</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a :href="`mailto:${subscription.customer_email}`" class="px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                إرسال إيميل
            </a>
            <a :href="`https://wa.me/${subscription.customer_phone?.replace(/[^0-9]/g, '')}`" target="_blank" class="px-4 py-2 bg-emerald-50 border border-emerald-200 rounded-xl text-sm font-semibold text-emerald-700 hover:bg-emerald-100 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                واتساب
            </a>
            <button
                v-if="subscription.status === 'active'"
                @click="showCancelConfirm = true"
                class="px-4 py-2 bg-red-50 border border-red-200 rounded-xl text-sm font-semibold text-red-700 hover:bg-red-100 transition flex items-center gap-2 ms-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/></svg>
                إلغاء الاشتراك
            </button>
        </div>

        <!-- Two columns: customer details + invoices/payments -->
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Customer details sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">بيانات العميل</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-gray-500">الاسم</dt>
                            <dd class="font-semibold text-gray-800">{{ subscription.customer_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">البريد الإلكتروني</dt>
                            <dd class="font-semibold text-gray-800 break-all" dir="ltr">{{ subscription.customer_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">الهاتف</dt>
                            <dd class="font-semibold text-gray-800" dir="ltr">{{ subscription.customer_phone }}</dd>
                        </div>
                        <div v-if="subscription.clinic_name">
                            <dt class="text-xs text-gray-500">العيادة</dt>
                            <dd class="font-semibold text-gray-800">{{ subscription.clinic_name }}</dd>
                        </div>
                        <div v-if="subscription.city">
                            <dt class="text-xs text-gray-500">المدينة</dt>
                            <dd class="font-semibold text-gray-800">{{ subscription.city }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-500">الدولة</dt>
                            <dd class="font-semibold text-gray-800">{{ subscription.country }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="text-sm font-bold text-gray-800 mb-4">معلومات الاشتراك</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-xs text-gray-500">أُنشئ في</dt>
                            <dd class="font-semibold text-gray-800">{{ fmtDateTime(subscription.created_at) }}</dd>
                        </div>
                        <div v-if="subscription.starts_at" class="flex justify-between">
                            <dt class="text-xs text-gray-500">بدأ في</dt>
                            <dd class="font-semibold text-gray-800">{{ fmtDateTime(subscription.starts_at) }}</dd>
                        </div>
                        <div v-if="subscription.ends_at" class="flex justify-between">
                            <dt class="text-xs text-gray-500">ينتهي في</dt>
                            <dd class="font-semibold text-gray-800">{{ fmtDateTime(subscription.ends_at) }}</dd>
                        </div>
                        <div v-if="subscription.cancelled_at" class="flex justify-between">
                            <dt class="text-xs text-gray-500">أُلغي في</dt>
                            <dd class="font-semibold text-red-600">{{ fmtDateTime(subscription.cancelled_at) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Invoices + Payments -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Invoices -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">الفواتير ({{ subscription.invoices?.length || 0 }})</h3>
                    </div>
                    <div v-if="!subscription.invoices?.length" class="p-8 text-center text-gray-400 text-sm">لا توجد فواتير</div>
                    <div v-else>
                        <div
                            v-for="invoice in subscription.invoices"
                            :key="invoice.id"
                            class="flex items-center gap-4 px-5 py-4 border-t border-gray-100 hover:bg-gray-50 transition-colors first:border-t-0"
                        >
                            <div class="w-10 h-10 rounded-xl bg-[#1B4F72]/5 flex items-center justify-center text-[#1B4F72]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="font-mono text-xs text-gray-600">{{ invoice.number }}</p>
                                    <span :class="invoiceStatusColors[invoice.status]" class="px-2 py-0.5 rounded-full text-[10px] font-bold">
                                        {{ invoice.status }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400">{{ fmtDate(invoice.paid_at || invoice.created_at) }}</p>
                            </div>
                            <div class="text-end">
                                <p class="font-bold text-gray-800 tabular-nums">{{ fmtMoney(invoice.total) }} {{ invoice.currency }}</p>
                                <p v-if="invoice.discount > 0" class="text-[10px] text-emerald-600">خصم -{{ fmtMoney(invoice.discount) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments -->
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h3 class="font-bold text-gray-800">سجل المدفوعات ({{ subscription.payments?.length || 0 }})</h3>
                    </div>
                    <div v-if="!subscription.payments?.length" class="p-8 text-center text-gray-400 text-sm">لا توجد مدفوعات</div>
                    <div v-else>
                        <div
                            v-for="payment in subscription.payments"
                            :key="payment.id"
                            class="flex items-center gap-4 px-5 py-4 border-t border-gray-100 hover:bg-gray-50 transition-colors first:border-t-0"
                        >
                            <div
                                class="w-10 h-10 rounded-xl flex items-center justify-center"
                                :class="payment.status === 'succeeded' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-50 text-gray-400'"
                            >
                                <svg v-if="payment.status === 'succeeded'" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <p class="text-sm font-semibold text-gray-700">{{ payment.gateway }}</p>
                                    <span :class="paymentStatusColors[payment.status]" class="px-2 py-0.5 rounded-full text-[10px] font-bold">
                                        {{ payment.status }}
                                    </span>
                                    <span v-if="payment.payment_method" class="text-[10px] text-gray-400 uppercase">{{ payment.payment_method }}</span>
                                </div>
                                <p class="text-xs text-gray-400">
                                    {{ fmtDateTime(payment.processed_at || payment.created_at) }}
                                    <span v-if="payment.gateway_transaction_id" class="ms-2 font-mono">#{{ payment.gateway_transaction_id }}</span>
                                </p>
                                <p v-if="payment.failure_reason" class="text-xs text-red-500 mt-0.5">{{ payment.failure_reason }}</p>
                            </div>
                            <div class="text-end">
                                <p class="font-bold tabular-nums" :class="payment.status === 'succeeded' ? 'text-emerald-600' : 'text-gray-500'">
                                    {{ payment.status === 'succeeded' ? '+' : '' }}{{ fmtMoney(payment.amount) }} {{ payment.currency }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cancel confirmation dialog -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showCancelConfirm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="showCancelConfirm = false">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-fade-up">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <h3 class="text-center text-lg font-bold text-gray-800 mb-2">إلغاء الاشتراك؟</h3>
                        <p class="text-center text-sm text-gray-500 mb-2">{{ subscription.customer_name }} — {{ subscription.reference }}</p>
                        <p class="text-center text-xs text-red-500 mb-6">سيتوقف الوصول للخدمة فوراً. لا يمكن التراجع.</p>
                        <div class="flex gap-3">
                            <button @click="showCancelConfirm = false" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">تراجع</button>
                            <button @click="cancelSubscription" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-colors">نعم، ألغِ</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AdminLayout>
</template>
