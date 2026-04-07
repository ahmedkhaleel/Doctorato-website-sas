<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ demos: Array, trialStats: Object });

const selectedDemo = ref(null);
const demoToDelete = ref(null);
const search = ref('');
const statusFilter = ref('all');
const trialFilter = ref('all'); // all | active | ending_soon | expired | reminders

const statusForm = useForm({ status: '', admin_notes: '' });
const extendForm = useForm({ days: 7 });

const statusLabels = {
    new: 'جديد', contacted: 'تم التواصل', demo_scheduled: 'عرض مجدول',
    demo_done: 'عرض مكتمل', converted: 'تم التحويل', lost: 'خسارة',
};
const statusColors = {
    new: 'from-blue-500 to-blue-600',
    contacted: 'from-amber-500 to-yellow-600',
    demo_scheduled: 'from-purple-500 to-indigo-600',
    demo_done: 'from-emerald-500 to-green-600',
    converted: 'from-teal-500 to-cyan-600',
    lost: 'from-red-500 to-rose-600',
};

const filteredDemos = computed(() => {
    let list = props.demos || [];
    if (statusFilter.value !== 'all') list = list.filter(d => d.status === statusFilter.value);
    if (trialFilter.value === 'active') list = list.filter(d => d.is_trial_active);
    if (trialFilter.value === 'ending_soon') list = list.filter(d => d.is_trial_ending_soon);
    if (trialFilter.value === 'expired') list = list.filter(d => d.trial_status === 'expired');
    if (trialFilter.value === 'reminders') list = list.filter(d => d.trial_status === 'expired' && !d.admin_reminder_seen);
    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        list = list.filter(d =>
            (d.clinic_name || '').toLowerCase().includes(q) ||
            (d.full_name || '').toLowerCase().includes(q) ||
            (d.email || '').toLowerCase().includes(q)
        );
    }
    return list;
});

const reminderList = computed(() =>
    (props.demos || []).filter(d => d.trial_status === 'expired' && !d.admin_reminder_seen)
);

function openDemo(demo) {
    selectedDemo.value = demo;
    statusForm.status = demo.status;
    statusForm.admin_notes = demo.admin_notes || '';
    if (demo.trial_status === 'expired' && !demo.admin_reminder_seen) {
        router.post(`/admin/demos/${demo.id}/seen`, {}, { preserveScroll: true, preserveState: true });
    }
}

function updateStatus() {
    statusForm.put(`/admin/demos/${selectedDemo.value.id}/status`, {
        onSuccess: () => { selectedDemo.value = null; }
    });
}

function extendTrial(days = 7) {
    if (!selectedDemo.value) return;
    extendForm.days = days;
    extendForm.post(`/admin/demos/${selectedDemo.value.id}/extend-trial`, {
        preserveScroll: true,
        onSuccess: () => { selectedDemo.value = null; },
    });
}

function dismissReminder(demo) {
    router.post(`/admin/demos/${demo.id}/seen`, {}, { preserveScroll: true });
}

function confirmDelete(demo) { demoToDelete.value = demo; }

function deleteDemo() {
    router.delete(`/admin/demos/${demoToDelete.value.id}`, {
        onSuccess: () => { demoToDelete.value = null; }
    });
}

function formatDate(date) {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric' });
}

function initials(name) {
    if (!name) return '?';
    return name.trim().split(/\s+/).slice(0, 2).map(p => p[0]).join('').toUpperCase();
}

function trialBadgeClass(d) {
    if (d.trial_status === 'expired') return 'bg-rose-50 text-rose-600 border-rose-200';
    if (d.is_trial_ending_soon) return 'bg-amber-50 text-amber-700 border-amber-200';
    if (d.is_trial_active) return 'bg-emerald-50 text-emerald-600 border-emerald-200';
    if (d.trial_status === 'extended') return 'bg-indigo-50 text-indigo-600 border-indigo-200';
    return 'bg-gray-50 text-gray-600 border-gray-200';
}

