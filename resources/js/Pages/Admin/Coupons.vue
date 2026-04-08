<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    coupons: { type: Array, default: () => [] },
    plans: { type: Array, default: () => [] },
    stats: { type: Object, required: true },
});

const showModal = ref(false);
const editing = ref(null);
const toDelete = ref(null);
const search = ref('');

const form = useForm({
    code: '',
    description_ar: '',
    description_en: '',
    type: 'percentage',
    value: 10,
    min_amount: null,
    max_uses: null,
    max_uses_per_customer: null,
    plan_ids: [],
    starts_at: '',
    expires_at: '',
    is_active: true,
});

const filtered = computed(() => {
    if (!search.value.trim()) return props.coupons;
    const q = search.value.toLowerCase();
    return props.coupons.filter(
        (c) =>
            (c.code || '').toLowerCase().includes(q) ||
            (c.description_ar || '').toLowerCase().includes(q)
    );
});

function isExpired(c) {
    return c.expires_at && new Date(c.expires_at) < new Date();
}

function isExhausted(c) {
    return c.max_uses && c.used_count >= c.max_uses;
}

function status(c) {
    if (!c.is_active) return { label: 'معطّل', color: 'bg-gray-100 text-gray-600' };
    if (isExpired(c)) return { label: 'منتهي', color: 'bg-red-100 text-red-700' };
    if (isExhausted(c)) return { label: 'نفد', color: 'bg-amber-100 text-amber-700' };
    return { label: 'نشط', color: 'bg-emerald-100 text-emerald-700' };
}

function openCreate() {
    editing.value = null;
    form.reset();
    form.type = 'percentage';
    form.value = 10;
    form.is_active = true;
    form.plan_ids = [];
    showModal.value = true;
}

function openEdit(c) {
    editing.value = c;
    form.code = c.code;
    form.description_ar = c.description_ar || '';
    form.description_en = c.description_en || '';
    form.type = c.type;
    form.value = parseFloat(c.value);
    form.min_amount = c.min_amount ? parseFloat(c.min_amount) : null;
    form.max_uses = c.max_uses;
    form.max_uses_per_customer = c.max_uses_per_customer;
    form.plan_ids = c.plan_ids || [];
    form.starts_at = c.starts_at ? c.starts_at.slice(0, 16) : '';
    form.expires_at = c.expires_at ? c.expires_at.slice(0, 16) : '';
    form.is_active = c.is_active;
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
        form.put(`/admin/coupons/${editing.value.id}`, opts);
    } else {
        form.post('/admin/coupons', opts);
    }
}

function confirmDelete(c) {
    toDelete.value = c;
}

