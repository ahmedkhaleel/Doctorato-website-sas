<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ plans: Array });

const showModal = ref(false);
const editingPlan = ref(null);
const planToDelete = ref(null);
const newFeatureAr = ref('');
const newFeatureEn = ref('');
const search = ref('');
const billingView = ref('monthly'); // monthly | yearly

const form = useForm({
    name_ar: '', name_en: '', slug: '', description_ar: '', description_en: '',
    monthly_price: 0, yearly_price: 0, currency: 'EGP',
    is_popular: false, is_custom: false, is_active: true,
    features_ar: [], features_en: [],
    max_users: null, max_patients: null, support_level: 'email', display_order: 0,
});

const filteredPlans = computed(() => {
    if (!search.value.trim()) return props.plans || [];
    const q = search.value.toLowerCase();
    return (props.plans || []).filter(p =>
        (p.name_ar || '').toLowerCase().includes(q) ||
        (p.name_en || '').toLowerCase().includes(q) ||
        (p.slug || '').toLowerCase().includes(q)
    );
});

const stats = computed(() => ({
    total: props.plans?.length || 0,
    active: props.plans?.filter(p => p.is_active).length || 0,
    popular: props.plans?.filter(p => p.is_popular).length || 0,
    custom: props.plans?.filter(p => p.is_custom).length || 0,
}));

function openAdd() {
    editingPlan.value = null;
    form.reset();
    form.is_active = true;
    form.features_ar = [];
    form.features_en = [];
    showModal.value = true;
}

function openEdit(plan) {
    editingPlan.value = plan;
    Object.keys(form.data()).forEach(key => {
        if (plan[key] !== undefined && plan[key] !== null) form[key] = plan[key];
    });
    form.features_ar = plan.features_ar || [];
    form.features_en = plan.features_en || [];
    showModal.value = true;
}

function addFeature() {
    if (newFeatureAr.value.trim()) {
        form.features_ar.push(newFeatureAr.value.trim());
        form.features_en.push(newFeatureEn.value.trim() || newFeatureAr.value.trim());
        newFeatureAr.value = '';
        newFeatureEn.value = '';
    }
}

function removeFeature(index) {
    form.features_ar.splice(index, 1);
    form.features_en.splice(index, 1);
}

function submit() {
    if (editingPlan.value) {
        form.put(`/admin/plans/${editingPlan.value.id}`, { onSuccess: () => { showModal.value = false; } });
    } else {
        form.post('/admin/plans', { onSuccess: () => { showModal.value = false; } });
    }
}

function confirmDelete(plan) {
    planToDelete.value = plan;
}

function deletePlan() {
    router.delete(`/admin/plans/${planToDelete.value.id}`, {
        onSuccess: () => { planToDelete.value = null; }
    });
}

function togglePopular(plan) {
    router.put(`/admin/plans/${plan.id}`, {
        ...plan,
        is_popular: !plan.is_popular,
    }, { preserveScroll: true });
}

function getYearlyDiscount(plan) {
    if (!plan.monthly_price || !plan.yearly_price) return 0;
    const fullYear = plan.monthly_price * 12;
    if (fullYear === 0) return 0;
    return Math.round(((fullYear - plan.yearly_price) / fullYear) * 100);
}

function formatPrice(price) {
    return new Intl.NumberFormat('ar-SA').format(price || 0);
}
</script>