function trialLabel(d) {
    if (d.trial_status === 'expired') return 'انتهت';
    if (d.is_trial_ending_soon) return `${d.trial_days_left} يوم متبقّي`;
    if (d.is_trial_active) return `${d.trial_days_left} يوم متبقّي`;
    if (d.trial_status === 'extended') return 'مُمدَّدة';
    return d.trial_status || '—';
}

function progressColor(d) {
    if (d.trial_status === 'expired') return 'from-rose-500 to-rose-600';
    if (d.is_trial_ending_soon) return 'from-amber-500 to-orange-500';
    return 'from-emerald-500 to-emerald-600';
}
</script>

<template>
    <AdminLayout>
        <Head title="طلبات النسخة التجريبية" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 animate-fade-down">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 text-[#8B6B2F] text-xs font-semibold mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        النسخ التجريبية
                    </div>
                    <h1 class="text-3xl font-extrabold text-[#0D2B45]">طلبات العرض التجريبي</h1>
                    <p class="text-gray-500 text-sm mt-1">إدارة العملاء، فترات التجربة (15 يوم)، والتذكيرات</p>
                </div>
            </div>

            <!-- Reminder Alerts -->
            <Transition
                enter-active-class="transition duration-400 ease-out"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
            >
                <div v-if="reminderList.length" class="relative overflow-hidden rounded-2xl border border-rose-200 bg-gradient-to-r from-rose-50 via-rose-50 to-orange-50 p-5 animate-fade-down">
                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-500 via-orange-500 to-rose-500 animate-shimmer"></div>
                    <div class="flex items-start gap-4">
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 flex items-center justify-center shadow-lg shadow-rose-500/30">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <span class="absolute -top-1 -end-1 w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] font-bold flex items-center justify-center animate-bounce">
                                {{ reminderList.length }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-extrabold text-rose-900 mb-1">
                                تذكير: {{ reminderList.length }} نسخة تجريبية انتهت تحتاج متابعة
                            </h3>
                            <p class="text-xs text-rose-700/80 mb-3">يجب التواصل مع هؤلاء العملاء لتفعيل اشتراك مدفوع أو متابعة الحالة</p>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="d in reminderList.slice(0, 5)"
                                    :key="d.id"
                                    @click="openDemo(d)"
                                    class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-rose-200 hover:border-rose-400 hover:bg-rose-50 transition-all text-xs font-semibold text-[#0D2B45] hover:-translate-y-0.5 shadow-sm"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    {{ d.clinic_name }}
                                    <svg class="w-3 h-3 text-rose-500 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <button
                                    v-if="reminderList.length > 5"
                                    @click="trialFilter = 'reminders'"
                                    class="px-3 py-1.5 rounded-lg bg-rose-600 text-white text-xs font-semibold hover:bg-rose-700 transition"
                                >
                                    عرض الكل ({{ reminderList.length }})
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="stat-card animate-fade-up" style="animation-delay:50ms">
                    <div class="stat-icon bg-gradient-to-br from-[#1B4F72] to-[#0D2B45]">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">إجمالي الطلبات</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ demos.length }}</p>
                    </div>
                </div>

                <div class="stat-card animate-fade-up" style="animation-delay:100ms">
                    <div class="stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">تجارب نشطة</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ trialStats?.active || 0 }}</p>
                    </div>
                </div>

                <div class="stat-card animate-fade-up" style="animation-delay:150ms">
                    <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">على وشك الانتهاء</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ trialStats?.ending_soon || 0 }}</p>
                    </div>
                </div>

                <div class="stat-card animate-fade-up" style="animation-delay:200ms">
                    <div class="stat-icon bg-gradient-to-br from-rose-500 to-rose-600">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">تجارب منتهية</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ trialStats?.expired || 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col md:flex-row gap-3 animate-fade-up">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-gray-400 absolute start-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input v-model="search" type="text" placeholder="ابحث بالاسم أو العيادة أو البريد..."
                        class="w-full ps-11 pe-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1B4F72] focus:bg-white focus:ring-2 focus:ring-[#1B4F72]/10 transition-all">
                </div>
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl p-1 overflow-x-auto">
                    <button
                        v-for="opt in [
                            { key: 'all', label: 'الكل' },
                            { key: 'active', label: 'نشطة' },
                            { key: 'ending_soon', label: 'تنتهي قريباً' },
                            { key: 'expired', label: 'منتهية' },
                            { key: 'reminders', label: 'تذكيرات' },
                        ]"
                        :key="opt.key"
                        @click="trialFilter = opt.key"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-all',
                            trialFilter === opt.key
                                ? 'bg-white text-[#1B4F72] shadow-sm'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >{{ opt.label }}</button>
                </div>
                <select v-model="statusFilter" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1B4F72]">
                    <option value="all">كل الحالات</option>
                    <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                </select>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                            <tr>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">العيادة</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">التواصل</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">حالة التجربة</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">المتابعة</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">انتهاء التجربة</th>
                                <th class="text-end px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr
                                v-for="(d, idx) in filteredDemos"
                                :key="d.id"
                                :class="[
                                    'group transition-all animate-fade-in',
                                    d.trial_status === 'expired' && !d.admin_reminder_seen
                                        ? 'bg-rose-50/40 hover:bg-rose-50/70'
                                        : 'hover:bg-gradient-to-r hover:from-[#FAF7F0]/50 hover:to-transparent'
                                ]"
                                :style="{ animationDelay: (idx * 30) + 'ms' }"
                            >
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="relative">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] flex items-center justify-center text-white font-bold text-sm shadow-md">
                                                {{ initials(d.clinic_name) }}
                                            </div>
                                            <span
                                                v-if="d.trial_status === 'expired' && !d.admin_reminder_seen"
                                                class="absolute -top-1 -end-1 w-3 h-3 rounded-full bg-rose-500 ring-2 ring-white animate-ping-slow"
                                            ></span>
                                        </div>
                                        <div>
                                            <p class="text-[#0D2B45] font-bold">{{ d.clinic_name }}</p>
                                            <p class="text-gray-500 text-xs">{{ d.full_name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="text-gray-700 text-xs" dir="ltr">{{ d.email }}</p>
                                    <p class="text-gray-400 text-xs mt-0.5" dir="ltr">{{ d.country_code }}{{ d.phone }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="space-y-2 min-w-[160px]">
                                        <span :class="['inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border', trialBadgeClass(d)]">
                                            <span :class="[
                                                'w-1.5 h-1.5 rounded-full',
                                                d.trial_status === 'expired' ? 'bg-rose-500' : (d.is_trial_ending_soon ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500 animate-pulse')
                                            ]"></span>
                                            {{ trialLabel(d) }}
                                        </span>
                                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div
                                                :class="['h-full bg-gradient-to-r rounded-full transition-all duration-700', progressColor(d)]"
                                                :style="{ width: (d.trial_progress || 0) + '%' }"
                                            ></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span :class="statusColors[d.status]" class="inline-flex items-center gap-1.5 bg-gradient-to-r text-white px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        {{ statusLabels[d.status] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">{{ formatDate(d.trial_ends_at) }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            v-if="d.trial_status === 'expired' && !d.admin_reminder_seen"
                                            @click="dismissReminder(d)"
                                            class="action-btn dismiss"
                                            title="تجاهل التذكير"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button @click="openDemo(d)" class="action-btn manage" title="إدارة">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button @click="confirmDelete(d)" class="action-btn delete" title="حذف">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!filteredDemos.length">
                                <td colspan="6" class="text-center py-12 text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    لا توجد طلبات مطابقة
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-200 ease-in"
            leave-to-class="opacity-0"
        >
            <div v-if="selectedDemo" class="fixed inset-0 bg-[#0D2B45]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="selectedDemo = null">
                <Transition
                    enter-active-class="transition duration-400 ease-out"
                    enter-from-class="opacity-0 translate-y-8 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    appear
                >
                    <div v-if="selectedDemo" class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col">
                        <!-- Header -->
                        <div class="relative bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] p-5 overflow-hidden">
                            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                            <div class="absolute top-0 end-0 w-40 h-40 bg-[#C4A265]/20 rounded-full blur-3xl"></div>
                            <div class="relative flex justify-between items-start">
                                <div class="flex items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur border border-white/20 flex items-center justify-center text-white font-bold text-lg">
                                        {{ initials(selectedDemo.clinic_name) }}
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-extrabold text-white">{{ selectedDemo.clinic_name }}</h3>
                                        <p class="text-white/70 text-xs">{{ selectedDemo.full_name }}</p>
                                    </div>
                                </div>
                                <button @click="selectedDemo = null" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-5">
                            <!-- Trial Card -->
                            <div :class="[
                                'rounded-2xl p-5 border-2 relative overflow-hidden',
                                selectedDemo.trial_status === 'expired'
                                    ? 'bg-gradient-to-br from-rose-50 to-orange-50 border-rose-200'
                                    : selectedDemo.is_trial_ending_soon
                                        ? 'bg-gradient-to-br from-amber-50 to-yellow-50 border-amber-200'
                                        : 'bg-gradient-to-br from-emerald-50 to-teal-50 border-emerald-200'
                            ]">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg :class="[
                                            'w-5 h-5',
                                            selectedDemo.trial_status === 'expired' ? 'text-rose-600' : (selectedDemo.is_trial_ending_soon ? 'text-amber-600' : 'text-emerald-600')
                                        ]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h4 class="font-extrabold text-[#0D2B45] text-sm">حالة التجربة (15 يوم)</h4>
                                    </div>
                                    <span :class="['inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border', trialBadgeClass(selectedDemo)]">
                                        {{ trialLabel(selectedDemo) }}
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-3 mb-3 text-xs">
                                    <div>
                                        <p class="text-gray-500">بدأت في</p>
                                        <p class="font-bold text-[#0D2B45]">{{ formatDate(selectedDemo.trial_started_at) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">تنتهي في</p>
                                        <p class="font-bold text-[#0D2B45]">{{ formatDate(selectedDemo.trial_ends_at) }}</p>
                                    </div>
                                </div>
                                <div class="h-2 bg-white/60 rounded-full overflow-hidden">
                                    <div
                                        :class="['h-full bg-gradient-to-r rounded-full transition-all duration-700', progressColor(selectedDemo)]"
                                        :style="{ width: (selectedDemo.trial_progress || 0) + '%' }"
                                    ></div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <button @click="extendTrial(7)" class="px-3 py-1.5 rounded-lg bg-white border border-[#1B4F72]/20 hover:bg-[#1B4F72] hover:text-white text-[#1B4F72] text-xs font-bold transition shadow-sm">
                                        + 7 أيام
                                    </button>
                                    <button @click="extendTrial(15)" class="px-3 py-1.5 rounded-lg bg-white border border-[#1B4F72]/20 hover:bg-[#1B4F72] hover:text-white text-[#1B4F72] text-xs font-bold transition shadow-sm">
                                        + 15 يوم
                                    </button>
                                    <button @click="extendTrial(30)" class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-[#C4A265] to-[#8B6B2F] text-white text-xs font-bold transition shadow-sm hover:-translate-y-0.5">
                                        + 30 يوم
                                    </button>
                                </div>
                            </div>

                            <!-- Contact Info -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 p-3 rounded-xl">
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">البريد</p>
                                    <p class="text-gray-800 text-xs mt-1 truncate" dir="ltr">{{ selectedDemo.email }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-xl">
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">الهاتف</p>
                                    <p class="text-gray-800 text-xs mt-1" dir="ltr">{{ selectedDemo.country_code }}{{ selectedDemo.phone }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-xl">
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">التخصص</p>
                                    <p class="text-gray-800 text-xs mt-1">{{ selectedDemo.specialty || '—' }}</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-xl">
                                    <p class="text-[10px] text-gray-500 uppercase font-bold">عدد الأطباء</p>
                                    <p class="text-gray-800 text-xs mt-1">{{ selectedDemo.doctors_count || '—' }}</p>
                                </div>
                            </div>

                            <div v-if="selectedDemo.notes" class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] text-gray-500 uppercase font-bold mb-1">ملاحظات العميل</p>
                                <p class="text-gray-700 text-xs">{{ selectedDemo.notes }}</p>
                            </div>

                            <!-- Status Form -->
                            <form @submit.prevent="updateStatus" class="space-y-3 border-t border-gray-100 pt-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">حالة المتابعة</label>
                                    <select v-model="statusForm.status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B4F72] focus:bg-white">
                                        <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">ملاحظات المدير</label>
                                    <textarea v-model="statusForm.admin_notes" rows="2" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#1B4F72] focus:bg-white"></textarea>
                                </div>
                                <button type="submit" :disabled="statusForm.processing"
                                    class="w-full bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white py-2.5 rounded-xl text-sm font-bold hover:shadow-lg shadow-[#1B4F72]/20 transition-all hover:-translate-y-0.5">
                                    تحديث الحالة
                                </button>
                            </form>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>

        <!-- Delete Confirmation -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            leave-active-class="transition duration-200 ease-in"
            leave-to-class="opacity-0"
        >
            <div v-if="demoToDelete" class="fixed inset-0 bg-[#0D2B45]/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="demoToDelete = null">
                <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-scale-in">
                    <div class="p-6 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-rose-100 mb-4 animate-pulse-slow">
                            <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-[#0D2B45] mb-2">حذف الطلب</h3>
                        <p class="text-sm text-gray-600">سيتم حذف <strong>{{ demoToDelete.clinic_name }}</strong> نهائياً.</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex gap-3">
                        <button @click="demoToDelete = null" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-bold text-gray-600 hover:bg-gray-100 transition">إلغاء</button>
                        <button @click="deleteDemo" class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 text-white text-sm font-bold shadow-lg shadow-rose-500/25 hover:-translate-y-0.5 transition">احذف</button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<style scoped>
.stat-card {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    background: white;
    border: 1px solid rgb(243 244 246);
    border-radius: 1rem;
    padding: 1.25rem;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.03);
    transition: all 0.3s ease;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    inset-inline-start: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(to right, #1B4F72, #C4A265);
    transform: scaleX(0);
    transform-origin: start;
    transition: transform 0.4s ease;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgb(27 79 114 / 0.1);
    border-color: rgb(196 162 101 / 0.3);
}
.stat-card:hover::before {
    transform: scaleX(1);
}
.stat-icon {
    width: 2.75rem;
    height: 2.75rem;
    border-radius: 0.875rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 4px 12px -2px rgb(27 79 114 / 0.25);
}

.action-btn {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F4F6;
    color: #6B7280;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.action-btn:hover { transform: scale(1.1); }
.action-btn.manage:hover { background: linear-gradient(135deg, #1B4F72, #0D2B45); color: white; }
.action-btn.delete:hover { background: linear-gradient(135deg, #ef4444, #e11d48); color: white; }
.action-btn.dismiss { background: #fef2f2; color: #ef4444; }
.action-btn.dismiss:hover { background: linear-gradient(135deg, #10b981, #059669); color: white; }

@keyframes fade-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fade-down { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
@keyframes scale-in { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
@keyframes pulse-slow { 0%,100% { transform: scale(1); } 50% { transform: scale(1.05); } }
@keyframes ping-slow { 75%,100% { transform: scale(1.8); opacity: 0; } }
@keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }

.animate-fade-up { animation: fade-up 0.6s ease-out backwards; }
.animate-fade-down { animation: fade-down 0.6s ease-out backwards; }
.animate-fade-in { animation: fade-in 0.5s ease-out backwards; }
.animate-scale-in { animation: scale-in 0.3s ease-out; }
.animate-pulse-slow { animation: pulse-slow 2s ease-in-out infinite; }
.animate-ping-slow { animation: ping-slow 2s cubic-bezier(0,0,0.2,1) infinite; }
.animate-shimmer {
    background-size: 200% 100%;
    animation: shimmer 3s linear infinite;
}
</style>
