<script setup>
/**
 * DemoRequestForm — v3 (June 2026 simplification).
 *
 * Previous version had 13 fields, a searchable specialty combobox, a
 * country-vs-country-code split, and an interested-modules checkbox grid.
 * High abandonment. Sales reported leads they had to re-qualify on call
 * anyway because half the qualifying fields were blank.
 *
 * v3 keeps only what's necessary to book the demo:
 *   - clinic_name, full_name, email, phone (+ country code), specialty
 *
 * Everything else (modules, doctor count, referral source) is captured
 * on the call itself, where conversion happens — not on the page where
 * we want a frictionless submit.
 *
 * Defaults:
 *   - country_code: '+20' (Egypt) — explicit user request
 *   - phone autocomplete on
 *
 * Bot defenses kept intact:
 *   - honeypot input (hp_trap)
 *   - rendered-at timestamp (timing check server-side)
 *   - reCAPTCHA v3 token attached on submit
 */
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useTracking } from '@/composables/useTracking';
import { useRecaptcha } from '@/composables/useRecaptcha';

const { t, locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
useScrollAnimation();

const showSuccess = ref(false);
const submitting = computed(() => form.processing);

const form = useForm({
    clinic_name: '',
    full_name: '',
    email: '',
    country_code: '+20',       // Egypt default
    phone: '',
    specialty: '',
    // Hidden — captured from ?ref= cookie if present
    referred_by_code: usePage().props.referralCode || '',
    // Bot defenses
    hp_trap: '',
    form_rendered_at: Date.now(),
    recaptcha_token: '',
});

const captcha = useRecaptcha();
const track = useTracking();

// Country codes — Egypt FIRST so it reads as the default at a glance.
const countryCodes = [
    { code: '+20',  flag: '🇪🇬', label: 'EG' },
    { code: '+966', flag: '🇸🇦', label: 'SA' },
    { code: '+971', flag: '🇦🇪', label: 'AE' },
    { code: '+965', flag: '🇰🇼', label: 'KW' },
    { code: '+974', flag: '🇶🇦', label: 'QA' },
    { code: '+973', flag: '🇧🇭', label: 'BH' },
    { code: '+968', flag: '🇴🇲', label: 'OM' },
    { code: '+962', flag: '🇯🇴', label: 'JO' },
    { code: '+961', flag: '🇱🇧', label: 'LB' },
    { code: '+212', flag: '🇲🇦', label: 'MA' },
    { code: '+216', flag: '🇹🇳', label: 'TN' },
    { code: '+1',   flag: '🇺🇸', label: 'US' },
    { code: '+44',  flag: '🇬🇧', label: 'GB' },
];

const specialties = computed(() => [
    { value: 'derma',      label: t('demo.specialty_derma'),      emoji: '🧴' },
    { value: 'cosmetics',  label: t('demo.specialty_cosmetics'),  emoji: '💆‍♀️' },
    { value: 'dental',     label: t('demo.specialty_dental'),     emoji: '🦷' },
    { value: 'pediatrics', label: t('demo.specialty_pediatrics'), emoji: '👶' },
    { value: 'gyn',        label: t('demo.specialty_gyn'),        emoji: '🤰' },
    { value: 'general',    label: t('demo.specialty_general'),    emoji: '🩺' },
    { value: 'multi',      label: t('demo.specialty_multi'),      emoji: '🏥' },
    { value: 'other',      label: t('demo.specialty_other'),      emoji: '✨' },
]);

async function submitForm() {
    // Attach reCAPTCHA token (graceful — empty token is OK for low-risk submit).
    try {
        form.recaptcha_token = (await captcha.execute('demo_request')) || '';
    } catch (e) {
        form.recaptcha_token = '';
    }

    form.post(route('demo.store'), {
        preserveScroll: true,
        onSuccess: () => {
            track.lead({ form: 'demo_request', clinic: form.clinic_name });
            showSuccess.value = true;
            form.reset();
            form.country_code = '+20';  // re-apply default after reset
            form.form_rendered_at = Date.now();
            // Smooth-scroll into the success card so it's visible.
            setTimeout(() => {
                document.getElementById('demo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 80);
        },
    });
}
</script>

<template>
    <section
        id="demo"
        class="relative py-12 sm:py-16 lg:py-20 scroll-mt-20 overflow-hidden"
    >
        <!-- Pastel background gradient + floating blobs -->
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-rose-50/60 via-amber-50/40 to-sky-50/60"></div>
        <div class="absolute top-10 -start-16 w-72 h-72 rounded-full bg-rose-200/25 blur-3xl animate-blob"></div>
        <div class="absolute bottom-10 -end-16 w-80 h-80 rounded-full bg-sky-200/25 blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-amber-200/20 blur-3xl animate-blob animation-delay-4000"></div>

        <div class="relative max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section header -->
            <div class="text-center mb-8 sm:mb-10 animate-fade-up">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/70 backdrop-blur-sm ring-1 ring-secondary/20 text-[11px] font-bold uppercase tracking-widest text-secondary">
                    <span class="w-1.5 h-1.5 rounded-full bg-secondary animate-pulse"></span>
                    {{ isAr ? 'احجز عرضك المجاني' : 'Book your free demo' }}
                </span>
                <h2 class="mt-3 text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] leading-tight">
                    {{ isAr ? 'دقيقة واحدة — وفريقنا يتواصل معاك' : 'One minute — and our team will reach out' }}
                </h2>
                <p class="mt-2 text-sm sm:text-base text-gray-500">
                    {{ isAr ? '5 حقول بس. مفيش بطاقة ائتمان. مفيش التزام.' : 'Just 5 fields. No card. No commitment.' }}
                </p>
            </div>

            <!-- Card -->
            <div class="relative bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl shadow-rose-100/40 ring-1 ring-white/60 p-6 sm:p-8 animate-fade-up overflow-hidden">
                <!-- Decorative shimmer -->
                <div class="absolute -top-12 -end-12 w-40 h-40 bg-gradient-to-br from-rose-200/30 to-amber-200/0 rounded-full blur-2xl pointer-events-none"></div>

                <!-- Success state -->
                <Transition
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 scale-90"
                    enter-to-class="opacity-100 scale-100"
                >
                    <div v-if="showSuccess" class="relative text-center py-8 sm:py-12">
                        <div class="relative inline-flex items-center justify-center w-24 h-24 mb-5">
                            <div class="absolute inset-0 bg-emerald-200/40 rounded-full animate-ping"></div>
                            <div class="absolute inset-2 bg-emerald-100 rounded-full"></div>
                            <svg class="relative w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-[#1C2833] mb-2">
                            {{ t('demo.success_title') }}
                        </h3>
                        <p class="text-gray-600 max-w-md mx-auto leading-relaxed">
                            {{ t('demo.success_message') }}
                        </p>
                        <div class="mt-6 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 text-emerald-700 text-sm font-semibold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ isAr ? 'تأكيد على إيميلك' : 'Confirmation on your email' }}
                        </div>
                    </div>
                </Transition>

                <!-- Form -->
                <form v-if="!showSuccess" @submit.prevent="submitForm" class="relative space-y-4 sm:space-y-5">
                    <!-- Honeypot -->
                    <div class="absolute opacity-0 pointer-events-none -z-10" aria-hidden="true">
                        <label>Website <input v-model="form.hp_trap" type="text" tabindex="-1" autocomplete="off" /></label>
                    </div>

                    <!-- Full name -->
                    <div class="field-anim" style="--field-delay: 0ms">
                        <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                            <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-rose-100/70 text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <span>{{ t('demo.full_name') }} *</span>
                        </label>
                        <input
                            v-model="form.full_name"
                            type="text"
                            required
                            autocomplete="name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm text-[#1C2833] placeholder:text-gray-400 outline-none transition-all duration-300 focus:border-[#C4A265] focus:ring-2 focus:ring-[#C4A265]/20 focus:bg-white hover:border-gray-300"
                            :placeholder="t('demo.full_name_placeholder')"
                        />
                        <p v-if="form.errors.full_name" class="text-rose-600 text-xs mt-1.5">{{ form.errors.full_name }}</p>
                    </div>

                    <!-- Clinic name -->
                    <div class="field-anim" style="--field-delay: 60ms">
                        <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                            <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-amber-100/70 text-amber-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m-1 4h1m4-4h1m-1 4h1m-5 4h6"/></svg>
                            </span>
                            <span>{{ t('demo.clinic_name') }} *</span>
                        </label>
                        <input
                            v-model="form.clinic_name"
                            type="text"
                            required
                            autocomplete="organization"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm text-[#1C2833] placeholder:text-gray-400 outline-none transition-all duration-300 focus:border-[#C4A265] focus:ring-2 focus:ring-[#C4A265]/20 focus:bg-white hover:border-gray-300"
                            :placeholder="t('demo.clinic_name_placeholder')"
                        />
                        <p v-if="form.errors.clinic_name" class="text-rose-600 text-xs mt-1.5">{{ form.errors.clinic_name }}</p>
                    </div>

                    <!-- Email -->
                    <div class="field-anim" style="--field-delay: 120ms">
                        <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                            <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-sky-100/70 text-sky-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <span>{{ t('demo.email') }} *</span>
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="email"
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm text-[#1C2833] placeholder:text-gray-400 outline-none transition-all duration-300 focus:border-[#C4A265] focus:ring-2 focus:ring-[#C4A265]/20 focus:bg-white hover:border-gray-300"
                            :placeholder="t('demo.email_placeholder')"
                        />
                        <p v-if="form.errors.email" class="text-rose-600 text-xs mt-1.5">{{ form.errors.email }}</p>
                    </div>

                    <!-- Phone with country code (Egypt default) -->
                    <div class="field-anim" style="--field-delay: 180ms">
                        <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                            <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-emerald-100/70 text-emerald-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <span>{{ t('demo.phone') }} *</span>
                        </label>
                        <div class="flex gap-2">
                            <div class="relative">
                                <select
                                    v-model="form.country_code"
                                    class="px-3 py-3 pe-9 w-32 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm text-[#1C2833] outline-none transition-all duration-300 focus:border-[#C4A265] focus:ring-2 focus:ring-[#C4A265]/20 focus:bg-white hover:border-gray-300 cursor-pointer text-sm tabular-nums appearance-none"
                                >
                                    <option
                                        v-for="cc in countryCodes"
                                        :key="cc.code"
                                        :value="cc.code"
                                    >
                                        {{ cc.flag }} {{ cc.code }}
                                    </option>
                                </select>
                                <svg class="absolute end-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <input
                                v-model="form.phone"
                                type="tel"
                                required
                                autocomplete="tel-national"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm text-[#1C2833] placeholder:text-gray-400 outline-none transition-all duration-300 focus:border-[#C4A265] focus:ring-2 focus:ring-[#C4A265]/20 focus:bg-white hover:border-gray-300"
                                :placeholder="isAr ? '1XXXXXXXXX' : '1XXXXXXXXX'"
                                inputmode="numeric"
                            />
                        </div>
                        <p v-if="form.errors.phone" class="text-rose-600 text-xs mt-1.5">{{ form.errors.phone }}</p>
                    </div>

                    <!-- Specialty -->
                    <div class="field-anim" style="--field-delay: 240ms">
                        <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                            <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-fuchsia-100/70 text-fuchsia-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <span>{{ t('demo.specialty') }}</span>
                            <span class="text-[11px] font-normal text-gray-400">{{ isAr ? '(اختياري)' : '(optional)' }}</span>
                        </label>
                        <div class="relative">
                            <select
                                v-model="form.specialty"
                                class="w-full px-4 py-3 pe-10 rounded-xl border border-gray-200 bg-white/80 backdrop-blur-sm text-[#1C2833] outline-none transition-all duration-300 focus:border-[#C4A265] focus:ring-2 focus:ring-[#C4A265]/20 focus:bg-white hover:border-gray-300 appearance-none cursor-pointer"
                            >
                                <option value="">{{ isAr ? 'اختر التخصص الطبي' : 'Pick your specialty' }}</option>
                                <option
                                    v-for="s in specialties"
                                    :key="s.value"
                                    :value="s.value"
                                >
                                    {{ s.emoji }} &nbsp; {{ s.label }}
                                </option>
                            </select>
                            <svg class="absolute end-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="pt-3 field-anim" style="--field-delay: 320ms">
                        <button
                            type="submit"
                            :disabled="submitting"
                            class="group relative w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-2xl font-bold text-white shadow-lg shadow-rose-200/40 hover:shadow-2xl hover:shadow-rose-200/60 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed overflow-hidden"
                            style="background: linear-gradient(135deg, #1B4F72 0%, #2E6FA1 50%, #C4A265 100%); background-size: 200% 200%; animation: gradientShift 6s ease infinite;"
                        >
                            <!-- Sheen sweep -->
                            <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>

                            <template v-if="submitting">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                <span class="relative">{{ t('demo.submitting') }}</span>
                            </template>
                            <template v-else>
                                <span class="relative text-base">{{ isAr ? 'اطلب عرضك المجاني' : 'Request your free demo' }}</span>
                                <svg class="relative w-5 h-5 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </template>
                        </button>

                        <!-- Reassurance row under the button -->
                        <div class="mt-4 flex flex-wrap justify-center gap-3 text-[11px] sm:text-xs text-gray-500">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ isAr ? 'تجربة 30 يوم مجانية' : '30-day free trial' }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ isAr ? 'بدون بطاقة ائتمان' : 'No credit card' }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ isAr ? 'تركيب مجاني' : 'Free setup' }}
                            </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* Staggered field entrance */
.field-anim {
    animation: fieldRise 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    animation-delay: var(--field-delay, 0ms);
}
@keyframes fieldRise {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Animated background blobs */
.animate-blob {
    animation: blob 18s ease-in-out infinite;
}
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }

@keyframes blob {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33%      { transform: translate(20px, -30px) scale(1.05); }
    66%      { transform: translate(-15px, 20px) scale(0.95); }
}

@keyframes gradientShift {
    0%, 100% { background-position: 0% 50%; }
    50%      { background-position: 100% 50%; }
}
</style>
