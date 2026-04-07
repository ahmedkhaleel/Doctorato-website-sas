<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    tracking: { type: Object, required: true },
});

const form = useForm({
    ga4_id: props.tracking.ga4_id || '',
    gtm_id: props.tracking.gtm_id || '',
    meta_pixel_id: props.tracking.meta_pixel_id || '',
    tiktok_pixel_id: props.tracking.tiktok_pixel_id || '',
    snapchat_pixel_id: props.tracking.snapchat_pixel_id || '',
    tracking_enabled: !!props.tracking.tracking_enabled,
});

function save() {
    form.put('/admin/settings/tracking', { preserveScroll: true });
}

const fields = [
    { key: 'ga4_id', label: 'Google Analytics 4', placeholder: 'G-XXXXXXXXXX', help: 'Measurement ID — Admin → Data Streams' },
    { key: 'gtm_id', label: 'Google Tag Manager', placeholder: 'GTM-XXXXXXX', help: 'اختياري — لإدارة كل الوسوم من مكان واحد' },
    { key: 'meta_pixel_id', label: 'Meta Pixel (Facebook + Instagram)', placeholder: '123456789012345', help: 'Events Manager → Pixels' },
    { key: 'tiktok_pixel_id', label: 'TikTok Pixel', placeholder: 'XXXXXXXXXXXXXXXXX', help: 'TikTok Ads → Assets → Events' },
    { key: 'snapchat_pixel_id', label: 'Snapchat Pixel', placeholder: 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx', help: 'Snap Ads Manager → Events Manager' },
];
</script>

<template>
    <Head title="الإعدادات — لوحة التحكم" />

    <AdminLayout page-title="إعدادات التتبع والتحليلات">
        <div class="max-w-3xl">
            <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
                <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">منصات التحليلات والإعلانات</h2>
                        <p class="text-sm text-gray-500 mt-1">أدخل معرّفات البكسل لتفعيل تتبع الزيارات والتحويلات</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input v-model="form.tracking_enabled" type="checkbox" class="sr-only peer" />
                        <div class="w-12 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-0.5 after:start-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all rtl:peer-checked:after:-translate-x-6"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700">تفعيل التتبع</span>
                    </label>
                </div>

                <form @submit.prevent="save" class="space-y-5">
                    <div v-for="f in fields" :key="f.key">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ f.label }}</label>
                        <input
                            v-model="form[f.key]"
                            type="text"
                            :placeholder="f.placeholder"
                            dir="ltr"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none transition"
                        />
                        <p class="text-xs text-gray-400 mt-1">{{ f.help }}</p>
                        <p v-if="form.errors[f.key]" class="text-xs text-red-600 mt-1">{{ form.errors[f.key] }}</p>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2.5 bg-[#1B4F72] hover:bg-[#0A1628] text-white rounded-lg text-sm font-semibold transition disabled:opacity-60"
                        >
                            {{ form.processing ? 'جاري الحفظ...' : 'حفظ التغييرات' }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6 bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-900">
                <p class="font-semibold mb-1">⚠️ ملاحظة</p>
                <p>التتبع معطّل افتراضياً في بيئة التطوير. شغّل المفتاح أعلاه يدوياً للتجربة، أو يفعّل تلقائياً في الإنتاج.</p>
            </div>
        </div>
    </AdminLayout>
</template>
