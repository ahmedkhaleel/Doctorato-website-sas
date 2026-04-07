<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: Object,
    trend: Array,
    recentDemos: Array,
    recentContacts: Array,
    recentPayments: { type: Array, default: () => [] },
    expiringSoon: { type: Array, default: () => [] },
});

function fmtMoney(v) {
    return new Intl.NumberFormat('ar-EG').format(Math.round(v || 0));
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('ar-EG', { day: '2-digit', month: 'short' });
}

function daysUntil(d) {
    if (!d) return null;
    const diff = Math.ceil((new Date(d).getTime() - Date.now()) / (1000 * 60 * 60 * 24));
    return diff;
}

const page = usePage();
const authUser = computed(() => page.props.auth?.user);

const greeting = computed(() => {
    const h = new Date().getHours();
    if (h < 12) return 'صباح الخير';
    if (h < 18) return 'مساء الخير';
    return 'مساء الخير';
});

const today = computed(() =>
    new Date().toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })
);

const mainCards = computed(() => [
    {
        key: 'demos',
        label: 'طلبات العرض',
        value: props.stats.demos,
        delta: props.stats.demos_this_week,
        deltaLabel: 'هذا الأسبوع',
        link: '/admin/demos',
        gradient: 'from-[#1B4F72] to-[#0D2B45]',
        icon: 'demo',
        accent: '#1B4F72',
    },
    {
        key: 'contacts',
        label: 'رسائل التواصل',
        value: props.stats.contacts,
        delta: props.stats.contacts_this_week,
        deltaLabel: 'هذا الأسبوع',
        link: '/admin/contacts',
        gradient: 'from-[#C4A265] to-[#B8925A]',
        icon: 'mail',
        accent: '#C4A265',
    },
    {
        key: 'subscribers',
        label: 'مشتركو النشرة',
        value: props.stats.subscribers,
        link: '/admin',
        gradient: 'from-emerald-500 to-teal-600',
        icon: 'mailbox',
        accent: '#10b981',
    },
    {
        key: 'users',
        label: 'المستخدمون',
        value: props.stats.users,
        link: '/admin/users',
        gradient: 'from-violet-500 to-purple-600',
        icon: 'users',
        accent: '#8b5cf6',
    },
]);

const contentCards = computed(() => [
    { label: 'الأسئلة الشائعة', value: props.stats.faqs, icon: 'faq', link: '/admin/faqs', color: '#3b82f6' },
    { label: 'خطط الأسعار', value: props.stats.plans, icon: 'price', link: '/admin/plans', color: '#10b981' },
    { label: 'الشهادات', value: props.stats.testimonials, icon: 'star', link: '/admin/testimonials', color: '#f59e0b' },
    { label: 'العملات', value: props.stats.currencies, icon: 'coin', link: '/admin/currencies', color: '#8b5cf6' },
]);

const maxTrend = computed(() =>
    Math.max(1, ...props.trend.flatMap((d) => [d.demos, d.contacts]))
);

function barHeight(value) {
    return (value / maxTrend.value) * 100;
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('ar-EG', { month: 'short', day: 'numeric' });
}

const statusLabels = {
    new: 'جديد', contacted: 'تم التواصل', demo_scheduled: 'مجدول',
    demo_done: 'مكتمل', converted: 'تم التحويل', lost: 'خسارة',
};
const statusColors = {
    new: 'bg-blue-100 text-blue-700', contacted: 'bg-yellow-100 text-yellow-700',
    demo_scheduled: 'bg-purple-100 text-purple-700', demo_done: 'bg-green-100 text-green-700',
    converted: 'bg-emerald-100 text-emerald-700', lost: 'bg-red-100 text-red-700',
};
</script>

