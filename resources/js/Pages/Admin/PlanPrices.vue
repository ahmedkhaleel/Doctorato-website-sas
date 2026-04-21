<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    plans: { type: Array, default: () => [] },
    prices: { type: Array, default: () => [] },
});

const showModal = ref(false);
const editing = ref(null);
const toDelete = ref(null);

const form = useForm({
    pricing_plan_id: null,
    country_code: '',
    country_name_ar: '',
    country_name_en: '',
    country_flag: '',
    currency_code: '',
    currency_symbol: '',
    monthly_price: 0,
    yearly_price: 0,
    setup_fee: 0,
    is_active: true,
});

// Group prices by country_code so we can render a pivoted table:
// rows = unique country codes, columns = plans, cell = price lookup.
const countries = computed(() => {
    const map = new Map();
    for (const p of props.prices) {
        if (!map.has(p.country_code)) {
            map.set(p.country_code, {
                country_code: p.country_code,
                country_name_ar: p.country_name_ar,
                country_name_en: p.country_name_en,
                country_flag: p.country_flag,
                currency_code: p.currency_code,
                currency_symbol: p.currency_symbol,
            });
        }
    }
    return [...map.values()].sort((a, b) => a.country_code.localeCompare(b.country_code));
});

function priceFor(planId, countryCode) {
    return props.prices.find((p) => p.pricing_plan_id === planId && p.country_code === countryCode);
}

// Handy presets used when adding a brand-new country row
const presets = [
    { code: 'EG', name_ar: 'مصر', name_en: 'Egypt', flag: '🇪🇬', currency: 'EGP', symbol: 'ج.م' },
    { code: 'SA', name_ar: 'السعودية', name_en: 'Saudi Arabia', flag: '🇸🇦', currency: 'SAR', symbol: 'ر.س' },
    { code: 'AE', name_ar: 'الإمارات', name_en: 'UAE', flag: '🇦🇪', currency: 'AED', symbol: 'د.إ' },
    { code: 'KW', name_ar: 'الكويت', name_en: 'Kuwait', flag: '🇰🇼', currency: 'KWD', symbol: 'د.ك' },
    { code: 'QA', name_ar: 'قطر', name_en: 'Qatar', flag: '🇶🇦', currency: 'QAR', symbol: 'ر.ق' },
    { code: 'BH', name_ar: 'البحرين', name_en: 'Bahrain', flag: '🇧🇭', currency: 'BHD', symbol: 'د.ب' },
    { code: 'OM', name_ar: 'عُمان', name_en: 'Oman', flag: '🇴🇲', currency: 'OMR', symbol: 'ر.ع' },
    { code: 'JO', name_ar: 'الأردن', name_en: 'Jordan', flag: '🇯🇴', currency: 'JOD', symbol: 'د.أ' },
    { code: 'IQ', name_ar: 'العراق', name_en: 'Iraq', flag: '🇮🇶', currency: 'IQD', symbol: 'د.ع' },
    { code: 'LB', name_ar: 'لبنان', name_en: 'Lebanon', flag: '🇱🇧', currency: 'USD', symbol: '$' },
    { code: 'MA', name_ar: 'المغرب', name_en: 'Morocco', flag: '🇲🇦', currency: 'MAD', symbol: 'د.م' },
    { code: 'US', name_ar: 'الولايات المتحدة', name_en: 'United States', flag: '🇺🇸', currency: 'USD', symbol: '$' },
];

function openCell(planId, countryCode) {
    const existing = priceFor(planId, countryCode);
    editing.value = existing || null;
    form.reset();

    form.pricing_plan_id = planId;
    if (existing) {
        form.country_code = existing.country_code;
        form.country_name_ar = existing.country_name_ar;
        form.country_name_en = existing.country_name_en;
        form.country_flag = existing.country_flag || '';
        form.currency_code = existing.currency_code;
        form.currency_symbol = existing.currency_symbol;
        form.monthly_price = parseFloat(existing.monthly_price);
        form.yearly_price = parseFloat(existing.yearly_price);
        form.setup_fee = parseFloat(existing.setup_fee || 0);
        form.is_active = existing.is_active;
    } else {
        // Prefill from any existing row for this country, or from presets
        const anyForCountry = props.prices.find((p) => p.country_code === countryCode);
        const src = anyForCountry || presets.find((p) => p.code === countryCode);
        if (src) {
            form.country_code = src.code || src.country_code;
            form.country_name_ar = src.name_ar || src.country_name_ar;
            form.country_name_en = src.name_en || src.country_name_en;
            form.country_flag = src.flag || src.country_flag || '';
            form.currency_code = src.currency || src.currency_code;
            form.currency_symbol = src.symbol || src.currency_symbol;
        }
        form.is_active = true;
    }
    showModal.value = true;
}

