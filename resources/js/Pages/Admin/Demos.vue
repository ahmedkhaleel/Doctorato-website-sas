<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ demos: Array });

const selectedDemo = ref(null);

const statusForm = useForm({ status: '', admin_notes: '' });

const statusLabels = {
    new: 'جديد', contacted: 'تم التواصل', demo_scheduled: 'عرض مجدول',
    demo_done: 'عرض مكتمل', converted: 'تم التحويل', lost: 'خسارة',
};
const statusColors = {
    new: 'bg-blue-100 text-blue-700', contacted: 'bg-yellow-100 text-yellow-700',
    demo_scheduled: 'bg-purple-100 text-purple-700', demo_done: 'bg-green-100 text-green-700',
    converted: 'bg-emerald-100 text-emerald-700', lost: 'bg-red-100 text-red-700',
};

function openDemo(demo) {
    selectedDemo.value = demo;
    statusForm.status = demo.status;
    statusForm.admin_notes = demo.admin_notes || '';
}

function updateStatus() {
    statusForm.put(`/admin/demos/${selectedDemo.value.id}/status`, {
        onSuccess: () => { selectedDemo.value = null; }
    });
}

function deleteDemo(demo) {
    if (confirm('هل أنت متأكد من حذف هذا الطلب؟')) {
        router.delete(`/admin/demos/${demo.id}`);
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric' });
}
</script>

<template>
    <AdminLayout>
        <Head title="طلبات العرض التجريبي" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">طلبات العرض التجريبي</h1>
            <p class="text-gray-500 text-sm mt-1">إدارة طلبات العرض التجريبي</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-3 text-gray-600">العيادة</th>
                        <th class="text-right px-4 py-3 text-gray-600">الاسم</th>
                        <th class="text-right px-4 py-3 text-gray-600">البريد</th>
                        <th class="text-right px-4 py-3 text-gray-600">الهاتف</th>
                        <th class="text-right px-4 py-3 text-gray-600">التخصص</th>
                        <th class="text-right px-4 py-3 text-gray-600">الحالة</th>
                        <th class="text-right px-4 py-3 text-gray-600">التاريخ</th>
                        <th class="text-right px-4 py-3 text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="d in demos" :key="d.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800 font-medium">{{ d.clinic_name }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ d.full_name }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs" dir="ltr">{{ d.email }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs" dir="ltr">{{ d.country_code }}{{ d.phone }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ d.specialty }}</td>
                        <td class="px-4 py-3">
                            <span :class="statusColors[d.status]" class="px-2 py-1 rounded-full text-xs">{{ statusLabels[d.status] }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ formatDate(d.created_at) }}</td>
                        <td class="px-4 py-3">
                            <button @click="openDemo(d)" class="text-blue-600 hover:text-blue-800 ml-3 text-xs">إدارة</button>
                            <button @click="deleteDemo(d)" class="text-red-500 hover:text-red-700 text-xs">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="!demos.length">
                        <td colspan="8" class="text-center py-8 text-gray-400">لا توجد طلبات بعد</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Detail Modal -->
        <div v-if="selectedDemo" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-800">{{ selectedDemo.clinic_name }}</h3>
                    <button @click="selectedDemo = null" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <div class="space-y-2 text-sm mb-4">
                    <div><span class="text-gray-500">الاسم:</span> {{ selectedDemo.full_name }}</div>
                    <div><span class="text-gray-500">البريد:</span> <span dir="ltr">{{ selectedDemo.email }}</span></div>
                    <div><span class="text-gray-500">الهاتف:</span> <span dir="ltr">{{ selectedDemo.country_code }}{{ selectedDemo.phone }}</span></div>
                    <div><span class="text-gray-500">عدد الأطباء:</span> {{ selectedDemo.doctors_count }}</div>
                    <div v-if="selectedDemo.notes"><span class="text-gray-500">ملاحظات:</span> {{ selectedDemo.notes }}</div>
                </div>
                <form @submit.prevent="updateStatus" class="space-y-3 border-t pt-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">الحالة</label>
                        <select v-model="statusForm.status" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">ملاحظات المدير</label>
                        <textarea v-model="statusForm.admin_notes" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
                    </div>
                    <button type="submit" :disabled="statusForm.processing" class="w-full bg-[#1B4F72] text-white py-2 rounded-lg text-sm hover:bg-[#1B4F72]/90">
                        تحديث الحالة
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
