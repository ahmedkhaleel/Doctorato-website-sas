<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ currencyList: Array });

const showModal = ref(false);
const showDeleteModal = ref(false);
const editingCurrency = ref(null);
const currencyToDelete = ref(null);
const search = ref('');
const filterStatus = ref('all'); // all | active | inactive
const viewMode = ref('grid'); // grid | table

const form = useForm({
    code: '',
    name_ar: '',
    name_en: '',
    symbol: '',
    symbol_position: 'after',
    decimal_places: 2,
    rate_to_sar: 1,
    country_code: '',
    flag_emoji: '',
    display_order: 0,
    is_active: true,
});

// Stats
const stats = computed(() => ({
    total: props.currencyList?.length || 0,
    active: props.currencyList?.filter(c => c.is_active).length || 0,
    inactive: props.currencyList?.filter(c => !c.is_active).length || 0,
    countries: new Set(props.currencyList?.map(c => c.country_code).filter(Boolean)).size || 0,
}));

// Filtered list
const filteredCurrencies = computed(() => {
    let list = props.currencyList || [];
    if (filterStatus.value === 'active') list = list.filter(c => c.is_active);
    if (filterStatus.value === 'inactive') list = list.filter(c => !c.is_active);
    if (search.value) {
        const q = search.value.toLowerCase();
        list = list.filter(c =>
            c.code?.toLowerCase().includes(q) ||
            c.name_ar?.toLowerCase().includes(q) ||
            c.name_en?.toLowerCase().includes(q)
        );
    }
    return list;
});

function openAdd() {
    editingCurrency.value = null;
    form.reset();
    form.is_active = true;
    form.symbol_position = 'after';
    form.decimal_places = 2;
    form.rate_to_sar = 1;
    showModal.value = true;
}

function openEdit(currency) {
    editingCurrency.value = currency;
    Object.keys(form.data()).forEach(key => {
        if (currency[key] !== undefined) form[key] = currency[key];
    });
    showModal.value = true;
}

function submit() {
    if (editingCurrency.value) {
        form.put(`/admin/currencies/${editingCurrency.value.id}`, { onSuccess: () => { showModal.value = false; } });
    } else {
        form.post('/admin/currencies', { onSuccess: () => { showModal.value = false; } });
    }
}

function confirmDelete(currency) {
    currencyToDelete.value = currency;
    showDeleteModal.value = true;
}

function deleteCurrency() {
    if (!currencyToDelete.value) return;
    router.delete(`/admin/currencies/${currencyToDelete.value.id}`, {
        onSuccess: () => {
            showDeleteModal.value = false;
            currencyToDelete.value = null;
        },
    });
}

function formatRate(rate) {
    return Number(rate).toLocaleString('en-US', { maximumFractionDigits: 6 });
}
</script>

