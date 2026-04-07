<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

const { t } = useI18n();
useScrollAnimation();

const showSuccess = ref(false);
const mounted = ref(false);
const countryDropdownOpen = ref(false);
const countrySearch = ref('');

onMounted(() => {
    setTimeout(() => (mounted.value = true), 50);
});

// Country codes (most relevant for the region first, then global)
const countries = [
    { code: 'EG', dial: '+20',  flag: '🇪🇬', name_ar: 'مصر',           name_en: 'Egypt' },
    { code: 'AE', dial: '+971', flag: '🇦🇪', name_ar: 'الإمارات',        name_en: 'United Arab Emirates' },
    { code: 'SA', dial: '+966', flag: '🇸🇦', name_ar: 'السعودية',        name_en: 'Saudi Arabia' },
    { code: 'KW', dial: '+965', flag: '🇰🇼', name_ar: 'الكويت',          name_en: 'Kuwait' },
    { code: 'QA', dial: '+974', flag: '🇶🇦', name_ar: 'قطر',            name_en: 'Qatar' },
    { code: 'BH', dial: '+973', flag: '🇧🇭', name_ar: 'البحرين',         name_en: 'Bahrain' },
    { code: 'OM', dial: '+968', flag: '🇴🇲', name_ar: 'عُمان',           name_en: 'Oman' },
    { code: 'JO', dial: '+962', flag: '🇯🇴', name_ar: 'الأردن',          name_en: 'Jordan' },
    { code: 'LB', dial: '+961', flag: '🇱🇧', name_ar: 'لبنان',           name_en: 'Lebanon' },
    { code: 'PS', dial: '+970', flag: '🇵🇸', name_ar: 'فلسطين',          name_en: 'Palestine' },
    { code: 'SY', dial: '+963', flag: '🇸🇾', name_ar: 'سوريا',           name_en: 'Syria' },
    { code: 'IQ', dial: '+964', flag: '🇮🇶', name_ar: 'العراق',          name_en: 'Iraq' },
    { code: 'YE', dial: '+967', flag: '🇾🇪', name_ar: 'اليمن',           name_en: 'Yemen' },
    { code: 'LY', dial: '+218', flag: '🇱🇾', name_ar: 'ليبيا',           name_en: 'Libya' },
    { code: 'TN', dial: '+216', flag: '🇹🇳', name_ar: 'تونس',            name_en: 'Tunisia' },
    { code: 'DZ', dial: '+213', flag: '🇩🇿', name_ar: 'الجزائر',         name_en: 'Algeria' },
    { code: 'MA', dial: '+212', flag: '🇲🇦', name_ar: 'المغرب',          name_en: 'Morocco' },
    { code: 'SD', dial: '+249', flag: '🇸🇩', name_ar: 'السودان',         name_en: 'Sudan' },
    { code: 'TR', dial: '+90',  flag: '🇹🇷', name_ar: 'تركيا',           name_en: 'Turkey' },
    { code: 'US', dial: '+1',   flag: '🇺🇸', name_ar: 'الولايات المتحدة', name_en: 'United States' },
    { code: 'GB', dial: '+44',  flag: '🇬🇧', name_ar: 'المملكة المتحدة',  name_en: 'United Kingdom' },
    { code: 'DE', dial: '+49',  flag: '🇩🇪', name_ar: 'ألمانيا',          name_en: 'Germany' },
    { code: 'FR', dial: '+33',  flag: '🇫🇷', name_ar: 'فرنسا',           name_en: 'France' },
    { code: 'IT', dial: '+39',  flag: '🇮🇹', name_ar: 'إيطاليا',         name_en: 'Italy' },
    { code: 'ES', dial: '+34',  flag: '🇪🇸', name_ar: 'إسبانيا',         name_en: 'Spain' },
    { code: 'CA', dial: '+1',   flag: '🇨🇦', name_ar: 'كندا',            name_en: 'Canada' },
    { code: 'IN', dial: '+91',  flag: '🇮🇳', name_ar: 'الهند',           name_en: 'India' },
    { code: 'PK', dial: '+92',  flag: '🇵🇰', name_ar: 'باكستان',         name_en: 'Pakistan' },
];

const selectedCountry = ref(countries[0]);

