<script setup>
/**
 * DemoRequestForm — v4 (June 2026 smart wizard).
 *
 * 3-step progressive form. Each step is short and focused, so the visitor
 * sees a quick win (and a progress bar) before committing to more.
 *
 * Step 1 — Identity:           full_name, clinic_name, email, phone (+EG code)
 * Step 2 — Your facility:      facility_type (solo/clinic/polyclinic/hospital),
 *                              doctors_count, specialty
 * Step 3 — Your needs:         interested_modules (multi), referral_source
 *
 * Smart bits:
 *   - Egypt is the default country code AND placed first in the list.
 *   - facility_type chips set a sensible doctors_count default
 *     (solo→1, clinic→2-5, polyclinic→6-15, hospital→16-50).
 *   - Step 2 doctors_count radios auto-adjust to facility scale.
 *   - Step 3 is technically optional — the visitor can submit from step 2.
 *   - Progress bar fills smoothly; pressing Enter on any step advances.
 *   - Server validates ONLY required fields (clinic_name, full_name,
 *     email, phone, country_code), so partial qualifying data is fine.
 *
 * Backend stays the same: POST /demo-request with all fields.
 */
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useTracking } from '@/composables/useTracking';
import { useRecaptcha } from '@/composables/useRecaptcha';

const { t, locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
useScrollAnimation();

const showSuccess = ref(false);
const currentStep = ref(1);
const totalSteps = 3;

const form = useForm({
    // Step 1
    full_name: '',
    clinic_name: '',
    email: '',
    country_code: '+20',          // Egypt default
    phone: '',
    // Step 2
    facility_type: '',
    doctors_count: '',
    specialty: '',
    // Step 3
    interested_modules: [],
    referral_source: '',
    // Hidden
    referred_by_code: usePage().props.referralCode || '',
    hp_trap: '',
    form_rendered_at: Date.now(),
    recaptcha_token: '',
});

const captcha = useRecaptcha();
const track = useTracking();
const submitting = computed(() => form.processing);

// Progress (1..totalSteps mapped to %)
const progress = computed(() => Math.round(((currentStep.value - 1) / totalSteps) * 100));

// ─── Step 1 ───
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

const step1Valid = computed(() =>
    form.full_name.trim().length >= 3 &&
    form.clinic_name.trim().length >= 2 &&
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.trim()) &&
    form.phone.trim().length >= 6
);

// ─── Step 2 ───
const facilityTypes = computed(() => [
    {
        value: 'solo',
        label: isAr.value ? 'طبيب فرد' : 'Solo doctor',
        desc: isAr.value ? 'عيادة خاصة بطبيب واحد' : 'A single-doctor private clinic',
        icon: '🩺',
        tint: 'from-rose-100 to-rose-50 ring-rose-200 text-rose-700',
    },
    {
        value: 'clinic',
        label: isAr.value ? 'عيادة' : 'Clinic',
        desc: isAr.value ? 'عيادة بفريق صغير وتخصص واضح' : 'Small clinic, focused specialty',
        icon: '🏥',
        tint: 'from-amber-100 to-amber-50 ring-amber-200 text-amber-700',
    },
    {
        value: 'polyclinic',
        label: isAr.value ? 'مركز طبي / بولي كلينك' : 'Polyclinic',
        desc: isAr.value ? 'مركز متعدد التخصصات بعدة أطباء' : 'Multi-specialty with several doctors',
        icon: '🏢',
        tint: 'from-sky-100 to-sky-50 ring-sky-200 text-sky-700',
    },
    {
        value: 'hospital',
        label: isAr.value ? 'مستشفى' : 'Hospital',
        desc: isAr.value ? 'مستشفى متكامل بفروع وأقسام' : 'Full hospital with branches and departments',
        icon: '🏨',
        tint: 'from-fuchsia-100 to-fuchsia-50 ring-fuchsia-200 text-fuchsia-700',
    },
]);

// Doctor count buckets, ordered. Sensible defaults set when facility_type changes.
const doctorCountOptions = ['1', '2-5', '6-15', '16-50', '50+'];
const facilityDefaultDoctors = { solo: '1', clinic: '2-5', polyclinic: '6-15', hospital: '16-50' };

