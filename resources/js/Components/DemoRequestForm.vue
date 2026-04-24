<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useTracking } from '@/composables/useTracking';
import { useRecaptcha } from '@/composables/useRecaptcha';

const { t, locale } = useI18n();
useScrollAnimation();

const showSuccess = ref(false);

const form = useForm({
    clinic_name: '',
    full_name: '',
    email: '',
    country_code: '+20',
    phone: '',
    country: '',
    doctors_count: '',
    specialty: '',
    interested_modules: [],
    referral_source: '',
    notes: '',
    // Bot defenses (honeypot + rendered-at timestamp + reCAPTCHA token).
    hp_trap: '',
    form_rendered_at: Date.now(),
    recaptcha_token: '',
});

const captcha = useRecaptcha();

const countryCodes = [
    { code: '+966', flag: '\u{1F1F8}\u{1F1E6}', label: 'SA' },
    { code: '+971', flag: '\u{1F1E6}\u{1F1EA}', label: 'AE' },
    { code: '+965', flag: '\u{1F1F0}\u{1F1FC}', label: 'KW' },
    { code: '+973', flag: '\u{1F1E7}\u{1F1ED}', label: 'BH' },
    { code: '+968', flag: '\u{1F1F4}\u{1F1F2}', label: 'OM' },
    { code: '+974', flag: '\u{1F1F6}\u{1F1E6}', label: 'QA' },
    { code: '+20', flag: '\u{1F1EA}\u{1F1EC}', label: 'EG' },
    { code: '+962', flag: '\u{1F1EF}\u{1F1F4}', label: 'JO' },
    { code: '+961', flag: '\u{1F1F1}\u{1F1E7}', label: 'LB' },
    { code: '+212', flag: '\u{1F1F2}\u{1F1E6}', label: 'MA' },
    { code: '+216', flag: '\u{1F1F9}\u{1F1F3}', label: 'TN' },
    { code: '+1', flag: '\u{1F1FA}\u{1F1F8}', label: 'US' },
    { code: '+44', flag: '\u{1F1EC}\u{1F1E7}', label: 'GB' },
];

const countryList = [
    { value: 'SA', flag: '\u{1F1F8}\u{1F1E6}', ar: 'المملكة العربية السعودية', en: 'Saudi Arabia' },
    { value: 'AE', flag: '\u{1F1E6}\u{1F1EA}', ar: 'الإمارات العربية المتحدة', en: 'United Arab Emirates' },
    { value: 'EG', flag: '\u{1F1EA}\u{1F1EC}', ar: 'مصر', en: 'Egypt' },
    { value: 'KW', flag: '\u{1F1F0}\u{1F1FC}', ar: 'الكويت', en: 'Kuwait' },
    { value: 'QA', flag: '\u{1F1F6}\u{1F1E6}', ar: 'قطر', en: 'Qatar' },
    { value: 'BH', flag: '\u{1F1E7}\u{1F1ED}', ar: 'البحرين', en: 'Bahrain' },
    { value: 'OM', flag: '\u{1F1F4}\u{1F1F2}', ar: 'عُمان', en: 'Oman' },
    { value: 'JO', flag: '\u{1F1EF}\u{1F1F4}', ar: 'الأردن', en: 'Jordan' },
    { value: 'LB', flag: '\u{1F1F1}\u{1F1E7}', ar: 'لبنان', en: 'Lebanon' },
    { value: 'IQ', flag: '\u{1F1EE}\u{1F1F6}', ar: 'العراق', en: 'Iraq' },
    { value: 'SY', flag: '\u{1F1F8}\u{1F1FE}', ar: 'سوريا', en: 'Syria' },
    { value: 'PS', flag: '\u{1F1F5}\u{1F1F8}', ar: 'فلسطين', en: 'Palestine' },
    { value: 'YE', flag: '\u{1F1FE}\u{1F1EA}', ar: 'اليمن', en: 'Yemen' },
    { value: 'SD', flag: '\u{1F1F8}\u{1F1E9}', ar: 'السودان', en: 'Sudan' },
    { value: 'LY', flag: '\u{1F1F1}\u{1F1FE}', ar: 'ليبيا', en: 'Libya' },
    { value: 'TN', flag: '\u{1F1F9}\u{1F1F3}', ar: 'تونس', en: 'Tunisia' },
    { value: 'DZ', flag: '\u{1F1E9}\u{1F1FF}', ar: 'الجزائر', en: 'Algeria' },
    { value: 'MA', flag: '\u{1F1F2}\u{1F1E6}', ar: 'المغرب', en: 'Morocco' },
    { value: 'MR', flag: '\u{1F1F2}\u{1F1F7}', ar: 'موريتانيا', en: 'Mauritania' },
    { value: 'SO', flag: '\u{1F1F8}\u{1F1F4}', ar: 'الصومال', en: 'Somalia' },
    { value: 'DJ', flag: '\u{1F1E9}\u{1F1EF}', ar: 'جيبوتي', en: 'Djibouti' },
    { value: 'KM', flag: '\u{1F1F0}\u{1F1F2}', ar: 'جزر القمر', en: 'Comoros' },
    { value: 'TR', flag: '\u{1F1F9}\u{1F1F7}', ar: 'تركيا', en: 'Turkey' },
    { value: 'US', flag: '\u{1F1FA}\u{1F1F8}', ar: 'الولايات المتحدة', en: 'United States' },
    { value: 'GB', flag: '\u{1F1EC}\u{1F1E7}', ar: 'المملكة المتحدة', en: 'United Kingdom' },
    { value: 'OTHER', flag: '\u{1F310}', ar: 'دولة أخرى', en: 'Other' },
];

