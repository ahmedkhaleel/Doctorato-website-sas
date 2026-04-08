<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ testimonials: { type: Array, default: () => [] } });

const showModal = ref(false);
const editingItem = ref(null);
const toDelete = ref(null);
const search = ref('');
const activeLang = ref('ar');

const form = useForm({
    client_name_ar: '', client_name_en: '',
    clinic_name_ar: '', clinic_name_en: '',
    role_ar: '', role_en: '',
    review_ar: '', review_en: '',
    rating: 5, display_order: 0, is_active: true,
});

const stats = computed(() => ({
    total: props.testimonials.length,
    active: props.testimonials.filter((t) => t.is_active).length,
    avg_rating: props.testimonials.length
        ? (props.testimonials.reduce((s, t) => s + (t.rating || 0), 0) / props.testimonials.length).toFixed(1)
        : '—',
    five_stars: props.testimonials.filter((t) => t.rating === 5).length,
}));

const filtered = computed(() => {
    if (!search.value.trim()) return props.testimonials;
    const q = search.value.toLowerCase();
    return props.testimonials.filter(
        (t) =>
            (t.client_name_ar || '').toLowerCase().includes(q) ||
            (t.clinic_name_ar || '').toLowerCase().includes(q) ||
            (t.review_ar || '').toLowerCase().includes(q)
    );
});

function openAdd() {
    editingItem.value = null;
    form.reset();
    form.is_active = true;
    form.rating = 5;
    activeLang.value = 'ar';
    showModal.value = true;
}

function openEdit(item) {
    editingItem.value = item;
    Object.keys(form.data()).forEach((key) => {
        if (item[key] !== undefined && item[key] !== null) form[key] = item[key];
    });
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
    if (editingItem.value) {
        form.put(`/admin/testimonials/${editingItem.value.id}`, opts);
    } else {
        form.post('/admin/testimonials', opts);
    }
}

function confirmDelete(item) {
    toDelete.value = item;
}

