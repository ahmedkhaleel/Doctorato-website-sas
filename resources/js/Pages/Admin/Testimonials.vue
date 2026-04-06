<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ testimonials: Array });

const showModal = ref(false);
const editingItem = ref(null);

const form = useForm({
    client_name_ar: '', client_name_en: '',
    clinic_name_ar: '', clinic_name_en: '',
    role_ar: '', role_en: '',
    review_ar: '', review_en: '',
    rating: 5, display_order: 0, is_active: true,
});

function openAdd() {
    editingItem.value = null;
    form.reset();
    form.is_active = true;
    form.rating = 5;
    showModal.value = true;
}

function openEdit(item) {
    editingItem.value = item;
    Object.keys(form.data()).forEach(key => {
        if (item[key] !== undefined && item[key] !== null) form[key] = item[key];
    });
    showModal.value = true;
}

function submit() {
    if (editingItem.value) {
        form.put(`/admin/testimonials/${editingItem.value.id}`, { onSuccess: () => { showModal.value = false; } });
    } else {
        form.post('/admin/testimonials', { onSuccess: () => { showModal.value = false; } });
    }
}

function deleteItem(item) {
    if (confirm('هل أنت متأكد من حذف هذه الشهادة؟')) {
        router.delete(`/admin/testimonials/${item.id}`);
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="إدارة الشهادات" />

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">الشهادات</h1>
                <p class="text-gray-500 text-sm mt-1">آراء وشهادات العملاء</p>
            </div>
            <button @click="openAdd" class="bg-[#1B4F72] text-white px-4 py-2 rounded-lg hover:bg-[#1B4F72]/90 transition text-sm">
                + إضافة شهادة
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-3 text-gray-600">العميل</th>
                        <th class="text-right px-4 py-3 text-gray-600">العيادة</th>
                        <th class="text-right px-4 py-3 text-gray-600">التقييم</th>
                        <th class="text-right px-4 py-3 text-gray-600">الحالة</th>
                        <th class="text-right px-4 py-3 text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="t in testimonials" :key="t.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">{{ t.client_name_ar }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ t.clinic_name_ar }}</td>
                        <td class="px-4 py-3">
                            <span class="text-yellow-500">{{ '★'.repeat(t.rating) }}{{ '☆'.repeat(5 - t.rating) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="t.is_active ? 'text-green-600' : 'text-red-500'" class="text-xs font-medium">
                                {{ t.is_active ? 'مفعّل' : 'معطّل' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button @click="openEdit(t)" class="text-blue-600 hover:text-blue-800 ml-3 text-xs">تعديل</button>
                            <button @click="deleteItem(t)" class="text-red-500 hover:text-red-700 text-xs">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="!testimonials.length">
                        <td colspan="5" class="text-center py-8 text-gray-400">لا توجد شهادات بعد</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ editingItem ? 'تعديل الشهادة' : 'إضافة شهادة جديدة' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">اسم العميل (عربي)</label>
                            <input v-model="form.client_name_ar" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">اسم العميل (إنجليزي)</label>
                            <input v-model="form.client_name_en" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">اسم العيادة (عربي)</label>
                            <input v-model="form.clinic_name_ar" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">اسم العيادة (إنجليزي)</label>
                            <input v-model="form.clinic_name_en" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">المنصب (عربي)</label>
                            <input v-model="form.role_ar" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">المنصب (إنجليزي)</label>
                            <input v-model="form.role_en" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">المراجعة (عربي)</label>
                        <textarea v-model="form.review_ar" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" dir="rtl"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">المراجعة (إنجليزي)</label>
                        <textarea v-model="form.review_en" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr"></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">التقييم</label>
                            <select v-model.number="form.rating" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option v-for="n in 5" :key="n" :value="n">{{ '★'.repeat(n) }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الترتيب</label>
                            <input v-model.number="form.display_order" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div class="flex items-end">
                            <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> مفعّلة</label>
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
