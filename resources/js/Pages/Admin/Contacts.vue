<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({ contacts: Array });

const selectedContact = ref(null);

function markRead(contact) {
    router.put(`/admin/contacts/${contact.id}/read`);
}

function deleteContact(contact) {
    if (confirm('هل أنت متأكد من حذف هذه الرسالة؟')) {
        router.delete(`/admin/contacts/${contact.id}`);
    }
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}
</script>

<template>
    <AdminLayout>
        <Head title="رسائل التواصل" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">رسائل التواصل</h1>
            <p class="text-gray-500 text-sm mt-1">رسائل الزوار من نموذج التواصل</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-right px-4 py-3 text-gray-600">الاسم</th>
                        <th class="text-right px-4 py-3 text-gray-600">البريد</th>
                        <th class="text-right px-4 py-3 text-gray-600">الموضوع</th>
                        <th class="text-right px-4 py-3 text-gray-600">التاريخ</th>
                        <th class="text-right px-4 py-3 text-gray-600">الحالة</th>
                        <th class="text-right px-4 py-3 text-gray-600">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr v-for="c in contacts" :key="c.id" :class="[!c.is_read ? 'bg-blue-50/50 font-medium' : '']" class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-800">{{ c.name }}</td>
                        <td class="px-4 py-3 text-gray-600" dir="ltr">{{ c.email }}</td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs truncate">{{ c.subject }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ formatDate(c.created_at) }}</td>
                        <td class="px-4 py-3">
                            <span :class="c.is_read ? 'text-green-600' : 'text-orange-500'" class="text-xs">
                                {{ c.is_read ? 'مقروءة' : 'جديدة' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <button @click="selectedContact = c" class="text-blue-600 hover:text-blue-800 text-xs">عرض</button>
                            <button v-if="!c.is_read" @click="markRead(c)" class="text-green-600 hover:text-green-800 text-xs">قراءة</button>
                            <button @click="deleteContact(c)" class="text-red-500 hover:text-red-700 text-xs">حذف</button>
                        </td>
                    </tr>
                    <tr v-if="!contacts.length">
                        <td colspan="6" class="text-center py-8 text-gray-400">لا توجد رسائل بعد</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- View Modal -->
        <div v-if="selectedContact" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl w-full max-w-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-800">{{ selectedContact.subject }}</h3>
                    <button @click="selectedContact = null" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
                </div>
                <div class="space-y-3 text-sm">
                    <div><span class="text-gray-500">الاسم:</span> <span class="text-gray-800">{{ selectedContact.name }}</span></div>
                    <div><span class="text-gray-500">البريد:</span> <span class="text-gray-800" dir="ltr">{{ selectedContact.email }}</span></div>
                    <div v-if="selectedContact.phone"><span class="text-gray-500">الهاتف:</span> <span class="text-gray-800" dir="ltr">{{ selectedContact.phone }}</span></div>
                    <div><span class="text-gray-500">التاريخ:</span> <span class="text-gray-800">{{ formatDate(selectedContact.created_at) }}</span></div>
                    <div class="pt-3 border-t">
                        <p class="text-gray-500 mb-1">الرسالة:</p>
                        <p class="text-gray-800 whitespace-pre-wrap bg-gray-50 p-3 rounded-lg">{{ selectedContact.message }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