function doDelete() {
    if (!toDelete.value) return;
    router.delete(`/admin/coupons/${toDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (toDelete.value = null),
    });
}

function copyCode(code) {
    navigator.clipboard?.writeText(code);
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('ar-EG', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="الكوبونات — لوحة التحكم" />

    <AdminLayout page-title="الكوبونات والخصومات">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100">
                <p class="text-xs text-emerald-700">نشط</p>
                <p class="text-3xl font-bold text-emerald-700 mt-1">{{ stats.active }}</p>
            </div>
            <div class="bg-red-50 rounded-2xl p-5 border border-red-100">
                <p class="text-xs text-red-700">منتهي</p>
                <p class="text-3xl font-bold text-red-700 mt-1">{{ stats.expired }}</p>
            </div>
            <div class="bg-gradient-to-br from-[#C4A265] to-[#D4B876] text-white rounded-2xl p-5">
                <p class="text-xs text-white/80">مرات الاستخدام</p>
                <p class="text-3xl font-bold mt-1">{{ stats.total_redemptions }}</p>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white rounded-xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input v-model="search" placeholder="بحث بالكود أو الوصف" class="flex-1 min-w-[240px] px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <button @click="openCreate" class="px-5 py-2 bg-gradient-to-br from-[#C4A265] to-[#D4B876] text-white rounded-lg text-sm font-semibold shadow hover:shadow-md transition">
                + كوبون جديد
            </button>
        </div>

        <!-- Coupon cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="c in filtered"
                :key="c.id"
                class="relative bg-white rounded-2xl border-2 border-dashed border-gray-200 hover:border-[#C4A265]/40 p-5 transition-all hover:shadow-xl hover:-translate-y-0.5 overflow-hidden"
            >
                <!-- Perforated side -->
                <div class="absolute top-0 bottom-0 start-1/3 w-px bg-gray-200" style="background-image: linear-gradient(to bottom, #d1d5db 50%, transparent 50%); background-size: 1px 8px; background-repeat: repeat-y;"></div>

                <div class="flex items-start justify-between mb-3">
                    <div class="min-w-0">
                        <button @click="copyCode(c.code)" class="font-mono font-black text-lg text-[#C4A265] hover:underline truncate block" :title="c.code">
                            {{ c.code }}
                        </button>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ c.description_ar || '—' }}</p>
                    </div>
                    <span :class="status(c).color" class="shrink-0 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">
                        {{ status(c).label }}
                    </span>
                </div>

                <div class="flex items-baseline gap-2 mb-4">
                    <span class="text-4xl font-black text-[#1B4F72] tabular-nums">
                        {{ c.value }}{{ c.type === 'percentage' ? '%' : '' }}
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ c.type === 'percentage' ? 'خصم' : 'ج.م خصم' }}
                    </span>
                </div>

                <div class="space-y-1.5 text-xs text-gray-500 mb-4 pt-3 border-t border-gray-100">
                    <div v-if="c.min_amount" class="flex justify-between">
                        <span>حد أدنى:</span>
                        <span class="font-semibold">{{ c.min_amount }} ج.م</span>
                    </div>
                    <div class="flex justify-between">
                        <span>الاستخدام:</span>
                        <span class="font-semibold">{{ c.used_count }}/{{ c.max_uses || '∞' }}</span>
                    </div>
                    <div v-if="c.expires_at" class="flex justify-between">
                        <span>ينتهي في:</span>
                        <span class="font-semibold">{{ fmtDate(c.expires_at) }}</span>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button @click="openEdit(c)" class="flex-1 px-3 py-1.5 bg-gray-50 hover:bg-[#1B4F72]/5 text-[#1B4F72] rounded-lg text-xs font-semibold transition">تعديل</button>
                    <button @click="confirmDelete(c)" class="px-3 py-1.5 text-red-500 hover:bg-red-50 rounded-lg text-xs font-semibold transition">حذف</button>
                </div>
            </div>

            <div v-if="!filtered.length" class="md:col-span-2 lg:col-span-3 bg-white rounded-2xl border border-gray-100 p-16 text-center text-gray-400">
                لا توجد كوبونات بعد — أنشئ كوبونك الأول!
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" @click.self="showModal = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden">
                    <div class="bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white p-6">
                        <h3 class="text-lg font-bold">{{ editing ? 'تعديل الكوبون' : 'كوبون جديد' }}</h3>
                        <p class="text-xs text-white/60 mt-1">أدخل تفاصيل الخصم وشروط الاستخدام</p>
                    </div>

                    <form @submit.prevent="save" class="p-6 space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">كود الكوبون</label>
                                <input v-model="form.code" type="text" required placeholder="LAUNCH30" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono font-bold uppercase focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                                <p v-if="form.errors.code" class="text-xs text-red-600 mt-1">{{ form.errors.code }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">نوع الخصم</label>
                                <select v-model="form.type" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                                    <option value="percentage">نسبة مئوية %</option>
                                    <option value="fixed">مبلغ ثابت (ج.م)</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">قيمة الخصم {{ form.type === 'percentage' ? '(0-100)' : '(ج.م)' }}</label>
                                <input v-model.number="form.value" type="number" required :max="form.type === 'percentage' ? 100 : null" min="0" step="0.01" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">حد أدنى للمبلغ (اختياري)</label>
                                <input v-model.number="form.min_amount" type="number" min="0" placeholder="مثلاً 500" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">الوصف (عربي)</label>
                                <input v-model="form.description_ar" type="text" placeholder="عرض إطلاق" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Description (English)</label>
                                <input v-model="form.description_en" type="text" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">أقصى عدد استخدامات</label>
                                <input v-model.number="form.max_uses" type="number" min="1" placeholder="∞" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">يبدأ في</label>
                                <input v-model="form.starts_at" type="datetime-local" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">ينتهي في</label>
                                <input v-model="form.expires_at" type="datetime-local" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">الباقات المسموح بها (اتركها فارغة = كل الباقات)</label>
                            <div class="flex flex-wrap gap-2">
                                <label v-for="plan in plans" :key="plan.id" class="inline-flex items-center gap-2 px-3 py-1.5 border border-gray-200 rounded-lg cursor-pointer text-sm hover:bg-gray-50">
                                    <input v-model="form.plan_ids" type="checkbox" :value="plan.id" />
                                    <span>{{ plan.name_ar }}</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">تفعيل الكوبون</p>
                                <p class="text-xs text-gray-500 mt-0.5">العملاء يستطيعون استخدامه فور التفعيل</p>
                            </div>
                            <button type="button" @click="form.is_active = !form.is_active" class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors" :class="form.is_active ? 'bg-emerald-500' : 'bg-gray-300'">
                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform" :class="form.is_active ? 'translate-x-1' : 'translate-x-6'"></span>
                            </button>
                        </div>

                        <div class="flex gap-3 justify-end pt-4 border-t border-gray-100">
                            <button type="button" @click="showModal = false" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold">إلغاء</button>
                            <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white rounded-lg text-sm font-semibold disabled:opacity-60">
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
                    <h3 class="text-lg font-bold text-gray-800 mb-2">حذف الكوبون؟</h3>
                    <p class="text-sm text-gray-500 mb-6 font-mono">{{ toDelete.code }}</p>
                    <div class="flex gap-3">
                        <button @click="toDelete = null" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">إلغاء</button>
                        <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg text-sm">احذف</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
