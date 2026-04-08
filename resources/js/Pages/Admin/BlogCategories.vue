<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    categories: { type: Array, default: () => [] },
});

const editing = ref(null);

const form = useForm({
    name_ar: '',
    name_en: '',
    display_order: 0,
});

function startCreate() {
    editing.value = null;
    form.reset();
}

function startEdit(cat) {
    editing.value = cat;
    form.name_ar = cat.name_ar;
    form.name_en = cat.name_en;
    form.display_order = cat.display_order || 0;
}

function save() {
    const opts = {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = null;
            form.reset();
        },
    };
    if (editing.value) {
        form.put(`/admin/blog/categories/${editing.value.id}`, opts);
    } else {
        form.post('/admin/blog/categories', opts);
    }
}

function destroy(cat) {
    if (!confirm(`حذف التصنيف "${cat.name_ar}"؟ المقالات المرتبطة لن تُحذف.`)) return;
    router.delete(`/admin/blog/categories/${cat.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head title="تصنيفات المدونة — لوحة التحكم" />

    <AdminLayout page-title="تصنيفات المدونة">
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Form -->
            <div class="lg:col-span-1 bg-white rounded-2xl border border-gray-100 p-6 h-fit">
                <h3 class="text-base font-bold text-gray-800 mb-4">{{ editing ? 'تعديل التصنيف' : 'تصنيف جديد' }}</h3>
                <form @submit.prevent="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">الاسم (عربي)</label>
                        <input v-model="form.name_ar" type="text" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                        <p v-if="form.errors.name_ar" class="text-xs text-red-600 mt-1">{{ form.errors.name_ar }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Name (English)</label>
                        <input v-model="form.name_en" type="text" dir="ltr" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">ترتيب العرض</label>
                        <input v-model.number="form.display_order" type="number" min="0" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-2 bg-[#1B4F72] hover:bg-[#0A1628] text-white rounded-lg text-sm font-semibold disabled:opacity-60">
                            {{ editing ? 'حفظ' : 'إنشاء' }}
                        </button>
                        <button v-if="editing" type="button" @click="startCreate" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">إلغاء</button>
                    </div>
                </form>
            </div>

            <!-- List -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-right">الترتيب</th>
                            <th class="px-4 py-3 text-right">الاسم</th>
                            <th class="px-4 py-3 text-right">Slug</th>
                            <th class="px-4 py-3 text-right">المقالات</th>
                            <th class="px-4 py-3 text-right">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cat in categories" :key="cat.id" class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500">{{ cat.display_order }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800">{{ cat.name_ar }}</div>
                                <div class="text-xs text-gray-400">{{ cat.name_en }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-mono text-gray-500">{{ cat.slug }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ cat.posts_count || 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <button @click="startEdit(cat)" class="text-xs text-[#1B4F72] hover:underline">تعديل</button>
                                    <button @click="destroy(cat)" class="text-xs text-red-600 hover:underline">حذف</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!categories.length">
                            <td colspan="5" class="px-4 py-12 text-center text-gray-400">لا توجد تصنيفات بعد</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
