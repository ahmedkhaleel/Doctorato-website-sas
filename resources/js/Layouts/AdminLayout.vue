<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const page = usePage();
const sidebarOpen = ref(true);
const flash = computed(() => page.props.flash || {});
const authUser = computed(() => page.props.auth?.user);
const showLogoutConfirm = ref(false);
const mobileMenuOpen = ref(false);

const showFlash = ref(false);
watch(flash, (v) => {
    if (v?.success || v?.error) {
        showFlash.value = true;
        setTimeout(() => (showFlash.value = false), 4000);
    }
}, { immediate: true, deep: true });

function hasPerm(perm) {
    if (!authUser.value) return false;
    if (authUser.value.role === 'super_admin') return true;
    return (authUser.value.permissions || []).includes(perm);
}

const menuGroups = computed(() => [
    {
        label: 'الرئيسية',
        items: [
            { label: 'لوحة التحكم', icon: 'dashboard', route: '/admin', perm: 'dashboard.view' },
        ],
    },
    {
        label: 'طلبات العملاء',
        items: [
            { label: 'طلبات العرض', icon: 'demo', route: '/admin/demos', perm: 'demos.manage', badge: 'new_demos' },
            { label: 'رسائل التواصل', icon: 'mail', route: '/admin/contacts', perm: 'contacts.manage', badge: 'unread_contacts' },
        ],
    },
    {
        label: 'المحتوى',
        items: [
            { label: 'الأسئلة الشائعة', icon: 'faq', route: '/admin/faqs', perm: 'faqs.manage' },
            { label: 'الشهادات', icon: 'star', route: '/admin/testimonials', perm: 'testimonials.manage' },
            { label: 'خطط الأسعار', icon: 'price', route: '/admin/plans', perm: 'plans.manage' },
            { label: 'العملات', icon: 'coin', route: '/admin/currencies', perm: 'currencies.manage' },
        ],
    },
    {
        label: 'النظام',
        items: [
            { label: 'المستخدمون والصلاحيات', icon: 'users', route: '/admin/users', perm: 'users.manage' },
        ],
    },
]);

const filteredGroups = computed(() =>
    menuGroups.value
        .map((g) => ({ ...g, items: g.items.filter((it) => hasPerm(it.perm)) }))
        .filter((g) => g.items.length > 0)
);

const currentPath = computed(() => page.url);
function isActive(route) {
    if (route === '/admin') return currentPath.value === '/admin';
    return currentPath.value.startsWith(route);
}

const badges = computed(() => ({
    new_demos: page.props.stats?.new_demos || 0,
    unread_contacts: page.props.stats?.unread_contacts || 0,
}));

function doLogout() {
    router.post('/admin/logout');
}

const userInitials = computed(() => {
    const name = authUser.value?.name || 'A';
    return name.split(' ').map((p) => p[0]).slice(0, 2).join('').toUpperCase();
});

const roleLabels = {
    super_admin: 'مدير عام',
    admin: 'مدير',
    manager: 'مدير محتوى',
    editor: 'محرر',
    viewer: 'مشاهد',
};
</script>