const doctorCountOptions = ['1-5', '6-15', '16-50', '50+'];

// Specialties shown in the pastel searchable combobox. Each one carries
// an emoji + a soft bg/ring tint so the dropdown feels like a palette,
// not a boring <select>. Order puts the common cosmetic/derma picks on
// top (where most new clinics start) followed by the other majors.
const specialties = computed(() => [
    { value: 'derma', label: t('demo.specialty_derma'),       icon: '🧴', tint: 'bg-rose-50 ring-rose-200/60 text-rose-700',      search: 'جلدية تجميل derma cosmetic skin جلد بوتكس فيلر' },
    { value: 'cosmetics', label: t('demo.specialty_cosmetics'), icon: '💆‍♀️', tint: 'bg-pink-50 ring-pink-200/60 text-pink-700',     search: 'تجميل نضارة بوتكس فيلر ليزر cosmetic beauty aesthetic laser' },
    { value: 'dental', label: t('demo.specialty_dental'),     icon: '🦷', tint: 'bg-sky-50 ring-sky-200/60 text-sky-700',         search: 'أسنان dentist dental teeth' },
    { value: 'pediatrics', label: t('demo.specialty_pediatrics'), icon: '👶', tint: 'bg-amber-50 ring-amber-200/60 text-amber-700', search: 'أطفال pediatrics children kids طب الأطفال' },
    { value: 'gyn', label: t('demo.specialty_gyn'),           icon: '🤰', tint: 'bg-fuchsia-50 ring-fuchsia-200/60 text-fuchsia-700', search: 'نساء توليد حمل ولادة gyn obstetrics womens' },
    { value: 'ortho', label: t('demo.specialty_ortho'),       icon: '🦴', tint: 'bg-stone-50 ring-stone-200/60 text-stone-700',   search: 'عظام مفاصل كسور ortho bones joints' },
    { value: 'cardio', label: t('demo.specialty_cardio'),     icon: '❤️', tint: 'bg-red-50 ring-red-200/60 text-red-700',          search: 'قلب قلبية cardio heart' },
    { value: 'ent', label: t('demo.specialty_ent'),           icon: '👂', tint: 'bg-indigo-50 ring-indigo-200/60 text-indigo-700', search: 'أنف أذن حنجرة ent otolaryngology' },
    { value: 'eye', label: t('demo.specialty_eye'),           icon: '👁️', tint: 'bg-cyan-50 ring-cyan-200/60 text-cyan-700',      search: 'عيون رمد eye ophthalmology' },
    { value: 'general', label: t('demo.specialty_general'),   icon: '🩺', tint: 'bg-emerald-50 ring-emerald-200/60 text-emerald-700', search: 'عام family باطنة general medicine internal' },
    { value: 'multi', label: t('demo.specialty_multi'),       icon: '🏥', tint: 'bg-violet-50 ring-violet-200/60 text-violet-700',  search: 'متعدد multi multispecialty clinic مركز' },
    { value: 'other', label: t('demo.specialty_other'),       icon: '✨', tint: 'bg-slate-50 ring-slate-200/60 text-slate-700',    search: 'أخرى other misc' },
]);

