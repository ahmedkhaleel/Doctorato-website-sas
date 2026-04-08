<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ faqs: { type: Array, default: () => [] } });

const showModal = ref(false);
const editingFaq = ref(null);
const search = ref('');
const categoryFilter = ref('all');
const expanded = ref(null);
const activeLang = ref('ar');
const faqToDelete = ref(null);

const form = useForm({
    category: 'general',
    question_ar: '',
    question_en: '',
    answer_ar: '',
    answer_en: '',
    display_order: 0,
    is_active: true,
});

const categoryLabels = { general: 'عام', pricing: 'أسعار', technical: 'تقني' };

const categoryStyle = {
    general: { bg: 'bg-blue-100', text: 'text-blue-700', accent: '#3B82F6', accentSoft: 'rgba(59,130,246,0.12)' },
    pricing: { bg: 'bg-emerald-100', text: 'text-emerald-700', accent: '#10B981', accentSoft: 'rgba(16,185,129,0.12)' },
    technical: { bg: 'bg-purple-100', text: 'text-purple-700', accent: '#8B5CF6', accentSoft: 'rgba(139,92,246,0.12)' },
};

const stats = computed(() => {
    const list = props.faqs || [];
    return {
        total: list.length,
        active: list.filter((f) => f.is_active).length,
        inactive: list.filter((f) => !f.is_active).length,
        general: list.filter((f) => f.category === 'general').length,
        pricing: list.filter((f) => f.category === 'pricing').length,
        technical: list.filter((f) => f.category === 'technical').length,
    };
});

const filteredFaqs = computed(() => {
    let list = props.faqs || [];
    if (categoryFilter.value !== 'all') list = list.filter((f) => f.category === categoryFilter.value);
    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        list = list.filter(
            (f) =>
                (f.question_ar || '').toLowerCase().includes(q) ||
                (f.question_en || '').toLowerCase().includes(q) ||
                (f.answer_ar || '').toLowerCase().includes(q)
        );
    }
    return [...list].sort((a, b) => (a.display_order || 0) - (b.display_order || 0));
});

function openAdd() {
    editingFaq.value = null;
    form.reset();
    form.is_active = true;
    activeLang.value = 'ar';
    showModal.value = true;
}

function openEdit(faq) {
    editingFaq.value = faq;
    form.category = faq.category;
    form.question_ar = faq.question_ar;
    form.question_en = faq.question_en;
    form.answer_ar = faq.answer_ar;
    form.answer_en = faq.answer_en;
    form.display_order = faq.display_order;
    form.is_active = faq.is_active;
    activeLang.value = 'ar';
    showModal.value = true;
}

function submit() {
    const opts = {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    };
    if (editingFaq.value) {
        form.put(`/admin/faqs/${editingFaq.value.id}`, opts);
    } else {
        form.post('/admin/faqs', opts);
    }
}

function confirmDelete(faq) {
    faqToDelete.value = faq;
}

