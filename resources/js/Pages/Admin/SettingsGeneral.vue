<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    general: { type: Object, required: true },
});

const form = useForm({
    company_email: props.general.company_email || '',
    company_phone: props.general.company_phone || '',
    company_whatsapp: props.general.company_whatsapp || '',
    company_address_ar: props.general.company_address_ar || '',
    company_address_en: props.general.company_address_en || '',
    social_twitter: props.general.social_twitter || '',
    social_facebook: props.general.social_facebook || '',
    social_instagram: props.general.social_instagram || '',
    social_linkedin: props.general.social_linkedin || '',
    social_tiktok: props.general.social_tiktok || '',
    social_youtube: props.general.social_youtube || '',
    banner_enabled: !!props.general.banner_enabled,
    banner_text_ar: props.general.banner_text_ar || '',
    banner_text_en: props.general.banner_text_en || '',
    banner_cta_label_ar: props.general.banner_cta_label_ar || '',
    banner_cta_label_en: props.general.banner_cta_label_en || '',
    banner_cta_url: props.general.banner_cta_url || '',
    footer_tagline_ar: props.general.footer_tagline_ar || '',
    footer_tagline_en: props.general.footer_tagline_en || '',
});

function save() {
    form.put('/admin/settings/general', { preserveScroll: true });
}

const socialFields = [
    { key: 'social_twitter', label: 'Twitter / X', icon: '𝕏' },
    { key: 'social_facebook', label: 'Facebook', icon: 'f' },
    { key: 'social_instagram', label: 'Instagram', icon: '◎' },
    { key: 'social_linkedin', label: 'LinkedIn', icon: 'in' },
    { key: 'social_tiktok', label: 'TikTok', icon: '♫' },
    { key: 'social_youtube', label: 'YouTube', icon: '▶' },
];
</script>

<template>
    <Head title="الإعدادات العامة — لوحة التحكم" />

    <AdminLayout page-title="الإعدادات العامة">
        <form @submit.prevent="save" class="max-w-4xl space-y-6">
            <!-- Contact Info -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">بيانات الاتصال</h2>
                        <p class="text-xs text-gray-500 mt-0.5">تُستخدم في صفحة Contact وـ Footer والـ JSON-LD</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">البريد الإلكتروني</label>
                        <input v-model="form.company_email" type="email" dir="ltr" placeholder="info@doctorato.com" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">الهاتف</label>
                        <input v-model="form.company_phone" type="tel" dir="ltr" placeholder="+20 10 xxxx xxxx" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">واتساب</label>
                        <input v-model="form.company_whatsapp" type="tel" dir="ltr" placeholder="+20 10 xxxx xxxx" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                    <div></div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">العنوان (عربي)</label>
                        <textarea v-model="form.company_address_ar" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Address (English)</label>
                        <textarea v-model="form.company_address_en" rows="2" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">روابط السوشيال ميديا</h2>
                        <p class="text-xs text-gray-500 mt-0.5">تظهر في الـ Footer والـ Organization schema</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div v-for="f in socialFields" :key="f.key">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1 flex items-center gap-2">
                            <span class="inline-flex w-5 h-5 rounded bg-gray-100 items-center justify-center text-gray-500 font-mono text-[10px]">{{ f.icon }}</span>
                            {{ f.label }}
                        </label>
                        <input v-model="form[f.key]" type="url" dir="ltr" :placeholder="`https://${f.label.toLowerCase().replace(/ .*/, '')}.com/doctorato`" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                </div>
            </div>

            <!-- Launch Banner -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
                <div class="flex items-center justify-between mb-6 pb-5 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-800">بانر إطلاق / عرض خاص</h2>
                            <p class="text-xs text-gray-500 mt-0.5">شريط علوي يظهر للزوار على كل الصفحات</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input v-model="form.banner_enabled" type="checkbox" class="sr-only peer" />
                        <div class="w-12 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-6 peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-0.5 after:start-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all rtl:peer-checked:after:-translate-x-6"></div>
                    </label>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">نص البانر (عربي)</label>
                        <input v-model="form.banner_text_ar" type="text" placeholder="عرض إطلاق: خصم 30٪ لأول 50 عيادة" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Banner Text (English)</label>
                        <input v-model="form.banner_text_en" type="text" dir="ltr" placeholder="Launch offer: 30% off for first 50 clinics" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">نص زر الدعوة (عربي)</label>
                        <input v-model="form.banner_cta_label_ar" type="text" placeholder="اكتشف الآن" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">CTA Label (English)</label>
                        <input v-model="form.banner_cta_label_en" type="text" dir="ltr" placeholder="Learn more" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">رابط الـ CTA</label>
                        <input v-model="form.banner_cta_url" type="text" dir="ltr" placeholder="/pricing" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none" />
                    </div>
                </div>
            </div>

            <!-- Footer tagline -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8">
                <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">نص الـ Footer</h2>
                        <p class="text-xs text-gray-500 mt-0.5">وصف قصير يظهر في ذيل الموقع</p>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Footer Tagline (عربي)</label>
                        <textarea v-model="form.footer_tagline_ar" rows="2" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Footer Tagline (English)</label>
                        <textarea v-model="form.footer_tagline_en" rows="2" dir="ltr" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Save button -->
            <div class="flex justify-end sticky bottom-4 z-10">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-8 py-3 bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl transition-all disabled:opacity-60"
                >
                    {{ form.processing ? 'جاري الحفظ...' : '💾 حفظ كل التغييرات' }}
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