function doDelete() {
    if (!toDelete.value) return;
    router.delete(`/admin/testimonials/${toDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (toDelete.value = null),
    });
}

function quickToggle(t) {
    router.put(`/admin/testimonials/${t.id}`, { ...t, is_active: !t.is_active }, { preserveScroll: true, preserveState: true });
}
</script>

<template>
    <Head title="الشهادات — لوحة التحكم" />

    <AdminLayout page-title="شهادات العملاء">
        <!-- Hero -->
        <div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white p-6 lg:p-8 shadow-xl">
            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-pulse-slow"></div>
            <div class="absolute bottom-0 start-0 w-48 h-48 bg-[#1B4F72]/30 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 animate-pulse-slow" style="animation-delay: -3s"></div>
            <svg class="absolute inset-0 w-full h-full opacity-[0.04] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="tm-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#tm-hex)" />
            </svg>

            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center shadow-lg shadow-[#C4A265]/30">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 text-[#C4A265] text-xs mb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></span>
                            <span class="uppercase tracking-widest font-semibold">Testimonials</span>
                        </div>
                        <h1 class="text-2xl lg:text-3xl font-extrabold mb-1">شهادات العملاء</h1>
                        <p class="text-white/60 text-sm">آراء حقيقية من عيادات تستخدم دكتوراتو</p>
                    </div>
                </div>

                <button
                    @click="openAdd"
                    class="group relative inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-br from-[#C4A265] to-[#D4B87A] text-white font-semibold rounded-xl shadow-lg shadow-[#C4A265]/30 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 overflow-hidden"
                >
                    <div class="absolute inset-0 overflow-hidden rounded-xl pointer-events-none">
                        <div class="absolute top-0 -start-1/2 w-1/2 h-full bg-gradient-to-r from-transparent via-white/30 to-transparent opacity-0 group-hover:opacity-100 group-hover:animate-shimmer"></div>
                    </div>
                    <svg class="relative w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="relative">إضافة شهادة</span>
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 animate-stagger">
            <div class="group relative bg-white rounded-2xl p-5 border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-[#1B4F72] to-transparent transition-all duration-700"></div>
                <p class="text-xs text-gray-500 mb-1">إجمالي</p>
                <p class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ stats.total }}</p>
            </div>
            <div class="group relative bg-emerald-50 rounded-2xl p-5 border border-emerald-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-emerald-700 mb-1">منشورة</p>
                <p class="text-3xl font-extrabold text-emerald-700 tabular-nums">{{ stats.active }}</p>
            </div>
            <div class="group relative bg-amber-50 rounded-2xl p-5 border border-amber-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-amber-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-amber-700 mb-1">متوسط التقييم</p>
                <p class="text-3xl font-extrabold text-amber-700 tabular-nums">{{ stats.avg_rating }} <span class="text-sm">★</span></p>
            </div>
            <div class="bg-gradient-to-br from-[#C4A265] to-[#D4B876] text-white rounded-2xl p-5">
                <p class="text-xs text-white/80 mb-1">★★★★★ تقييمات</p>
                <p class="text-3xl font-bold mt-1">{{ stats.five_stars }}</p>
            </div>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 mb-4">
            <div class="relative">
                <svg class="absolute top-1/2 -translate-y-1/2 start-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input v-model="search" type="text" placeholder="بحث باسم العميل أو العيادة" class="w-full ps-10 pe-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition" />
            </div>
        </div>

        <!-- Grid of testimonial cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="t in filtered"
                :key="t.id"
                class="group relative bg-white rounded-2xl border border-gray-100 hover:border-[#C4A265]/30 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 p-5 overflow-hidden"
            >
                <!-- Quote mark -->
                <svg class="absolute top-2 end-3 w-10 h-10 text-[#C4A265]/10" fill="currentColor" viewBox="0 0 24 24"><path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/></svg>

                <!-- Rating -->
                <div class="flex items-center gap-0.5 mb-3 text-amber-400">
                    <svg v-for="n in 5" :key="n" class="w-4 h-4" :class="n <= t.rating ? '' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>

                <!-- Review text -->
                <p class="text-sm text-gray-700 leading-relaxed mb-4 line-clamp-4 relative">
                    {{ t.review_ar }}
                </p>

                <!-- Author -->
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white flex items-center justify-center font-bold text-sm shrink-0">
                        {{ (t.client_name_ar || '?')[0] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 text-sm truncate">{{ t.client_name_ar }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ t.role_ar }} · {{ t.clinic_name_ar }}</p>
                    </div>
                </div>

                <!-- Actions footer -->
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-50">
                    <button @click="quickToggle(t)" class="relative inline-flex h-5 w-10 items-center rounded-full transition-colors" :class="t.is_active ? 'bg-emerald-500' : 'bg-gray-300'">
                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform" :class="t.is_active ? 'translate-x-0.5' : 'translate-x-5'"></span>
                    </button>
                    <div class="flex items-center gap-1">
                        <button @click="openEdit(t)" class="w-8 h-8 rounded-lg text-gray-500 hover:text-[#1B4F72] hover:bg-[#1B4F72]/5 flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button @click="confirmDelete(t)" class="w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!filtered.length" class="md:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-16 text-center text-gray-400">
                لا توجد شهادات بعد — أضف أول شهادة!
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" @click.self="showModal = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden animate-fade-up">
                    <div class="relative bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white p-6 overflow-hidden">
                        <div class="absolute top-0 end-0 w-32 h-32 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                        <div class="relative flex items-center justify-between">
                            <h3 class="text-lg font-bold">{{ editingItem ? 'تعديل الشهادة' : 'إضافة شهادة جديدة' }}</h3>
                            <button @click="showModal = false" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <!-- Language tabs -->
                        <div class="flex gap-2 p-1 bg-gray-50 rounded-xl">
                            <button type="button" @click="activeLang = 'ar'" :class="activeLang === 'ar' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">عربي</button>
                            <button type="button" @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">English</button>
                        </div>

                        <div v-show="activeLang === 'ar'" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">اسم العميل</label>
                                    <input v-model="form.client_name_ar" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">العيادة</label>
                                    <input v-model="form.clinic_name_ar" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">المنصب</label>
                                <input v-model="form.role_ar" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الشهادة</label>
                                <textarea v-model="form.review_ar" rows="4" dir="rtl" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>

                        <div v-show="activeLang === 'en'" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Client name</label>
                                    <input v-model="form.client_name_en" type="text" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Clinic</label>
                                    <input v-model="form.clinic_name_en" type="text" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Role</label>
                                <input v-model="form.role_en" type="text" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Review</label>
                                <textarea v-model="form.review_en" rows="4" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="grid md:grid-cols-3 gap-4 pt-4 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">التقييم</label>
                                <select v-model.number="form.rating" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                                    <option v-for="n in 5" :key="n" :value="n">{{ '★'.repeat(n) }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الترتيب</label>
                                <input v-model.number="form.display_order" type="number" min="0" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div class="flex items-end">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <button type="button" @click="form.is_active = !form.is_active" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="form.is_active ? 'bg-emerald-500' : 'bg-gray-300'">
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="form.is_active ? 'translate-x-1' : 'translate-x-6'"></span>
                                    </button>
                                    <span class="text-sm">مفعّلة</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold">إلغاء</button>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white rounded-lg text-sm font-semibold disabled:opacity-60">
                                {{ form.processing ? 'جاري الحفظ...' : (editingItem ? 'حفظ' : 'إضافة') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Delete dialog -->
        <Teleport to="body">
            <div v-if="toDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="toDelete = null">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-red-50 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </div>
                    <h3 class="text-center text-lg font-bold text-gray-800 mb-2">حذف الشهادة؟</h3>
                    <p class="text-center text-sm text-gray-500 mb-6">{{ toDelete.client_name_ar }} — {{ toDelete.clinic_name_ar }}</p>
                    <div class="flex gap-3">
                        <button @click="toDelete = null" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">إلغاء</button>
                        <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg text-sm">احذف</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
