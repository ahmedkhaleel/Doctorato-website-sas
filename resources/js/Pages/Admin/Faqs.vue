<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ faqs: Array });

const showModal = ref(false);
const editingFaq = ref(null);

const form = useForm({
    category: 'general',
    question_ar: '',
    question_en: '',
    answer_ar: '',
    answer_en: '',
    display_order: 0,
    is_active: true,
});

function openAdd() {
    editingFaq.value = null;
    form.reset();
    form.is_active = true;
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
    showModal.value = true;
}

function submit() {
    if (editingFaq.value) {
        form.put(`/admin/faqs/${editingFaq.value.id}`, { onSuccess: () => { showModal.value = false; } });
    } else {
        form.post('/admin/faqs', { onSuccess: () => { showModal.value = false; } });
    }
}

function deleteFaq(faq) {
    if (confirm('هل أنت متأكد من حذف هذا السؤال؟')) {
        router.delete(`/admin/faqs/${faq.id}`);
    }
}

const categoryLabels = { general: 'عام', pricing: 'أسعار', technical: 'تقني' };
</script>

<template>
    <AdminLayout>
        <Head title="إدارة الأسئلة الشائعة" />

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">الأسئلة الشائعة</h1>
                <p class="text-gray-500 text-sm mt-1">إدارة الأسئلة والأجوبة المتكررة</p>
            </div>
            <button @click="openAdd" class="bg-[#1B4F72] text-white px-4 py-2 rounded-lg hover:bg-[#1B4F72]/90 transition text-sm">
                + إضافة سؤال
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-3 text-gray-600">#</th>
                        <th class="text-right px-4 py-3 text-gray-600">السؤال (عربي)</th>
                        <th class="text-right px-4 py-3 text-gray-600">التصنيف</th>
                        <th class="text-right px-4 py-3 text-gray-600">الترتيب</th>
                        <th class="text-right px-4 py-3 text-gray-600">الحالة</th>
                        <th class="text-right px-4 py-3 text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="faq in faqs" :key="faq.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-500">{{ faq.id }}</td>
                        <td class="px-4 py-3 text-gray-800 max-w-xs truncate">{{ faq.question_ar }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs" :class="{
                                'bg-blue-100 text-blue-700': faq.category === 'general',
                                'bg-green-100 text-green-700': faq.category === 'pricing',
                                'bg-purple-100 text-purple-700': faq.category === 'technical',
                            }">{{ categoryLabels[faq.category] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ faq.display_order }}</td>
                        <td class="px-4 py-3">
                            <span :class="faq.is_active ? 'text-green-600' : 'text-red-500'" class="text-xs font-medium">
                                {{ faq.is_active ? 'مفعّل' : 'معطّل' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <button @click="openEdit(faq)" class="text-blue-600 hover:text-blue-800 ml-3 text-xs">تعديل</button>
                            <button @click="deleteFaq(faq)" class="text-red-500 hover:text-red-700 text-xs">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="!faqs.length">
                        <td colspan="6" class="text-center py-8 text-gray-400">لا توجد أسئلة بعد</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ editingFaq ? 'تعديل السؤال' : 'إضافة سؤال جديد' }}</h3>

                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">التصنيف</label>
                            <select v-model="form.category" class="w-full border rounded-lg px-3 py-2 text-sm">
                                <option value="general">عام</option>
                                <option value="pricing">أسعار</option>
                                <option value="technical">تقني</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الترتيب</label>
                            <input v-model.number="form.display_order" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">السؤال (عربي)</label>
                        <input v-model="form.question_ar" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="rtl" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">السؤال (إنجليزي)</label>
                        <input v-model="form.question_en" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr" />
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">الإجابة (عربي)</label>
                        <textarea v-model="form.answer_ar" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" dir="rtl"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">الإجابة (إنجليزي)</label>
                        <textarea v-model="form.answer_en" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input v-model="form.is_active" type="checkbox" id="active" class="rounded" />
                        <label for="active" class="text-sm text-gray-600">مفعّل</label>
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
