<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: Array,
    roles: Object,
    availablePermissions: Object,
});

const page = usePage();
const currentUserId = computed(() => page.props.auth?.user?.id);

const showForm = ref(false);
const editing = ref(null);
const search = ref('');

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'admin',
    permissions: [],
    is_active: true,
});

const filteredUsers = computed(() => {
    if (!search.value) return props.users;
    const q = search.value.toLowerCase();
    return props.users.filter((u) =>
        u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)
    );
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.role = 'admin';
    form.permissions = defaultPermsFor('admin');
    form.is_active = true;
    showForm.value = true;
}

function openEdit(user) {
    // Reset any state from a prior add/edit session before populating.
    form.reset();
    form.clearErrors();
    editing.value = user;
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    form.permissions = [...(user.permissions || [])];
    form.is_active = user.is_active;
    showForm.value = true;
}

function defaultPermsFor(role) {
    const presets = {
        super_admin: Object.keys(props.availablePermissions),
        admin: ['dashboard.view', 'faqs.manage', 'plans.manage', 'testimonials.manage', 'currencies.manage', 'contacts.manage', 'demos.manage'],
        manager: ['dashboard.view', 'faqs.manage', 'testimonials.manage', 'contacts.manage', 'demos.manage'],
        editor: ['dashboard.view', 'faqs.manage', 'testimonials.manage'],
        viewer: ['dashboard.view'],
    };
    return presets[role] || ['dashboard.view'];
}

function onRoleChange() {
    form.permissions = defaultPermsFor(form.role);
}

function togglePermission(perm) {
    const idx = form.permissions.indexOf(perm);
    if (idx > -1) form.permissions.splice(idx, 1);
    else form.permissions.push(perm);
}

