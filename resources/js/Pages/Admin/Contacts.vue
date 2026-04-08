<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({ contacts: Array });

const selectedContact = ref(null);
const contactToDelete = ref(null);
const search = ref('');
const readFilter = ref('all');

const filtered = computed(() => {
    let list = props.contacts || [];
    if (readFilter.value === 'unread') list = list.filter(c => !c.is_read);
    if (readFilter.value === 'read') list = list.filter(c => c.is_read);
    if (search.value.trim()) {
        const q = search.value.toLowerCase();
        list = list.filter(c =>
            (c.name || '').toLowerCase().includes(q) ||
            (c.email || '').toLowerCase().includes(q) ||
            (c.subject || '').toLowerCase().includes(q)
        );
    }
    return list;
});

const unreadCount = computed(() => (props.contacts || []).filter(c => !c.is_read).length);

const stats = computed(() => {
    const list = props.contacts || [];
    const today = list.filter((c) => {
        const d = new Date(c.created_at);
        const now = new Date();
        return d.toDateString() === now.toDateString();
    }).length;
    const week = list.filter((c) => {
        const d = new Date(c.created_at);
        return (Date.now() - d.getTime()) / 86400000 < 7;
    }).length;
    return {
        total: list.length,
        unread: list.filter((c) => !c.is_read).length,
        today,
        week,
    };
});

function markRead(contact) {
    router.put(`/admin/contacts/${contact.id}/read`);
}

function confirmDelete(contact) {
    contactToDelete.value = contact;
}