const filteredCountries = computed(() => {
    if (!countrySearch.value) return countries;
    const q = countrySearch.value.toLowerCase();
    return countries.filter(c =>
        c.name_ar.toLowerCase().includes(q) ||
        c.name_en.toLowerCase().includes(q) ||
        c.dial.includes(q) ||
        c.code.toLowerCase().includes(q)
    );
});

function selectCountry(c) {
    selectedCountry.value = c;
    countryDropdownOpen.value = false;
    countrySearch.value = '';
}

function closeCountryDropdown(e) {
    if (!e.target.closest('.country-selector')) {
        countryDropdownOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', closeCountryDropdown);
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    country_code: '+20',
    subject: '',
    message: '',
});

const subjects = computed(() => [
    { value: 'general', label: t('contact.subject_general') },
    { value: 'sales', label: t('contact.subject_sales') },
    { value: 'support', label: t('contact.subject_support') },
    { value: 'partnership', label: t('contact.subject_partnership') },
    { value: 'other', label: t('contact.subject_other') },
]);

// Contact channels
const phones = [
    {
        country: 'مصر',
        countryEn: 'Egypt',
        flag: '🇪🇬',
        number: '+20 101 296 7285',
        raw: '+201012967285',
        whatsapp: 'https://wa.me/201012967285',
    },
    {
        country: 'الإمارات',
        countryEn: 'UAE',
        flag: '🇦🇪',
        number: '+971 55 796 1688',
        raw: '+971557961688',
        whatsapp: 'https://wa.me/971557961688',
    },
];

const email = 'info@markeza-group.com';

