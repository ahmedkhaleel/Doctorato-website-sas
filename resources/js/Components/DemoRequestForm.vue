<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useTracking } from '@/composables/useTracking';

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
});

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

const specialties = computed(() => [
    { value: 'derma', label: t('demo.specialty_derma') },
    { value: 'dental', label: t('demo.specialty_dental') },
    { value: 'pediatrics', label: t('demo.specialty_pediatrics') },
    { value: 'general', label: t('demo.specialty_general') },
    { value: 'multi', label: t('demo.specialty_multi') },
    { value: 'other', label: t('demo.specialty_other') },
]);

const moduleOptions = computed(() => [
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

function submitForm() {
    form.post(route('demo.store'), {
        onSuccess: () => {
            track.lead({ form: 'demo_request', clinic: form.clinic_name });
            showSuccess.value = true;
            form.reset();
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
    <section id="demo" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 animate-fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    {{ $t('demo.title') }}
                </h2>
                <p class="text-lg text-gray max-w-2xl mx-auto">
                    {{ $t('demo.subtitle') }}
                </p>
                <div class="w-20 h-1 bg-secondary mx-auto mt-6 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
                <!-- Form Side -->
                <div class="bg-light-blue rounded-2xl p-6 sm:p-8 shadow-md animate-fade-up">
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

                        <!-- Specialty -->
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1.5">{{ $t('demo.specialty') }}</label>
                            <select
                                v-model="form.specialty"
                                class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                            >
                                <option value="">{{ $t('demo.select_specialty') }}</option>
                                <option v-for="s in specialties" :key="s.value" :value="s.value">{{ s.label }}</option>
                            </select>
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

                <!-- Benefits Side -->
                <div class="animate-fade-up">
                    <h3 class="text-2xl font-bold text-dark mb-8">{{ $t('demo.why_try') }}</h3>
                    <div class="space-y-6">
                        <div
                            v-for="(benefit, idx) in benefits"
                            :key="idx"
                            class="flex items-start gap-4"
                        >
                            <div class="w-10 h-10 rounded-full bg-success/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-dark font-medium">{{ benefit.text }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trust indicators -->
                    <div class="mt-12 p-6 bg-light-gold rounded-2xl">
                        <div class="flex items-center gap-3 mb-3">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <h4 class="text-lg font-bold text-dark">{{ $t('demo.trust_title') }}</h4>
                        </div>
                        <p class="text-sm text-gray leading-relaxed">{{ $t('demo.trust_description') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