<template>
    <AdminLayout>
        <Head title="لوحة التحكم" />

        <!-- Welcome header -->
        <div class="mb-8 relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white p-6 lg:p-8 shadow-xl">
            <!-- Decorative elements -->
            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 start-0 w-48 h-48 bg-[#1B4F72]/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>

            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 text-[#C4A265] text-sm mb-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ today }}</span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold mb-1">
                        {{ greeting }}، {{ authUser?.name || 'مدير النظام' }}
                    </h1>
                    <p class="text-white/70 text-sm">إليك نظرة سريعة على أحدث نشاطات الموقع</p>
                </div>

                <div class="flex gap-3">
                    <div v-if="stats.new_demos > 0" class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                        <div class="text-2xl font-bold text-[#C4A265]">{{ stats.new_demos }}</div>
                        <div class="text-[11px] text-white/70">طلبات جديدة</div>
                    </div>
                    <div v-if="stats.unread_contacts > 0" class="px-4 py-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/20">
                        <div class="text-2xl font-bold text-red-300">{{ stats.unread_contacts }}</div>
                        <div class="text-[11px] text-white/70">رسائل غير مقروءة</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue & Subscription cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- MRR -->
            <div class="relative bg-gradient-to-br from-[#1B4F72] via-[#0D2B45] to-[#0A1628] text-white rounded-2xl p-5 shadow-lg overflow-hidden">
                <div class="absolute top-0 end-0 w-32 h-32 bg-[#C4A265]/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <div class="relative">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#C4A265]/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="text-xs text-white/60 font-medium">MRR</span>
                    </div>
                    <div class="text-3xl font-extrabold tabular-nums mb-1">{{ fmtMoney(stats.mrr) }}</div>
                    <div class="text-xs text-white/60">جنيه مصري شهرياً</div>
                </div>
            </div>

            <!-- Month revenue -->
            <Link href="/admin/analytics" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">إيراد آخر 30 يوم</span>
                </div>
                <div class="text-3xl font-extrabold text-[#1C2833] tabular-nums mb-1">{{ fmtMoney(stats.month_revenue) }}</div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-400">ج.م</span>
                    <span v-if="stats.revenue_delta !== null"
                          :class="stats.revenue_delta >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50'"
                          class="px-2 py-0.5 rounded-full font-semibold">
                        {{ stats.revenue_delta >= 0 ? '▲' : '▼' }} {{ Math.abs(stats.revenue_delta) }}%
                    </span>
                </div>
            </Link>

            <!-- Active subscriptions -->
            <Link href="/admin/subscriptions?status=active" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#C4A265]/15 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">اشتراكات نشطة</span>
                </div>
                <div class="text-3xl font-extrabold text-[#1C2833] tabular-nums mb-1">{{ stats.active_subscriptions || 0 }}</div>
                <div class="text-xs text-gray-400">+{{ stats.new_subs_this_month || 0 }} هذا الشهر</div>
            </Link>

            <!-- Pending checkouts -->
            <Link href="/admin/subscriptions?status=pending" class="group bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">بانتظار الدفع</span>
                </div>
                <div class="text-3xl font-extrabold text-amber-700 tabular-nums mb-1">{{ stats.pending_subscriptions || 0 }}</div>
                <div class="text-xs text-gray-400">إجمالي مدفوع: {{ fmtMoney(stats.total_revenue) }} ج.م</div>
            </Link>
        </div>

        <!-- Main stat cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <Link
                v-for="card in mainCards"
                :key="card.key"
                :href="card.link"
                class="group relative bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden"
            >
                <!-- Gradient accent -->
                <div class="absolute top-0 end-0 w-32 h-32 rounded-full opacity-5 group-hover:opacity-10 transition-opacity -translate-y-1/2 translate-x-1/2 bg-gradient-to-br" :class="card.gradient"></div>
                <div class="absolute top-0 start-0 w-full h-1 bg-gradient-to-r opacity-0 group-hover:opacity-100 transition-opacity" :class="card.gradient"></div>

                <div class="relative">
                    <div class="flex items-start justify-between mb-4">
                        <div
                            class="w-12 h-12 rounded-xl flex items-center justify-center bg-gradient-to-br shadow-md group-hover:scale-110 group-hover:rotate-3 transition-all duration-500"
                            :class="card.gradient"
                        >
                            <svg v-if="card.icon === 'demo'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <svg v-else-if="card.icon === 'mail'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <svg v-else-if="card.icon === 'mailbox'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10.5h-1.586a1 1 0 00-.707.293l-1.414 1.414a1 1 0 01-.707.293h-8.172a1 1 0 01-.707-.293l-1.414-1.414a1 1 0 00-.707-.293H3M9 16h6" />
                            </svg>
                            <svg v-else-if="card.icon === 'users'" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <svg class="w-5 h-5 text-gray-300 group-hover:text-[#C4A265] group-hover:-translate-x-1 transition-all rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <div class="text-3xl font-extrabold text-[#1C2833] mb-1 tabular-nums">{{ card.value || 0 }}</div>
                    <div class="text-sm text-gray-500 mb-3">{{ card.label }}</div>

                    <div v-if="card.delta !== undefined" class="flex items-center gap-1.5 text-xs">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 font-semibold">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" />
                            </svg>
                            +{{ card.delta }}
                        </span>
                        <span class="text-gray-400">{{ card.deltaLabel }}</span>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Trend chart + Content cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Trend chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-base font-bold text-[#1C2833]">نشاط آخر 7 أيام</h3>
                        <p class="text-xs text-gray-500 mt-0.5">طلبات العرض ورسائل التواصل يومياً</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-[#1B4F72]"></div>
                            <span class="text-gray-600">طلبات العرض</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-[#C4A265]"></div>
                            <span class="text-gray-600">الرسائل</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-end justify-between gap-2 sm:gap-4 h-48">
                    <div v-for="(day, idx) in trend" :key="idx" class="flex-1 flex flex-col items-center gap-2">
                        <div class="flex-1 w-full flex items-end justify-center gap-1">
                            <div
                                class="flex-1 rounded-t-lg bg-gradient-to-t from-[#1B4F72] to-[#2471A3] transition-all duration-700 hover:opacity-80 relative group/bar min-h-[4px]"
                                :style="{ height: `${barHeight(day.demos)}%` }"
                            >
                                <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-[#1B4F72] opacity-0 group-hover/bar:opacity-100 transition-opacity">{{ day.demos }}</span>
                            </div>
                            <div
                                class="flex-1 rounded-t-lg bg-gradient-to-t from-[#C4A265] to-[#D4B876] transition-all duration-700 hover:opacity-80 relative group/bar min-h-[4px]"
                                :style="{ height: `${barHeight(day.contacts)}%` }"
                            >
                                <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-[#C4A265] opacity-0 group-hover/bar:opacity-100 transition-opacity">{{ day.contacts }}</span>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium">{{ day.label }}</span>
                    </div>
                </div>
            </div>

            <!-- Content cards -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-base font-bold text-[#1C2833] mb-1">المحتوى</h3>
                <p class="text-xs text-gray-500 mb-5">إحصائيات محتوى الموقع</p>

                <div class="space-y-3">
                    <Link
                        v-for="item in contentCards"
                        :key="item.label"
                        :href="item.link"
                        class="group flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-all"
                    >
                        <div
                            class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                            :style="{ backgroundColor: item.color + '15' }"
                        >
                            <svg v-if="item.icon === 'faq'" class="w-5 h-5" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <svg v-else-if="item.icon === 'price'" class="w-5 h-5" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <svg v-else-if="item.icon === 'star'" class="w-5 h-5" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <svg v-else class="w-5 h-5" :style="{ color: item.color }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-bold text-[#1C2833]">{{ item.label }}</div>
                        </div>
                        <div class="text-lg font-bold tabular-nums" :style="{ color: item.color }">{{ item.value || 0 }}</div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Recent activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent demos -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-[#1C2833]">أحدث طلبات العرض</h3>
                    <Link href="/admin/demos" class="text-xs text-[#C4A265] hover:underline font-semibold">عرض الكل ←</Link>
                </div>

                <div v-if="recentDemos.length === 0" class="text-center py-8 text-gray-400 text-sm">
                    لا توجد طلبات بعد
                </div>

                <div v-else class="space-y-2">
                    <Link
                        v-for="demo in recentDemos"
                        :key="demo.id"
                        href="/admin/demos"
                        class="group flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all"
                    >
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ demo.clinic_name?.[0]?.toUpperCase() || '?' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-[#1C2833] truncate">{{ demo.clinic_name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ demo.full_name }}</div>
                        </div>
                        <div class="text-end shrink-0">
                            <span :class="statusColors[demo.status]" class="px-2 py-0.5 rounded-full text-[10px] font-semibold">
                                {{ statusLabels[demo.status] }}
                            </span>
                            <div class="text-[10px] text-gray-400 mt-1">{{ formatDate(demo.created_at) }}</div>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- Recent contacts -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-[#1C2833]">أحدث الرسائل</h3>
                    <Link href="/admin/contacts" class="text-xs text-[#C4A265] hover:underline font-semibold">عرض الكل ←</Link>
                </div>

                <div v-if="recentContacts.length === 0" class="text-center py-8 text-gray-400 text-sm">
                    لا توجد رسائل بعد
                </div>

                <div v-else class="space-y-2">
                    <Link
                        v-for="contact in recentContacts"
                        :key="contact.id"
                        href="/admin/contacts"
                        class="group flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-all"
                    >
                        <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-[#C4A265] to-[#B8925A] text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ contact.name?.[0]?.toUpperCase() || '?' }}
                            <span v-if="!contact.is_read" class="absolute -top-1 -end-1 w-3 h-3 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-[#1C2833] truncate">{{ contact.name }}</div>
                            <div class="text-xs text-gray-500 truncate">{{ contact.subject }}</div>
                        </div>
                        <div class="text-[10px] text-gray-400 shrink-0">{{ formatDate(contact.created_at) }}</div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Subscription activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Recent payments -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-bold text-[#1C2833]">آخر المدفوعات</h3>
                        <p class="text-xs text-gray-500 mt-0.5">آخر 5 دفعات ناجحة</p>
                    </div>
                    <Link href="/admin/subscriptions" class="text-xs text-emerald-600 hover:underline font-semibold">عرض الكل ←</Link>
                </div>

                <div v-if="recentPayments.length === 0" class="text-center py-8 text-gray-400 text-sm">
                    لا توجد دفعات بعد
                </div>

                <div v-else class="space-y-2">
                    <div
                        v-for="p in recentPayments"
                        :key="p.id"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-emerald-50/50 transition-all border border-transparent hover:border-emerald-100"
                    >
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-[#1C2833] truncate">{{ p.subscription?.customer_name || 'عميل' }}</div>
                            <div class="text-[11px] text-gray-500 font-mono">{{ p.subscription?.reference }}</div>
                        </div>
                        <div class="text-end shrink-0">
                            <div class="text-sm font-bold text-emerald-600 tabular-nums">+{{ fmtMoney(p.amount) }} {{ p.currency }}</div>
                            <div class="text-[10px] text-gray-400">{{ fmtDate(p.processed_at) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expiring soon -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-base font-bold text-[#1C2833]">اشتراكات على وشك الانتهاء</h3>
                        <p class="text-xs text-gray-500 mt-0.5">خلال الـ 7 أيام القادمة</p>
                    </div>
                    <Link href="/admin/subscriptions?status=active" class="text-xs text-amber-600 hover:underline font-semibold">عرض الكل ←</Link>
                </div>

                <div v-if="expiringSoon.length === 0" class="text-center py-8 text-gray-400 text-sm">
                    🎉 لا توجد اشتراكات على وشك الانتهاء
                </div>

                <div v-else class="space-y-2">
                    <div
                        v-for="sub in expiringSoon"
                        :key="sub.id"
                        class="flex items-center gap-3 p-3 rounded-xl hover:bg-amber-50/50 transition-all border border-transparent hover:border-amber-100"
                    >
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                            {{ daysUntil(sub.ends_at) }}d
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-bold text-[#1C2833] truncate">{{ sub.customer_name }}</div>
                            <div class="text-[11px] text-gray-500 truncate">{{ sub.plan?.name_ar || sub.plan?.name_en }} · {{ sub.billing_cycle === 'yearly' ? 'سنوي' : 'شهري' }}</div>
                        </div>
                        <div class="text-end shrink-0">
                            <div class="text-sm font-bold text-amber-700 tabular-nums">{{ fmtMoney(sub.amount) }}</div>
                            <div class="text-[10px] text-gray-400">{{ fmtDate(sub.ends_at) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
