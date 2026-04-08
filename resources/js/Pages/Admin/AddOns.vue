<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    addons: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editing = ref(null);
const toDelete = ref(null);

const form = useForm({
    name_ar: '',
    name_en: '',
    description_ar: '',
    description_en: '',
    price_egp: 0,
    period: 'monthly',
    icon: '',
    badge_ar: '',
    badge_en: '',
    is_active: true,
    is_featured: false,
    display_order: 0,
});

const periodLabels = { monthly: 'شهرياً', yearly: 'سنوياً', one_time: 'لمرة واحدة' };
const iconOptions = ['user', 'sms', 'whatsapp', 'domain', 'backup', 'brand', 'cloud', 'shield'];

function openCreate() {
    editing.value = null;
    form.reset();
    form.period = 'monthly';
    form.is_active = true;
    showModal.value = true;
}

function openEdit(addon) {
    editing.value = addon;
    Object.keys(form.data()).forEach((k) => {
        if (addon[k] !== undefined && addon[k] !== null) form[k] = addon[k];
    });
    form.price_egp = parseFloat(addon.price_egp);
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
        form.put(`/admin/addons/${editing.value.id}`, opts);
    } else {
        form.post('/admin/addons', opts);
    }
}

function confirmDelete(addon) {
    toDelete.value = addon;
}

function doDelete() {
    if (!toDelete.value) return;
    router.delete(`/admin/addons/${toDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (toDelete.value = null),
    });
}

function fmtMoney(v) {
    return new Intl.NumberFormat('ar-EG').format(Math.round(v || 0));
}
</script>

<template>
    <Head title="Add-ons — لوحة التحكم" />

    <AdminLayout page-title="الإضافات (Add-ons)">
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500">إدارة الإضافات المعروضة أسفل صفحة الأسعار</p>
            <button @click="openCreate" class="px-5 py-2 bg-[#1B4F72] hover:bg-[#0A1628] text-white rounded-lg text-sm font-semibold">+ إضافة جديدة</button>
        </div>

        <!-- Grid of cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="addon in addons"
                :key="addon.id"
                class="group relative bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-xl hover:-translate-y-0.5 transition-all overflow-hidden"
            >
                <div v-if="addon.badge_ar" class="absolute top-3 end-3">
                    <span class="px-2 py-0.5 rounded-full bg-[#C4A265]/15 text-[#C4A265] text-[10px] font-bold">{{ addon.badge_ar }}</span>
                </div>

                <div class="flex items-start gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#1B4F72]/5 text-[#1B4F72] flex items-center justify-center font-bold text-xs uppercase">
                        {{ addon.icon || '?' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-800 truncate">{{ addon.name_ar }}</h3>
                        <p class="text-xs text-gray-400 truncate">{{ addon.name_en }}</p>
                    </div>
                </div>

                <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ addon.description_ar }}</p>

                <div class="flex items-baseline gap-2 mb-4 pt-3 border-t border-gray-100">
                    <span class="text-2xl font-black text-[#1B4F72] tabular-nums">{{ fmtMoney(addon.price_egp) }}</span>
                    <span class="text-xs text-gray-500">ج.م / {{ periodLabels[addon.period] }}</span>
                </div>

                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span v-if="!addon.is_active" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-red-50 text-red-600">معطّل</span>
                        <span v-if="addon.is_featured" class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700">مميّز</span>
                    </div>
                    <div class="flex gap-1">
                        <button @click="openEdit(addon)" class="text-xs px-2 py-1 rounded-lg text-[#1B4F72] hover:bg-[#1B4F72]/5">تعديل</button>
                        <button @click="confirmDelete(addon)" class="text-xs px-2 py-1 rounded-lg text-red-500 hover:bg-red-50">حذف</button>
                    </div>
                </div>
            </div>

            <div v-if="!addons.length" class="md:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-16 text-center text-gray-400">
                لا توجد إضافات بعد
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" @click.self="showModal = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden">
                    <div class="bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white p-6">
                        <h3 class="text-lg font-bold">{{ editing ? 'تعديل Add-on' : 'إضافة جديدة' }}</h3>
                    </div>

                    <form @submit.prevent="save" class="p-6 space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الاسم (عربي)</label>
                                <input v-model="form.name_ar" type="text" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Name (English)</label>
                                <input v-model="form.name_en" type="text" dir="ltr" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الوصف (عربي)</label>
                                <input v-model="form.description_ar" type="text" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Description (English)</label>
                                <input v-model="form.description_en" type="text" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">السعر (ج.م)</label>
                                <input v-model.number="form.price_egp" type="number" min="0" step="0.01" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الدورة</label>
                                <select v-model="form.period" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                                    <option value="monthly">شهرياً</option>
                                    <option value="yearly">سنوياً</option>
                                    <option value="one_time">لمرة واحدة</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الأيقونة</label>
                                <select v-model="form.icon" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                                    <option value="">—</option>
                                    <option v-for="ic in iconOptions" :key="ic" :value="ic">{{ ic }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الترتيب</label>
                                <input v-model.number="form.display_order" type="number" min="0" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Badge (عربي)</label>
                                <input v-model="form.badge_ar" type="text" placeholder="الأكثر طلباً" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Badge (English)</label>
                                <input v-model="form.badge_en" type="text" dir="ltr" placeholder="Most popular" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </div>

                        <div class="flex items-center gap-6 pt-2">
                            <label class="inline-flex items-center gap-2">
                                <input v-model="form.is_active" type="checkbox" />
                                <span class="text-sm">مفعّل</span>
                            </label>
                            <label class="inline-flex items-center gap-2">
                                <input v-model="form.is_featured" type="checkbox" />
                                <span class="text-sm">مميّز</span>
                            </label>
                        </div>

                        <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold">إلغاء</button>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#1B4F72] text-white rounded-lg text-sm font-semibold disabled:opacity-60">
                                {{ editing ? 'حفظ' : 'إنشاء' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <Teleport to="body">
            <div v-if="toDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="toDelete = null">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">حذف الإضافة؟</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ toDelete.name_ar }}</p>
                    <div class="flex gap-3">
                        <button @click="toDelete = null" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">إلغاء</button>
                        <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg text-sm">احذف</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
