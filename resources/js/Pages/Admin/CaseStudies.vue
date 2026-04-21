<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    cases: { type: Array, default: () => [] },
    stats: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const showModal = ref(false);
const editing = ref(null);
const search = ref(props.filters?.q || '');
const activeLang = ref('ar');
const toDelete = ref(null);

const newMetric = ref({ label_ar: '', label_en: '', value: 0, suffix: '%' });
const newModule = ref('');

const form = useForm({
    slug: '',
    client_name_ar: '',
    client_name_en: '',
    industry_ar: '',
    industry_en: '',
    location: '',
    logo: '',
    hero_image: '',
    title_ar: '',
    title_en: '',
    summary_ar: '',
    summary_en: '',
    challenge_ar: '',
    challenge_en: '',
    solution_ar: '',
    solution_en: '',
    results_ar: '',
    results_en: '',
    metrics: [],
    modules_used: [],
    testimonial_ar: '',
    testimonial_en: '',
    testimonial_author: '',
    testimonial_role: '',
    seo_title: '',
    seo_description: '',
    is_published: false,
    is_featured: false,
    display_order: 0,
});

const filtered = computed(() => {
    if (!search.value.trim()) return props.cases;
    const q = search.value.toLowerCase();
    return props.cases.filter(
        (c) =>
            (c.title_ar || '').toLowerCase().includes(q) ||
            (c.client_name_ar || '').toLowerCase().includes(q) ||
            (c.slug || '').toLowerCase().includes(q)
    );
});

function openCreate() {
    editing.value = null;
    form.reset();
    activeLang.value = 'ar';
    showModal.value = true;
}

function openEdit(c) {
    // Reset any state from a prior add/edit session before populating.
    form.reset();
    form.clearErrors();
    editing.value = c;
    Object.keys(form.data()).forEach((key) => {
        if (c[key] !== undefined && c[key] !== null) {
            form[key] = c[key];
        }
    });
    form.metrics = c.metrics || [];
    form.modules_used = c.modules_used || [];
    activeLang.value = 'ar';
    showModal.value = true;
}

function save() {
    const opts = {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    };
    if (editing.value) {
        form.put(`/admin/case-studies/${editing.value.id}`, opts);
    } else {
        form.post('/admin/case-studies', opts);
    }
}

function confirmDelete(c) {
    toDelete.value = c;
}

