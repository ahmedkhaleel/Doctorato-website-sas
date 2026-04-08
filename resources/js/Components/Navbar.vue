<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import CurrencySwitcher from '@/Components/CurrencySwitcher.vue';

const { t, locale } = useI18n();
const { switchLocale } = useLocale();

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
// Tracks which desktop dropdown is open ('product' | 'resources' | null).
// A string key (instead of a boolean per menu) keeps the state flat and
// ensures opening one dropdown automatically closes the other.
const openDesktopDropdown = ref(null);
const openMobileDropdown = ref(null);

function toggleMobileDropdown(label) {
    openMobileDropdown.value = openMobileDropdown.value === label ? null : label;
}

// Navbar structure — 6 top-level items, 2 dropdowns (Product / Resources).
// This mirrors the standard SaaS layout used by Stripe, Linear, and Notion:
// primary entry points stay flat, while supporting content lives under a
// single "Resources" menu instead of cluttering the main bar.
const navLinks = computed(() => [
    { label: t('nav.home'), href: '/' },
    {
        label: t('nav.product'),
        dropdown: 'product',
        children: [
            { label: t('nav.features'), href: '/features', desc: t('nav.features_desc'), icon: 'features' },
            { label: t('nav.portals'), href: '/portals', desc: t('nav.portals_desc'), icon: 'portals' },
            { label: t('nav.dental'), href: '/dental', desc: t('nav.dental_desc'), icon: 'dental' },
            { label: t('nav.solutions'), href: '/solutions', desc: t('nav.solutions_desc'), icon: 'solutions' },
            { label: t('nav.technology'), href: '/technology', desc: t('nav.technology_desc'), icon: 'technology' },
            { label: t('nav.reports'), href: '/reports', desc: t('nav.reports_desc'), icon: 'reports' },
        ],
    },
    { label: t('nav.pricing'), href: '/pricing' },
    {
        label: locale.value === 'ar' ? 'الموارد' : 'Resources',
        dropdown: 'resources',
        children: [
            { label: locale.value === 'ar' ? 'دراسات الحالة' : 'Case studies', href: '/case-studies', desc: locale.value === 'ar' ? 'قصص نجاح عملاء حقيقيين' : 'Real customer success stories', icon: 'case' },
            { label: locale.value === 'ar' ? 'حاسبة العائد' : 'ROI calculator', href: '/roi-calculator', desc: locale.value === 'ar' ? 'احسب توفيرك خلال دقيقة' : 'Calculate your savings in a minute', icon: 'roi' },
            { label: t('nav.blog'), href: '/blog', desc: locale.value === 'ar' ? 'مقالات ونصائح لإدارة العيادات' : 'Articles & tips for clinics', icon: 'blog' },
            { label: t('nav.faq'), href: '/faq', desc: locale.value === 'ar' ? 'إجابات الأسئلة المتكررة' : 'Answers to common questions', icon: 'faq' },
        ],
    },
    { label: t('nav.about'), href: '/about' },
    { label: t('nav.contact'), href: '/contact' },
]);

function handleScroll() {
    isScrolled.value = window.scrollY > 80;
}

function closeMobileMenu() {
    isMobileMenuOpen.value = false;
    openMobileDropdown.value = null;
}

let dropdownTimeout = null;
function openDropdown(key) {
    clearTimeout(dropdownTimeout);
    openDesktopDropdown.value = key;
}
function closeDropdown() {
    dropdownTimeout = setTimeout(() => {
        openDesktopDropdown.value = null;
    }, 150);
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});
</script>

