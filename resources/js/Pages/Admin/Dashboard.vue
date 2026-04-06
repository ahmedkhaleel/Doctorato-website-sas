<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});

const cards = [
    { key: 'faqs', label: 'الأسئلة الشائعة', icon: '❓', color: 'bg-blue-500', link: '/admin/faqs' },
    { key: 'plans', label: 'خطط الأسعار', icon: '💰', color: 'bg-green-500', link: '/admin/plans' },
    { key: 'testimonials', label: 'الشهادات', icon: '⭐', color: 'bg-yellow-500', link: '/admin/testimonials' },
    { key: 'currencies', label: 'العملات', icon: '💱', color: 'bg-purple-500', link: '/admin/currencies' },
    { key: 'contacts', label: 'رسائل التواصل', icon: '📩', color: 'bg-red-500', link: '/admin/contacts' },
    { key: 'demos', label: 'طلبات العرض', icon: '📋', color: 'bg-indigo-500', link: '/admin/demos' },
    { key: 'subscribers', label: 'المشتركين', icon: '📧', color: 'bg-teal-500', link: '#' },
    { key: 'posts', label: 'المقالات', icon: '📝', color: 'bg-orange-500', link: '#' },
];
</script>

<template>
    <AdminLayout>
        <Head title="لوحة التحكم" />

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">لوحة التحكم</h1>
            <p class="text-gray-500 mt-1">نظرة عامة على إحصائيات الموقع</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <Link
                v-for="card in cards"
                :key="card.key"
                :href="card.link"
                class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md transition-shadow border border-gray-100"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ card.label }}</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats[card.key] || 0 }}</p>
                    </div>
                    <div :class="card.color" class="w-12 h-12 rounded-lg flex items-center justify-center text-xl">
                        {{ card.icon }}
                    </div>
                </div>
            </Link>
        </div>

        <!-- Quick Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-semibold text-gray-700 mb-4">📌 تنبيهات</h3>
                <div class="space-y-3">
                    <div v-if="stats.unread_contacts > 0" class="flex items-center gap-3 bg-red-50 px-4 py-3 rounded-lg">
                        <span class="text-red-500 font-bold">{{ stats.unread_contacts }}</span>
                        <span class="text-sm text-red-700">رسائل تواصل غير مقروءة</span>
                    </div>
                    <div v-if="stats.new_demos > 0" class="flex items-center gap-3 bg-blue-50 px-4 py-3 rounded-lg">
                        <span class="text-blue-500 font-bold">{{ stats.new_demos }}</span>
                        <span class="text-sm text-blue-700">طلبات عرض جديدة</span>
                    </div>
                    <div v-if="!stats.unread_contacts && !stats.new_demos" class="text-center text-gray-400 py-4">
                        لا توجد تنبيهات جديدة ✅
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <h3 class="font-semibold text-gray-700 mb-4">⚡ إجراءات سريعة</h3>
                <div class="grid grid-cols-2 gap-3">
                    <Link href="/admin/faqs" class="bg-gray-50 hover:bg-gray-100 rounded-lg p-3 text-center text-sm transition-colors">
                        إضافة سؤال
                    </Link>
                    <Link href="/admin/plans" class="bg-gray-50 hover:bg-gray-100 rounded-lg p-3 text-center text-sm transition-colors">
                        تعديل الأسعار
                    </Link>
                    <Link href="/admin/testimonials" class="bg-gray-50 hover:bg-gray-100 rounded-lg p-3 text-center text-sm transition-colors">
                        إضافة شهادة
                    </Link>
                    <Link href="/admin/currencies" class="bg-gray-50 hover:bg-gray-100 rounded-lg p-3 text-center text-sm transition-colors">
                        إدارة العملات
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
