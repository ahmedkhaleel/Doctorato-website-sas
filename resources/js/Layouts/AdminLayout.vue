<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, h } from 'vue';

// Compact stroke icon set used by the sidebar. Adding an icon = adding a
// case here — keeps the template lean and the icon inventory in one place.
const NAV_ICONS = {
    dashboard: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    demo: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
    mail: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    faq: 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    star: 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
    price: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    card: 'M3 10h18M5 6h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2zm2 8h4',
    chart: 'M9 19v-6m4 6V9m4 10v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z',
    cog: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
    doc: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    tag: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
    clock: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    coin: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    users: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    // New icons for reorganized menu
    ticket: 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
    target: 'M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3-9a9 9 0 11-9 9 9 9 0 019-9zm0 4a5 5 0 11-5 5 5 5 0 015-5z',
    quote: 'M7 8h3a2 2 0 012 2v.01M7 8c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h.01M7 8V7a2 2 0 012-2m5 3h3a2 2 0 012 2v.01M14 8c-1.1 0-2 .9-2 2v4c0 1.1.9 2 2 2h.01M14 8V7a2 2 0 012-2',
    // Added for the Launch Offer / Security settings entry
    shield: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
};

const NavIcon = {
    props: ['name'],
    setup(props) {
        return () => h('svg', {
            class: 'w-4 h-4',
            fill: 'none',
            stroke: 'currentColor',
            viewBox: '0 0 24 24',
        }, [
            h('path', {
                'stroke-linecap': 'round',
                'stroke-linejoin': 'round',
                'stroke-width': '1.8',
                d: NAV_ICONS[props.name] || NAV_ICONS.dashboard,
            }),
        ]);
    },
};

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
    // super_admin and admin roles have unrestricted access
    if (authUser.value.role === 'super_admin' || authUser.value.role === 'admin') return true;
    return (authUser.value.permissions || []).includes(perm);
}

// Reorganized menu — 6 logical groups, collapsible, no redundancy
const menuGroups = computed(() => [
    {
        key: 'main',
        label: 'الرئيسية',
        // Groups with a single top-level item are rendered flat (no accordion)
        items: [
            { label: 'لوحة التحكم', icon: 'dashboard', route: '/admin', perm: 'dashboard.view' },
        ],
    },
    {
        key: 'crm',
        label: 'العملاء',
        items: [
            { label: 'طلبات التجربة', icon: 'demo', route: '/admin/demos', perm: 'demos.manage', badge: 'new_demos' },
            { label: 'رسائل التواصل', icon: 'mail', route: '/admin/contacts', perm: 'contacts.manage', badge: 'unread_contacts' },
        ],
    },
    {
        key: 'commerce',
        label: 'المبيعات والمالية',
        items: [
            { label: 'الاشتراكات', icon: 'card', route: '/admin/subscriptions', perm: 'dashboard.view' },
            { label: 'الفواتير', icon: 'doc', route: '/admin/invoices', perm: 'dashboard.view' },
            { label: 'الكوبونات', icon: 'ticket', route: '/admin/coupons', perm: 'dashboard.view' },
            { label: 'خطط الأسعار', icon: 'price', route: '/admin/plans', perm: 'plans.manage' },
            { label: 'الأسعار حسب الدولة', icon: 'coin', route: '/admin/plan-prices', perm: 'dashboard.view' },
            { label: 'الإضافات', icon: 'tag', route: '/admin/addons', perm: 'dashboard.view' },
            { label: 'العملات', icon: 'coin', route: '/admin/currencies', perm: 'currencies.manage' },
        ],
    },
    {
        key: 'content',
        label: 'المحتوى',
        items: [
            { label: 'المدونة', icon: 'doc', route: '/admin/blog/posts', perm: 'dashboard.view', matchPrefix: '/admin/blog' },
            { label: 'دراسات الحالة', icon: 'star', route: '/admin/case-studies', perm: 'dashboard.view' },
            { label: 'الشهادات', icon: 'quote', route: '/admin/testimonials', perm: 'testimonials.manage' },
            { label: 'الأسئلة الشائعة', icon: 'faq', route: '/admin/faqs', perm: 'faqs.manage' },
        ],
    },
    {
        key: 'analytics',
        label: 'التقارير والتحليلات',
        items: [
            { label: 'التحليلات والإيرادات', icon: 'chart', route: '/admin/analytics', perm: 'dashboard.view' },
        ],
    },
    {
        key: 'settings',
        label: 'الإعدادات',
        items: [
            { label: 'الإعدادات العامة', icon: 'cog', route: '/admin/settings/general', perm: 'dashboard.view' },
            { label: 'عرض الإطلاق والحماية', icon: 'shield', route: '/admin/settings/launch', perm: 'dashboard.view' },
            { label: 'Pixels وتتبع الإعلانات', icon: 'target', route: '/admin/settings/tracking', perm: 'dashboard.view' },
            { label: 'قوالب البريد', icon: 'mail', route: '/admin/email-templates', perm: 'dashboard.view' },
        ],
    },
    {
        key: 'system',
        label: 'النظام',
        items: [
            { label: 'المستخدمون والصلاحيات', icon: 'users', route: '/admin/users', perm: 'users.manage' },
            { label: 'سجل النشاط', icon: 'clock', route: '/admin/activity-logs', perm: 'dashboard.view' },
        ],
    },
]);

