<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    templates: { type: Array, default: () => [] },
});

const selected = ref(props.templates[0] || null);
const activeLang = ref('ar');

const form = useForm({
    subject_ar: selected.value?.subject_ar || '',
    subject_en: selected.value?.subject_en || '',
    body_ar: selected.value?.body_ar || '',
    body_en: selected.value?.body_en || '',
    is_active: selected.value?.is_active ?? true,
});

function select(tpl) {
    selected.value = tpl;
    form.subject_ar = tpl.subject_ar;
    form.subject_en = tpl.subject_en;
    form.body_ar = tpl.body_ar;
    form.body_en = tpl.body_en;
    form.is_active = !!tpl.is_active;
    activeLang.value = 'ar';
}

function save() {
    if (!selected.value) return;
    form.put(`/admin/email-templates/${selected.value.id}`, { preserveScroll: true });
}

function insertVar(variable) {
    const textarea = document.getElementById(`body-${activeLang.value}`);
    if (!textarea) return;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const tag = `{{${variable}}}`;
    const key = activeLang.value === 'ar' ? 'body_ar' : 'body_en';
    form[key] = form[key].slice(0, start) + tag + form[key].slice(end);
    // Defer cursor reposition
    setTimeout(() => {
        textarea.focus();
        textarea.setSelectionRange(start + tag.length, start + tag.length);
    }, 0);
}
</script>

<template>
    <Head title="قوالب البريد — لوحة التحكم" />

    <AdminLayout page-title="قوالب البريد الإلكتروني">
        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Template list -->
            <aside class="lg:col-span-1 space-y-2">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 px-2">القوالب المتاحة</p>
                <button
                    v-for="tpl in templates"
                    :key="tpl.id"
                    @click="select(tpl)"
                    :class="selected?.id === tpl.id
                        ? 'bg-[#1B4F72] text-white shadow-lg'
                        : 'bg-white text-gray-700 border border-gray-100 hover:border-[#1B4F72]/30'"
                    class="w-full text-start px-4 py-3 rounded-xl transition-all"
                >
                    <div class="flex items-center justify-between mb-1">
                        <span class="font-semibold text-sm">{{ tpl.label_ar }}</span>
                        <span v-if="!tpl.is_active" class="text-[10px] px-1.5 py-0.5 rounded-full bg-red-100 text-red-600">معطّل</span>
                    </div>
                    <p class="text-xs opacity-70 font-mono truncate">{{ tpl.key }}</p>
                </button>
            </aside>

            <!-- Editor -->
            <main class="lg:col-span-3">
                <div v-if="!selected" class="bg-white rounded-2xl border border-gray-100 p-16 text-center text-gray-400">
                    اختر قالباً للتعديل
                </div>
                <form v-else @submit.prevent="save" class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 space-y-5">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">{{ selected.label_ar }}</h2>
                            <p class="text-xs text-gray-500 mt-1">{{ selected.description }}</p>
                            <code class="inline-block mt-2 text-xs font-mono px-2 py-0.5 rounded bg-gray-50 text-[#1B4F72]">{{ selected.key }}</code>
                        </div>
                        <button type="button" @click="form.is_active = !form.is_active" class="relative inline-flex h-7 w-12 items-center rounded-full transition-colors" :class="form.is_active ? 'bg-emerald-500' : 'bg-gray-300'">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-sm transition-transform" :class="form.is_active ? 'translate-x-1' : 'translate-x-6'"></span>
                        </button>
                    </div>

                    <!-- Lang tabs -->
                    <div class="flex gap-2 p-1 bg-gray-50 rounded-xl">
                        <button type="button" @click="activeLang = 'ar'" :class="activeLang === 'ar' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">عربي</button>
                        <button type="button" @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-white text-[#1B4F72] shadow-sm' : 'text-gray-500'" class="flex-1 py-2 rounded-lg text-sm font-semibold transition-all">English</button>
                    </div>

                    <!-- Variables toolbar -->
                    <div v-if="selected.variables && selected.variables.length" class="flex flex-wrap gap-2">
                        <span class="text-xs text-gray-500 self-center">المتغيرات المتاحة:</span>
                        <button
                            v-for="v in selected.variables"
                            :key="v"
                            type="button"
                            @click="insertVar(v)"
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-mono font-bold transition"
                        >
                            <span>{</span><span>{</span>{{ v }}<span>}</span><span>}</span>
                        </button>
                    </div>

                    <!-- Arabic -->
                    <div v-show="activeLang === 'ar'" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">عنوان الرسالة (عربي)</label>
                            <input v-model="form.subject_ar" type="text" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">محتوى الرسالة (HTML)</label>
                            <textarea id="body-ar" v-model="form.body_ar" rows="12" dir="rtl" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none"></textarea>
                        </div>
                    </div>

                    <div v-show="activeLang === 'en'" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Subject (English)</label>
                            <input v-model="form.subject_en" type="text" dir="ltr" required class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Body (HTML)</label>
                            <textarea id="body-en" v-model="form.body_en" rows="12" dir="ltr" required class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-[#1B4F72]/20 focus:border-[#1B4F72] outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Preview -->
                    <details class="border-t border-gray-100 pt-4">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700">👁 معاينة الـ HTML</summary>
                        <div class="mt-3 p-4 border border-gray-200 rounded-xl bg-gray-50 prose-sm max-w-none" :dir="activeLang === 'ar' ? 'rtl' : 'ltr'" v-html="activeLang === 'ar' ? form.body_ar : form.body_en"></div>
                    </details>

                    <div class="flex justify-end border-t border-gray-100 pt-5">
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] text-white rounded-xl text-sm font-bold shadow-lg hover:shadow-xl disabled:opacity-60">
                            {{ form.processing ? 'جاري الحفظ...' : 'حفظ القالب' }}
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </AdminLayout>
</template>
