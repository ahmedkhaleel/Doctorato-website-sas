<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ plans: Array });

const showModal = ref(false);
const editingPlan = ref(null);
const newFeatureAr = ref('');
const newFeatureEn = ref('');

const form = useForm({
    name_ar: '', name_en: '', slug: '', description_ar: '', description_en: '',
    monthly_price: 0, yearly_price: 0, currency: 'SAR',
    is_popular: false, is_custom: false, is_active: true,
    features_ar: [], features_en: [],
    max_users: null, max_patients: null, support_level: 'email', display_order: 0,
});

function openAdd() {
    editingPlan.value = null;
    form.reset();
    form.is_active = true;
    form.features_ar = [];
    form.features_en = [];
    showModal.value = true;
}

function openEdit(plan) {
    editingPlan.value = plan;
    Object.keys(form.data()).forEach(key => {
        if (plan[key] !== undefined && plan[key] !== null) form[key] = plan[key];
    });
    form.features_ar = plan.features_ar || [];
    form.features_en = plan.features_en || [];
    showModal.value = true;
}

function addFeature() {
    if (newFeatureAr.value.trim()) {
        form.features_ar.push(newFeatureAr.value.trim());
        form.features_en.push(newFeatureEn.value.trim() || newFeatureAr.value.trim());
        newFeatureAr.value = '';
        newFeatureEn.value = '';
    }
}

function removeFeature(index) {
    form.features_ar.splice(index, 1);
    form.features_en.splice(index, 1);
}

function submit() {
    if (editingPlan.value) {
        form.put(`/admin/plans/${editingPlan.value.id}`, { onSuccess: () => { showModal.value = false; } });
    } else {
        form.post('/admin/plans', { onSuccess: () => { showModal.value = false; } });
    }
}

function deletePlan(plan) {
    if (confirm('هل أنت متأكد من حذف هذه الخطة؟')) {
        router.delete(`/admin/plans/${plan.id}`);
    }
}
</script>

<template>
    <AdminLayout>
        <Head title="إدارة خطط الأسعار" />

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">خطط الأسعار</h1>
                <p class="text-gray-500 text-sm mt-1">إدارة خطط وباقات الأسعار</p>
            </div>
            <button @click="openAdd" class="bg-[#1B4F72] text-white px-4 py-2 rounded-lg hover:bg-[#1B4F72]/90 transition text-sm">
                + إضافة خطة
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="plan in plans" :key="plan.id" class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 relative">
                <span v-if="plan.is_popular" class="absolute top-2 left-2 bg-[#C4A265] text-white text-xs px-2 py-1 rounded-full">الأكثر طلباً</span>
                <span v-if="!plan.is_active" class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">معطّل</span>

                <h3 class="text-lg font-bold text-gray-800 mb-1">{{ plan.name_ar }}</h3>
                <p class="text-xs text-gray-500 mb-3">{{ plan.name_en }}</p>

                <div v-if="!plan.is_custom" class="mb-3">
                    <span class="text-2xl font-bold text-[#1B4F72]">{{ plan.monthly_price }}</span>
                    <span class="text-sm text-gray-500"> ر.س / شهر</span>
                </div>
                <div v-else class="mb-3">
                    <span class="text-lg font-bold text-[#C4A265]">تواصل معنا</span>
                </div>

                <div class="text-xs text-gray-500 mb-3">
                    <span>المستخدمين: {{ plan.max_users || 'غير محدود' }}</span> •
                    <span>المرضى: {{ plan.max_patients || 'غير محدود' }}</span>
                </div>

                <p class="text-xs text-gray-500 mb-3">{{ (plan.features_ar || []).length }} ميزة</p>

                <div class="flex gap-2">
                    <button @click="openEdit(plan)" class="flex-1 text-center bg-blue-50 text-blue-600 py-2 rounded-lg text-xs hover:bg-blue-100">تعديل</button>
                    <button @click="deletePlan(plan)" class="bg-red-50 text-red-500 px-3 py-2 rounded-lg text-xs hover:bg-red-100">حذف</button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ editingPlan ? 'تعديل الخطة' : 'إضافة خطة جديدة' }}</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الاسم (عربي)</label>
                            <input v-model="form.name_ar" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الاسم (إنجليزي)</label>
                            <input v-model="form.name_en" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Slug</label>
                            <input v-model="form.slug" type="text" class="w-full border rounded-lg px-3 py-2 text-sm font-mono" dir="ltr" :disabled="!!editingPlan" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الوصف (عربي)</label>
                            <input v-model="form.description_ar" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الوصف (إنجليزي)</label>
                            <input v-model="form.description_en" type="text" class="w-full border rounded-lg px-3 py-2 text-sm" dir="ltr" />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">السعر الشهري</label>
                            <input v-model.number="form.monthly_price" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">السعر السنوي</label>
                            <input v-model.number="form.yearly_price" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الحد الأقصى للمستخدمين</label>
                            <input v-model.number="form.max_users" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="فارغ = غير محدود" />
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">الحد الأقصى للمرضى</label>
                            <input v-model.number="form.max_patients" type="number" class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="فارغ = غير محدود" />
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 text-sm"><input v-model="form.is_popular" type="checkbox" class="rounded" /> الأكثر طلباً</label>
                        <label class="flex items-center gap-2 text-sm"><input v-model="form.is_custom" type="checkbox" class="rounded" /> خطة مخصصة</label>
                        <label class="flex items-center gap-2 text-sm"><input v-model="form.is_active" type="checkbox" class="rounded" /> مفعّلة</label>
                    </div>

                    <!-- Features -->
                    <div class="border-t pt-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">الميزات</label>
                        <div class="space-y-2 max-h-40 overflow-y-auto mb-3">
                            <div v-for="(feature, i) in form.features_ar" :key="i" class="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-lg text-sm">
                                <span class="flex-1">{{ feature }}</span>
                                <button type="button" @click="removeFeature(i)" class="text-red-400 hover:text-red-600">✕</button>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <input v-model="newFeatureAr" type="text" placeholder="الميزة بالعربي" class="flex-1 border rounded-lg px-3 py-2 text-sm" @keyup.enter.prevent="addFeature" />
                            <input v-model="newFeatureEn" type="text" placeholder="بالإنجليزي" class="flex-1 border rounded-lg px-3 py-2 text-sm" dir="ltr" @keyup.enter.prevent="addFeature" />
                            <button type="button" @click="addFeature" class="bg-gray-200 px-3 py-2 rounded-lg text-sm hover:bg-gray-300">+</button>
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
