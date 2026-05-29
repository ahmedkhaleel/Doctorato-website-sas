<script setup>
/**
 * 2FA Challenge — the second screen of the login flow.
 *
 * The user has passed credentials + the is_active check; the
 * AuthController parked their user_id in the session and bounced
 * them here. They need to enter a valid TOTP code (from their
 * authenticator app) OR toggle to recovery mode and burn one of
 * their 8 single-use recovery codes.
 *
 * Session expires after 15 minutes — if they sit on this page
 * longer than that, the verify endpoint returns them to /login.
 */
import { Head, useForm } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';

const form = useForm({
    code: '',
    is_recovery: false,
});

const codeInputRef = ref(null);

function submit() {
    form.post('/admin/2fa/verify', {
        onFinish: () => form.reset('code'),
    });
}

function toggleRecovery() {
    form.is_recovery = !form.is_recovery;
    form.code = '';
    nextTick(() => codeInputRef.value?.focus());
}

onMounted(() => codeInputRef.value?.focus());
</script>

<template>
    <Head title="التحقق الثنائي - لوحة التحكم" />

    <div class="min-h-screen bg-gradient-to-br from-[#1C2833] to-[#1B4F72] flex items-center justify-center p-4" dir="rtl">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-56 h-auto mx-auto mb-4 logo-white" />
                <p class="text-white/60 text-sm">لوحة تحكم الموقع</p>
            </div>

            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <!-- Lock icon as visual cue this is a security step -->
                <div class="flex justify-center mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-[#C4A265]/15 flex items-center justify-center">
                        <svg class="w-7 h-7 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <h2 class="text-xl font-bold text-gray-800 mb-2 text-center">التحقق الثنائي</h2>
                <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
                    <template v-if="!form.is_recovery">
                        افتح تطبيق المصادقة وأدخل الكود الظاهر لـ Doctorato.
                    </template>
                    <template v-else>
                        أدخل أحد أكواد الاسترداد اللي حفظتها وقت إعداد الـ 2FA.
                    </template>
                </p>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ form.is_recovery ? 'كود الاسترداد' : 'الكود (6 أرقام)' }}
                        </label>
                        <input
                            ref="codeInputRef"
                            v-model="form.code"
                            :type="form.is_recovery ? 'text' : 'tel'"
                            :inputmode="form.is_recovery ? 'text' : 'numeric'"
                            :pattern="form.is_recovery ? null : '[0-9]*'"
                            :maxlength="form.is_recovery ? 16 : 6"
                            dir="ltr"
                            autocomplete="one-time-code"
                            :placeholder="form.is_recovery ? 'XXXXXXXXXX' : '000000'"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-center text-2xl tracking-[0.4em] font-mono focus:ring-2 focus:ring-[#1B4F72] focus:border-transparent outline-none transition"
                            required
                        />
                        <p v-if="form.errors.code" class="text-rose-500 text-xs mt-2 text-center">{{ form.errors.code }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-[#1B4F72] hover:bg-[#0A1628] text-white font-semibold py-3 rounded-lg transition disabled:opacity-60"
                    >
                        {{ form.processing ? 'جارٍ التحقق…' : 'تحقق وادخل' }}
                    </button>
                </form>

                <!-- Toggle between TOTP and recovery -->
                <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                    <button
                        type="button"
                        @click="toggleRecovery"
                        class="text-sm text-gray-500 hover:text-[#1B4F72] transition"
                    >
                        {{ form.is_recovery ? '← استخدم كود التطبيق' : 'فقدت تطبيقك؟ استخدم كود الاسترداد' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.logo-white { filter: brightness(0) invert(1); }
</style>
