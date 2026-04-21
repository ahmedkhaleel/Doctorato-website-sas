<script setup>
/**
 * Floating WhatsApp button that picks its phone number based on the
 * visitor's detected country. Falls back to Egypt when the country
 * isn't recognized. The message is pre-filled in the visitor's
 * language so the conversation starts one-tap sooner — no typing,
 * no context-switch, no dropped lead.
 */
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();
const page = usePage();
const isHovered = ref(false);

// Per-country numbers. Keep in sync with CountryLandingController
// ($markets map) and SiteSetting.company_whatsapp fallback.
const numbers = {
    EG: { number: '201012967285', name_ar: 'مصر', name_en: 'Egypt', flag: '🇪🇬' },
    SA: { number: '966555555555', name_ar: 'السعودية', name_en: 'Saudi Arabia', flag: '🇸🇦' },
    AE: { number: '971557961688', name_ar: 'الإمارات', name_en: 'UAE', flag: '🇦🇪' },
    KW: { number: '201012967285', name_ar: 'الكويت', name_en: 'Kuwait', flag: '🇰🇼' },
    QA: { number: '201012967285', name_ar: 'قطر', name_en: 'Qatar', flag: '🇶🇦' },
    BH: { number: '201012967285', name_ar: 'البحرين', name_en: 'Bahrain', flag: '🇧🇭' },
    OM: { number: '201012967285', name_ar: 'عُمان', name_en: 'Oman', flag: '🇴🇲' },
    JO: { number: '201012967285', name_ar: 'الأردن', name_en: 'Jordan', flag: '🇯🇴' },
    IQ: { number: '201012967285', name_ar: 'العراق', name_en: 'Iraq', flag: '🇮🇶' },
    LB: { number: '201012967285', name_ar: 'لبنان', name_en: 'Lebanon', flag: '🇱🇧' },
    MA: { number: '201012967285', name_ar: 'المغرب', name_en: 'Morocco', flag: '🇲🇦' },
    US: { number: '201012967285', name_ar: 'أمريكا', name_en: 'USA', flag: '🇺🇸' },
};

const activeCountry = computed(() => page.props.activeCountry || 'EG');
const entry = computed(() => numbers[activeCountry.value] || numbers.EG);

const message = computed(() => {
    const text = locale.value === 'ar'
        ? 'مرحباً، أريد الاستفسار عن نظام دكتوراتو لإدارة العيادات'
        : 'Hi, I would like to learn more about the Doctorato clinic management system';
    return encodeURIComponent(text);
});

const href = computed(() => `https://wa.me/${entry.value.number}?text=${message.value}`);
const tooltip = computed(() => {
    const name = locale.value === 'ar' ? entry.value.name_ar : entry.value.name_en;
    return locale.value === 'ar'
        ? `واتساب مباشر · ${entry.value.flag} ${name}`
        : `Chat on WhatsApp · ${entry.value.flag} ${name}`;
});
</script>

<template>
    <a
        :href="href"
        target="_blank"
        rel="noopener noreferrer"
        class="fixed bottom-6 start-6 z-50 bg-[#25D366] text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform duration-300 group"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>

        <!-- Country flag badge on the button so users see which number they're calling before they tap -->
        <span class="absolute -top-1 -end-1 w-6 h-6 rounded-full bg-white shadow-md flex items-center justify-center text-sm ring-2 ring-[#25D366]">
            {{ entry.flag }}
        </span>

        <Transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 scale-0 -translate-x-2"
            enter-to-class="opacity-100 scale-100 translate-x-0"
            leave-active-class="transition-all duration-200"
            leave-from-class="opacity-100 scale-100 translate-x-0"
            leave-to-class="opacity-0 scale-0 -translate-x-2"
        >
            <span
                v-if="isHovered"
                class="absolute start-16 bg-white text-dark text-sm px-3 py-1.5 rounded-lg shadow-lg whitespace-nowrap flex items-center gap-1.5"
            >
                {{ tooltip }}
            </span>
        </Transition>
    </a>
</template>