const socialLinks = [
    { name: 'WhatsApp', color: '#25D366', href: 'https://wa.me/201012967285', path: 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z' },
    { name: 'LinkedIn', color: '#0077B5', href: '#', path: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' },
    { name: 'Instagram', color: '#E1306C', href: '#', path: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z' },
    { name: 'Twitter', color: '#1DA1F2', href: '#', path: 'M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z' },
];

function submitForm() {
    form.country_code = selectedCountry.value.dial;
    form.post(route('contact.store'), {
        onSuccess: () => {
            showSuccess.value = true;
            form.reset();
            selectedCountry.value = countries[0];
        },
    });
}

// Floating particles for hero
const particles = Array.from({ length: 20 }, (_, i) => ({
    id: i,
    x: Math.random() * 100,
    y: Math.random() * 100,
    size: Math.random() * 3 + 1,
    duration: Math.random() * 10 + 10,
    delay: Math.random() * 5,
}));
</script>

<template>
    <Head :title="t('contact.page_title')" />
    <MainLayout>
        <!-- ===== HERO SECTION ===== -->
        <section class="relative min-h-[70vh] flex items-center overflow-hidden bg-[#0D2B45]">
            <!-- Animated Gradient Mesh -->
            <div class="absolute inset-0">
                <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-gradient-to-br from-[#1B4F72] to-[#2E86C1] rounded-full blur-3xl opacity-40 animate-blob"></div>
                <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-gradient-to-br from-[#C4A265] to-[#D4B87A] rounded-full blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-0 left-1/3 w-[450px] h-[450px] bg-gradient-to-br from-[#2E86C1] to-[#1B4F72] rounded-full blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
            </div>

            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px); background-size: 50px 50px;"></div>

            <!-- Floating Particles -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div
                    v-for="p in particles"
                    :key="p.id"
                    class="absolute rounded-full bg-white/30 animate-float-particle"
                    :style="{
                        left: `${p.x}%`,
                        top: `${p.y}%`,
                        width: `${p.size}px`,
                        height: `${p.size}px`,
                        animationDuration: `${p.duration}s`,
                        animationDelay: `${p.delay}s`,
                    }"
                ></div>
            </div>

            <div class="container mx-auto px-4 relative z-10 py-20">
                <div class="max-w-3xl mx-auto text-center">
                    <!-- Badge -->
                    <div
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-4'"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 mb-6 transition-all duration-700"
                    >
                        <span class="relative flex w-2 h-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span class="text-white/90 text-xs font-medium">متاحون الآن للرد على استفساراتك</span>
                    </div>

                    <h1
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 transition-all duration-700 delay-100 leading-tight"
                    >
                        لنبدأ
                        <span class="relative inline-block">
                            <span class="relative z-10 bg-gradient-to-r from-[#C4A265] via-[#D4B87A] to-[#C4A265] bg-clip-text text-transparent">محادثة</span>
                            <svg class="absolute -bottom-2 left-0 w-full" height="8" viewBox="0 0 200 8" preserveAspectRatio="none">
                                <path d="M0,4 Q50,0 100,4 T200,4" stroke="#C4A265" stroke-width="2" fill="none" stroke-linecap="round" />
                            </svg>
                        </span>
                        <br />
                        تستحق وقتك
                    </h1>

                    <p
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="text-lg md:text-xl text-white/70 max-w-2xl mx-auto transition-all duration-700 delay-200"
                    >
                        فريقنا جاهز لمساعدتك في كل خطوة. اختر الطريقة الأنسب لك للتواصل معنا
                    </p>

                    <!-- Quick Action Pills -->
                    <div
                        :class="mounted ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-6'"
                        class="flex flex-wrap items-center justify-center gap-3 mt-10 transition-all duration-700 delay-300"
                    >
                        <a href="#contact-form" class="group relative overflow-hidden px-6 py-3 rounded-full bg-[#C4A265] text-white font-semibold text-sm transition-all hover:scale-105 hover:shadow-2xl hover:shadow-[#C4A265]/50">
                            <span class="relative z-10 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                إرسال رسالة
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-[#D4B87A] to-[#C4A265] translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                        </a>
                        <a :href="phones[0].whatsapp" target="_blank" class="group px-6 py-3 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white font-semibold text-sm transition-all hover:bg-white/20 hover:scale-105 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347" /></svg>
                            واتساب فوري
                        </a>
                    </div>
                </div>
            </div>

        </section>

        <!-- ===== QUICK CONTACT CARDS ===== -->
        <section class="relative -mt-8 z-20 pb-16">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Phone Egypt -->
                    <a
                        :href="`tel:${phones[0].raw}`"
                        class="group relative bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-[#1B4F72]/20 transition-all duration-500 hover:-translate-y-2 overflow-hidden border border-gray-100 reveal"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-[#1B4F72]/0 via-[#1B4F72]/0 to-[#1B4F72]/0 group-hover:from-[#1B4F72]/5 group-hover:to-[#2E86C1]/5 transition-all duration-500"></div>
                        <div class="absolute -top-10 -end-10 w-32 h-32 bg-[#1B4F72]/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#1B4F72] to-[#2E86C1] flex items-center justify-center group-hover:rotate-12 group-hover:scale-110 transition-all duration-500 shadow-lg shadow-[#1B4F72]/30">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <span class="text-3xl">{{ phones[0].flag }}</span>
                            </div>
                            <p class="text-xs uppercase tracking-wider text-gray-400 mb-1 font-semibold">{{ phones[0].country }} · Egypt</p>
                            <p class="text-lg font-bold text-[#1C2833] mb-3" dir="ltr">{{ phones[0].number }}</p>
                            <div class="flex items-center gap-2 text-xs text-[#1B4F72] font-semibold">
                                <span>اتصل الآن</span>
                                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </div>
                        </div>
                    </a>

                    <!-- Phone UAE -->
                    <a
                        :href="`tel:${phones[1].raw}`"
                        class="group relative bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-[#C4A265]/20 transition-all duration-500 hover:-translate-y-2 overflow-hidden border border-gray-100 reveal"
                        style="animation-delay: 100ms"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-[#C4A265]/0 to-[#D4B87A]/0 group-hover:from-[#C4A265]/5 group-hover:to-[#D4B87A]/5 transition-all duration-500"></div>
                        <div class="absolute -top-10 -end-10 w-32 h-32 bg-[#C4A265]/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B87A] flex items-center justify-center group-hover:rotate-12 group-hover:scale-110 transition-all duration-500 shadow-lg shadow-[#C4A265]/30">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                </div>
                                <span class="text-3xl">{{ phones[1].flag }}</span>
                            </div>
                            <p class="text-xs uppercase tracking-wider text-gray-400 mb-1 font-semibold">{{ phones[1].country }} · UAE</p>
                            <p class="text-lg font-bold text-[#1C2833] mb-3" dir="ltr">{{ phones[1].number }}</p>
                            <div class="flex items-center gap-2 text-xs text-[#C4A265] font-semibold">
                                <span>اتصل الآن</span>
                                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </div>
                        </div>
                    </a>

                    <!-- Email -->
                    <a
                        :href="`mailto:${email}`"
                        class="group relative bg-white rounded-3xl p-6 shadow-xl shadow-gray-200/50 hover:shadow-2xl hover:shadow-emerald-500/20 transition-all duration-500 hover:-translate-y-2 overflow-hidden border border-gray-100 reveal"
                        style="animation-delay: 200ms"
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-teal-500/0 group-hover:from-emerald-500/5 group-hover:to-teal-500/5 transition-all duration-500"></div>
                        <div class="absolute -top-10 -end-10 w-32 h-32 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center group-hover:rotate-12 group-hover:scale-110 transition-all duration-500 shadow-lg shadow-emerald-500/30">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <span class="text-2xl">✉️</span>
                            </div>
                            <p class="text-xs uppercase tracking-wider text-gray-400 mb-1 font-semibold">البريد الإلكتروني</p>
                            <p class="text-sm md:text-base font-bold text-[#1C2833] mb-3 break-all" dir="ltr">{{ email }}</p>
                            <div class="flex items-center gap-2 text-xs text-emerald-600 font-semibold">
                                <span>راسلنا</span>
                                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== FORM + SIDEBAR ===== -->
        <section id="contact-form" class="py-16 bg-gradient-to-b from-white to-gray-50">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-5 gap-8">
                    <!-- Contact Form -->
                    <div class="lg:col-span-3 reveal">
                        <Transition
                            enter-active-class="transition duration-700 ease-out"
                            enter-from-class="opacity-0 scale-90 -translate-y-4"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition duration-300 ease-in"
                            leave-from-class="opacity-100 scale-100"
                            leave-to-class="opacity-0 scale-90"
                            mode="out-in"
                        >
                            <div
                                v-if="showSuccess"
                                key="success"
                                class="relative bg-white rounded-3xl p-10 shadow-xl border border-gray-100 text-center overflow-hidden"
                            >
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50"></div>
                                <div class="relative">
                                    <div class="w-24 h-24 mx-auto mb-6 relative">
                                        <div class="absolute inset-0 bg-emerald-200 rounded-full animate-ping"></div>
                                        <div class="relative w-24 h-24 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                            <svg class="w-12 h-12 text-white animate-check-draw" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h3 class="text-2xl md:text-3xl font-bold text-[#1C2833] mb-3">{{ t('contact.success_title') }}</h3>
                                    <p class="text-gray-600 mb-8">{{ t('contact.success_message') }}</p>
                                    <button
                                        @click="showSuccess = false"
                                        class="px-8 py-3 bg-[#1B4F72] hover:bg-[#0D2B45] text-white rounded-full font-semibold transition-all hover:scale-105 hover:shadow-lg"
                                    >
                                        {{ t('contact.send_another') }}
                                    </button>
                                </div>
                            </div>

                            <form
                                v-else
                                key="form"
                                @submit.prevent="submitForm"
                                class="relative bg-white rounded-3xl p-6 sm:p-10 shadow-xl border border-gray-100 overflow-hidden"
                            >
                                <!-- Decorative gradient -->
                                <div class="absolute -top-20 -end-20 w-64 h-64 bg-gradient-to-br from-[#1B4F72]/5 to-[#C4A265]/5 rounded-full blur-3xl"></div>

                                <div class="relative">
                                    <div class="mb-8">
                                        <span class="inline-block px-3 py-1 rounded-full bg-[#1B4F72]/10 text-[#1B4F72] text-xs font-bold mb-3">📨 نموذج التواصل</span>
                                        <h2 class="text-2xl md:text-3xl font-bold text-[#1C2833] mb-2">{{ t('contact.form_title') }}</h2>
                                        <p class="text-gray-500 text-sm">سنرد عليك خلال ساعات عمل قليلة</p>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                        <!-- Name -->
                                        <div class="floating-field">
                                            <input
                                                v-model="form.name"
                                                type="text"
                                                id="name"
                                                required
                                                placeholder=" "
                                                class="peer w-full px-4 pt-5 pb-2 rounded-2xl border-2 border-gray-200 bg-gray-50/50 focus:border-[#1B4F72] focus:bg-white outline-none transition-all"
                                            />
                                            <label for="name" class="absolute start-4 top-4 text-gray-400 text-sm pointer-events-none transition-all peer-focus:top-1.5 peer-focus:text-xs peer-focus:text-[#1B4F72] peer-[:not(:placeholder-shown)]:top-1.5 peer-[:not(:placeholder-shown)]:text-xs">
                                                {{ t('contact.name') }} *
                                            </label>
                                            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1.5 ms-2">{{ form.errors.name }}</p>
                                        </div>

                                        <!-- Email -->
                                        <div class="floating-field">
                                            <input
                                                v-model="form.email"
                                                type="email"
                                                id="email"
                                                required
                                                placeholder=" "
                                                class="peer w-full px-4 pt-5 pb-2 rounded-2xl border-2 border-gray-200 bg-gray-50/50 focus:border-[#1B4F72] focus:bg-white outline-none transition-all"
                                                dir="ltr"
                                            />
                                            <label for="email" class="absolute start-4 top-4 text-gray-400 text-sm pointer-events-none transition-all peer-focus:top-1.5 peer-focus:text-xs peer-focus:text-[#1B4F72] peer-[:not(:placeholder-shown)]:top-1.5 peer-[:not(:placeholder-shown)]:text-xs">
                                                {{ t('contact.email') }} *
                                            </label>
                                            <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5 ms-2">{{ form.errors.email }}</p>
                                        </div>

                                        <!-- Phone with Country Code -->
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1.5 ms-1">
                                                {{ t('contact.phone') }}
                                            </label>
                                            <div class="phone-wrapper group flex items-center h-[52px] rounded-2xl border-2 border-gray-200 bg-gray-50/50 focus-within:border-[#1B4F72] focus-within:bg-white focus-within:shadow-sm transition-all relative">
                                                <!-- Country Selector Button -->
                                                <div class="country-selector relative h-full">
                                                    <button
                                                        type="button"
                                                        @click.stop="countryDropdownOpen = !countryDropdownOpen"
                                                        class="h-full flex items-center gap-2 ps-4 pe-3 hover:bg-gray-100/70 rounded-s-[14px] transition-colors"
                                                    >
                                                        <span class="text-2xl leading-none">{{ selectedCountry.flag }}</span>
                                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180 text-[#1B4F72]': countryDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>
                                                    </button>

                                                    <!-- Dropdown -->
                                                    <Transition
                                                        enter-active-class="transition duration-200 ease-out"
                                                        enter-from-class="opacity-0 -translate-y-2 scale-95"
                                                        enter-to-class="opacity-100 translate-y-0 scale-100"
                                                        leave-active-class="transition duration-150 ease-in"
                                                        leave-from-class="opacity-100 scale-100"
                                                        leave-to-class="opacity-0 scale-95"
                                                    >
                                                        <div
                                                            v-if="countryDropdownOpen"
                                                            class="absolute top-full mt-2 start-0 w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden origin-top"
                                                            @click.stop
                                                        >
                                                            <!-- Search -->
                                                            <div class="p-3 border-b border-gray-100 bg-gray-50/50">
                                                                <div class="relative">
                                                                    <svg class="absolute start-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                                                    <input
                                                                        v-model="countrySearch"
                                                                        type="text"
                                                                        placeholder="ابحث عن دولة..."
                                                                        class="w-full ps-9 pe-3 py-2 text-sm rounded-lg bg-white border border-gray-200 focus:border-[#1B4F72] outline-none transition"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <!-- List -->
                                                            <div class="max-h-64 overflow-y-auto custom-scroll">
                                                                <button
                                                                    v-for="c in filteredCountries"
                                                                    :key="c.code"
                                                                    type="button"
                                                                    @click="selectCountry(c)"
                                                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-[#1B4F72]/5 transition-colors text-start"
                                                                    :class="{ 'bg-[#C4A265]/10': selectedCountry.code === c.code }"
                                                                >
                                                                    <span class="text-xl leading-none">{{ c.flag }}</span>
                                                                    <span class="flex-1 text-gray-700 truncate">{{ c.name_ar }}</span>
                                                                    <span class="text-gray-400 text-xs font-mono" dir="ltr">{{ c.dial }}</span>
                                                                    <svg v-if="selectedCountry.code === c.code" class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                                                </button>
                                                                <div v-if="!filteredCountries.length" class="px-4 py-6 text-center text-sm text-gray-400">
                                                                    لا توجد نتائج
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </Transition>
                                                </div>

                                                <!-- Divider -->
                                                <div class="h-7 w-px bg-gray-200"></div>

                                                <!-- Dial Code (visual, not editable) -->
                                                <span class="ps-3 pe-1 text-sm font-semibold text-gray-600 select-none" dir="ltr">
                                                    {{ selectedCountry.dial }}
                                                </span>

                                                <!-- Phone Input -->
                                                <input
                                                    v-model="form.phone"
                                                    type="tel"
                                                    id="phone"
                                                    placeholder="1012345678"
                                                    class="flex-1 min-w-0 h-full ps-2 pe-4 bg-transparent outline-none text-[#1C2833] placeholder:text-gray-300"
                                                    dir="ltr"
                                                />
                                            </div>
                                            <p v-if="form.errors.phone" class="text-red-500 text-xs mt-1.5 ms-2">{{ form.errors.phone }}</p>
                                        </div>

                                        <!-- Subject -->
                                        <div class="floating-field">
                                            <select
                                                v-model="form.subject"
                                                id="subject"
                                                required
                                                class="peer w-full px-4 pt-5 pb-2 rounded-2xl border-2 border-gray-200 bg-gray-50/50 focus:border-[#1B4F72] focus:bg-white outline-none transition-all appearance-none cursor-pointer"
                                            >
                                                <option value=""></option>
                                                <option v-for="s in subjects" :key="s.value" :value="s.value">{{ s.label }}</option>
                                            </select>
                                            <label for="subject" class="absolute start-4 text-gray-400 text-xs pointer-events-none top-1.5 text-[#1B4F72]">
                                                {{ t('contact.subject') }} *
                                            </label>
                                            <svg class="absolute end-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </div>
                                    </div>

                                    <!-- Message -->
                                    <div class="floating-field mt-5">
                                        <textarea
                                            v-model="form.message"
                                            id="message"
                                            rows="6"
                                            required
                                            placeholder=" "
                                            class="peer w-full px-4 pt-6 pb-2 rounded-2xl border-2 border-gray-200 bg-gray-50/50 focus:border-[#1B4F72] focus:bg-white outline-none transition-all resize-none"
                                        ></textarea>
                                        <label for="message" class="absolute start-4 top-4 text-gray-400 text-sm pointer-events-none transition-all peer-focus:top-2 peer-focus:text-xs peer-focus:text-[#1B4F72] peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-xs">
                                            {{ t('contact.message') }} *
                                        </label>
                                        <p v-if="form.errors.message" class="text-red-500 text-xs mt-1.5 ms-2">{{ form.errors.message }}</p>
                                    </div>

                                    <!-- Submit -->
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="group relative mt-8 w-full overflow-hidden px-10 py-4 bg-gradient-to-r from-[#1B4F72] to-[#2E86C1] text-white font-bold rounded-2xl transition-all hover:shadow-2xl hover:shadow-[#1B4F72]/40 hover:scale-[1.01] disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        <div class="absolute inset-0 bg-gradient-to-r from-[#C4A265] to-[#D4B87A] translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
                                        <span class="relative flex items-center justify-center gap-3">
                                            <svg
                                                v-if="form.processing"
                                                class="w-5 h-5 animate-spin"
                                                fill="none" viewBox="0 0 24 24"
                                            >
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                            </svg>
                                            <svg v-else class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                            <span>{{ form.processing ? t('contact.sending') : t('contact.send') }}</span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </Transition>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-2 space-y-6 reveal" style="animation-delay: 150ms">
                        <!-- Office Hours -->
                        <div class="relative bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#2E86C1] rounded-3xl p-8 shadow-xl overflow-hidden">
                            <div class="absolute -top-10 -end-10 w-40 h-40 bg-[#C4A265]/20 rounded-full blur-2xl"></div>
                            <div class="absolute -bottom-10 -start-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>

                            <div class="relative">
                                <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-xl font-bold text-white mb-1">ساعات العمل</h3>
                                <p class="text-white/60 text-xs mb-6">نحن هنا لخدمتك</p>

                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                        <span class="text-white/80 text-sm">السبت — الخميس</span>
                                        <span class="text-[#C4A265] font-bold text-sm" dir="ltr">9:00 — 18:00</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/5 backdrop-blur-sm border border-white/10">
                                        <span class="text-white/80 text-sm">الجمعة</span>
                                        <span class="text-red-300 font-bold text-sm">مغلق</span>
                                    </div>
                                </div>

                                <div class="mt-5 flex items-center gap-2 text-xs text-emerald-300">
                                    <span class="relative flex w-2 h-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                                    </span>
                                    <span>متاحون الآن — رد سريع</span>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="bg-white rounded-3xl p-8 shadow-xl border border-gray-100">
                            <h3 class="text-xl font-bold text-[#1C2833] mb-2">تابعنا على</h3>
                            <p class="text-gray-500 text-xs mb-5">آخر التحديثات والأخبار</p>

                            <div class="grid grid-cols-4 gap-3">
                                <a
                                    v-for="(social, idx) in socialLinks"
                                    :key="social.name"
                                    :href="social.href"
                                    target="_blank"
                                    :aria-label="social.name"
                                    class="group relative aspect-square rounded-2xl bg-gray-50 hover:bg-white flex items-center justify-center transition-all duration-500 hover:scale-110 hover:-translate-y-1 hover:shadow-xl border border-gray-100"
                                    :style="{ '--brand': social.color, animationDelay: `${idx * 100}ms` }"
                                >
                                    <div class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500" :style="{ background: social.color }"></div>
                                    <svg class="relative w-5 h-5 text-gray-500 group-hover:text-white transition-colors duration-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path :d="social.path" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Why Choose Us -->
                        <div class="bg-gradient-to-br from-[#FFF8E7] to-white rounded-3xl p-8 shadow-xl border border-[#C4A265]/20">
                            <h3 class="text-xl font-bold text-[#1C2833] mb-5">لماذا تتواصل معنا؟</h3>
                            <ul class="space-y-3">
                                <li class="flex items-start gap-3 text-sm text-gray-700">
                                    <div class="w-6 h-6 rounded-lg bg-[#C4A265]/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                    <span>دعم فني على مدار الساعة</span>
                                </li>
                                <li class="flex items-start gap-3 text-sm text-gray-700">
                                    <div class="w-6 h-6 rounded-lg bg-[#C4A265]/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                    <span>استشارات مجانية للعملاء الجدد</span>
                                </li>
                                <li class="flex items-start gap-3 text-sm text-gray-700">
                                    <div class="w-6 h-6 rounded-lg bg-[#C4A265]/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                    <span>فريق متعدد اللغات (عربي / إنجليزي)</span>
                                </li>
                                <li class="flex items-start gap-3 text-sm text-gray-700">
                                    <div class="w-6 h-6 rounded-lg bg-[#C4A265]/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <svg class="w-4 h-4 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                    <span>رد سريع خلال أقل من 24 ساعة</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>

<style scoped>
/* Reveal Animation */
.reveal {
    animation: revealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

@keyframes revealUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Blob Animation */
@keyframes blob {
    0%, 100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(40px, -60px) scale(1.1);
    }
    50% {
        transform: translate(-30px, 30px) scale(0.9);
    }
    75% {
        transform: translate(50px, 50px) scale(1.05);
    }
}

.animate-blob {
    animation: blob 20s ease-in-out infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Floating particles */
@keyframes float-particle {
    0%, 100% {
        transform: translate(0, 0);
        opacity: 0.3;
    }
    50% {
        transform: translate(30px, -50px);
        opacity: 0.8;
    }
}

.animate-float-particle {
    animation: float-particle ease-in-out infinite;
}

/* Check draw animation */
@keyframes check-draw {
    0% {
        stroke-dasharray: 50;
        stroke-dashoffset: 50;
    }
    100% {
        stroke-dasharray: 50;
        stroke-dashoffset: 0;
    }
}

.animate-check-draw path {
    animation: check-draw 0.6s ease-out forwards;
}

/* Floating field */
.floating-field {
    position: relative;
}

.floating-field input,
.floating-field textarea,
.floating-field select {
    font-size: 0.95rem;
}

/* RTL adjustments for floating labels */
[dir="rtl"] .floating-field label {
    right: 1rem;
    left: auto;
}

/* Custom scrollbar for country dropdown */
.custom-scroll::-webkit-scrollbar {
    width: 6px;
}
.custom-scroll::-webkit-scrollbar-track {
    background: #f3f4f6;
}
.custom-scroll::-webkit-scrollbar-thumb {
    background: #1B4F72;
    border-radius: 3px;
}
.custom-scroll::-webkit-scrollbar-thumb:hover {
    background: #C4A265;
}
</style>