function openAddCountry() {
    editing.value = null;
    form.reset();
    form.pricing_plan_id = props.plans[0]?.id;
    form.is_active = true;
    showModal.value = true;
}

function applyPreset(code) {
    const preset = presets.find((p) => p.code === code);
    if (!preset) return;
    form.country_code = preset.code;
    form.country_name_ar = preset.name_ar;
    form.country_name_en = preset.name_en;
    form.country_flag = preset.flag;
    form.currency_code = preset.currency;
    form.currency_symbol = preset.symbol;
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
        form.put(`/admin/plan-prices/${editing.value.id}`, opts);
    } else {
        form.post('/admin/plan-prices', opts);
    }
}

function confirmDelete(price) {
    toDelete.value = price;
}

function doDelete() {
    if (!toDelete.value) return;
    router.delete(`/admin/plan-prices/${toDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => (toDelete.value = null),
    });
}

function fmt(v) {
    if (v === null || v === undefined) return '—';
    return new Intl.NumberFormat('ar-EG').format(Math.round(Number(v)));
}
</script>

<template>
    <Head title="أسعار الدول — لوحة التحكم" />

    <AdminLayout page-title="الأسعار حسب الدولة">
        <!-- Header strip -->
        <div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white p-6 md:p-7 shadow-xl">
            <div class="absolute top-0 end-0 w-56 h-56 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-pulse-slow"></div>
            <div class="relative flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center shadow-lg text-2xl">
                        🌍
                    </div>
                    <div>
                        <div class="text-[#C4A265] text-xs uppercase tracking-widest font-semibold mb-1">Multi-currency pricing</div>
                        <h1 class="text-xl md:text-2xl font-extrabold">الأسعار حسب الدولة</h1>
                        <p class="text-white/60 text-sm">كل دولة لها سعرها الخاص — يُعرض تلقائياً لزوار الموقع حسب مكانهم</p>
                    </div>
                </div>
                <button
                    @click="openAddCountry"
                    class="px-5 py-2.5 bg-gradient-to-br from-[#C4A265] to-[#D4B87A] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition inline-flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    إضافة سعر
                </button>
            </div>
        </div>

        <!-- Pivot table: countries × plans -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-widest">الدولة</th>
                            <th v-for="plan in plans" :key="plan.id" class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-widest">
                                {{ plan.name_ar }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="country in countries" :key="country.country_code" class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <span class="text-xl">{{ country.country_flag }}</span>
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ country.country_name_ar }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono">{{ country.country_code }} · {{ country.currency_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td v-for="plan in plans" :key="plan.id" class="px-4 py-3 text-center">
                                <button
                                    v-if="priceFor(plan.id, country.country_code)"
                                    @click="openCell(plan.id, country.country_code)"
                                    class="group inline-flex flex-col items-center rounded-lg px-3 py-1.5 hover:bg-[#1B4F72]/5 transition"
                                >
                                    <span class="font-bold text-gray-800 tabular-nums">
                                        {{ fmt(priceFor(plan.id, country.country_code).monthly_price) }}
                                        <span class="text-[10px] text-gray-400 font-normal">{{ priceFor(plan.id, country.country_code).currency_symbol }}</span>
                                    </span>
                                    <span class="text-[10px] text-gray-400 mt-0.5">
                                        /شهر · سنوي {{ fmt(priceFor(plan.id, country.country_code).yearly_price) }}
                                    </span>
                                    <span v-if="Number(priceFor(plan.id, country.country_code).setup_fee) > 0" class="text-[10px] text-amber-600 mt-0.5 font-semibold">
                                        إعداد: {{ fmt(priceFor(plan.id, country.country_code).setup_fee) }}
                                    </span>
                                    <span v-if="!priceFor(plan.id, country.country_code).is_active" class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded-full bg-red-50 text-red-600 mt-1">
                                        معطّل
                                    </span>
                                </button>
                                <button
                                    v-else
                                    @click="openCell(plan.id, country.country_code)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-dashed border-gray-200 text-gray-400 hover:text-[#1B4F72] hover:border-[#1B4F72]/30 text-xs transition"
                                >
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    أضف
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!countries.length">
                            <td :colspan="plans.length + 1" class="px-4 py-16 text-center text-gray-400">
                                لا توجد أسعار حسب الدولة بعد — اضغط "إضافة سعر"
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto" @click.self="showModal = false">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl my-8 overflow-hidden animate-fade-up">
                    <div class="relative bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white p-6">
                        <div class="absolute top-0 end-0 w-32 h-32 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                        <div class="relative flex items-center justify-between">
                            <h3 class="text-lg font-bold">{{ editing ? 'تعديل السعر' : 'إضافة سعر لدولة' }}</h3>
                            <button @click="showModal = false" class="w-9 h-9 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="save" class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">الباقة</label>
                            <select v-model="form.pricing_plan_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm">
                                <option v-for="p in plans" :key="p.id" :value="p.id">{{ p.name_ar }} · {{ p.name_en }}</option>
                            </select>
                        </div>

                        <div v-if="!editing">
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">قوالب سريعة</label>
                            <div class="flex flex-wrap gap-1.5">
                                <button
                                    v-for="p in presets"
                                    :key="p.code"
                                    type="button"
                                    @click="applyPreset(p.code)"
                                    class="px-2 py-1 rounded-md bg-gray-100 hover:bg-[#1B4F72]/10 text-xs"
                                >
                                    {{ p.flag }} {{ p.code }}
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">كود الدولة (ISO)</label>
                                <input v-model="form.country_code" maxlength="2" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm uppercase font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">علم (emoji)</label>
                                <input v-model="form.country_flag" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-lg" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">اسم الدولة (عربي)</label>
                                <input v-model="form.country_name_ar" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Country name (EN)</label>
                                <input v-model="form.country_name_en" dir="ltr" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">العملة (ISO 4217)</label>
                                <input v-model="form.currency_code" maxlength="3" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm uppercase font-mono" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">رمز العملة</label>
                                <input v-model="form.currency_symbol" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">السعر الشهري</label>
                                <input v-model.number="form.monthly_price" type="number" min="0" step="0.01" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">السعر السنوي</label>
                                <input v-model.number="form.yearly_price" type="number" min="0" step="0.01" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                            </div>
                        </div>

                        <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-4">
                            <label class="block text-xs font-bold text-amber-700 uppercase mb-1">رسوم الإعداد (مرة واحدة)</label>
                            <input v-model.number="form.setup_fee" type="number" min="0" step="0.01" class="w-full px-4 py-2.5 border border-amber-200 bg-white rounded-lg text-sm" />
                            <p class="text-[11px] text-amber-700/80 mt-1.5">
                                رسوم التسطيب والتدريب ونقل البيانات. المشتركين سنوياً يحصلون تلقائياً على خصم 50% منها.
                            </p>
                        </div>

                        <div class="flex items-center justify-between bg-gray-50 rounded-xl p-4">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">ظهور علني</p>
                                <p class="text-xs text-gray-500 mt-0.5">يظهر للزوار من هذه الدولة</p>
                            </div>
                            <button type="button" @click="form.is_active = !form.is_active" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" :class="form.is_active ? 'bg-emerald-500' : 'bg-gray-300'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="form.is_active ? 'translate-x-1' : 'translate-x-6'"></span>
                            </button>
                        </div>

                        <div class="flex gap-3 justify-between pt-4 border-t border-gray-100">
                            <button v-if="editing" type="button" @click="confirmDelete(editing); showModal = false" class="px-5 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm font-semibold">حذف</button>
                            <div class="flex gap-2 ms-auto">
                                <button type="button" @click="showModal = false" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold">إلغاء</button>
                                <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white rounded-lg text-sm font-semibold disabled:opacity-60">
                                    {{ form.processing ? 'جاري الحفظ...' : 'حفظ' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

        <!-- Delete confirm -->
        <Teleport to="body">
            <div v-if="toDelete" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" @click.self="toDelete = null">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">حذف السعر؟</h3>
                    <p class="text-sm text-gray-500 mb-6">{{ toDelete.country_name_ar }} — {{ toDelete.currency_code }} {{ fmt(toDelete.monthly_price) }}</p>
                    <div class="flex gap-3">
                        <button @click="toDelete = null" class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm">إلغاء</button>
                        <button @click="doDelete" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg text-sm">احذف</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