watch(() => form.facility_type, (val) => {
    // Only set a default count if the user hasn't already picked one;
    // otherwise we'd overwrite their choice when they swap facility.
    if (!form.doctors_count || form.doctors_count === facilityDefaultDoctors[Object.keys(facilityDefaultDoctors).find(k => facilityDefaultDoctors[k] === form.doctors_count)] ) {
        form.doctors_count = facilityDefaultDoctors[val] || '';
    }
});

const specialties = computed(() => [
    { value: 'derma',      label: t('demo.specialty_derma'),      emoji: '🧴' },
    { value: 'cosmetics',  label: t('demo.specialty_cosmetics'),  emoji: '💆‍♀️' },
    { value: 'dental',     label: t('demo.specialty_dental'),     emoji: '🦷' },
    { value: 'pediatrics', label: t('demo.specialty_pediatrics'), emoji: '👶' },
    { value: 'gyn',        label: t('demo.specialty_gyn'),        emoji: '🤰' },
    { value: 'ortho',      label: t('demo.specialty_ortho'),      emoji: '🦴' },
    { value: 'cardio',     label: t('demo.specialty_cardio'),     emoji: '❤️' },
    { value: 'ent',        label: t('demo.specialty_ent'),        emoji: '👂' },
    { value: 'eye',        label: t('demo.specialty_eye'),        emoji: '👁️' },
    { value: 'general',    label: t('demo.specialty_general'),    emoji: '🩺' },
    { value: 'multi',      label: t('demo.specialty_multi'),      emoji: '🏥' },
    { value: 'other',      label: t('demo.specialty_other'),      emoji: '✨' },
]);

const step2Valid = computed(() =>
    form.facility_type && form.doctors_count && form.specialty
);

// ─── Step 3 ───
const modules = computed(() => [
    { value: 'emr',          label: isAr.value ? 'السجل الطبي الإلكتروني (EMR)' : 'EMR / patient records',     icon: '📋' },
    { value: 'booking',      label: isAr.value ? 'الحجز والمواعيد'           : 'Booking & appointments',       icon: '📅' },
    { value: 'billing',      label: isAr.value ? 'الفواتير والإيصال الإلكتروني' : 'Billing & e-receipt',       icon: '💳' },
    { value: 'whatsapp',     label: isAr.value ? 'WhatsApp Business'           : 'WhatsApp Business',            icon: '💬' },
    { value: 'telemedicine', label: isAr.value ? 'استشارات أونلاين (Telemed)'  : 'Telemedicine (online consults)', icon: '🎥' },
    { value: 'insurance',    label: isAr.value ? 'تكامل التأمين الطبي'         : 'Insurance integration',        icon: '🛡️' },
    { value: 'lab',          label: isAr.value ? 'تكامل المختبرات'             : 'Lab integration',              icon: '🧪' },
    { value: 'pharmacy',     label: isAr.value ? 'إدارة الصيدلية / المخزون'    : 'Pharmacy & inventory',         icon: '💊' },
    { value: 'hr',           label: isAr.value ? 'الموارد البشرية والرواتب'    : 'HR & payroll',                 icon: '👥' },
    { value: 'analytics',    label: isAr.value ? 'التقارير والتحليلات'         : 'Reports & analytics',          icon: '📊' },
]);

const referralOptions = computed(() => [
    { value: 'google',       label: isAr.value ? 'بحث Google'              : 'Google search',         icon: '🔎' },
    { value: 'social',       label: isAr.value ? 'فيسبوك / إنستجرام'        : 'Facebook / Instagram',  icon: '📱' },
    { value: 'colleague',    label: isAr.value ? 'زميل أو صديق'             : 'Colleague or friend',   icon: '🤝' },
    { value: 'ad',           label: isAr.value ? 'إعلان رأيته'              : 'An ad I saw',           icon: '📢' },
    { value: 'conference',   label: isAr.value ? 'مؤتمر طبي'               : 'Medical conference',    icon: '🎤' },
    { value: 'sales_rep',    label: isAr.value ? 'مندوب مبيعات تواصل معي'    : 'A sales rep reached out', icon: '📞' },
    { value: 'other',        label: isAr.value ? 'مصدر آخر'                 : 'Other',                  icon: '✨' },
]);

function toggleModule(val) {
    const i = form.interested_modules.indexOf(val);
    if (i > -1) form.interested_modules.splice(i, 1);
    else form.interested_modules.push(val);
}

