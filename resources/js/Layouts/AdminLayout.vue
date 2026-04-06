<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const sidebarOpen = ref(true);
const flash = computed(() => page.props.flash || {});

const menuItems = [
    { label: 'لوحة التحكم', icon: '📊', route: '/admin' },
    { label: 'الأسئلة الشائعة', icon: '❓', route: '/admin/faqs' },
    { label: 'خطط الأسعار', icon: '💰', route: '/admin/plans' },
    { label: 'الشهادات', icon: '⭐', route: '/admin/testimonials' },
    { label: 'العملات', icon: '💱', route: '/admin/currencies' },
    { label: 'رسائل التواصل', icon: '📩', route: '/admin/contacts' },
    { label: 'طلبات العرض', icon: '📋', route: '/admin/demos' },
];

const currentPath = computed(() => page.url);

function isActive(route) {
    if (route === '/admin') return currentPath.value === '/admin';
    return currentPath.value.startsWith(route);
}
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex" dir="rtl">
        <!-- Sidebar -->
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="bg-[#1C2833] text-white transition-all duration-300 fixed h-full z-30"
        >
            <!-- Logo -->
            <div class="p-4 border-b border-white/10 flex items-center justify-between">
                <span v-if="sidebarOpen" class="text-xl font-bold text-[#C4A265]">Doctorato</span>
                <button @click="sidebarOpen = !sidebarOpen" class="text-white/60 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Menu -->
            <nav class="mt-4 space-y-1 px-2">
                <Link
                    v-for="item in menuItems"
                    :key="item.route"
                    :href="item.route"
                    :class="[
                        'flex items-center gap-3 px-3 py-3 rounded-lg transition-colors text-sm',
                        isActive(item.route)
                            ? 'bg-[#C4A265] text-white'
                            : 'text-white/70 hover:bg-white/10 hover:text-white'
                    ]"
                >
                    <span class="text-lg">{{ item.icon }}</span>
                    <span v-if="sidebarOpen">{{ item.label }}</span>
                </Link>
            </nav>

            <!-- Back to site -->
            <div class="absolute bottom-4 w-full px-2">
                <a
                    href="/"
                    class="flex items-center gap-3 px-3 py-3 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-colors text-sm"
                >
                    <span class="text-lg">🌐</span>
                    <span v-if="sidebarOpen">العودة للموقع</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="sidebarOpen ? 'mr-64' : 'mr-16'" class="flex-1 transition-all duration-300">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between sticky top-0 z-20">
                <h2 class="text-lg font-semibold text-gray-700">لوحة تحكم دكتوراتو</h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">مرحباً، المدير</span>
                </div>
            </header>

            <!-- Flash Messages -->
            <div v-if="flash.success" class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ flash.success }}
            </div>

            <!-- Page Content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