function doDelete() {
    if (!faqToDelete.value) return;
    router.delete(`/admin/faqs/${faqToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            faqToDelete.value = null;
        },
    });
}

function toggleExpanded(id) {
    expanded.value = expanded.value === id ? null : id;
}

function quickToggleActive(faq) {
    router.put(
        `/admin/faqs/${faq.id}`,
        { ...faq, is_active: !faq.is_active },
        { preserveScroll: true, preserveState: true }
    );
}
</script>

<template>
    <AdminLayout page-title="إدارة الأسئلة الشائعة">
        <Head title="إدارة الأسئلة الشائعة — لوحة التحكم" />

        <!-- Hero Header -->
        <div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white p-6 lg:p-8 shadow-xl">
            <!-- Animated background layers -->
            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-pulse-slow"></div>
            <div class="absolute bottom-0 start-0 w-48 h-48 bg-[#1B4F72]/30 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 animate-pulse-slow" style="animation-delay: -3s"></div>
            <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>
            <!-- Hex pattern -->
            <svg class="absolute inset-0 w-full h-full opacity-[0.04] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="faqs-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#faqs-hex)" />
            </svg>

            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center shadow-lg shadow-[#C4A265]/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 text-[#C4A265] text-xs mb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></span>
                            <span class="uppercase tracking-widest font-semibold">FAQ Management</span>
                        </div>
                        <h1 class="text-2xl lg:text-3xl font-extrabold mb-1">الأسئلة الشائعة</h1>
                        <p class="text-white/60 text-sm">إدارة الأسئلة المتكررة بلغتين مع تصنيفات وترتيب مرن</p>
                    </div>
                </div>

                <button
                    @click="openAdd"
                    class="group relative inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-[#C4A265] to-[#D4B87A] text-white font-semibold rounded-xl shadow-lg shadow-[#C4A265]/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 overflow-hidden"
                >
                    <div class="absolute inset-0 overflow-hidden rounded-xl pointer-events-none">
                        <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/30 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"></div>
                    </div>
                    <svg class="relative w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="relative">إضافة سؤال جديد</span>
                </button>
            </div>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6 animate-stagger">
            <div class="group relative bg-white rounded-2xl p-5 border border-gray-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-[#1B4F72] to-transparent transition-all duration-700"></div>
                <p class="text-xs text-gray-500 mb-1">إجمالي</p>
                <p class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ stats.total }}</p>
            </div>
            <div class="group relative bg-emerald-50 rounded-2xl p-5 border border-emerald-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-emerald-700 mb-1">مفعّل</p>
                <p class="text-3xl font-extrabold text-emerald-700 tabular-nums">{{ stats.active }}</p>
            </div>
            <div class="group relative bg-red-50 rounded-2xl p-5 border border-red-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-red-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-red-700 mb-1">معطّل</p>
                <p class="text-3xl font-extrabold text-red-700 tabular-nums">{{ stats.inactive }}</p>
            </div>
            <div class="group relative bg-blue-50 rounded-2xl p-5 border border-blue-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-blue-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-blue-700 mb-1">عام</p>
                <p class="text-3xl font-extrabold text-blue-700 tabular-nums">{{ stats.general }}</p>
            </div>
            <div class="group relative bg-emerald-50 rounded-2xl p-5 border border-emerald-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-emerald-700 mb-1">أسعار</p>
                <p class="text-3xl font-extrabold text-emerald-700 tabular-nums">{{ stats.pricing }}</p>
            </div>
            <div class="group relative bg-purple-50 rounded-2xl p-5 border border-purple-100 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-purple-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-purple-700 mb-1">تقني</p>
                <p class="text-3xl font-extrabold text-purple-700 tabular-nums">{{ stats.technical }}</p>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-4 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <svg class="absolute top-1/2 -translate-y-1/2 start-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input
                    v-model="search"
                    type="text"
                    placeholder="بحث في الأسئلة والأجوبة..."
                    class="w-full ps-10 pe-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition"
                />
            </div>
            <div class="flex gap-1.5 p-1 bg-gray-50 rounded-xl">
                <button
                    v-for="cat in [{ k: 'all', l: 'الكل' }, { k: 'general', l: 'عام' }, { k: 'pricing', l: 'أسعار' }, { k: 'technical', l: 'تقني' }]"
                    :key="cat.k"
                    @click="categoryFilter = cat.k"
                    :class="categoryFilter === cat.k
                        ? 'bg-white text-[#1B4F72] shadow-sm'
                        : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all"
                >
                    {{ cat.l }}
                </button>
            </div>
        </div>

        <!-- FAQ Accordion list -->
        <div class="space-y-3">
            <div
                v-for="faq in filteredFaqs"
                :key="faq.id"
                class="group relative bg-white rounded-2xl border border-gray-100 hover:border-[#1B4F72]/20 hover:shadow-xl transition-all duration-500 overflow-hidden"
            >
                <!-- Accent border -->
                <div
                    class="absolute top-0 start-0 w-1 h-full transition-all duration-500 group-hover:w-1.5"
                    :style="{ background: categoryStyle[faq.category]?.accent }"
                ></div>

                <div class="flex items-center gap-4 ps-6 pe-4 py-4">
                    <!-- Order badge -->
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center font-bold text-gray-500 text-sm">
                        {{ faq.display_order || 0 }}
                    </div>

                    <!-- Question + meta -->
                    <button @click="toggleExpanded(faq.id)" class="flex-1 text-start min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span :class="[categoryStyle[faq.category]?.bg, categoryStyle[faq.category]?.text]" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">
                                {{ categoryLabels[faq.category] }}
                            </span>
                            <span v-if="!faq.is_active" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-100">
                                معطّل
                            </span>
                        </div>
                        <h3 class="font-bold text-gray-800 truncate group-hover:text-[#1B4F72] transition-colors">{{ faq.question_ar }}</h3>
                        <p v-if="faq.question_en" class="text-xs text-gray-400 truncate mt-0.5" dir="ltr">{{ faq.question_en }}</p>
                    </button>

                    <!-- Quick toggle active -->
                    <button
                        @click="quickToggleActive(faq)"
                        :title="faq.is_active ? 'تعطيل' : 'تفعيل'"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors"
                        :class="faq.is_active ? 'bg-emerald-500' : 'bg-gray-300'"
                    >
                        <span
                            class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                            :class="faq.is_active ? 'translate-x-1' : 'translate-x-6'"
                        ></span>
                    </button>

                    <!-- Actions -->
                    <div class="flex items-center gap-1">
                        <button @click="openEdit(faq)" title="تعديل" class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-500 hover:text-[#1B4F72] hover:bg-[#1B4F72]/5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button @click="confirmDelete(faq)" title="حذف" class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                        <button @click="toggleExpanded(faq.id)" title="عرض الإجابة" class="w-9 h-9 rounded-lg flex items-center justify-center text-gray-400 hover:text-[#1B4F72] hover:bg-[#1B4F72]/5 transition-all">
                            <svg class="w-4 h-4 transition-transform duration-300" :class="{ 'rotate-180': expanded === faq.id }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Expanded answer -->
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="max-h-0 opacity-0"
                    enter-to-class="max-h-[400px] opacity-100"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="max-h-[400px] opacity-100"
                    leave-to-class="max-h-0 opacity-0"
                >
                    <div v-if="expanded === faq.id" class="overflow-hidden">
                        <div class="ps-6 pe-6 pb-5 border-t border-gray-100 pt-4 space-y-3">
                            <div>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">الإجابة (عربي)</div>
                                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-lg p-3" dir="rtl">{{ faq.answer_ar }}</p>
                            </div>
                            <div v-if="faq.answer_en">
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Answer (English)</div>
                                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-lg p-3" dir="ltr">{{ faq.answer_en }}</p>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>

            <!-- Empty state -->
            <div v-if="!filteredFaqs.length" class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-gray-500 font-medium">لا توجد أسئلة مطابقة</p>
                <p class="text-xs text-gray-400 mt-1">جرّب تعديل البحث أو الفلتر، أو أنشئ سؤالاً جديداً</p>
            </div>
        </div>

        <!-- Create / Edit Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" @click.self="showModal = false">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden animate-fade-up">
                        <!-- Modal header -->
                        <div class="relative bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white p-6 overflow-hidden">
                            <div class="absolute top-0 end-0 w-32 h-32 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                            <div class="relative flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#C4A265]/20 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold">{{ editingFaq ? 'تعديل السؤال' : 'إضافة سؤال جديد' }}</h3>
                                        <p class="text-xs text-white/60 mt-0.5">املأ الحقول بالعربية والإنجليزية</p>
                                    </div>
                                </div>
                                <button @click="showModal = false" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal body -->
                        <form @submit.prevent="submit" class="p-6 space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">التصنيف</label>
                                    <select v-model="form.category" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition">
                                        <option value="general">عام</option>
                                        <option value="pricing">أسعار</option>
                                        <option value="technical">تقني</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">الترتيب</label>
                                    <input v-model.number="form.display_order" type="number" min="0" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition" />
                                </div>
                            </div>

                            <!-- Language tabs -->
                            <div class="flex gap-2 p-1 bg-gray-50 rounded-xl">
                                <button type="button" @click="activeLang = 'ar'" :class="activeLang === 'ar' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">عربي</button>
                                <button type="button" @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">English</button>
                            </div>

                            <!-- Arabic fields -->
                            <div v-show="activeLang === 'ar'" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">السؤال (عربي)</label>
                                    <input v-model="form.question_ar" type="text" required dir="rtl" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">الإجابة (عربي)</label>
                                    <textarea v-model="form.answer_ar" rows="5" required dir="rtl" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition resize-none"></textarea>
                                </div>
                            </div>

                            <!-- English fields -->
                            <div v-show="activeLang === 'en'" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Question (English)</label>
                                    <input v-model="form.question_en" type="text" required dir="ltr" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Answer (English)</label>
                                    <textarea v-model="form.answer_en" rows="5" required dir="ltr" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition resize-none"></textarea>
                                </div>
                            </div>

                            <!-- Active toggle -->
                            <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">حالة النشر</p>
                                    <p class="text-xs text-gray-500 mt-0.5">السؤال يظهر في الموقع للزوار عند التفعيل</p>
                                </div>
                                <button type="button" @click="form.is_active = !form.is_active" class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors" :class="form.is_active ? 'bg-emerald-500' : 'bg-gray-300'">
                                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform" :class="form.is_active ? 'translate-x-1' : 'translate-x-6'"></span>
                                </button>
                            </div>

                            <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                                <button type="button" @click="showModal = false" class="px-5 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">إلغاء</button>
                                <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white rounded-xl text-sm font-semibold hover:shadow-lg disabled:opacity-60 transition-all">
                                    {{ form.processing ? 'جاري الحفظ...' : (editingFaq ? 'حفظ التعديلات' : 'إضافة السؤال') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Delete confirm dialog -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="faqToDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="faqToDelete = null">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 animate-fade-up">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <h3 class="text-center text-lg font-bold text-gray-800 mb-2">حذف السؤال؟</h3>
                        <p class="text-center text-sm text-gray-500 mb-2 line-clamp-2">{{ faqToDelete.question_ar }}</p>
                        <p class="text-center text-xs text-red-500 mb-6">هذا الإجراء لا يمكن التراجع عنه</p>
                        <div class="flex gap-3">
                            <button @click="faqToDelete = null" class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition-colors">إلغاء</button>
                            <button @click="doDelete" class="flex-1 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition-colors">نعم، احذف</button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AdminLayout>
</template>