<template>
    <AdminLayout>
        <Head title="إدارة خطط الأسعار" />

        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0D2B45]">خطط الأسعار</h1>
                <p class="text-gray-500 text-sm mt-1">إدارة الباقات والخطط المعروضة على الموقع</p>
            </div>
            <button @click="openAdd" class="inline-flex items-center gap-2 bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-[#1B4F72]/20 hover:shadow-xl hover:shadow-[#1B4F72]/30 hover:scale-[1.02] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                إضافة خطة
            </button>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-500">إجمالي الخطط</p>
                        <p class="text-xl font-bold text-gray-800">{{ stats.total }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-500">الفعّالة</p>
                        <p class="text-xl font-bold text-gray-800">{{ stats.active }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#C4A265] to-[#a1864e] flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-500">الأكثر طلباً</p>
                        <p class="text-xl font-bold text-gray-800">{{ stats.popular }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] text-gray-500">مخصصة</p>
                        <p class="text-xl font-bold text-gray-800">{{ stats.custom }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters / Toolbar -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-5 h-5 text-gray-400 absolute start-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="search" type="text" placeholder="بحث بالاسم أو المعرّف..."
                    class="w-full ps-10 pe-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#1B4F72] focus:ring-2 focus:ring-[#1B4F72]/10 outline-none text-sm">
            </div>
            <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl">
                <button @click="billingView = 'monthly'" :class="billingView === 'monthly' ? 'bg-white shadow-sm text-[#1B4F72]' : 'text-gray-500'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition">شهري</button>
                <button @click="billingView = 'yearly'" :class="billingView === 'yearly' ? 'bg-white shadow-sm text-[#1B4F72]' : 'text-gray-500'"
                    class="px-4 py-2 rounded-lg text-xs font-semibold transition">سنوي</button>
            </div>
        </div>

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            <div v-for="plan in filteredPlans" :key="plan.id"
                class="plan-card group relative bg-white rounded-2xl border transition-all duration-500 hover:-translate-y-1 hover:shadow-2xl overflow-hidden"
                :class="plan.is_popular ? 'border-[#C4A265]/40 shadow-lg shadow-[#C4A265]/10' : 'border-gray-100 shadow-sm hover:shadow-xl'">

                <!-- Popular ribbon -->
                <div v-if="plan.is_popular" class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#C4A265] via-[#D4B876] to-[#C4A265]"></div>

                <!-- Inactive overlay -->
                <div v-if="!plan.is_active" class="absolute inset-0 bg-red-500/[0.03] pointer-events-none z-10">
                    <div class="absolute top-4 end-4 bg-red-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-md">معطّل</div>
                </div>

                <div class="p-5 relative">
                    <!-- Badges row -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex flex-wrap gap-1.5">
                            <span v-if="plan.is_popular" class="inline-flex items-center gap-1 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                الأكثر طلباً
                            </span>
                            <span v-if="plan.is_custom" class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 text-[10px] font-bold px-2.5 py-1 rounded-full">
                                مخصصة
                            </span>
                        </div>
                        <!-- Quick star toggle -->
                        <button @click="togglePopular(plan)" class="text-gray-300 hover:text-[#C4A265] transition-colors"
                            :class="plan.is_popular ? 'text-[#C4A265]' : ''" title="تمييز كأكثر طلباً">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </button>
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-[#0D2B45] leading-tight">{{ plan.name_ar }}</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5" dir="ltr">{{ plan.name_en }}</p>
                    </div>

                    <!-- Price -->
                    <div class="mb-4 pb-4 border-b border-dashed border-gray-100">
                        <div v-if="!plan.is_custom">
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] bg-clip-text text-transparent">
                                    {{ formatPrice(billingView === 'monthly' ? plan.monthly_price : plan.yearly_price) }}
                                </span>
                                <span class="text-xs text-gray-500 font-medium">{{ plan.currency || 'ر.س' }}</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-0.5">/ {{ billingView === 'monthly' ? 'شهر' : 'سنة' }}</p>
                            <div v-if="billingView === 'yearly' && getYearlyDiscount(plan) > 0" class="inline-flex items-center gap-1 mt-2 bg-emerald-50 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"/></svg>
                                توفير {{ getYearlyDiscount(plan) }}%
                            </div>
                        </div>
                        <div v-else class="py-1">
                            <span class="text-xl font-bold text-[#C4A265]">تواصل معنا</span>
                            <p class="text-[11px] text-gray-400 mt-0.5">سعر مخصص حسب احتياجاتك</p>
                        </div>
                    </div>

                    <!-- Limits -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="bg-gray-50 rounded-lg p-2.5">
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mb-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                مستخدمين
                            </div>
                            <p class="text-sm font-bold text-gray-800">{{ plan.max_users || '∞' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-2.5">
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-500 mb-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                مرضى
                            </div>
                            <p class="text-sm font-bold text-gray-800">{{ plan.max_patients || '∞' }}</p>
                        </div>
                    </div>

                    <!-- Features count -->
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                        <span class="inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ (plan.features_ar || []).length }} ميزة
                        </span>
                        <span class="text-[10px] text-gray-400 font-mono" dir="ltr">{{ plan.slug }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                        <button @click="openEdit(plan)" class="flex-1 action-btn edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>تعديل</span>
                        </button>
                        <button @click="confirmDelete(plan)" class="action-btn delete" title="حذف">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="!filteredPlans.length" class="col-span-full bg-white rounded-2xl border border-dashed border-gray-200 p-12 text-center">
                <svg class="w-14 h-14 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                <p class="text-gray-500 text-sm mb-4">لا توجد خطط مطابقة</p>
                <button @click="openAdd" class="inline-flex items-center gap-2 bg-[#1B4F72] text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-[#0D2B45] transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    إضافة أول خطة
                </button>
            </div>
        </div>

        <!-- Modal -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in" leave-to-class="opacity-0">
            <div v-if="showModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl w-full max-w-3xl max-h-[92vh] overflow-hidden shadow-2xl flex flex-col">
                    <!-- Modal header -->
                    <div class="bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] p-5 text-white flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">{{ editingPlan ? 'تعديل الخطة' : 'إضافة خطة جديدة' }}</h3>
                                <p class="text-white/60 text-xs">{{ editingPlan ? editingPlan.name_ar : 'أنشئ باقة جديدة للعرض على الموقع' }}</p>
                            </div>
                        </div>
                        <button @click="showModal = false" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <form @submit.prevent="submit" class="p-6 overflow-y-auto flex-1 space-y-5">
                        <!-- Section: Basic info -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-1 h-4 bg-[#1B4F72] rounded-full"></span>
                                المعلومات الأساسية
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">الاسم (عربي) *</label>
                                    <input v-model="form.name_ar" type="text" required class="input" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">الاسم (إنجليزي) *</label>
                                    <input v-model="form.name_en" type="text" required class="input" dir="ltr" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">المعرّف (Slug) *</label>
                                    <input v-model="form.slug" type="text" required class="input font-mono" dir="ltr" :disabled="!!editingPlan" placeholder="basic-plan" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">الوصف (عربي)</label>
                                    <input v-model="form.description_ar" type="text" class="input" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">الوصف (إنجليزي)</label>
                                    <input v-model="form.description_en" type="text" class="input" dir="ltr" />
                                </div>
                            </div>
                        </div>

                        <!-- Section: Pricing -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-1 h-4 bg-[#C4A265] rounded-full"></span>
                                الأسعار والحدود
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">السعر الشهري</label>
                                    <div class="relative">
                                        <input v-model.number="form.monthly_price" type="number" class="input pe-10" />
                                        <span class="absolute end-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 font-semibold">ر.س</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">السعر السنوي</label>
                                    <div class="relative">
                                        <input v-model.number="form.yearly_price" type="number" class="input pe-10" />
                                        <span class="absolute end-3 top-1/2 -translate-y-1/2 text-[11px] text-gray-400 font-semibold">ر.س</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">حد المستخدمين</label>
                                    <input v-model.number="form.max_users" type="number" class="input" placeholder="غير محدود" />
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">حد المرضى</label>
                                    <input v-model.number="form.max_patients" type="number" class="input" placeholder="غير محدود" />
                                </div>
                            </div>
                        </div>

                        <!-- Section: Flags -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-1 h-4 bg-emerald-500 rounded-full"></span>
                                الخيارات
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <label class="flag-card" :class="form.is_popular ? 'active' : ''">
                                    <input v-model="form.is_popular" type="checkbox" class="sr-only" />
                                    <svg class="w-5 h-5" :class="form.is_popular ? 'text-[#C4A265]' : 'text-gray-300'" fill="currentColor" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                    <div>
                                        <p class="text-xs font-bold">الأكثر طلباً</p>
                                        <p class="text-[10px] text-gray-500">تمييز الخطة بشارة مميزة</p>
                                    </div>
                                </label>
                                <label class="flag-card" :class="form.is_custom ? 'active' : ''">
                                    <input v-model="form.is_custom" type="checkbox" class="sr-only" />
                                    <svg class="w-5 h-5" :class="form.is_custom ? 'text-purple-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a6.759 6.759 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <div>
                                        <p class="text-xs font-bold">خطة مخصصة</p>
                                        <p class="text-[10px] text-gray-500">سعر حسب الطلب</p>
                                    </div>
                                </label>
                                <label class="flag-card" :class="form.is_active ? 'active' : ''">
                                    <input v-model="form.is_active" type="checkbox" class="sr-only" />
                                    <svg class="w-5 h-5" :class="form.is_active ? 'text-emerald-600' : 'text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <div>
                                        <p class="text-xs font-bold">مفعّلة</p>
                                        <p class="text-[10px] text-gray-500">ظاهرة على الموقع</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Section: Features -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                                الميزات ({{ form.features_ar.length }})
                            </h4>
                            <div class="space-y-2 max-h-48 overflow-y-auto mb-3 pr-1">
                                <div v-for="(feature, i) in form.features_ar" :key="i"
                                    class="group flex items-center gap-2 bg-gradient-to-r from-gray-50 to-white border border-gray-100 px-3 py-2 rounded-xl text-sm hover:border-[#1B4F72]/30 transition-all">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="flex-1 text-gray-700">{{ feature }}</span>
                                    <button type="button" @click="removeFeature(i)" class="w-7 h-7 rounded-lg text-gray-300 hover:text-white hover:bg-red-500 transition-all flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                <div v-if="!form.features_ar.length" class="text-center py-6 text-gray-400 text-xs">لم تُضف أي ميزة بعد</div>
                            </div>
                            <div class="flex gap-2">
                                <input v-model="newFeatureAr" type="text" placeholder="الميزة بالعربي" class="input flex-1" @keyup.enter.prevent="addFeature" />
                                <input v-model="newFeatureEn" type="text" placeholder="بالإنجليزي" class="input flex-1" dir="ltr" @keyup.enter.prevent="addFeature" />
                                <button type="button" @click="addFeature" class="shrink-0 bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white px-4 rounded-xl hover:shadow-lg transition text-sm font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Modal footer -->
                    <div class="border-t border-gray-100 p-4 bg-gray-50/50 flex gap-2 justify-end shrink-0">
                        <button type="button" @click="showModal = false" class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-white transition">إلغاء</button>
                        <button type="button" @click="submit" :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white rounded-xl text-sm font-semibold shadow-lg shadow-[#1B4F72]/20 hover:shadow-xl hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            {{ form.processing ? 'جاري الحفظ...' : 'حفظ الخطة' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Delete Confirmation -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in" leave-to-class="opacity-0">
            <div v-if="planToDelete" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">حذف خطة "{{ planToDelete.name_ar }}"</h3>
                    <p class="text-gray-500 text-sm mb-5">سيتم حذف الخطة نهائياً ولا يمكن التراجع</p>
                    <div class="flex gap-2">
                        <button @click="planToDelete = null" class="flex-1 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">إلغاء</button>
                        <button @click="deletePlan" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold hover:shadow-lg transition">حذف</button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<style scoped>
.input {
    width: 100%;
    padding: 0.625rem 0.875rem;
    border-radius: 0.75rem;
    border: 1px solid #E5E7EB;
    background: #FFFFFF;
    font-size: 0.8125rem;
    outline: none;
    transition: all 0.2s;
}
.input:focus {
    border-color: #1B4F72;
    box-shadow: 0 0 0 3px rgba(27, 79, 114, 0.08);
}
.input:disabled {
    background: #F9FAFB;
    color: #9CA3AF;
    cursor: not-allowed;
}

.flag-card {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.75rem;
    border-radius: 0.875rem;
    border: 1.5px solid #E5E7EB;
    background: #FFFFFF;
    cursor: pointer;
    transition: all 0.25s;
}
.flag-card:hover {
    border-color: #D1D5DB;
    background: #F9FAFB;
}
.flag-card.active {
    border-color: #1B4F72;
    background: linear-gradient(135deg, rgba(27, 79, 114, 0.04), rgba(196, 162, 101, 0.04));
    box-shadow: 0 4px 12px -2px rgba(27, 79, 114, 0.1);
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 0.5rem 0.875rem;
    border-radius: 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    background: #F3F4F6;
    color: #6B7280;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px -4px rgba(0,0,0,0.15);
}
.action-btn.edit:hover {
    background: linear-gradient(135deg, #1B4F72, #0D2B45);
    color: white;
}
.action-btn.delete {
    width: 2.25rem;
    padding: 0.5rem;
}
.action-btn.delete:hover {
    background: linear-gradient(135deg, #ef4444, #e11d48);
    color: white;
    transform: translateY(-1px) rotate(-4deg);
}

.plan-card:hover {
    border-color: #1B4F72;
}

/* Hide number input arrows */
.input[type=number]::-webkit-inner-spin-button,
.input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
.input[type=number] {
    -moz-appearance: textfield;
}
</style>