// Per-group accordion state — auto-expands the group containing the active route.
const expandedGroups = ref({});

function isGroupActive(group) {
    return group.items.some((item) => isActive(item.matchPrefix || item.route));
}

function toggleGroup(key) {
    expandedGroups.value[key] = !expandedGroups.value[key];
}

// On mount, expand whatever group contains the active route; collapse others.
onMounted(() => {
    filteredGroups.value.forEach((g) => {
        expandedGroups.value[g.key] = isGroupActive(g);
    });
});

const filteredGroups = computed(() =>
    menuGroups.value
        .map((g) => ({ ...g, items: g.items.filter((it) => hasPerm(it.perm)) }))
        .filter((g) => g.items.length > 0)
);

const currentPath = computed(() => page.url);
function isActive(route) {
    if (!route) return false;
    if (route === '/admin') return currentPath.value === '/admin';
    return currentPath.value.startsWith(route);
}

const badges = computed(() => ({
    new_demos: page.props.stats?.new_demos || 0,
    unread_contacts: page.props.stats?.unread_contacts || 0,
}));

// Global search
const searchQuery = ref('');
const searchResults = ref([]);
const searchFocused = ref(false);
const searchBusy = ref(false);
let searchTimeout = null;

function onSearchInput() {
    clearTimeout(searchTimeout);
    if (searchQuery.value.trim().length < 2) {
        searchResults.value = [];
        return;
    }
    searchBusy.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/admin/search?q=${encodeURIComponent(searchQuery.value)}`, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });
            if (res.ok) {
                const data = await res.json();
                searchResults.value = data.results || [];
            }
        } finally {
            searchBusy.value = false;
        }
    }, 250);
}

function resetSearch() {
    searchQuery.value = '';
    searchResults.value = [];
    searchFocused.value = false;
}

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
                <div class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
                    <div v-if="sidebarOpen" class="flex items-center gap-2">
                        <img src="/images/doctorato-logo.png" alt="Doctorato" class="h-7 w-auto logo-white" />
                    </div>
                    <div v-else class="mx-auto">
                        <img src="/images/doctorato-logo.png" alt="Doctorato" class="h-5 w-auto max-w-[56px] object-contain logo-white" />
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
                <nav class="flex-1 overflow-y-auto py-3 px-2 space-y-1 custom-scroll">
                    <template v-for="group in filteredGroups" :key="group.key">
                        <!-- Single-item group (e.g. Dashboard, Analytics) renders flat -->
                        <Link
                            v-if="group.items.length === 1 && sidebarOpen"
                            :href="group.items[0].route"
                            :class="[
                                'group relative flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-200 text-[13px]',
                                isActive(group.items[0].route)
                                    ? 'bg-gradient-to-r from-[#C4A265] to-[#B8925A] text-white shadow-md shadow-[#C4A265]/20 font-semibold'
                                    : 'text-white/75 hover:bg-white/[0.06] hover:text-white'
                            ]"
                        >
                            <div
                                v-if="isActive(group.items[0].route)"
                                class="absolute start-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-e-full"
                            ></div>
                            <span class="shrink-0 w-4 h-4 flex items-center justify-center">
                                <NavIcon :name="group.items[0].icon" />
                            </span>
                            <span class="flex-1 font-medium">{{ group.items[0].label }}</span>
                        </Link>

                        <!-- Multi-item group: collapsible accordion -->
                        <div v-else-if="sidebarOpen">
                            <button
                                type="button"
                                @click="toggleGroup(group.key)"
                                :class="[
                                    'w-full flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-200',
                                    isGroupActive(group) && !expandedGroups[group.key]
                                        ? 'text-[#C4A265] bg-white/[0.04]'
                                        : 'text-white/50 hover:text-white/80 hover:bg-white/[0.04]'
                                ]"
                            >
                                <span class="text-[10px] font-bold uppercase tracking-[0.15em]">{{ group.label }}</span>
                                <svg
                                    class="w-3.5 h-3.5 transition-transform duration-300"
                                    :class="{ 'rotate-180': expandedGroups[group.key] }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <Transition
                                enter-active-class="transition-all duration-300 ease-out overflow-hidden"
                                enter-from-class="max-h-0 opacity-0"
                                enter-to-class="max-h-[600px] opacity-100"
                                leave-active-class="transition-all duration-200 ease-in overflow-hidden"
                                leave-from-class="max-h-[600px] opacity-100"
                                leave-to-class="max-h-0 opacity-0"
                            >
                                <div v-if="expandedGroups[group.key]" class="overflow-hidden">
                                    <div class="mt-1 mb-2 ps-3 space-y-0.5 border-s border-white/10 ms-3">
                                        <Link
                                            v-for="item in group.items"
                                            :key="item.route"
                                            :href="item.route"
                                            :class="[
                                                'group relative flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-[13px]',
                                                isActive(item.matchPrefix || item.route)
                                                    ? 'bg-gradient-to-r from-[#C4A265] to-[#B8925A] text-white shadow-md shadow-[#C4A265]/20 font-semibold'
                                                    : 'text-white/70 hover:bg-white/[0.06] hover:text-white'
                                            ]"
                                        >
                                            <span class="shrink-0 w-4 h-4 flex items-center justify-center opacity-80 group-hover:opacity-100">
                                                <NavIcon :name="item.icon" />
                                            </span>
                                            <span class="flex-1 font-medium truncate">{{ item.label }}</span>
                                            <span
                                                v-if="item.badge && badges[item.badge] > 0"
                                                class="px-1.5 py-0.5 rounded-full bg-red-500 text-white text-[9px] font-bold min-w-[16px] text-center"
                                            >
                                                {{ badges[item.badge] }}
                                            </span>
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Collapsed sidebar: show icons only, no groups -->
                        <template v-else>
                            <Link
                                v-for="item in group.items"
                                :key="item.route"
                                :href="item.route"
                                :title="item.label"
                                :class="[
                                    'group relative flex items-center justify-center w-12 h-12 mx-auto rounded-xl transition-all duration-200',
                                    isActive(item.matchPrefix || item.route)
                                        ? 'bg-gradient-to-br from-[#C4A265] to-[#B8925A] text-white shadow-md shadow-[#C4A265]/20'
                                        : 'text-white/70 hover:bg-white/[0.06] hover:text-white'
                                ]"
                            >
                                <NavIcon :name="item.icon" />
                                <span
                                    v-if="item.badge && badges[item.badge] > 0"
                                    class="absolute top-1 end-1 w-2 h-2 rounded-full bg-red-500 animate-pulse"
                                ></span>
                            </Link>
                        </template>
                    </template>
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

                    <!-- Global search -->
                    <div class="hidden md:block relative flex-1 max-w-md mx-6">
                        <div class="relative">
                            <svg class="absolute top-1/2 -translate-y-1/2 start-3 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <input
                                v-model="searchQuery"
                                @input="onSearchInput"
                                @focus="searchFocused = true"
                                @blur="setTimeout(() => searchFocused = false, 200)"
                                type="text"
                                placeholder="بحث موحّد في الاشتراكات والفواتير والطلبات..."
                                class="w-full ps-10 pe-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition"
                            />
                            <span v-if="searchBusy" class="absolute top-1/2 -translate-y-1/2 end-3 text-xs text-gray-400">...</span>
                        </div>

                        <!-- Results dropdown -->
                        <div
                            v-if="searchFocused && (searchResults.length || (searchQuery.length >= 2 && !searchBusy))"
                            class="absolute top-full inset-x-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 max-h-[500px] overflow-y-auto z-50"
                        >
                            <div v-if="!searchResults.length" class="p-6 text-center text-sm text-gray-400">لا توجد نتائج مطابقة</div>
                            <Link
                                v-for="(r, i) in searchResults"
                                :key="`${r.type}-${i}`"
                                :href="r.url"
                                @click="resetSearch"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-b-0"
                            >
                                <span class="text-xl">{{ r.icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ r.title }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ r.subtitle }}</p>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400 shrink-0">{{ r.type_label }}</span>
                            </Link>
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