function submit() {
    if (editing.value) {
        form.put(`/admin/users/${editing.value.id}`, {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        form.post('/admin/users', {
            onSuccess: () => { showForm.value = false; },
        });
    }
}

function toggleActive(user) {
    router.put(`/admin/users/${user.id}/toggle-active`);
}

const deleteTarget = ref(null);
function confirmDelete(user) {
    deleteTarget.value = user;
}
function doDelete() {
    router.delete(`/admin/users/${deleteTarget.value.id}`, {
        onSuccess: () => { deleteTarget.value = null; },
    });
}

function formatDate(date) {
    if (!date) return '—';
    return new Date(date).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' });
}

const roleColors = {
    super_admin: { bg: 'bg-gradient-to-r from-red-500 to-rose-600', text: 'text-white' },
    admin: { bg: 'bg-gradient-to-r from-[#1B4F72] to-[#0D2B45]', text: 'text-white' },
    manager: { bg: 'bg-gradient-to-r from-[#C4A265] to-[#B8925A]', text: 'text-white' },
    editor: { bg: 'bg-gradient-to-r from-emerald-500 to-teal-600', text: 'text-white' },
    viewer: { bg: 'bg-gray-100', text: 'text-gray-700' },
};

function initials(name) {
    return (name || 'A').split(' ').map((p) => p[0]).slice(0, 2).join('').toUpperCase();
}
</script>

<template>
    <AdminLayout>
        <Head title="المستخدمون والصلاحيات" />

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[#1C2833]">المستخدمون والصلاحيات</h1>
                <p class="text-sm text-gray-500 mt-1">إدارة حسابات المدراء وتحديد صلاحياتهم</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <svg class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="بحث..."
                        class="ps-10 pe-4 py-2.5 rounded-xl border border-gray-200 bg-white text-sm focus:border-[#C4A265] focus:ring-2 focus:ring-[#C4A265]/20 outline-none transition w-full sm:w-64"
                    />
                </div>
                <button
                    @click="openCreate"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white text-sm font-semibold shadow-lg shadow-[#1B4F72]/20 hover:shadow-xl hover:scale-[1.02] transition-all"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">إضافة مستخدم</span>
                </button>
            </div>
        </div>

        <!-- Users table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50/70 border-b border-gray-100">
                        <tr>
                            <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">المستخدم</th>
                            <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الدور</th>
                            <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الصلاحيات</th>
                            <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="text-start px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">آخر دخول</th>
                            <th class="text-center px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white flex items-center justify-center font-bold shadow-md">
                                        {{ initials(user.name) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-[#1C2833]">{{ user.name }}</div>
                                        <div class="text-xs text-gray-500" dir="ltr">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    :class="[roleColors[user.role]?.bg, roleColors[user.role]?.text]"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold shadow-sm"
                                >
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                    </svg>
                                    {{ roles[user.role] }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-xs text-gray-600">
                                    <span v-if="user.role === 'super_admin'" class="text-[#C4A265] font-semibold">جميع الصلاحيات</span>
                                    <span v-else>{{ user.permissions.length }} صلاحية</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    :class="user.is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200'"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border"
                                >
                                    <span :class="user.is_active ? 'bg-emerald-500' : 'bg-gray-400'" class="w-1.5 h-1.5 rounded-full"></span>
                                    {{ user.is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500">{{ formatDate(user.last_login_at) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button
                                        @click="openEdit(user)"
                                        class="action-btn group/btn edit"
                                        title="تعديل"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="toggleActive(user)"
                                        v-if="user.id !== currentUserId"
                                        class="action-btn group/btn"
                                        :class="user.is_active ? 'toggle-off' : 'toggle-on'"
                                        :title="user.is_active ? 'تعطيل' : 'تفعيل'"
                                    >
                                        <svg v-if="user.is_active" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="confirmDelete(user)"
                                        v-if="user.id !== currentUserId"
                                        class="action-btn group/btn delete"
                                        title="حذف"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!filteredUsers.length">
                            <td colspan="6" class="text-center py-12 text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                لا توجد مستخدمون
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- User form modal -->
        <Transition
            enter-active-class="transition duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showForm"
                @click.self="showForm = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 overflow-y-auto"
            >
                <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full my-8 overflow-hidden">
                    <!-- Header -->
                    <div class="relative p-6 bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white overflow-hidden">
                        <div class="absolute top-0 end-0 w-48 h-48 bg-[#C4A265]/10 rounded-full blur-3xl"></div>
                        <div class="relative flex items-start justify-between">
                            <div>
                                <h3 class="text-xl font-bold">{{ editing ? 'تعديل المستخدم' : 'إضافة مستخدم جديد' }}</h3>
                                <p class="text-sm text-white/70 mt-1">حدد الاسم، الدور، والصلاحيات</p>
                            </div>
                            <button @click="showForm = false" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">الاسم الكامل</label>
                                <input v-model="form.name" type="text" required class="input" />
                                <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">البريد الإلكتروني</label>
                                <input v-model="form.email" type="email" required class="input" dir="ltr" />
                                <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">
                                    كلمة المرور <span v-if="editing" class="text-gray-400 font-normal">(اختياري)</span>
                                </label>
                                <input v-model="form.password" type="password" :required="!editing" class="input" placeholder="8 أحرف على الأقل" />
                                <p v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5">الدور</label>
                                <select v-model="form.role" @change="onRoleChange" class="input">
                                    <option v-for="(label, key) in roles" :key="key" :value="key">{{ label }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Active toggle -->
                        <div class="flex items-center justify-between p-4 rounded-xl bg-gray-50">
                            <div>
                                <div class="text-sm font-bold text-[#1C2833]">الحساب نشط</div>
                                <div class="text-xs text-gray-500">يسمح للمستخدم بتسجيل الدخول</div>
                            </div>
                            <button type="button" @click="form.is_active = !form.is_active" class="relative w-12 h-6 rounded-full transition-colors" :class="form.is_active ? 'bg-emerald-500' : 'bg-gray-300'">
                                <span class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow-md transition-all" :class="form.is_active ? 'start-6' : 'start-0.5'"></span>
                            </button>
                        </div>

                        <!-- Permissions -->
                        <div v-if="form.role !== 'super_admin'">
                            <label class="block text-xs font-bold text-gray-600 mb-3">الصلاحيات</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <label
                                    v-for="(label, key) in availablePermissions"
                                    :key="key"
                                    class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                    :class="form.permissions.includes(key) ? 'border-[#C4A265] bg-[#C4A265]/5' : 'border-gray-200 hover:border-gray-300'"
                                >
                                    <input
                                        type="checkbox"
                                        :checked="form.permissions.includes(key)"
                                        @change="togglePermission(key)"
                                        class="sr-only"
                                    />
                                    <div
                                        class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all"
                                        :class="form.permissions.includes(key) ? 'bg-[#C4A265] border-[#C4A265]' : 'border-gray-300'"
                                    >
                                        <svg v-if="form.permissions.includes(key)" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-sm text-[#1C2833] font-medium">{{ label }}</span>
                                </label>
                            </div>
                        </div>
                        <div v-else class="p-4 rounded-xl bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-sm text-red-700">
                            <svg class="w-5 h-5 inline me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            المدير العام يمتلك جميع الصلاحيات تلقائياً
                        </div>
                    </form>

                    <div class="flex items-center justify-end gap-3 p-5 border-t bg-gray-50">
                        <button @click="showForm = false" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-medium hover:bg-white transition">
                            إلغاء
                        </button>
                        <button
                            @click="submit"
                            :disabled="form.processing"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white font-medium hover:shadow-lg transition disabled:opacity-50"
                        >
                            {{ editing ? 'حفظ التعديلات' : 'إضافة المستخدم' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Delete confirmation -->
        <Transition
            enter-active-class="transition duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="deleteTarget" @click.self="deleteTarget = null" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#1C2833] mb-2">حذف المستخدم</h3>
                    <p class="text-sm text-gray-500 mb-6">هل أنت متأكد من حذف <strong>{{ deleteTarget.name }}</strong>؟ هذا الإجراء لا يمكن التراجع عنه.</p>
                    <div class="flex gap-3">
                        <button @click="deleteTarget = null" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition">
                            إلغاء
                        </button>
                        <button @click="doDelete" class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white font-medium hover:shadow-lg transition">
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </AdminLayout>
</template>

<style scoped>
.input {
    width: 100%;
    padding: 0.625rem 1rem;
    border-radius: 0.75rem;
    border: 1px solid #E5E7EB;
    background: #FFFFFF;
    font-size: 0.875rem;
    outline: none;
    transition: all 0.2s;
}
.input:focus {
    border-color: #C4A265;
    box-shadow: 0 0 0 3px rgba(196, 162, 101, 0.2);
}
.action-btn {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: #F3F4F6;
    color: #6B7280;
}
.action-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px -2px rgba(0,0,0,0.15);
}
.action-btn.edit:hover {
    background: linear-gradient(135deg, #1B4F72, #0D2B45);
    color: white;
    transform: scale(1.1) rotate(-4deg);
}
.action-btn.delete:hover {
    background: linear-gradient(135deg, #ef4444, #e11d48);
    color: white;
    transform: scale(1.1) rotate(4deg);
}
.action-btn.toggle-off:hover {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}
.action-btn.toggle-on:hover {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}
</style>