// Searchable specialty combobox state.
const specialtyOpen = ref(false);
const specialtySearch = ref('');
const specialtyRef = ref(null);

const filteredSpecialties = computed(() => {
    const q = specialtySearch.value.trim().toLowerCase();
    if (!q) return specialties.value;
    return specialties.value.filter(s =>
        `${s.label} ${s.search || ''}`.toLowerCase().includes(q)
    );
});
const selectedSpecialty = computed(() =>
    specialties.value.find(s => s.value === form.specialty) || null
);
function pickSpecialty(s) {
    form.specialty = s.value;
    specialtyOpen.value = false;
    specialtySearch.value = '';
}
function clearSpecialty() {
    form.specialty = '';
    specialtySearch.value = '';
}
function handleSpecialtyOutside(e) {
    if (specialtyRef.value && !specialtyRef.value.contains(e.target)) {
        specialtyOpen.value = false;
    }
}
onMounted(() => document.addEventListener('click', handleSpecialtyOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleSpecialtyOutside));

const moduleOptions = computed(() => [
    { value: 'derma', label: t('demo.module_derma') },
    { value: 'dental', label: t('demo.module_dental') },
    { value: 'pediatrics', label: t('demo.module_pediatrics') },
    { value: 'telemedicine', label: t('demo.module_telemedicine') },
    { value: 'crm', label: t('demo.module_crm') },
    { value: 'hr', label: t('demo.module_hr') },
    { value: 'inventory', label: t('demo.module_inventory') },
    { value: 'insurance', label: t('demo.module_insurance') },
]);

const referralSources = computed(() => [
    { value: 'google', label: t('demo.referral_google') },
    { value: 'social_media', label: t('demo.referral_social') },
    { value: 'friend', label: t('demo.referral_friend') },
    { value: 'ad', label: t('demo.referral_ad') },
    { value: 'other', label: t('demo.referral_other') },
]);

const benefits = computed(() => [
    { icon: 'trial', text: t('demo.benefit_trial') },
    { icon: 'card', text: t('demo.benefit_no_card') },
    { icon: 'clock', text: t('demo.benefit_setup') },
    { icon: 'support', text: t('demo.benefit_support') },
    { icon: 'secure', text: t('demo.benefit_secure') },
    { icon: 'cancel', text: t('demo.benefit_cancel') },
]);

const track = useTracking();

async function submitForm() {
    form.recaptcha_token = (await captcha.execute('demo_request')) || '';
    form.post(route('demo.store'), {
        onSuccess: () => {
            track.lead({ form: 'demo_request', clinic: form.clinic_name });
            showSuccess.value = true;
            form.reset();
            form.form_rendered_at = Date.now();
        },
    });
}

function toggleModule(moduleValue) {
    const idx = form.interested_modules.indexOf(moduleValue);
    if (idx > -1) {
        form.interested_modules.splice(idx, 1);
    } else {
        form.interested_modules.push(moduleValue);
    }
}
</script>