<template>
    <nav
        class="fixed top-0 inset-x-0 z-50 transition-all duration-300 bg-white/95 backdrop-blur-md"
        :class="isScrolled ? 'shadow-lg' : 'shadow-sm border-b border-gray-100'"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <Link href="/" class="flex items-center shrink-0">
                    <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-36 lg:w-40 h-auto" />
                </Link>

                <!-- Desktop Nav Links -->
                <div class="hidden lg:flex items-center gap-1">
                    <template v-for="link in navLinks" :key="link.label">
                        <!-- Dropdown item -->
                        <div
                            v-if="link.dropdown"
                            class="relative"
                            @mouseenter="openDropdown(link.dropdown)"
                            @mouseleave="closeDropdown"
                        >
                            <button
                                :class="[
                                    'px-4 py-2 rounded-lg text-[13px] font-medium transition-all duration-200 flex items-center gap-1',
                                    openDesktopDropdown === link.dropdown
                                        ? 'text-primary bg-light-blue'
                                        : 'text-dark hover:text-primary hover:bg-light-blue'
                                ]"
                            >
                                {{ link.label }}
                                <svg class="w-4 h-4 transition-transform" :class="openDesktopDropdown === link.dropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="opacity-0 translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-150 ease-in"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-2"
                            >
                                <div
                                    v-if="openDesktopDropdown === link.dropdown"
                                    class="absolute top-full start-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
                                >
                                    <div class="p-3">
                                        <Link
                                            v-for="child in link.children"
                                            :key="child.href"
                                            :href="child.href"
                                            class="flex items-start gap-3 p-3 rounded-xl hover:bg-light-blue transition-colors group"
                                            @click="openDesktopDropdown = null"
                                        >
                                            <div class="w-10 h-10 rounded-xl bg-primary/5 group-hover:bg-primary/10 flex items-center justify-center shrink-0 transition-colors">
                                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path v-if="child.icon === 'features'" stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    <path v-else-if="child.icon === 'portals'" stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                                    <path v-else-if="child.icon === 'dental'" stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                                    <path v-else-if="child.icon === 'solutions'" stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    <path v-else-if="child.icon === 'technology'" stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    <path v-else-if="child.icon === 'reports'" stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6m4 6V9m4 10v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    <path v-else-if="child.icon === 'case'" stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                                    <path v-else-if="child.icon === 'roi'" stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    <path v-else-if="child.icon === 'blog'" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    <path v-else-if="child.icon === 'faq'" stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-dark group-hover:text-primary transition-colors">{{ child.label }}</p>
                                                <p class="text-xs text-gray mt-0.5 leading-relaxed">{{ child.desc }}</p>
                                            </div>
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Regular link -->
                        <Link
                            v-else
                            :href="link.href"
                            class="px-4 py-2 rounded-lg text-[13px] font-medium transition-all duration-200 text-dark hover:text-primary hover:bg-light-blue"
                        >
                            {{ link.label }}
                        </Link>
                    </template>
                </div>

                <!-- Desktop Right Actions -->
                <div class="hidden lg:flex items-center gap-3">
                    <CurrencySwitcher />

                    <LanguageSwitcher variant="default" />

                    <Link
                        href="/demo"
                        class="px-6 py-2.5 bg-secondary hover:bg-secondary-dark text-white text-sm font-semibold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25 hover:-translate-y-0.5"
                    >
                        {{ $t('nav.cta') }}
                    </Link>
                </div>

                <!-- Mobile Right Actions: language toggle + hamburger -->
                <div class="lg:hidden flex items-center gap-1">
                    <!-- Quick language toggle (icon + label) -->
                    <button
                        @click="switchLocale"
                        :aria-label="locale === 'ar' ? 'Switch to English' : 'التبديل إلى العربية'"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-dark hover:text-primary hover:bg-light-blue transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-xs font-bold tracking-wide">{{ locale === 'ar' ? 'EN' : 'AR' }}</span>
                    </button>

                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="p-2 rounded-lg transition-colors text-dark hover:bg-gray-100"
                    :aria-label="isMobileMenuOpen ? 'Close menu' : 'Open menu'"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            v-if="!isMobileMenuOpen"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            v-else
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
                </div>
            </div>
        </div>

        <!--
          Mobile Menu Overlay + Drawer — teleported to <body>.
          The parent <nav> uses backdrop-blur, which makes it a containing
          block for any position:fixed descendants. Without Teleport, the
          drawer's `top-0 bottom-0` would be relative to the (~80px) nav
          instead of the viewport, so it wouldn't render full-height.
        -->
        <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isMobileMenuOpen"
                class="fixed inset-0 bg-black/50 z-40 lg:hidden"
                @click="closeMobileMenu"
            />
        </Transition>

        <!-- Mobile Slide-in Drawer -->
        <Transition
            enter-active-class="transition-transform duration-300 ease-out"
            :enter-from-class="locale === 'ar' ? 'translate-x-full' : '-translate-x-full'"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-x-0"
            :leave-to-class="locale === 'ar' ? 'translate-x-full' : '-translate-x-full'"
        >
            <div
                v-if="isMobileMenuOpen"
                class="fixed top-0 bottom-0 w-80 max-w-[85vw] bg-white z-50 shadow-2xl overflow-y-auto lg:hidden"
                :class="locale === 'ar' ? 'right-0' : 'left-0'"
            >
                <!-- Drawer Header -->
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <Link href="/" class="text-xl font-bold text-primary" @click="closeMobileMenu">
                        <span class="bg-gradient-to-r from-secondary to-secondary-light bg-clip-text text-transparent">D</span>octorato
                    </Link>
                    <button
                        @click="closeMobileMenu"
                        class="p-2 rounded-lg text-gray hover:bg-gray-100 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Drawer Links -->
                <div class="p-4 space-y-1">
                    <template v-for="link in navLinks" :key="link.label">
                        <!-- Collapsible dropdown — same items as desktop, but as an accordion -->
                        <div v-if="link.dropdown" class="rounded-xl overflow-hidden">
                            <button
                                type="button"
                                @click="toggleMobileDropdown(link.label)"
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-dark hover:bg-light-blue hover:text-primary font-medium transition-colors"
                                :class="{ 'bg-light-blue text-primary': openMobileDropdown === link.label }"
                            >
                                <span>{{ link.label }}</span>
                                <svg
                                    class="w-4 h-4 transition-transform duration-300"
                                    :class="{ 'rotate-180': openMobileDropdown === link.label }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Animated children panel -->
                            <Transition
                                enter-active-class="transition-all duration-300 ease-out"
                                enter-from-class="max-h-0 opacity-0"
                                enter-to-class="max-h-[600px] opacity-100"
                                leave-active-class="transition-all duration-200 ease-in"
                                leave-from-class="max-h-[600px] opacity-100"
                                leave-to-class="max-h-0 opacity-0"
                            >
                                <div v-if="openMobileDropdown === link.label" class="overflow-hidden">
                                    <div class="ms-4 mt-1 ps-3 border-s-2 border-secondary/30 space-y-0.5 py-1">
                                        <Link
                                            v-for="child in link.children"
                                            :key="child.href"
                                            :href="child.href"
                                            @click="closeMobileMenu"
                                            class="block px-3 py-2 rounded-lg text-dark hover:bg-light-blue hover:text-primary font-medium transition-colors text-sm"
                                        >
                                            {{ child.label }}
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Regular link -->
                        <Link
                            v-else
                            :href="link.href"
                            @click="closeMobileMenu"
                            class="block px-4 py-3 rounded-xl text-dark hover:bg-light-blue hover:text-primary font-medium transition-colors"
                        >
                            {{ link.label }}
                        </Link>
                    </template>
                </div>

                <!-- Drawer Switchers -->
                <div class="px-4 py-4 border-t border-gray-100 space-y-3">
                    <div class="flex items-center gap-3">
                        <LanguageSwitcher variant="default" />
                        <CurrencySwitcher />
                    </div>
                </div>

                <!-- Drawer CTA -->
                <div class="p-4">
                    <Link
                        href="/demo"
                        @click="closeMobileMenu"
                        class="block w-full text-center px-6 py-3 bg-secondary hover:bg-secondary-dark text-white font-semibold rounded-full transition-colors"
                    >
                        {{ $t('nav.cta') }}
                    </Link>
                </div>
            </div>
        </Transition>
        </Teleport>
    </nav>
</template>