<template>
    <div class="min-h-screen bg-[#F3F4F6] flex" dir="rtl">
        <!-- Mobile backdrop -->
        <div
            v-if="mobileMenuOpen"
            @click="mobileMenuOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"
        ></div>

        <!-- Sidebar -->
        <aside
            :class="[
                sidebarOpen ? 'w-72' : 'w-20',
                mobileMenuOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0',
            ]"
            class="fixed h-full z-50 transition-all duration-300 bg-gradient-to-b from-[#0D2B45] via-[#1C2833] to-[#0D2B45] text-white shadow-2xl"
        >
            <div class="absolute inset-0 opacity-30 pointer-events-none" style="background-image: radial-gradient(circle at 20% 0%, rgba(196,162,101,0.15), transparent 50%), radial-gradient(circle at 80% 100%, rgba(27,79,114,0.2), transparent 50%);"></div>

            <div class="relative h-full flex flex-col">
                <!-- Logo -->
                <div class="px-4 py-5 border-b border-white/10 flex items-center justify-between">
                    <div v-if="sidebarOpen" class="flex items-center gap-2">
                        <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-36 h-auto logo-white" />
                    </div>
                    <div v-else class="mx-auto">
                        <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-10 h-10 object-contain logo-white" />
                    </div>

                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="hidden lg:flex w-8 h-8 rounded-lg items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all"
                    >
                        <svg class="w-4 h-4 transition-transform" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Menu -->
                <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-6 custom-scroll">
                    <div v-for="group in filteredGroups" :key="group.label">
                        <div v-if="sidebarOpen" class="px-3 mb-2 text-[10px] font-bold text-white/40 uppercase tracking-widest">
                            {{ group.label }}
                        </div>
                        <div class="space-y-1">
                            <Link
                                v-for="item in group.items"
                                :key="item.route"
                                :href="item.route"
                                :class="[
                                    'group relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 text-sm',
                                    isActive(item.route)
                                        ? 'bg-gradient-to-r from-[#C4A265] to-[#B8925A] text-white shadow-lg shadow-[#C4A265]/20'
                                        : 'text-white/70 hover:bg-white/5 hover:text-white'
                                ]"
                                :title="!sidebarOpen ? item.label : ''"
                            >
                                <div
                                    v-if="isActive(item.route)"
                                    class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-white rounded-e-full"
                                ></div>

                                <div class="relative shrink-0 w-5 h-5 flex items-center justify-center">
                                    <svg v-if="item.icon === 'dashboard'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                    <svg v-else-if="item.icon === 'demo'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    <svg v-else-if="item.icon === 'mail'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <svg v-else-if="item.icon === 'faq'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg v-else-if="item.icon === 'star'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    <svg v-else-if="item.icon === 'price'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <svg v-else-if="item.icon === 'coin'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <svg v-else-if="item.icon === 'users'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>

                                <span v-if="sidebarOpen" class="flex-1 font-medium">{{ item.label }}</span>

                                <span
                                    v-if="sidebarOpen && item.badge && badges[item.badge] > 0"
                                    class="px-2 py-0.5 rounded-full bg-red-500 text-white text-[10px] font-bold min-w-[18px] text-center"
                                >
                                    {{ badges[item.badge] }}
                                </span>
                                <span
                                    v-else-if="!sidebarOpen && item.badge && badges[item.badge] > 0"
                                    class="absolute top-1 end-1 w-2 h-2 rounded-full bg-red-500 animate-pulse"
                                ></span>
                            </Link>
                        </div>
                    </div>
                </nav>

                <!-- Bottom -->
                <div class="border-t border-white/10 p-3">
                    <a
                        href="/"
                        target="_blank"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/60 hover:text-white hover:bg-white/5 transition-all text-sm"
                        :title="!sidebarOpen ? 'عرض الموقع' : ''"
                    >
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12h18M12 3a15 15 0 010 18M12 3a15 15 0 000 18" />
                        </svg>
                        <span v-if="sidebarOpen">عرض الموقع</span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main -->
        <div
            :class="sidebarOpen ? 'lg:mr-72' : 'lg:mr-20'"
            class="flex-1 transition-all duration-300 min-h-screen flex flex-col"
        >
            <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-gray-200/70 shadow-sm">
                <div class="px-4 lg:px-8 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button
                            @click="mobileMenuOpen = true"
                            class="lg:hidden w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                        <div>
                            <h2 class="text-lg font-bold text-[#1C2833]">لوحة تحكم دكتوراتو</h2>
                            <p class="text-xs text-gray-500 hidden sm:block">إدارة متكاملة لكل محتويات الموقع</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="hidden md:flex items-center gap-2">
                            <Link
                                v-if="badges.new_demos > 0"
                                href="/admin/demos"
                                class="relative w-10 h-10 rounded-xl bg-blue-50 hover:bg-blue-100 flex items-center justify-center text-blue-600 transition"
                                title="طلبات جديدة"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                                <span class="absolute -top-1 -end-1 w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">{{ badges.new_demos }}</span>
                            </Link>
                            <Link
                                v-if="badges.unread_contacts > 0"
                                href="/admin/contacts"
                                class="relative w-10 h-10 rounded-xl bg-amber-50 hover:bg-amber-100 flex items-center justify-center text-amber-600 transition"
                                title="رسائل جديدة"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="absolute -top-1 -end-1 w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold flex items-center justify-center">{{ badges.unread_contacts }}</span>
                            </Link>
                        </div>

                        <div class="flex items-center gap-3 pr-3 border-r border-gray-200">
                            <div class="text-end hidden sm:block">
                                <div class="text-sm font-bold text-[#1C2833]">{{ authUser?.name || 'المدير' }}</div>
                                <div class="text-[11px] text-[#C4A265] font-medium">{{ roleLabels[authUser?.role] || 'مستخدم' }}</div>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white flex items-center justify-center font-bold text-sm shadow-md">
                                {{ userInitials }}
                            </div>
                            <button
                                @click="showLogoutConfirm = true"
                                class="w-10 h-10 rounded-xl bg-red-50 hover:bg-red-100 flex items-center justify-center text-red-600 transition"
                                title="تسجيل الخروج"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash toast -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-y-4"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div
                    v-if="showFlash && (flash.success || flash.error)"
                    class="fixed top-20 end-6 z-50 max-w-sm"
                >
                    <div
                        :class="flash.success
                            ? 'bg-gradient-to-r from-emerald-500 to-green-600'
                            : 'bg-gradient-to-r from-red-500 to-rose-600'"
                        class="rounded-xl shadow-2xl text-white px-5 py-4 flex items-center gap-3"
                    >
                        <svg v-if="flash.success" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg v-else class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="text-sm font-medium">{{ flash.success || flash.error }}</span>
                    </div>
                </div>
            </Transition>

            <main class="flex-1 p-4 lg:p-8">
                <slot />
            </main>
        </div>

        <!-- Logout confirm -->
        <Transition
            enter-active-class="transition duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showLogoutConfirm"
                @click.self="showLogoutConfirm = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex items-center justify-center p-4"
            >
                <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full p-6 text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-[#1C2833] mb-2">تأكيد تسجيل الخروج</h3>
                    <p class="text-sm text-gray-500 mb-6">هل أنت متأكد من رغبتك في تسجيل الخروج من لوحة التحكم؟</p>
                    <div class="flex gap-3">
                        <button
                            @click="showLogoutConfirm = false"
                            class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition"
                        >
                            إلغاء
                        </button>
                        <button
                            @click="doLogout"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-gradient-to-r from-red-500 to-rose-600 text-white font-medium hover:shadow-lg transition"
                        >
                            خروج
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.custom-scroll::-webkit-scrollbar {
    width: 4px;
}
.custom-scroll::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 2px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.2);
}
</style>