<template>
    <section id="demo" class="py-12 sm:py-16 lg:py-20 bg-white scroll-mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-12 animate-fade-up">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                    {{ locale === 'ar' ? 'املأ الفورم' : 'Fill the form' }}
                </p>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-3 leading-tight">
                    {{ $t('demo.title') }}
                </h2>
                <p class="text-sm sm:text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('demo.subtitle') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 lg:gap-10 items-start">
                <!-- Form Side -->
                <div class="lg:col-span-3 bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-7 lg:p-8 shadow-xl border border-gray-100 animate-fade-up">
                    <!-- Success Message -->
                    <Transition
                        enter-active-class="transition duration-500 ease-out"
                        enter-from-class="opacity-0 scale-90"
                        enter-to-class="opacity-100 scale-100"
                    >
                        <div
                            v-if="showSuccess"
                            class="text-center py-12"
                        >
                            <div class="w-20 h-20 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
                                <svg class="w-10 h-10 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-dark mb-2">{{ $t('demo.success_title') }}</h3>
                            <p class="text-gray">{{ $t('demo.success_message') }}</p>
                        </div>
                    </Transition>

                    <form v-if="!showSuccess" @submit.prevent="submitForm" class="space-y-5">
                        <!-- Honeypot: hidden from real users, bots fill it. -->
                        <div class="absolute opacity-0 pointer-events-none -z-10" aria-hidden="true">
                            <label>Website <input v-model="form.hp_trap" type="text" tabindex="-1" autocomplete="off" /></label>
                        </div>

                        <!-- Clinic Name -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.clinic_name') }} *</label>
                            <input
                                v-model="form.clinic_name"
                                type="text"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                :placeholder="$t('demo.clinic_name_placeholder')"
                            />
                            <p v-if="form.errors.clinic_name" class="text-danger text-xs mt-1">{{ form.errors.clinic_name }}</p>
                        </div>

                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.full_name') }} *</label>
                            <input
                                v-model="form.full_name"
                                type="text"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                :placeholder="$t('demo.full_name_placeholder')"
                            />
                            <p v-if="form.errors.full_name" class="text-danger text-xs mt-1">{{ form.errors.full_name }}</p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.email') }} *</label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                :placeholder="$t('demo.email_placeholder')"
                            />
                            <p v-if="form.errors.email" class="text-danger text-xs mt-1">{{ form.errors.email }}</p>
                        </div>

                        <!-- Phone with Country Code -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.phone') }} *</label>
                            <div class="flex gap-2">
                                <select
                                    v-model="form.country_code"
                                    class="w-32 px-3 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all text-sm"
                                >
                                    <option
                                        v-for="cc in countryCodes"
                                        :key="cc.code"
                                        :value="cc.code"
                                    >
                                        {{ cc.flag }} {{ cc.code }}
                                    </option>
                                </select>
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    required
                                    class="flex-1 px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                    :placeholder="$t('demo.phone_placeholder')"
                                />
                            </div>
                            <p v-if="form.errors.phone" class="text-danger text-xs mt-1">{{ form.errors.phone }}</p>
                        </div>

                        <!-- Country -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.country') }}</label>
                            <div class="relative">
                                <select
                                    v-model="form.country"
                                    class="w-full appearance-none px-4 py-3 pe-10 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all cursor-pointer"
                                >
                                    <option value="" disabled>{{ $t('demo.country_placeholder') }}</option>
                                    <option
                                        v-for="c in countryList"
                                        :key="c.value"
                                        :value="c.value"
                                    >
                                        {{ c.flag }}  {{ locale === 'ar' ? c.ar : c.en }}
                                    </option>
                                </select>
                                <svg class="absolute end-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <p v-if="form.errors.country" class="text-danger text-xs mt-1">{{ form.errors.country }}</p>
                        </div>

                        <!-- Doctors Count (Radio) -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">{{ $t('demo.doctors_count') }} *</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label
                                    v-for="option in doctorCountOptions"
                                    :key="option"
                                    class="flex items-center justify-center px-4 py-2.5 rounded-xl border-2 cursor-pointer transition-all text-sm font-medium"
                                    :class="form.doctors_count === option
                                        ? 'border-secondary bg-secondary/10 text-secondary'
                                        : 'border-gray-light/50 text-gray hover:border-secondary/50'"
                                >
                                    <input
                                        type="radio"
                                        :value="option"
                                        v-model="form.doctors_count"
                                        class="sr-only"
                                    />
                                    {{ option }}
                                </label>
                            </div>
                            <p v-if="form.errors.doctors_count" class="text-danger text-xs mt-1">{{ form.errors.doctors_count }}</p>
                        </div>

                        <!-- Specialty (searchable pastel combobox) -->
                        <div ref="specialtyRef" class="relative">
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.specialty') }}</label>

                            <!-- Trigger / search input -->
                            <button
                                type="button"
                                @click="specialtyOpen = !specialtyOpen"
                                class="w-full flex items-center gap-2 px-3 py-3 rounded-xl border bg-white transition-all text-start"
                                :class="specialtyOpen
                                    ? 'border-secondary ring-2 ring-secondary/20'
                                    : 'border-gray-light/50 hover:border-secondary/50'"
                            >
                                <template v-if="selectedSpecialty">
                                    <span
                                        class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center ring-1 text-base"
                                        :class="selectedSpecialty.tint"
                                    >{{ selectedSpecialty.icon }}</span>
                                    <span class="flex-1 text-sm font-medium text-dark truncate">{{ selectedSpecialty.label }}</span>
                                    <button
                                        type="button"
                                        @click.stop="clearSpecialty"
                                        class="shrink-0 p-1 rounded-full text-gray hover:bg-gray-100 hover:text-dark transition-colors"
                                        :aria-label="locale === 'ar' ? 'مسح' : 'Clear'"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </template>
                                <template v-else>
                                    <span class="shrink-0 w-8 h-8 rounded-lg bg-light-gold/40 ring-1 ring-secondary/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <span class="flex-1 text-sm text-gray">{{ $t('demo.select_specialty') }}</span>
                                </template>
                                <svg
                                    class="shrink-0 w-4 h-4 text-gray transition-transform"
                                    :class="{ 'rotate-180': specialtyOpen }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown panel -->
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="opacity-0 -translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-150 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <div
                                    v-if="specialtyOpen"
                                    class="absolute z-30 top-full inset-x-0 mt-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
                                >
                                    <!-- Search bar -->
                                    <div class="p-3 border-b border-gray-100 bg-gradient-to-r from-light-blue/40 to-light-gold/30">
                                        <div class="relative">
                                            <svg class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                            <input
                                                v-model="specialtySearch"
                                                type="text"
                                                autofocus
                                                class="w-full ps-9 pe-3 py-2 rounded-xl text-sm bg-white border border-gray-light/50 focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none"
                                                :placeholder="locale === 'ar' ? 'ابحث عن تخصص...' : 'Search specialty...'"
                                                @keydown.esc="specialtyOpen = false"
                                            />
                                        </div>
                                    </div>

                                    <!-- Options -->
                                    <div class="max-h-72 overflow-y-auto p-2">
                                        <button
                                            v-for="s in filteredSpecialties"
                                            :key="s.value"
                                            type="button"
                                            @click="pickSpecialty(s)"
                                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-start transition-all"
                                            :class="form.specialty === s.value
                                                ? `${s.tint} ring-1`
                                                : 'hover:bg-gray-50'"
                                        >
                                            <span
                                                class="shrink-0 w-9 h-9 rounded-lg flex items-center justify-center ring-1 text-lg"
                                                :class="s.tint"
                                            >{{ s.icon }}</span>
                                            <span class="flex-1 text-sm font-medium text-dark">{{ s.label }}</span>
                                            <svg
                                                v-if="form.specialty === s.value"
                                                class="w-4 h-4 text-secondary shrink-0"
                                                fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </button>
                                        <div v-if="!filteredSpecialties.length" class="py-6 text-center text-sm text-gray">
                                            {{ locale === 'ar' ? 'لا توجد نتائج' : 'No matches' }}
                                        </div>
                                    </div>
                                </div>
                            </Transition>

                            <p v-if="form.errors.specialty" class="text-danger text-xs mt-1">{{ form.errors.specialty }}</p>
                        </div>

                        <!-- Interested Modules (Checkboxes) -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">{{ $t('demo.interested_modules') }}</label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                <label
                                    v-for="mod in moduleOptions"
                                    :key="mod.value"
                                    class="flex items-center gap-2 px-3 py-2.5 rounded-xl border-2 cursor-pointer transition-all text-sm"
                                    :class="form.interested_modules.includes(mod.value)
                                        ? 'border-secondary bg-secondary/10 text-secondary'
                                        : 'border-gray-light/50 text-gray hover:border-secondary/50'"
                                >
                                    <input
                                        type="checkbox"
                                        :value="mod.value"
                                        :checked="form.interested_modules.includes(mod.value)"
                                        @change="toggleModule(mod.value)"
                                        class="sr-only"
                                    />
                                    <svg
                                        class="w-4 h-4 shrink-0"
                                        :class="form.interested_modules.includes(mod.value) ? 'text-secondary' : 'text-gray-light'"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    >
                                        <path
                                            v-if="form.interested_modules.includes(mod.value)"
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                        <path
                                            v-else
                                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 2a10 10 0 100 20 10 10 0 000-20z"
                                        />
                                    </svg>
                                    <span>{{ mod.label }}</span>
                                </label>
                            </div>
                            <p v-if="form.errors.interested_modules" class="text-danger text-xs mt-1">{{ form.errors.interested_modules }}</p>
                        </div>

                        <!-- Referral Source -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.referral_source') }}</label>
                            <select
                                v-model="form.referral_source"
                                class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                            >
                                <option value="">{{ $t('demo.select_referral') }}</option>
                                <option v-for="r in referralSources" :key="r.value" :value="r.value">{{ r.label }}</option>
                            </select>
                            <p v-if="form.errors.referral_source" class="text-danger text-xs mt-1">{{ form.errors.referral_source }}</p>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.notes') }}</label>
                            <textarea
                                v-model="form.notes"
                                rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all resize-none"
                                :placeholder="$t('demo.notes_placeholder')"
                            ></textarea>
                            <p v-if="form.errors.notes" class="text-danger text-xs mt-1">{{ form.errors.notes }}</p>
                        </div>

                        <!-- Submit -->
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-4 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                        >
                            <svg
                                v-if="form.processing"
                                class="w-5 h-5 animate-spin"
                                fill="none" viewBox="0 0 24 24"
                            >
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            <span>{{ form.processing ? $t('demo.submitting') : $t('demo.submit') }}</span>
                        </button>
                    </form>
                </div>

                <!-- Benefits Side — sits next to the form on desktop, hidden
                     on tablet/mobile so the form has room to breathe. -->
                <aside class="hidden lg:block lg:col-span-2 animate-fade-up">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <h3 class="text-xl font-bold text-[#1C2833]">{{ $t('demo.why_try') }}</h3>
                        <ul class="space-y-3.5">
                            <li
                                v-for="(benefit, idx) in benefits"
                                :key="idx"
                                class="flex items-start gap-3"
                            >
                                <span class="shrink-0 w-7 h-7 rounded-full bg-emerald-50 ring-1 ring-emerald-200 flex items-center justify-center mt-0.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <p class="text-sm text-gray-700 leading-relaxed pt-1">{{ benefit.text }}</p>
                            </li>
                        </ul>

                        <!-- Trust card -->
                        <div class="rounded-2xl p-5 bg-gradient-to-br from-light-gold/60 to-[#C4A265]/15 border border-[#C4A265]/20">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <h4 class="text-sm font-bold text-[#1C2833]">{{ $t('demo.trust_title') }}</h4>
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed">{{ $t('demo.trust_description') }}</p>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</template>
