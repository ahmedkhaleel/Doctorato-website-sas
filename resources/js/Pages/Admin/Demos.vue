<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ demos: Array });

const selectedDemo = ref(null);
const demoToDelete = ref(null);
const search = ref('');
const statusFilter = ref('all');

const statusForm = useForm({ status: '', admin_notes: '' });

const statusLabels = {
    new: 'جديد', contacted: 'تم التواصل', demo_scheduled: 'عرض مجدول',
    demo_done: 'عرض مكتمل', converted: 'تم التحويل', lost: 'خسارة',
};
const statusColors = {
    new: 'from-blue-500 to-blue-600',
    contacted: 'from-amber-500 to-yellow-600',
    demo_scheduled: 'from-purple-500 to-indigo-600',
    demo_done: 'from-emerald-500 to-green-600',
    converted: 'from-teal-500 to-cyan-600',
    lost: 'from-red-500 to-rose-600',
};

const filteredDemos = computed(() => {
    let list = props.demos || [];
    if (statusFilter.value !== 'all') list = list.filter(d => d.status === statusFilter.value);
    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        list = list.filter(d =>
            (d.clinic_name || '').toLowerCase().includes(q) ||
            (d.full_name || '').toLowerCase().includes(q) ||
            (d.email || '').toLowerCase().includes(q)
        );
    }
    return list;
});

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

function confirmDelete(demo) {
    demoToDelete.value = demo;
}

function deleteDemo() {
    router.delete(`/admin/demos/${demoToDelete.value.id}`, {
        onSuccess: () => { demoToDelete.value = null; }
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric' });
}

function initials(name) {
    if (!name) return '?';
    return name.trim().split(/\s+/).slice(0, 2).map(p => p[0]).join('').toUpperCase();
}
</script>

<template>
    <AdminLayout>
        <Head title="طلبات العرض التجريبي" />

        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-[#0D2B45]">طلبات العرض التجريبي</h1>
                <p class="text-gray-500 text-sm mt-1">إدارة ومتابعة طلبات العملاء المحتملين</p>
            </div>
            <div class="flex items-center gap-2 bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white px-5 py-3 rounded-2xl shadow-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div>
                    <p class="text-[10px] text-white/70">الإجمالي</p>
                    <p class="text-lg font-bold leading-none">{{ demos.length }}</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-5 h-5 text-gray-400 absolute start-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="search" type="text" placeholder="بحث بالاسم أو العيادة أو البريد..."
                    class="w-full ps-10 pe-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#1B4F72] focus:ring-2 focus:ring-[#1B4F72]/10 outline-none text-sm">
            </div>
            <select v-model="statusFilter" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#1B4F72] outline-none text-sm min-w-[180px]">
                <option value="all">كل الحالات</option>
                <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">العيادة / الاسم</th>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">التواصل</th>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">التخصص</th>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">الحالة</th>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">التاريخ</th>
                            <th class="text-center px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="d in filteredDemos" :key="d.id" class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] flex items-center justify-center text-white font-bold text-sm shadow-md">
                                        {{ initials(d.clinic_name) }}
                                    </div>
                                    <div>
                                        <p class="text-gray-800 font-semibold">{{ d.clinic_name }}</p>
                                        <p class="text-gray-500 text-xs">{{ d.full_name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-gray-700 text-xs" dir="ltr">{{ d.email }}</p>
                                <p class="text-gray-500 text-xs mt-0.5" dir="ltr">{{ d.country_code }}{{ d.phone }}</p>
                            </td>
                            <td class="px-5 py-4 text-gray-600 text-xs">{{ d.specialty }}</td>
                            <td class="px-5 py-4">
                                <span :class="statusColors[d.status]" class="inline-flex items-center gap-1.5 bg-gradient-to-r text-white px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    {{ statusLabels[d.status] }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">{{ formatDate(d.created_at) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="openDemo(d)" class="action-btn manage" title="إدارة">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(d)" class="action-btn delete" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filteredDemos.length">
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                لا توجد طلبات مطابقة
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Detail Modal -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in" leave-to-class="opacity-0">
            <div v-if="selectedDemo" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] p-5 text-white flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center font-bold">
                                {{ initials(selectedDemo.clinic_name) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">{{ selectedDemo.clinic_name }}</h3>
                                <p class="text-white/70 text-xs">{{ selectedDemo.full_name }}</p>
                            </div>
                        </div>
                        <button @click="selectedDemo = null" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] text-gray-500 uppercase">البريد</p>
                                <p class="text-gray-800 text-xs mt-1" dir="ltr">{{ selectedDemo.email }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] text-gray-500 uppercase">الهاتف</p>
                                <p class="text-gray-800 text-xs mt-1" dir="ltr">{{ selectedDemo.country_code }}{{ selectedDemo.phone }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] text-gray-500 uppercase">التخصص</p>
                                <p class="text-gray-800 text-xs mt-1">{{ selectedDemo.specialty }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] text-gray-500 uppercase">عدد الأطباء</p>
                                <p class="text-gray-800 text-xs mt-1">{{ selectedDemo.doctors_count }}</p>
                            </div>
                        </div>
                        <div v-if="selectedDemo.notes" class="bg-gray-50 p-3 rounded-xl">
                            <p class="text-[10px] text-gray-500 uppercase mb-1">ملاحظات العميل</p>
                            <p class="text-gray-700 text-xs">{{ selectedDemo.notes }}</p>
                        </div>
                        <form @submit.prevent="updateStatus" class="space-y-3 border-t border-gray-100 pt-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">الحالة</label>
                                <select v-model="statusForm.status" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-[#1B4F72] outline-none">
                                    <option v-for="(label, key) in statusLabels" :key="key" :value="key">{{ label }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">ملاحظات المدير</label>
                                <textarea v-model="statusForm.admin_notes" rows="2" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-[#1B4F72] outline-none"></textarea>
                            </div>
                            <button type="submit" :disabled="statusForm.processing"
                                class="w-full bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white py-2.5 rounded-xl text-sm font-semibold hover:shadow-lg transition-all hover:scale-[1.02]">
                                تحديث الحالة
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Delete Confirmation -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in" leave-to-class="opacity-0">
            <div v-if="demoToDelete" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">تأكيد الحذف</h3>
                    <p class="text-gray-500 text-sm mb-5">سيتم حذف الطلب نهائياً ولا يمكن التراجع</p>
                    <div class="flex gap-2">
                        <button @click="demoToDelete = null" class="flex-1 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">إلغاء</button>
                        <button @click="deleteDemo" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold hover:shadow-lg transition">حذف</button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<style scoped>
.action-btn {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F3F4F6;
    color: #6B7280;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}
.action-btn:hover {
    transform: scale(1.12);
    box-shadow: 0 8px 20px -6px rgba(0,0,0,0.2);
}
.action-btn.manage:hover {
    background: linear-gradient(135deg, #1B4F72, #0D2B45);
    color: white;
    transform: scale(1.12) rotate(90deg);
}
.action-btn.delete:hover {
    background: linear-gradient(135deg, #ef4444, #e11d48);
    color: white;
    transform: scale(1.12) rotate(-6deg);
}
</style>