function doDelete() {
    if (!toDelete.value) return;
    router.delete(`/admin/case-studies/${toDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (toDelete.value = null),
    });
}

function addMetric() {
    if (!newMetric.value.label_ar) return;
    form.metrics.push({ ...newMetric.value });
    newMetric.value = { label_ar: '', label_en: '', value: 0, suffix: '%' };
}
function removeMetric(i) {
    form.metrics.splice(i, 1);
}

function addModule() {
    if (!newModule.value.trim()) return;
    if (!form.modules_used.includes(newModule.value.trim())) {
        form.modules_used.push(newModule.value.trim());
    }
    newModule.value = '';
}
function removeModule(m) {
    form.modules_used = form.modules_used.filter((x) => x !== m);
}
</script>

<template>
    <Head title="دراسات الحالة — لوحة التحكم" />

    <AdminLayout page-title="دراسات الحالة">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                <p class="text-xs text-emerald-700">منشور</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">{{ stats.published }}</p>
            </div>
            <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100">
                <p class="text-xs text-amber-700">مميّز</p>
                <p class="text-3xl font-bold text-amber-700 mt-1">{{ stats.featured }}</p>
            </div>
            <div class="bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white rounded-2xl p-5">
                <p class="text-xs text-white/70">إجمالي المشاهدات</p>
                <p class="text-3xl font-bold mt-1">{{ new Intl.NumberFormat('ar-EG').format(stats.total_views) }}</p>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white rounded-xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input v-model="search" placeholder="بحث بالعنوان أو العميل أو الـ slug" class="flex-1 min-w-[240px] px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <button @click="openCreate" class="px-5 py-2 bg-[#1B4F72] hover:bg-[#0A1628] text-white rounded-lg text-sm font-semibold">+ دراسة حالة جديدة</button>
        </div>

        <!-- List -->
        <div class="space-y-3">
            <div
                v-for="c in filtered"
                :key="c.id"
                class="group relative bg-white rounded-2xl border border-gray-100 hover:border-[#1B4F72]/20 hover:shadow-xl transition-all overflow-hidden"
            >
                <div class="absolute top-0 start-0 w-1 h-full bg-[#1B4F72]"></div>
                <div class="flex items-center gap-4 ps-6 pe-4 py-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white flex items-center justify-center font-bold">
                        {{ (c.client_name_ar || '?')[0] }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span v-if="c.is_featured" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">⭐ مميّز</span>
                            <span :class="c.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">
                                {{ c.is_published ? 'منشور' : 'مسودة' }}
                            </span>
                            <span class="text-xs text-gray-500">{{ c.industry_ar }} · {{ c.location }}</span>
                        </div>
                        <h3 class="font-bold text-gray-800 truncate">{{ c.title_ar }}</h3>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ c.client_name_ar }} · {{ c.views_count || 0 }} مشاهدة · /case-studies/{{ c.slug }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <a :href="`/case-studies/${c.slug}`" target="_blank" class="text-xs px-3 py-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50">عرض</a>
                        <button @click="openEdit(c)" class="text-xs px-3 py-1.5 rounded-lg text-[#1B4F72] hover:bg-[#1B4F72]/5">تعديل</button>
                        <button @click="confirmDelete(c)" class="text-xs px-3 py-1.5 rounded-lg text-red-600 hover:bg-red-50">حذف</button>
                    </div>
                </div>
            </div>

            <div v-if="!filtered.length" class="bg-white rounded-2xl border border-gray-100 p-16 text-center text-gray-400">
                لا توجد دراسات حالة بعد — أنشئ أول دراسة!
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" @click.self="showModal = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl my-8 overflow-hidden">
                    <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">{{ editing ? 'تعديل دراسة الحالة' : 'دراسة حالة جديدة' }}</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                    </div>

                    <form @submit.prevent="save" class="p-6 space-y-5">
                        <!-- Lang tabs -->
                        <div class="flex gap-2 border-b border-gray-100 pb-3">
                            <button type="button" @click="activeLang = 'ar'" :class="activeLang === 'ar' ? 'bg-[#1B4F72] text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-semibold">عربي</button>
                            <button type="button" @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-[#1B4F72] text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-semibold">English</button>
                        </div>

                        <!-- Client info -->
                        <div class="grid md:grid-cols-2 gap-4">
                            <div v-show="activeLang === 'ar'">
                                <label class="block text-sm font-medium mb-1">اسم العميل (عربي)</label>
                                <input v-model="form.client_name_ar" type="text" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div v-show="activeLang === 'en'">
                                <label class="block text-sm font-medium mb-1">Client Name (English)</label>
                                <input v-model="form.client_name_en" type="text" dir="ltr" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div v-show="activeLang === 'ar'">
                                <label class="block text-sm font-medium mb-1">المجال (عربي)</label>
                                <input v-model="form.industry_ar" type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div v-show="activeLang === 'en'">
                                <label class="block text-sm font-medium mb-1">Industry (English)</label>
                                <input v-model="form.industry_en" type="text" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">الموقع</label>
                                <input v-model="form.location" type="text" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Slug (URL)</label>
                                <input v-model="form.slug" type="text" dir="ltr" placeholder="auto" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm font-mono" />
                            </div>
                        </div>

                        <!-- Title + summary -->
                        <div v-show="activeLang === 'ar'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">العنوان (عربي)</label>
                                <input v-model="form.title_ar" type="text" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">الملخص (عربي)</label>
                                <textarea v-model="form.summary_ar" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">التحدي (عربي)</label>
                                <textarea v-model="form.challenge_ar" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">الحل (عربي)</label>
                                <textarea v-model="form.solution_ar" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">النتائج (عربي)</label>
                                <textarea v-model="form.results_ar" rows="3" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>

                        <div v-show="activeLang === 'en'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Title (English)</label>
                                <input v-model="form.title_en" type="text" dir="ltr" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Summary (English)</label>
                                <textarea v-model="form.summary_en" rows="2" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Challenge (English)</label>
                                <textarea v-model="form.challenge_en" rows="3" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Solution (English)</label>
                                <textarea v-model="form.solution_en" rows="3" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Results (English)</label>
                                <textarea v-model="form.results_en" rows="3" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>

                        <!-- Metrics editor -->
                        <details class="border-t border-gray-100 pt-5" open>
                            <summary class="cursor-pointer text-sm font-semibold text-gray-700">📊 الأرقام والنتائج (Metrics)</summary>
                            <div class="mt-4 space-y-2">
                                <div v-for="(m, i) in form.metrics" :key="i" class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                                    <span class="text-2xl font-bold text-[#1B4F72] tabular-nums">{{ m.value }}{{ m.suffix }}</span>
                                    <div class="flex-1 text-sm">
                                        <div class="font-semibold">{{ m.label_ar }}</div>
                                        <div class="text-xs text-gray-500">{{ m.label_en }}</div>
                                    </div>
                                    <button type="button" @click="removeMetric(i)" class="text-red-500 text-xs px-2">✕</button>
                                </div>
                                <div class="grid grid-cols-12 gap-2 mt-2">
                                    <input v-model="newMetric.label_ar" placeholder="التسمية (عربي)" class="col-span-4 px-3 py-2 border border-gray-200 rounded-lg text-xs" />
                                    <input v-model="newMetric.label_en" placeholder="Label (EN)" dir="ltr" class="col-span-4 px-3 py-2 border border-gray-200 rounded-lg text-xs" />
                                    <input v-model.number="newMetric.value" type="number" placeholder="0" class="col-span-2 px-3 py-2 border border-gray-200 rounded-lg text-xs" />
                                    <input v-model="newMetric.suffix" placeholder="%" class="col-span-1 px-3 py-2 border border-gray-200 rounded-lg text-xs" />
                                    <button type="button" @click="addMetric" class="col-span-1 bg-[#1B4F72] text-white rounded-lg text-xs">+</button>
                                </div>
                            </div>
                        </details>

                        <!-- Modules used -->
                        <div class="border-t border-gray-100 pt-5">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">الوحدات المستخدمة</label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span v-for="m in form.modules_used" :key="m" class="inline-flex items-center gap-1 px-3 py-1 bg-light-blue text-primary rounded-full text-xs font-semibold">
                                    {{ m }}
                                    <button type="button" @click="removeModule(m)" class="text-primary/60 hover:text-red-500">✕</button>
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <input v-model="newModule" @keydown.enter.prevent="addModule" placeholder="أضف وحدة..." class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                                <button type="button" @click="addModule" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm">إضافة</button>
                            </div>
                        </div>

                        <!-- Testimonial -->
                        <details class="border-t border-gray-100 pt-5">
                            <summary class="cursor-pointer text-sm font-semibold text-gray-700">💬 شهادة العميل</summary>
                            <div class="grid md:grid-cols-2 gap-4 mt-4">
                                <textarea v-model="form.testimonial_ar" rows="3" placeholder="الشهادة (عربي)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm md:col-span-2"></textarea>
                                <textarea v-model="form.testimonial_en" rows="3" dir="ltr" placeholder="Testimonial (English)" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm md:col-span-2"></textarea>
                                <input v-model="form.testimonial_author" placeholder="اسم القائل" class="px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                                <input v-model="form.testimonial_role" placeholder="المنصب" class="px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </details>

                        <!-- Settings -->
                        <div class="border-t border-gray-100 pt-5 grid md:grid-cols-3 gap-4">
                            <label class="flex items-center gap-2">
                                <input v-model="form.is_published" type="checkbox" />
                                <span class="text-sm">منشور</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input v-model="form.is_featured" type="checkbox" />
                                <span class="text-sm">مميّز</span>
                            </label>
                            <div>
                                <label class="block text-xs font-medium mb-1">الترتيب</label>
                                <input v-model.number="form.display_order" type="number" min="0" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                            <button type="button" @click="showModal = false" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold">إلغاء</button>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#1B4F72] hover:bg-[#0A1628] text-white rounded-lg text-sm font-semibold disabled:opacity-60">
                                {{ form.processing ? 'جاري الحفظ...' : (editing ? 'حفظ' : 'إنشاء') }}
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
                    <h3 class="text-lg font-bold text-gray-800 mb-2">حذف دراسة الحالة؟</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ toDelete.title_ar }}</p>
                    <div class="flex gap-3">
                        <button @click="toDelete = null" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">إلغاء</button>
                        <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg text-sm">احذف</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
