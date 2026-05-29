<script setup>
/**
 * 2FA Settings — self-service enrolment / management for the
 * signed-in admin. Three states:
 *
 *   1. Not enabled, not in setup
 *       Single button: "Enable two-factor authentication".
 *
 *   2. Mid-setup (pendingSecret returned by /setup)
 *       Show QR (rendered via Google Chart API as a fallback so we
 *       don't need to bundle a QR library), plus the raw secret for
 *       manual entry, plus a 6-digit input to confirm. After confirm,
 *       the server returns the freshly-minted recovery codes which
 *       the user MUST save somewhere before navigating away.
 *
 *   3. Enabled
 *       Shows "confirmed at" date, count of recovery codes
 *       remaining, and two destructive buttons: regenerate recovery
 *       codes and disable.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    enabled: Boolean,
    confirmedAt: { type: String, default: null },
    recoveryCodesRemaining: { type: Number, default: 0 },
});

const page = usePage();
const pendingSecret = computed(() => page.props.flash?.twoFactorSetup ?? null);
const newRecoveryCodes = computed(() => page.props.flash?.twoFactorRecovery ?? null);

const setupForm = useForm({});
const confirmForm = useForm({ code: '' });
const disableForm = useForm({ password: '' });
const regenForm = useForm({});

const showDisablePrompt = ref(false);
const copiedSecret = ref(false);
const copiedCodes = ref(false);

function startSetup() { setupForm.post('/admin/2fa/setup'); }
function confirmSetup() { confirmForm.post('/admin/2fa/confirm', { onFinish: () => confirmForm.reset('code') }); }
function disable() { disableForm.post('/admin/2fa/disable', { onFinish: () => { disableForm.reset(); showDisablePrompt.value = false; } }); }
function regenerate() { regenForm.post('/admin/2fa/recovery'); }

function copySecret() {
    if (!pendingSecret.value?.secret) return;
    navigator.clipboard.writeText(pendingSecret.value.secret);
    copiedSecret.value = true;
    setTimeout(() => copiedSecret.value = false, 2000);
}
function copyCodes() {
    if (!newRecoveryCodes.value) return;
    navigator.clipboard.writeText(newRecoveryCodes.value.join('\n'));
    copiedCodes.value = true;
    setTimeout(() => copiedCodes.value = false, 2000);
}

// External QR endpoint — fine because the URI itself doesn't contain
// the user's email, just the otpauth string. Falls back gracefully
// to the manual-entry secret if the image fails to load.
function qrUrl(uri) {
    return `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(uri)}`;
}
</script>

<template>
    <Head title="التحقق الثنائي 2FA" />
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6" dir="rtl">
            <header class="mb-8">
                <h1 class="text-2xl font-bold text-[#0A1628] mb-1">التحقق الثنائي (2FA)</h1>
                <p class="text-sm text-gray-500">
                    طبقة حماية إضافية لحسابك — حتى لو سُربت كلمة المرور، لن يقدر أحد على الدخول بدون موبايلك.
                </p>
            </header>

            <!-- ─── State 1: not enabled, no pending setup ─── -->
            <div v-if="!enabled && !pendingSecret" class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="font-bold text-lg text-[#0A1628] mb-2">2FA غير مفعّل</h2>
                <p class="text-sm text-gray-500 mb-6 max-w-md mx-auto leading-relaxed">
                    نوصي بشدة بتفعيل التحقق الثنائي — خاصةً للحسابات التي تملك صلاحيات إدارة المدفوعات أو المستخدمين.
                </p>
                <button @click="startSetup" :disabled="setupForm.processing" class="bg-[#1B4F72] hover:bg-[#0A1628] text-white font-semibold px-6 py-3 rounded-lg transition disabled:opacity-60">
                    {{ setupForm.processing ? 'جارٍ…' : 'تفعيل التحقق الثنائي' }}
                </button>
            </div>

            <!-- ─── State 2: setup in progress ─── -->
            <div v-else-if="pendingSecret" class="bg-white rounded-2xl border border-gray-200 p-8">
                <h2 class="font-bold text-lg text-[#0A1628] mb-4">الخطوة 1 من 2 — امسح الـ QR</h2>
                <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                    افتح تطبيق المصادقة (Google Authenticator أو Authy أو Microsoft Authenticator) وامسح هذا الـ QR، أو أدخل الكود يدوياً.
                </p>

                <div class="flex flex-col sm:flex-row gap-6 items-start">
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                        <img :src="qrUrl(pendingSecret.uri)" alt="QR" class="w-48 h-48" />
                    </div>
                    <div class="flex-1">
                        <p class="text-xs font-bold uppercase text-gray-500 tracking-wider mb-2">الكود (للإدخال اليدوي)</p>
                        <div class="flex items-center gap-2">
                            <code class="flex-1 font-mono text-xs bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 break-all" dir="ltr">{{ pendingSecret.secret }}</code>
                            <button @click="copySecret" class="text-xs font-semibold text-[#1B4F72] hover:text-[#0A1628] px-3 py-2 rounded-lg bg-[#1B4F72]/10 hover:bg-[#1B4F72]/20 transition">
                                {{ copiedSecret ? 'تم النسخ ✓' : 'نسخ' }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-gray-100">
                    <h3 class="font-bold text-base text-[#0A1628] mb-3">الخطوة 2 من 2 — أكّد الكود</h3>
                    <form @submit.prevent="confirmSetup" class="flex flex-col sm:flex-row gap-3 max-w-md">
                        <input
                            v-model="confirmForm.code"
                            type="tel"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6"
                            dir="ltr"
                            placeholder="000000"
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-3 text-center text-xl tracking-[0.4em] font-mono focus:ring-2 focus:ring-[#1B4F72] outline-none transition"
                            required
                        />
                        <button type="submit" :disabled="confirmForm.processing" class="bg-[#1B4F72] hover:bg-[#0A1628] text-white font-semibold px-5 py-3 rounded-lg transition disabled:opacity-60">
                            تأكيد
                        </button>
                    </form>
                    <p v-if="confirmForm.errors.code" class="text-rose-500 text-xs mt-2">{{ confirmForm.errors.code }}</p>
                </div>
            </div>

            <!-- ─── State 3: enabled ─── -->
            <div v-else class="space-y-4">
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/15 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-emerald-900 mb-0.5">2FA مفعّل</p>
                        <p class="text-xs text-emerald-700">منذ {{ confirmedAt ? new Date(confirmedAt).toLocaleDateString('ar') : '—' }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <h3 class="font-bold text-[#0A1628] mb-1">أكواد الاسترداد</h3>
                    <p class="text-sm text-gray-500 mb-4 leading-relaxed">
                        متبقّي <strong>{{ recoveryCodesRemaining }}</strong> كود. لو نزلت تحت 3، أعد توليدهم.
                    </p>
                    <button @click="regenerate" :disabled="regenForm.processing" class="text-sm font-semibold text-[#1B4F72] hover:text-[#0A1628] px-4 py-2 rounded-lg bg-[#1B4F72]/10 hover:bg-[#1B4F72]/20 transition disabled:opacity-60">
                        إعادة توليد الأكواد
                    </button>
                </div>

                <div class="bg-white rounded-2xl border border-rose-200 p-6">
                    <h3 class="font-bold text-rose-900 mb-1">تعطيل 2FA</h3>
                    <p class="text-sm text-gray-500 mb-4 leading-relaxed">
                        لو عطّلته، أي شخص يعرف كلمة مرورك يقدر يدخل. ما ننصحش بده.
                    </p>
                    <button v-if="!showDisablePrompt" @click="showDisablePrompt = true" class="text-sm font-semibold text-rose-700 hover:text-rose-800 px-4 py-2 rounded-lg bg-rose-50 hover:bg-rose-100 transition">
                        تعطيل التحقق الثنائي
                    </button>
                    <form v-else @submit.prevent="disable" class="flex flex-col sm:flex-row gap-3">
                        <input
                            v-model="disableForm.password"
                            type="password"
                            placeholder="أدخل كلمة المرور للتأكيد"
                            class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-rose-300 outline-none"
                            required
                        />
                        <button type="submit" :disabled="disableForm.processing" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-4 py-2 rounded-lg text-sm transition disabled:opacity-60">
                            تأكيد التعطيل
                        </button>
                        <button type="button" @click="showDisablePrompt = false" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">
                            إلغاء
                        </button>
                    </form>
                    <p v-if="disableForm.errors.password" class="text-rose-500 text-xs mt-2">{{ disableForm.errors.password }}</p>
                </div>
            </div>

            <!-- ─── Recovery codes modal (shown right after enabling or regenerating) ─── -->
            <div v-if="newRecoveryCodes" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-2xl max-w-lg w-full p-8 max-h-[90vh] overflow-y-auto">
                    <h2 class="text-xl font-bold text-[#0A1628] mb-2">احفظ هذه الأكواد الآن</h2>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                        هذه أكواد الاسترداد. كل كود يُستخدم مرة واحدة فقط — لن نعرضهم لك مرة أخرى. خزّنهم في مدير كلمات المرور أو اطبعهم.
                    </p>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 font-mono text-sm grid grid-cols-2 gap-2 mb-4" dir="ltr">
                        <span v-for="code in newRecoveryCodes" :key="code" class="text-gray-800">{{ code }}</span>
                    </div>
                    <div class="flex gap-3">
                        <button @click="copyCodes" class="flex-1 bg-[#1B4F72] hover:bg-[#0A1628] text-white font-semibold py-2.5 rounded-lg transition">
                            {{ copiedCodes ? 'تم النسخ ✓' : 'نسخ الأكواد' }}
                        </button>
                        <button @click="router.reload({ only: [] })" class="flex-1 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-2.5 rounded-lg transition">
                            حفظتهم — أغلق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