// ─── Step navigation ───
function nextStep() {
    if (currentStep.value === 1 && !step1Valid.value) return;
    if (currentStep.value === 2 && !step2Valid.value) return;
    if (currentStep.value < totalSteps) currentStep.value += 1;
}
function prevStep() {
    if (currentStep.value > 1) currentStep.value -= 1;
}

// ─── Submit ───
async function submitForm() {
    try {
        form.recaptcha_token = (await captcha.execute('demo_request')) || '';
    } catch (e) {
        form.recaptcha_token = '';
    }
    form.post('/demo-request', {
        // preserveState keeps THIS component instance alive across the
        // back() redirect — so showSuccess stays true and the user
        // actually sees the success banner. Without it, Inertia replaces
        // the page after the redirect and the banner flashes for 1 frame.
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            track.lead({
                form: 'demo_request',
                clinic: form.clinic_name,
                facility_type: form.facility_type,
                doctors_count: form.doctors_count,
            });
            showSuccess.value = true;
            form.reset();
            form.country_code = '+20';
            currentStep.value = 1;
            form.form_rendered_at = Date.now();
            setTimeout(() => {
                document.getElementById('demo')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 80);
        },
    });
}

// Fallback path: if the redirect arrives with a flash.success set (e.g.
// the user navigated back to /demo via browser back button and Laravel
// re-played the flash), surface the success banner anyway. Belt-and-
// suspenders with the onSuccess callback above.
onMounted(() => {
    if (usePage().props.flash?.success) {
        showSuccess.value = true;
    }
});
</script>