function deleteContact() {
    router.delete(`/admin/contacts/${contactToDelete.value.id}`, {
        onSuccess: () => { contactToDelete.value = null; }
    });
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('ar-SA', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function initials(name) {
    if (!name) return '?';
    return name.trim().split(/\s+/).slice(0, 2).map(p => p[0]).join('').toUpperCase();
}
</script>

<template>
    <AdminLayout page-title="رسائل التواصل">
        <Head title="رسائل التواصل" />

        <!-- Hero Header -->
        <div class="relative mb-6 overflow-hidden rounded-3xl bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white p-6 lg:p-8 shadow-xl">
            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-pulse-slow"></div>
            <div class="absolute bottom-0 start-0 w-48 h-48 bg-[#1B4F72]/30 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 animate-pulse-slow" style="animation-delay: -3s"></div>
            <svg class="absolute inset-0 w-full h-full opacity-[0.04] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="ct-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#ct-hex)" />
            </svg>

            <div class="relative flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center shadow-lg shadow-[#C4A265]/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-[#C4A265] text-xs mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></span>
                        <span class="uppercase tracking-widest font-semibold">Contact Messages</span>
                    </div>
                    <h1 class="text-2xl lg:text-3xl font-extrabold mb-1">رسائل التواصل</h1>
                    <p class="text-white/60 text-sm">رسائل الزوار من نموذج التواصل</p>
                </div>
            </div>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 animate-stagger">
            <div class="group relative bg-white rounded-2xl p-5 border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-[#1B4F72] to-transparent transition-all duration-700"></div>
                <p class="text-xs text-gray-500 mb-1">إجمالي</p>
                <p class="text-3xl font-extrabold text-gray-800 tabular-nums">{{ stats.total }}</p>
            </div>
            <div class="group relative bg-orange-50 rounded-2xl p-5 border border-orange-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-orange-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-orange-700 mb-1">غير مقروءة</p>
                <p class="text-3xl font-extrabold text-orange-700 tabular-nums">{{ stats.unread }}</p>
            </div>
            <div class="group relative bg-emerald-50 rounded-2xl p-5 border border-emerald-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-500 overflow-hidden">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-0 group-hover:w-full h-[2px] bg-gradient-to-r from-transparent via-emerald-500 to-transparent transition-all duration-700"></div>
                <p class="text-xs text-emerald-700 mb-1">اليوم</p>
                <p class="text-3xl font-extrabold text-emerald-700 tabular-nums">{{ stats.today }}</p>
            </div>
            <div class="bg-gradient-to-br from-[#C4A265] to-[#D4B876] text-white rounded-2xl p-5">
                <p class="text-xs text-white/80 mb-1">آخر 7 أيام</p>
                <p class="text-3xl font-bold mt-1 tabular-nums">{{ stats.week }}</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="w-5 h-5 text-gray-400 absolute start-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input v-model="search" type="text" placeholder="بحث بالاسم أو الموضوع أو البريد..."
                    class="w-full ps-10 pe-4 py-2.5 rounded-xl border border-gray-200 focus:border-[#1B4F72] focus:ring-2 focus:ring-[#1B4F72]/10 outline-none text-sm">
            </div>
            <div class="flex gap-2">
                <button @click="readFilter = 'all'" :class="readFilter === 'all' ? 'bg-[#1B4F72] text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold transition">الكل</button>
                <button @click="readFilter = 'unread'" :class="readFilter === 'unread' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold transition">غير مقروءة</button>
                <button @click="readFilter = 'read'" :class="readFilter === 'read' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600'"
                    class="px-4 py-2.5 rounded-xl text-xs font-semibold transition">مقروءة</button>
                <a href="/admin/export/contacts" class="px-4 py-2.5 rounded-xl text-xs font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    CSV
                </a>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">المرسل</th>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">الموضوع</th>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">التاريخ</th>
                            <th class="text-start px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">الحالة</th>
                            <th class="text-center px-5 py-4 text-gray-600 font-semibold text-xs uppercase tracking-wide">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="c in filtered" :key="c.id" :class="!c.is_read ? 'bg-blue-50/40' : ''" class="hover:bg-gray-50/70 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#C4A265] to-[#a1864e] flex items-center justify-center text-white font-bold text-sm shadow-md">
                                            {{ initials(c.name) }}
                                        </div>
                                        <span v-if="!c.is_read" class="absolute -top-0.5 -end-0.5 w-3 h-3 rounded-full bg-orange-500 ring-2 ring-white animate-pulse"></span>
                                    </div>
                                    <div>
                                        <p class="text-gray-800 font-semibold">{{ c.name }}</p>
                                        <p class="text-gray-500 text-xs" dir="ltr">{{ c.email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-700 max-w-xs truncate">{{ c.subject }}</td>
                            <td class="px-5 py-4 text-gray-500 text-xs whitespace-nowrap">{{ formatDate(c.created_at) }}</td>
                            <td class="px-5 py-4">
                                <span v-if="c.is_read" class="inline-flex items-center gap-1.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    مقروءة
                                </span>
                                <span v-else class="inline-flex items-center gap-1.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white px-3 py-1 rounded-full text-[11px] font-semibold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                    جديدة
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button @click="selectedContact = c" class="action-btn view" title="عرض">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button v-if="!c.is_read" @click="markRead(c)" class="action-btn mark-read" title="تحديد كمقروءة">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                    <button @click="confirmDelete(c)" class="action-btn delete" title="حذف">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filtered.length">
                            <td colspan="5" class="text-center py-12 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                لا توجد رسائل مطابقة
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- View Modal -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in" leave-to-class="opacity-0">
            <div v-if="selectedContact" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] p-5 text-white flex justify-between items-start">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center font-bold">
                                {{ initials(selectedContact.name) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold">{{ selectedContact.subject }}</h3>
                                <p class="text-white/70 text-xs">{{ selectedContact.name }}</p>
                            </div>
                        </div>
                        <button @click="selectedContact = null" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-3 text-sm">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] text-gray-500 uppercase">البريد</p>
                                <p class="text-gray-800 text-xs mt-1" dir="ltr">{{ selectedContact.email }}</p>
                            </div>
                            <div v-if="selectedContact.phone" class="bg-gray-50 p-3 rounded-xl">
                                <p class="text-[10px] text-gray-500 uppercase">الهاتف</p>
                                <p class="text-gray-800 text-xs mt-1" dir="ltr">{{ selectedContact.phone }}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl">
                            <p class="text-[10px] text-gray-500 uppercase">التاريخ</p>
                            <p class="text-gray-800 text-xs mt-1">{{ formatDate(selectedContact.created_at) }}</p>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-xs font-semibold text-gray-600 mb-2">الرسالة</p>
                            <p class="text-gray-800 whitespace-pre-wrap bg-gradient-to-br from-gray-50 to-blue-50/30 p-4 rounded-xl text-sm leading-relaxed">{{ selectedContact.message }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Delete Confirmation -->
        <Transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0"
            leave-active-class="transition duration-150 ease-in" leave-to-class="opacity-0">
            <div v-if="contactToDelete" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl w-full max-w-sm p-6 shadow-2xl text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-100 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">تأكيد الحذف</h3>
                    <p class="text-gray-500 text-sm mb-5">سيتم حذف الرسالة نهائياً ولا يمكن التراجع</p>
                    <div class="flex gap-2">
                        <button @click="contactToDelete = null" class="flex-1 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-semibold hover:bg-gray-200 transition">إلغاء</button>
                        <button @click="deleteContact" class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white text-sm font-semibold hover:shadow-lg transition">حذف</button>
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
}
.action-btn:hover {
    transform: scale(1.12);
    box-shadow: 0 8px 20px -6px rgba(0,0,0,0.2);
}
.action-btn.view:hover {
    background: linear-gradient(135deg, #1B4F72, #0D2B45);
    color: white;
    transform: scale(1.12) rotate(-4deg);
}
.action-btn.mark-read:hover {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    transform: scale(1.12) rotate(4deg);
}
.action-btn.delete:hover {
    background: linear-gradient(135deg, #ef4444, #e11d48);
    color: white;
    transform: scale(1.12) rotate(-6deg);
}
</style>