<template>
    <AdminLayout>
        <Head title="إدارة العملات" />

        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 animate-fade-down">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C4A265]/10 border border-[#C4A265]/20 text-[#8B6B2F] text-xs font-semibold mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        إدارة العملات
                    </div>
                    <h1 class="text-3xl font-extrabold text-[#0D2B45]">العملات وأسعار الصرف</h1>
                    <p class="text-gray-500 text-sm mt-1">أدر العملات المدعومة وحدّث أسعار التحويل من الريال السعودي</p>
                </div>
                <button
                    @click="openAdd"
                    class="group relative inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white font-semibold text-sm shadow-lg shadow-[#1B4F72]/20 hover:shadow-xl hover:shadow-[#1B4F72]/30 hover:-translate-y-0.5 transition-all duration-300"
                >
                    <span class="absolute inset-0 rounded-2xl bg-gradient-to-r from-[#C4A265]/0 via-[#C4A265]/20 to-[#C4A265]/0 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                    <svg class="relative w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="relative">إضافة عملة جديدة</span>
                </button>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total -->
                <div class="stat-card animate-fade-up" style="animation-delay: 50ms">
                    <div class="stat-icon bg-gradient-to-br from-[#1B4F72] to-[#0D2B45]">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">إجمالي العملات</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ stats.total }}</p>
                    </div>
                </div>

                <!-- Active -->
                <div class="stat-card animate-fade-up" style="animation-delay: 100ms">
                    <div class="stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">مفعّلة</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ stats.active }}</p>
                    </div>
                </div>

                <!-- Inactive -->
                <div class="stat-card animate-fade-up" style="animation-delay: 150ms">
                    <div class="stat-icon bg-gradient-to-br from-rose-500 to-rose-600">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">معطّلة</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ stats.inactive }}</p>
                    </div>
                </div>

                <!-- Countries -->
                <div class="stat-card animate-fade-up" style="animation-delay: 200ms">
                    <div class="stat-icon bg-gradient-to-br from-[#C4A265] to-[#8B6B2F]">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 font-medium mb-1">الدول</p>
                        <p class="text-2xl font-extrabold text-[#0D2B45]">{{ stats.countries }}</p>
                    </div>
                </div>
            </div>

            <!-- Filters Bar -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col md:flex-row gap-3 animate-fade-up" style="animation-delay: 250ms">
                <!-- Search -->
                <div class="relative flex-1">
                    <svg class="absolute start-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="ابحث بالكود، الاسم بالعربية أو الإنجليزية..."
                        class="w-full ps-11 pe-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-[#1B4F72] focus:bg-white focus:ring-2 focus:ring-[#1B4F72]/10 transition-all"
                    />
                </div>

                <!-- Status Filter -->
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl p-1">
                    <button
                        v-for="opt in [
                            { key: 'all', label: 'الكل' },
                            { key: 'active', label: 'مفعّلة' },
                            { key: 'inactive', label: 'معطّلة' }
                        ]"
                        :key="opt.key"
                        @click="filterStatus = opt.key"
                        :class="[
                            'px-4 py-1.5 rounded-lg text-xs font-semibold transition-all',
                            filterStatus === opt.key
                                ? 'bg-white text-[#1B4F72] shadow-sm'
                                : 'text-gray-500 hover:text-gray-700'
                        ]"
                    >
                        {{ opt.label }}
                    </button>
                </div>

                <!-- View Toggle -->
                <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl p-1">
                    <button
                        @click="viewMode = 'grid'"
                        :class="[
                            'p-2 rounded-lg transition-all',
                            viewMode === 'grid' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-400 hover:text-gray-600'
                        ]"
                        title="عرض شبكي"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button
                        @click="viewMode = 'table'"
                        :class="[
                            'p-2 rounded-lg transition-all',
                            viewMode === 'table' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-400 hover:text-gray-600'
                        ]"
                        title="عرض جدولي"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                <TransitionGroup
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 translate-y-4 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-200 ease-in absolute"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <div
                        v-for="(c, idx) in filteredCurrencies"
                        :key="c.id"
                        class="currency-card group"
                        :style="{ animationDelay: (idx * 40) + 'ms' }"
                    >
                        <!-- Top accent line -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#1B4F72] via-[#C4A265] to-[#1B4F72] opacity-0 group-hover:opacity-100 transition-opacity"></div>

                        <!-- Status badge -->
                        <div class="absolute top-4 end-4">
                            <span
                                :class="[
                                    'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold',
                                    c.is_active
                                        ? 'bg-emerald-50 text-emerald-600 border border-emerald-200'
                                        : 'bg-rose-50 text-rose-600 border border-rose-200'
                                ]"
                            >
                                <span :class="['w-1.5 h-1.5 rounded-full', c.is_active ? 'bg-emerald-500' : 'bg-rose-500']"></span>
                                {{ c.is_active ? 'مفعّلة' : 'معطّلة' }}
                            </span>
                        </div>

                        <!-- Flag & Code -->
                        <div class="flex items-center gap-3 mb-4">
                            <div class="relative w-14 h-14 rounded-2xl bg-gradient-to-br from-gray-50 to-gray-100 border border-gray-200 flex items-center justify-center text-3xl overflow-hidden group-hover:scale-110 transition-transform duration-300">
                                <span class="relative z-10">{{ c.flag_emoji || '🌐' }}</span>
                                <div class="absolute inset-0 bg-gradient-to-br from-[#C4A265]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="font-mono font-extrabold text-[#0D2B45] text-lg">{{ c.code }}</span>
                                    <span class="text-gray-400 text-xs font-semibold">{{ c.symbol }}</span>
                                </div>
                                <p class="text-xs text-gray-600 truncate">{{ c.name_ar }}</p>
                            </div>
                        </div>

                        <!-- Rate -->
                        <div class="bg-gradient-to-br from-[#FAF7F0] to-[#F5EFE0] rounded-xl p-3 mb-4 border border-[#C4A265]/20">
                            <p class="text-[10px] text-[#8B6B2F] font-semibold uppercase tracking-wider mb-1">سعر الصرف من الريال</p>
                            <div class="flex items-baseline gap-1">
                                <span class="text-xl font-extrabold bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] bg-clip-text text-transparent">
                                    {{ formatRate(c.rate_to_sar) }}
                                </span>
                                <span class="text-[10px] text-gray-500">1 SAR =</span>
                            </div>
                        </div>

                        <!-- Meta -->
                        <div class="flex items-center justify-between text-[10px] text-gray-400 mb-4">
                            <span>الكسور: {{ c.decimal_places }}</span>
                            <span>{{ c.symbol_position === 'before' ? 'الرمز قبل' : 'الرمز بعد' }}</span>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2 pt-3 border-t border-gray-100">
                            <button
                                @click="openEdit(c)"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-[#1B4F72]/5 hover:bg-[#1B4F72] hover:text-white text-[#1B4F72] text-xs font-semibold transition-all group/btn"
                            >
                                <svg class="w-3.5 h-3.5 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                تعديل
                            </button>
                            <button
                                @click="confirmDelete(c)"
                                class="inline-flex items-center justify-center p-2 rounded-lg bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-500 transition-all group/btn"
                            >
                                <svg class="w-3.5 h-3.5 group-hover/btn:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </TransitionGroup>

                <!-- Empty State -->
                <div v-if="!filteredCurrencies.length" class="col-span-full text-center py-16 animate-fade-up">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-4">
                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">لا توجد عملات تطابق البحث</p>
                    <p class="text-gray-400 text-xs mt-1">جرّب تغيير معايير البحث أو إضافة عملة جديدة</p>
                </div>
            </div>

            <!-- Table View -->
            <div v-if="viewMode === 'table'" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-up">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                            <tr>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">العلم</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الكود</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الاسم</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الرمز</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">سعر الصرف</th>
                                <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="text-end px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr
                                v-for="(c, idx) in filteredCurrencies"
                                :key="c.id"
                                class="group hover:bg-gradient-to-r hover:from-[#FAF7F0]/50 hover:to-transparent transition-all animate-fade-in"
                                :style="{ animationDelay: (idx * 30) + 'ms' }"
                            >
                                <td class="px-5 py-4 text-2xl">{{ c.flag_emoji || '🌐' }}</td>
                                <td class="px-5 py-4">
                                    <span class="font-mono font-extrabold text-[#0D2B45]">{{ c.code }}</span>
                                </td>
                                <td class="px-5 py-4 text-gray-700">{{ c.name_ar }}</td>
                                <td class="px-5 py-4 text-gray-600 font-semibold">{{ c.symbol }}</td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-[#1B4F72]">{{ formatRate(c.rate_to_sar) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold',
                                            c.is_active
                                                ? 'bg-emerald-50 text-emerald-600 border border-emerald-200'
                                                : 'bg-rose-50 text-rose-600 border border-rose-200'
                                        ]"
                                    >
                                        <span :class="['w-1.5 h-1.5 rounded-full', c.is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500']"></span>
                                        {{ c.is_active ? 'مفعّلة' : 'معطّلة' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="openEdit(c)" class="p-2 rounded-lg bg-[#1B4F72]/5 hover:bg-[#1B4F72] hover:text-white text-[#1B4F72] transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button @click="confirmDelete(c)" class="p-2 rounded-lg bg-rose-50 hover:bg-rose-500 hover:text-white text-rose-500 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!filteredCurrencies.length">
                                <td colspan="7" class="text-center py-12 text-gray-400">
                                    <p class="text-sm">لا توجد عملات تطابق البحث</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0D2B45]/60 backdrop-blur-sm" @click.self="showModal = false">
                <Transition
                    enter-active-class="transition duration-400 ease-out"
                    enter-from-class="opacity-0 translate-y-8 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                    appear
                >
                    <div v-if="showModal" class="bg-white rounded-3xl w-full max-w-3xl max-h-[90vh] overflow-hidden shadow-2xl flex flex-col">
                        <!-- Modal Header -->
                        <div class="relative bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] px-6 py-5 overflow-hidden">
                            <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                            <div class="absolute top-0 end-0 w-40 h-40 bg-[#C4A265]/20 rounded-full blur-3xl"></div>

                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur border border-white/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-extrabold text-white">
                                            {{ editingCurrency ? 'تعديل العملة' : 'إضافة عملة جديدة' }}
                                        </h3>
                                        <p class="text-white/70 text-xs mt-0.5">
                                            {{ editingCurrency ? 'حدّث بيانات العملة وسعر الصرف' : 'أضف عملة جديدة إلى قائمة العملات المدعومة' }}
                                        </p>
                                    </div>
                                </div>
                                <button @click="showModal = false" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <form @submit.prevent="submit" class="flex-1 overflow-y-auto">
                            <div class="p-6 space-y-6">
                                <!-- Basic Info Section -->
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-1 h-5 rounded-full bg-gradient-to-b from-[#1B4F72] to-[#C4A265]"></div>
                                        <h4 class="text-sm font-bold text-[#0D2B45]">المعلومات الأساسية</h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="admin-label">الكود <span class="text-rose-500">*</span></label>
                                            <input
                                                v-model="form.code"
                                                type="text"
                                                maxlength="3"
                                                placeholder="SAR"
                                                class="admin-input font-mono uppercase"
                                                :disabled="!!editingCurrency"
                                            />
                                            <p v-if="form.errors.code" class="text-rose-500 text-xs mt-1">{{ form.errors.code }}</p>
                                        </div>
                                        <div>
                                            <label class="admin-label">الرمز <span class="text-rose-500">*</span></label>
                                            <input v-model="form.symbol" type="text" placeholder="ر.س" class="admin-input" />
                                            <p v-if="form.errors.symbol" class="text-rose-500 text-xs mt-1">{{ form.errors.symbol }}</p>
                                        </div>
                                        <div>
                                            <label class="admin-label">العلم (Emoji)</label>
                                            <input v-model="form.flag_emoji" type="text" placeholder="🇸🇦" class="admin-input text-center text-2xl" />
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        <div>
                                            <label class="admin-label">الاسم بالعربية <span class="text-rose-500">*</span></label>
                                            <input v-model="form.name_ar" type="text" placeholder="ريال سعودي" class="admin-input" dir="rtl" />
                                            <p v-if="form.errors.name_ar" class="text-rose-500 text-xs mt-1">{{ form.errors.name_ar }}</p>
                                        </div>
                                        <div>
                                            <label class="admin-label">الاسم بالإنجليزية <span class="text-rose-500">*</span></label>
                                            <input v-model="form.name_en" type="text" placeholder="Saudi Riyal" class="admin-input" dir="ltr" />
                                            <p v-if="form.errors.name_en" class="text-rose-500 text-xs mt-1">{{ form.errors.name_en }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Exchange Rate Section -->
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-1 h-5 rounded-full bg-gradient-to-b from-[#1B4F72] to-[#C4A265]"></div>
                                        <h4 class="text-sm font-bold text-[#0D2B45]">سعر الصرف والتنسيق</h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="admin-label">سعر الصرف من الريال <span class="text-rose-500">*</span></label>
                                            <div class="relative">
                                                <input v-model.number="form.rate_to_sar" type="number" step="0.000001" class="admin-input pe-12" />
                                                <span class="absolute end-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 font-mono">SAR</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="admin-label">الكسور العشرية</label>
                                            <input v-model.number="form.decimal_places" type="number" min="0" max="4" class="admin-input" />
                                        </div>
                                        <div>
                                            <label class="admin-label">موقع الرمز</label>
                                            <select v-model="form.symbol_position" class="admin-input">
                                                <option value="before">قبل المبلغ ($100)</option>
                                                <option value="after">بعد المبلغ (100 ر.س)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Advanced Section -->
                                <div>
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="w-1 h-5 rounded-full bg-gradient-to-b from-[#1B4F72] to-[#C4A265]"></div>
                                        <h4 class="text-sm font-bold text-[#0D2B45]">إعدادات متقدمة</h4>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label class="admin-label">كود الدولة</label>
                                            <input v-model="form.country_code" type="text" maxlength="2" placeholder="SA" class="admin-input uppercase" />
                                        </div>
                                        <div>
                                            <label class="admin-label">ترتيب العرض</label>
                                            <input v-model.number="form.display_order" type="number" class="admin-input" />
                                        </div>
                                        <div>
                                            <label class="admin-label">الحالة</label>
                                            <button
                                                type="button"
                                                @click="form.is_active = !form.is_active"
                                                :class="[
                                                    'w-full h-[42px] flex items-center justify-between px-4 rounded-xl border-2 transition-all',
                                                    form.is_active
                                                        ? 'bg-emerald-50 border-emerald-300 text-emerald-700'
                                                        : 'bg-gray-50 border-gray-200 text-gray-500'
                                                ]"
                                            >
                                                <span class="text-xs font-semibold">{{ form.is_active ? 'مفعّلة' : 'معطّلة' }}</span>
                                                <div
                                                    :class="[
                                                        'w-9 h-5 rounded-full relative transition-colors',
                                                        form.is_active ? 'bg-emerald-500' : 'bg-gray-300'
                                                    ]"
                                                >
                                                    <div
                                                        :class="[
                                                            'absolute top-0.5 w-4 h-4 bg-white rounded-full shadow transition-all',
                                                            form.is_active ? 'start-[18px]' : 'start-0.5'
                                                        ]"
                                                    ></div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Live Preview -->
                                <div class="bg-gradient-to-br from-[#FAF7F0] to-[#F5EFE0] rounded-2xl p-5 border border-[#C4A265]/20">
                                    <p class="text-[10px] text-[#8B6B2F] font-bold uppercase tracking-wider mb-3">معاينة مباشرة</p>
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-4xl shadow-sm">
                                            {{ form.flag_emoji || '🌐' }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="font-mono font-extrabold text-[#0D2B45] text-lg">{{ form.code || 'XXX' }}</span>
                                                <span class="text-sm text-gray-400">{{ form.symbol || '?' }}</span>
                                            </div>
                                            <p class="text-sm text-gray-700">{{ form.name_ar || 'اسم العملة' }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                1 SAR =
                                                <span class="font-bold text-[#1B4F72]">{{ formatRate(form.rate_to_sar || 0) }}</span>
                                                {{ form.code || 'XXX' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 flex justify-end gap-3">
                                <button type="button" @click="showModal = false" class="px-5 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
                                    إلغاء
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white font-semibold text-sm shadow-lg shadow-[#1B4F72]/20 hover:shadow-xl hover:-translate-y-0.5 transition-all disabled:opacity-50"
                                >
                                    <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ form.processing ? 'جاري الحفظ...' : (editingCurrency ? 'حفظ التعديلات' : 'إضافة العملة') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </Transition>
            </div>
        </Transition>

        <!-- Delete Confirmation Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0D2B45]/60 backdrop-blur-sm" @click.self="showDeleteModal = false">
                <div class="bg-white rounded-3xl w-full max-w-md overflow-hidden shadow-2xl animate-scale-in">
                    <div class="p-6 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-rose-100 mb-4 animate-pulse-slow">
                            <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-extrabold text-[#0D2B45] mb-2">حذف العملة</h3>
                        <p class="text-sm text-gray-600 mb-1">
                            هل أنت متأكد من حذف العملة
                            <span class="font-mono font-bold text-[#1B4F72]">{{ currencyToDelete?.code }}</span>
                            ؟
                        </p>
                        <p class="text-xs text-gray-400">لا يمكن التراجع عن هذا الإجراء</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex gap-3">
                        <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-600 hover:bg-gray-100 transition">
                            إلغاء
                        </button>
                        <button @click="deleteCurrency" class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-rose-600 text-white text-sm font-semibold shadow-lg shadow-rose-500/25 hover:-translate-y-0.5 transition">
                            نعم، احذف
                        </button>
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

.currency-card {
    position: relative;
    background: white;
    border: 1px solid rgb(243 244 246);
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.04);
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}
.currency-card:hover {
    transform: translateY(-4px);
    border-color: rgb(196 162 101 / 0.4);
    box-shadow: 0 20px 40px -12px rgb(27 79 114 / 0.15);
}

.admin-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 600;
    color: rgb(75 85 99);
    margin-bottom: 0.5rem;
}
.admin-input {
    width: 100%;
    padding: 0.625rem 0.875rem;
    background: rgb(249 250 251);
    border: 1px solid rgb(229 231 235);
    border-radius: 0.75rem;
    font-size: 0.875rem;
    color: rgb(13 43 69);
    transition: all 0.2s ease;
}
.admin-input:focus {
    outline: none;
    background: white;
    border-color: #1B4F72;
    box-shadow: 0 0 0 3px rgb(27 79 114 / 0.08);
}
.admin-input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@keyframes fade-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fade-down {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes scale-in {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}
@keyframes pulse-slow {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
.animate-fade-up {
    animation: fade-up 0.6s ease-out backwards;
}
.animate-fade-down {
    animation: fade-down 0.6s ease-out backwards;
}
.animate-fade-in {
    animation: fade-in 0.5s ease-out backwards;
}
.animate-scale-in {
    animation: scale-in 0.3s ease-out;
}
.animate-pulse-slow {
    animation: pulse-slow 2s ease-in-out infinite;
}
</style>