<template>
    <section
        id="demo"
        class="relative py-12 sm:py-16 lg:py-20 scroll-mt-20 overflow-hidden"
    >
        <!-- Pastel animated background -->
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-rose-50/60 via-amber-50/40 to-sky-50/60"></div>
        <div class="absolute top-10 -start-16 w-72 h-72 rounded-full bg-rose-200/25 blur-3xl animate-blob"></div>
        <div class="absolute bottom-10 -end-16 w-80 h-80 rounded-full bg-sky-200/25 blur-3xl animate-blob animation-delay-2000"></div>
        <div class="absolute top-1/2 left-1/2 w-64 h-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-amber-200/20 blur-3xl animate-blob animation-delay-4000"></div>

        <div class="relative max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section header -->
            <div class="text-center mb-6 sm:mb-8 animate-fade-up">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/70 backdrop-blur-sm ring-1 ring-[#C4A265]/20 text-[11px] font-bold uppercase tracking-widest text-[#C4A265]">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></span>
                    {{ isAr ? 'احجز عرضك المجاني' : 'Book your free demo' }}
                </span>
                <h2 class="mt-3 text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] leading-tight">
                    {{ isAr ? 'ادفع لما تتأكد. مش قبل.' : "Pay when you're sure. Not before." }}
                </h2>
                <p class="mt-2 text-sm sm:text-base text-gray-500">
                    {{ isAr ? '3 خطوات قصيرة. أقل من دقيقة. وفريقنا يتواصل معاك خلال 24 ساعة.' : '3 short steps. Under a minute. Our team gets back to you within 24 hours.' }}
                </p>
            </div>

            <!-- Card -->
            <div class="relative bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl shadow-rose-100/40 ring-1 ring-white/60 p-6 sm:p-8 animate-fade-up overflow-hidden">
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
                <form v-if="!showSuccess" @submit.prevent="submitForm" class="relative">
                    <!-- Honeypot -->
                    <div class="absolute opacity-0 pointer-events-none -z-10" aria-hidden="true">
                        <label>Website <input v-model="form.hp_trap" type="text" tabindex="-1" autocomplete="off" /></label>
                    </div>

                    <!-- Progress -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between text-xs font-semibold text-gray-500 mb-2">
                            <span>{{ isAr ? `خطوة ${currentStep} من ${totalSteps}` : `Step ${currentStep} of ${totalSteps}` }}</span>
                            <span class="text-[#C4A265]">{{ Math.round((currentStep / totalSteps) * 100) }}%</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div
                                class="h-full bg-gradient-to-r from-[#1B4F72] via-[#2E6FA1] to-[#C4A265] rounded-full transition-all duration-500 ease-out"
                                :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- STEPS -->
                    <Transition
                        enter-active-class="transition duration-400 ease-out"
                        enter-from-class="opacity-0 translate-x-4 rtl:-translate-x-4"
                        enter-to-class="opacity-100 translate-x-0"
                        leave-active-class="transition duration-200 ease-in absolute inset-0"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0 -translate-x-4 rtl:translate-x-4"
                        mode="out-in"
                    >
                        <!-- ─── STEP 1 — Identity ─────────────────────── -->
                        <div v-if="currentStep === 1" key="step1" class="space-y-4">
                            <div class="text-center mb-1">
                                <h3 class="text-lg font-extrabold text-[#1C2833]">
                                    {{ isAr ? 'نتعرّف عليك' : 'Tell us about you' }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ isAr ? 'بياناتك للتواصل فقط — مش هتشاركها مع أي حد' : 'Just for our team to reach you — never shared' }}
                                </p>
                            </div>

                            <div class="field-anim" style="--field-delay: 0ms">
                                <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                                    <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-rose-100/70 text-rose-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </span>
                                    <span>{{ t('demo.full_name') }} *</span>
                                </label>
                                <input v-model="form.full_name" type="text" required autocomplete="name" class="field-input" :placeholder="t('demo.full_name_placeholder')" />
                                <p v-if="form.errors.full_name" class="text-rose-600 text-xs mt-1.5">{{ form.errors.full_name }}</p>
                            </div>

                            <div class="field-anim" style="--field-delay: 60ms">
                                <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                                    <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-amber-100/70 text-amber-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m-1 4h1m4-4h1m-1 4h1m-5 4h6"/></svg>
                                    </span>
                                    <span>{{ t('demo.clinic_name') }} *</span>
                                </label>
                                <input v-model="form.clinic_name" type="text" required autocomplete="organization" class="field-input" :placeholder="t('demo.clinic_name_placeholder')" />
                                <p v-if="form.errors.clinic_name" class="text-rose-600 text-xs mt-1.5">{{ form.errors.clinic_name }}</p>
                            </div>

                            <div class="field-anim" style="--field-delay: 120ms">
                                <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                                    <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-sky-100/70 text-sky-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </span>
                                    <span>{{ t('demo.email') }} *</span>
                                </label>
                                <input v-model="form.email" type="email" required autocomplete="email" class="field-input" :placeholder="t('demo.email_placeholder')" />
                                <p v-if="form.errors.email" class="text-rose-600 text-xs mt-1.5">{{ form.errors.email }}</p>
                            </div>

                            <div class="field-anim" style="--field-delay: 180ms">
                                <label class="flex items-center gap-2 text-sm font-semibold text-[#1C2833] mb-2">
                                    <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-lg ring-1 ring-black/5 bg-emerald-100/70 text-emerald-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </span>
                                    <span>{{ t('demo.phone') }} *</span>
                                </label>
                                <div class="flex gap-2">
                                    <div class="relative">
                                        <select v-model="form.country_code" class="select-input w-32">
                                            <option v-for="cc in countryCodes" :key="cc.code" :value="cc.code">{{ cc.flag }} {{ cc.code }}</option>
                                        </select>
                                        <svg class="absolute end-2 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                    <input v-model="form.phone" type="tel" required autocomplete="tel-national" inputmode="numeric" class="field-input flex-1" placeholder="1XXXXXXXXX" />
                                </div>
                                <p v-if="form.errors.phone" class="text-rose-600 text-xs mt-1.5">{{ form.errors.phone }}</p>
                            </div>
                        </div>

                        <!-- ─── STEP 2 — Facility ─────────────────────── -->
                        <div v-else-if="currentStep === 2" key="step2" class="space-y-5">
                            <div class="text-center mb-1">
                                <h3 class="text-lg font-extrabold text-[#1C2833]">
                                    {{ isAr ? 'عيادتك أو مركزك' : 'Your facility' }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ isAr ? 'عشان نجهز لك العرض المناسب لحجم منشأتك' : 'So we can tailor the demo to your scale' }}
                                </p>
                            </div>

                            <!-- Facility type -->
                            <div class="field-anim" style="--field-delay: 0ms">
                                <label class="block text-sm font-semibold text-[#1C2833] mb-2">
                                    {{ isAr ? 'نوع المنشأة' : 'Facility type' }} *
                                </label>
                                <div class="grid grid-cols-2 gap-2.5">
                                    <button
                                        v-for="f in facilityTypes" :key="f.value"
                                        type="button"
                                        @click="form.facility_type = f.value"
                                        :class="['relative text-start p-3 rounded-2xl ring-1 transition-all duration-300 hover:scale-[1.02] hover:shadow-md',
                                            form.facility_type === f.value
                                                ? `bg-gradient-to-br ${f.tint} ring-2 shadow-md`
                                                : 'bg-white/80 ring-gray-200 hover:ring-gray-300']"
                                    >
                                        <div class="text-2xl mb-1">{{ f.icon }}</div>
                                        <div class="text-sm font-bold text-[#1C2833]">{{ f.label }}</div>
                                        <div class="text-[11px] text-gray-500 leading-tight mt-0.5">{{ f.desc }}</div>
                                        <svg v-if="form.facility_type === f.value" class="absolute top-2 end-2 w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Doctors count -->
                            <div class="field-anim" style="--field-delay: 100ms">
                                <label class="block text-sm font-semibold text-[#1C2833] mb-2">
                                    {{ isAr ? 'عدد الأطباء' : 'Number of doctors' }} *
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="opt in doctorCountOptions" :key="opt"
                                        type="button"
                                        @click="form.doctors_count = opt"
                                        :class="['px-4 py-2 rounded-xl text-sm font-bold ring-1 transition-all duration-200',
                                            form.doctors_count === opt
                                                ? 'bg-[#1B4F72] text-white ring-[#1B4F72] shadow-md'
                                                : 'bg-white/80 text-gray-700 ring-gray-200 hover:ring-[#1B4F72]/50 hover:text-[#1B4F72]']"
                                    >
                                        {{ opt }}
                                    </button>
                                </div>
                            </div>

                            <!-- Specialty -->
                            <div class="field-anim" style="--field-delay: 200ms">
                                <label class="block text-sm font-semibold text-[#1C2833] mb-2">
                                    {{ isAr ? 'التخصص الرئيسي' : 'Main specialty' }} *
                                </label>
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                    <button
                                        v-for="s in specialties" :key="s.value"
                                        type="button"
                                        @click="form.specialty = s.value"
                                        :class="['p-2 rounded-xl text-center ring-1 transition-all duration-200 hover:scale-105',
                                            form.specialty === s.value
                                                ? 'bg-gradient-to-br from-fuchsia-100 to-rose-50 ring-2 ring-fuchsia-300 shadow-sm'
                                                : 'bg-white/80 ring-gray-200 hover:ring-fuchsia-200']"
                                    >
                                        <div class="text-xl">{{ s.emoji }}</div>
                                        <div class="text-[10px] sm:text-[11px] font-semibold text-[#1C2833] mt-0.5 leading-tight">{{ s.label }}</div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- ─── STEP 3 — Needs ────────────────────────── -->
                        <div v-else key="step3" class="space-y-5">
                            <div class="text-center mb-1">
                                <h3 class="text-lg font-extrabold text-[#1C2833]">
                                    {{ isAr ? 'احتياجاتك (اختياري)' : 'Your priorities (optional)' }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ isAr ? 'يساعدنا نوريك الميزات اللي تهمك أول العرض' : 'Helps us show what matters to you first' }}
                                </p>
                            </div>

                            <!-- Modules — multi-select chips -->
                            <div class="field-anim" style="--field-delay: 0ms">
                                <label class="block text-sm font-semibold text-[#1C2833] mb-2">
                                    {{ isAr ? 'الوحدات اللي تهمك' : 'Modules you care about' }}
                                    <span class="text-[11px] font-normal text-gray-400">{{ isAr ? '(اختر أكثر من واحد)' : '(pick any number)' }}</span>
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-2 gap-2">
                                    <button
                                        v-for="m in modules" :key="m.value"
                                        type="button"
                                        @click="toggleModule(m.value)"
                                        :class="['flex items-center gap-2 p-2.5 rounded-xl text-start ring-1 transition-all duration-200',
                                            form.interested_modules.includes(m.value)
                                                ? 'bg-gradient-to-br from-sky-100 to-sky-50 ring-2 ring-sky-300 shadow-sm'
                                                : 'bg-white/80 ring-gray-200 hover:ring-sky-200']"
                                    >
                                        <span class="text-lg">{{ m.icon }}</span>
                                        <span class="text-xs font-semibold text-[#1C2833] flex-1 leading-tight">{{ m.label }}</span>
                                        <svg v-if="form.interested_modules.includes(m.value)" class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Referral source -->
                            <div class="field-anim" style="--field-delay: 200ms">
                                <label class="block text-sm font-semibold text-[#1C2833] mb-2">
                                    {{ isAr ? 'إزاي وصلت إلينا؟' : 'How did you hear about us?' }}
                                </label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button
                                        v-for="r in referralOptions" :key="r.value"
                                        type="button"
                                        @click="form.referral_source = r.value"
                                        :class="['flex items-center gap-2 px-3 py-2 rounded-xl text-start ring-1 transition-all duration-200',
                                            form.referral_source === r.value
                                                ? 'bg-gradient-to-br from-amber-100 to-amber-50 ring-2 ring-amber-300'
                                                : 'bg-white/80 ring-gray-200 hover:ring-amber-200']"
                                    >
                                        <span class="text-base">{{ r.icon }}</span>
                                        <span class="text-xs font-semibold text-[#1C2833] flex-1">{{ r.label }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Transition>

                    <!-- Navigation buttons -->
                    <div class="mt-7 flex gap-3 items-center">
                        <button
                            v-if="currentStep > 1"
                            type="button"
                            @click="prevStep"
                            class="px-4 py-3 rounded-2xl font-bold text-sm text-[#1B4F72] bg-white/80 ring-1 ring-gray-200 hover:bg-white hover:ring-[#1B4F72]/40 transition-all"
                        >
                            <svg class="w-4 h-4 inline-block rtl:rotate-180 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            {{ isAr ? 'رجوع' : 'Back' }}
                        </button>

                        <button
                            v-if="currentStep < totalSteps"
                            type="button"
                            @click="nextStep"
                            :disabled="(currentStep === 1 && !step1Valid) || (currentStep === 2 && !step2Valid)"
                            class="group flex-1 relative inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-bold text-white shadow-lg shadow-[#1B4F72]/20 hover:shadow-2xl hover:shadow-[#1B4F72]/30 hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 overflow-hidden"
                            style="background: linear-gradient(135deg, #1B4F72 0%, #2E6FA1 50%, #C4A265 100%); background-size: 200% 200%; animation: gradientShift 6s ease infinite;"
                        >
                            <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>
                            <span class="relative">{{ isAr ? 'التالي' : 'Next' }}</span>
                            <svg class="relative w-5 h-5 group-hover:translate-x-1 rtl:group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </button>

                        <button
                            v-else
                            type="submit"
                            :disabled="submitting"
                            class="group flex-1 relative inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-2xl font-bold text-white shadow-lg shadow-emerald-200/40 hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-60 overflow-hidden"
                            style="background: linear-gradient(135deg, #059669 0%, #1B4F72 50%, #C4A265 100%); background-size: 200% 200%; animation: gradientShift 6s ease infinite;"
                        >
                            <span class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-700 bg-gradient-to-r from-transparent via-white/30 to-transparent"></span>
                            <template v-if="submitting">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                <span class="relative">{{ t('demo.submitting') }}</span>
                            </template>
                            <template v-else>
                                <svg class="relative w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="relative">{{ isAr ? 'احجز عرضي المجاني' : 'Book my free demo' }}</span>
                            </template>
                        </button>
                    </div>

                    <!-- Reassurance row -->
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
                </form>
            </div>
        </div>
    </section>
</template>

<style scoped>
.field-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    background-color: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
    color: #1C2833;
    outline: none;
    transition: all 0.3s;
}
.field-input::placeholder { color: #9ca3af; }
.field-input:focus {
    border-color: #C4A265;
    box-shadow: 0 0 0 3px rgba(196, 162, 101, 0.2);
    background-color: white;
}
.field-input:hover:not(:focus) { border-color: #d1d5db; }

.select-input {
    padding: 0.75rem 2.25rem 0.75rem 0.75rem;
    border-radius: 0.75rem;
    border: 1px solid #e5e7eb;
    background-color: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(4px);
    color: #1C2833;
    outline: none;
    transition: all 0.3s;
    cursor: pointer;
    font-size: 0.875rem;
    font-variant-numeric: tabular-nums;
    appearance: none;
}
.select-input:focus {
    border-color: #C4A265;
    box-shadow: 0 0 0 3px rgba(196, 162, 101, 0.2);
    background-color: white;
}

/* Staggered field entrance */
.field-anim {
    animation: fieldRise 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    animation-delay: var(--field-delay, 0ms);
}
@keyframes fieldRise {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.animate-blob { animation: blob 18s ease-in-out infinite; }
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
