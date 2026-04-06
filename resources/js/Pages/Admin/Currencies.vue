<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ currencyList: Array });

const showModal = ref(false);
const editingCurrency = ref(null);

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

function deleteCurrency(currency) {
    if (confirm('هل أنت متأكد من حذف هذه العملة؟')) {
        router.delete(`/admin/currencies/${currency.id}`);
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="إدارة العملات" />

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">العملات</h1>
                <p class="text-gray-500 text-sm mt-1">إدارة العملات وأسعار الصرف</p>
            </div>
            <button @click="openAdd" class="bg-[#1B4F72] text-white px-4 py-2 rounded-lg hover:bg-[#1B4F72]/90 transition text-sm">
                + إضافة عملة
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-3 text-gray-600">العلم</th>
                        <th class="text-right px-4 py-3 text-gray-600">الكود</th>
                        <th class="text-right px-4 py-3 text-gray-600">الاسم</th>
                        <th class="text-right px-4 py-3 text-gray-600">الرمز</th>
                        <th class="text-right px-4 py-3 text-gray-600">سعر الصرف</th>
                        <th class="text-right px-4 py-3 text-gray-600">الحالة</th>
                        <th class="text-right px-4 py-3 text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="c in currencyList" :key="c.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xl">{{ c.flag_emoji }}</td>
                        <td class="px-4 py-3 font-mono font-bold text-gray-800">{{ c.code }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ c.name_ar }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ c.symbol }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ c.rate_to_sar }}</td>
                        <td class="px-4 py-3">
                            <span :class="c.is_active ? 'text-green-600' : 'text-red-500'" class="text-xs font-medium">
                                {{ c.is_active ? 'مفعّل' : 'معطّل' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button @click="openEdit(c)" class="text-blue-600 hover:text-blue-800 ml-3 text-xs">تعديل</button>
                            <button @click="deleteCurrency(c)" class="text-red-500 hover:text-red-700 text-xs">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="!currencyList.length">
                        <td colspan="7" class="text-center py-8 text-gray-400">لا توجد عملات بعد</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ editingCurrency ? 'تعديل العملة' : 'إضافة عملة جديدة' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الكود (مثل SAR)</label>
                            <input v-model="form.code" type="text" maxlength="3" class="w-full border rounded-lg px-3 py-2 text-sm font-mono uppercase" :disabled="!!editingCurrency" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الرمز</label>
                            <input v-model="form.symbol" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">العلم</label>
                            <input v-model="form.flag_emoji" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الاسم (عربي)</label>
                            <input v-model="form.name_ar" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="rtl" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الاسم (إنجليزي)</label>
                            <input v-model="form.name_en" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr" />
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">سعر الصرف (من ريال)</label>
                            <input v-model.number="form.rate_to_sar" type="number" step="0.000001" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الكسور العشرية</label>
                            <input v-model.number="form.decimal_places" type="number" min="0" max="4" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">موقع الرمز</label>
                            <select v-model="form.symbol_position" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="before">قبل المبلغ</option>
                                <option value="after">بعد المبلغ</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الترتيب</label>
                            <input v-model.number="form.display_order" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input v-model="form.is_active" type="checkbox" class="rounded" /> مفعّلة
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-3 justify-end pt-4 border-t">
                        <button type="button" @click="showModal = false" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">إلغاء</button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-[#1B4F72] text-white rounded-lg text-sm hover:bg-[#1B4F72]/90 disabled:opacity-50">
                            {{ form.processing ? 'جاري الحفظ...' : 'حفظ' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
