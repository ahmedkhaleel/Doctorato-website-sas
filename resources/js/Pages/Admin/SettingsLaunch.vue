<script setup>
/**
 * Admin · Launch offer & bot-protection controls.
 *
 * One page, two cards:
 *   1. Launch offer — toggle the "first 50 clinics" banner and the
 *      seat count. Live snapshot (used/remaining) is shown alongside
 *      so the admin sees the current state of the scarcity counter.
 *   2. reCAPTCHA keys — plain inputs for the v3 site + secret keys.
 *      The secret is write-only (never echoed back) for basic hygiene.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    launch: { type: Object, required: true },
    recaptcha: { type: Object, required: true },
});

const form = useForm({
    active: props.launch.active,
    total_slots: props.launch.total_slots,
    recaptcha_site_key: props.recaptcha.site_key || '',
    recaptcha_secret_key: '', // Always blank on load; empty = "don't overwrite"
});

const seatColor = computed(() => {
    const r = props.launch.remaining_slots;
    if (r <= 10) return 'text-red-600';
    if (r <= 20) return 'text-amber-600';
    return 'text-emerald-600';
});

function submit() {
    form.put(route('admin.settings.launch.update'), {
        preserveScroll: true,
        onSuccess: () => { form.recaptcha_secret_key = ''; },
    });
}
</script>

<template>
    <Head title="إعدادات الإطلاق — لوحة التحكم" />

    <AdminLayout page-title="إعدادات الإطلاق والحماية">
        <form @submit.prevent="submit" class="space-y-6 max-w-3xl">
            <!-- Launch offer card -->
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                <header class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center text-2xl shrink-0">🎁</div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">عرض الإطلاق</h2>
                        <p class="text-sm text-gray-500 mt-0.5">تحكّم في الـ banner اللي بيظهر في صفحة الأسعار والعدّاد الحي.</p>
                    </div>
                </header>

                <!-- Live snapshot -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 text-center">
                        <p class="text-[10px] text-gray-400 uppercase tracking-widest mb-1">إجمالي</p>
                        <p class="text-2xl font-extrabold text-gray-800 tabular-nums">{{ launch.total_slots }}</p>
                    </div>
                    <div class="rounded-xl bg-blue-50 border border-blue-100 p-4 text-center">
                        <p class="text-[10px] text-blue-500 uppercase tracking-widest mb-1">مستخدم</p>
                        <p class="text-2xl font-extrabold text-blue-700 tabular-nums">{{ launch.used_slots }}</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-center">
                        <p class="text-[10px] text-emerald-500 uppercase tracking-widest mb-1">متبقي</p>
                        <p class="text-2xl font-extrabold tabular-nums" :class="seatColor">{{ launch.remaining_slots }}</p>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="mb-6">
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-l from-[#C4A265] to-[#1B4F72] transition-all" :style="{ width: `${launch.progress_percent}%` }"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">{{ launch.progress_percent }}% ممتلئ</p>
                </div>

                <!-- Toggle -->
                <label class="flex items-center justify-between bg-gray-50 rounded-xl p-4 mb-4 cursor-pointer">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">عرض الـ banner في صفحة الأسعار</p>
                        <p class="text-xs text-gray-500 mt-0.5">لما تطفّيه، الـ banner يختفي تلقائياً من كل الموقع.</p>
                    </div>
                    <input type="checkbox" v-model="form.active" class="sr-only peer" />
                    <div class="relative w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-emerald-500 transition">
                        <div class="absolute top-0.5 start-0.5 w-5 h-5 bg-white rounded-full transition peer-checked:translate-x-5 rtl:peer-checked:-translate-x-5"></div>
                    </div>
                </label>

                <!-- Total slots -->
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">إجمالي المقاعد المتاحة</label>
                    <input
                        v-model.number="form.total_slots"
                        type="number" min="1" max="10000" step="1" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#C4A265]/30 focus:border-[#C4A265] outline-none transition"
                    />
                    <p class="text-[11px] text-gray-400 mt-1.5">
                        العدد اللي الـ banner هيقول "باقي X من {{ form.total_slots }}". يحسب تلقائياً الاشتراكات النشطة/التجريبية.
                    </p>
                    <p v-if="form.errors.total_slots" class="text-red-500 text-xs mt-1">{{ form.errors.total_slots }}</p>
                </div>
            </section>

            <!-- reCAPTCHA card -->
            <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                <header class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-2xl shrink-0">🛡️</div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800">Google reCAPTCHA v3</h2>
                        <p class="text-sm text-gray-500 mt-0.5">حماية إضافية ضد الـ spam على فورم التجربة والـ demo والتواصل.</p>
                    </div>
                </header>

                <div class="mb-5 rounded-xl p-4"
                     :class="recaptcha.secret_key_set ? 'bg-emerald-50 border border-emerald-100' : 'bg-amber-50 border border-amber-100'">
                    <p class="text-sm font-semibold"
                       :class="recaptcha.secret_key_set ? 'text-emerald-800' : 'text-amber-800'">
                        {{ recaptcha.secret_key_set
                            ? '✓ reCAPTCHA مفعّل — الفورم بتتحقق من Google قبل القبول'
                            : '⚠ reCAPTCHA غير مُعد — الموقع يستخدم Honeypot + timing فقط' }}
                    </p>
                    <p class="text-xs mt-1 leading-relaxed"
                       :class="recaptcha.secret_key_set ? 'text-emerald-700/80' : 'text-amber-700/80'">
                        احصل على المفاتيح من
                        <a href="https://www.google.com/recaptcha/admin" target="_blank" class="underline">Google reCAPTCHA Admin</a>
                        — اختر v3.
                    </p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">Site Key (عام)</label>
                        <input
                            v-model="form.recaptcha_site_key"
                            type="text" dir="ltr"
                            placeholder="6Lxxx..."
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition"
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1.5">
                            Secret Key (سرّي)
                            <span v-if="recaptcha.secret_key_set" class="ms-2 text-[10px] font-semibold text-emerald-600 normal-case">· مخزّن حالياً</span>
                        </label>
                        <input
                            v-model="form.recaptcha_secret_key"
                            type="password" dir="ltr"
                            :placeholder="recaptcha.secret_key_set ? '••••••••••••• (اتركه فارغاً للاحتفاظ بالقيمة الحالية)' : '6Lxxx...'"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500 outline-none transition"
                        />
                        <p class="text-[11px] text-gray-400 mt-1.5">
                            لا يُعرَض أبداً في الواجهة. اتركه فارغاً لعدم تغيير المفتاح الحالي.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Save bar -->
            <div class="flex items-center gap-3 sticky bottom-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-2.5 bg-gradient-to-r from-[#1B4F72] to-[#0A1628] text-white font-semibold rounded-full shadow-lg hover:shadow-xl disabled:opacity-60 transition"
                >
                    {{ form.processing ? 'جاري الحفظ...' : 'حفظ الإعدادات' }}
                </button>
                <p v-if="$page.props.flash?.success" class="text-emerald-600 text-sm font-semibold">✓ {{ $page.props.flash.success }}</p>
            </div>
        </form>
    </AdminLayout>
</template>
