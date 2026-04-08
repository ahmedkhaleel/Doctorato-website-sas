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
const isProductDropdownOpen = ref(false);
const openMobileDropdown = ref(null);

function toggleMobileDropdown(label) {
    openMobileDropdown.value = openMobileDropdown.value === label ? null : label;
}

const navLinks = computed(() => [
    { label: t('nav.home'), href: '/' },
    {
        label: t('nav.product'),
        dropdown: true,
        children: [
            { label: t('nav.features'), href: '/features', desc: t('nav.features_desc') },
            { label: t('nav.portals'), href: '/portals', desc: t('nav.portals_desc') },
            { label: t('nav.dental'), href: '/dental', desc: t('nav.dental_desc') },
            { label: t('nav.solutions'), href: '/solutions', desc: t('nav.solutions_desc') },
            { label: t('nav.technology'), href: '/technology', desc: t('nav.technology_desc') },
            { label: t('nav.reports'), href: '/reports', desc: t('nav.reports_desc') },
        ],
    },
    { label: t('nav.pricing'), href: '/pricing' },
    { label: t('nav.about'), href: '/about' },
    { label: t('nav.blog'), href: '/blog' },
    { label: t('nav.faq'), href: '/faq' },
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
function openDropdown() {
    clearTimeout(dropdownTimeout);
    isProductDropdownOpen.value = true;
}
function closeDropdown() {
    dropdownTimeout = setTimeout(() => {
        isProductDropdownOpen.value = false;
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
                            @mouseenter="openDropdown"
                            @mouseleave="closeDropdown"
                        >
                            <button
                                class="px-4 py-2 rounded-lg text-[13px] font-medium transition-all duration-200 flex items-center gap-1 text-dark hover:text-primary hover:bg-light-blue"
                            >
                                {{ link.label }}
                                <svg class="w-4 h-4 transition-transform" :class="isProductDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    v-if="isProductDropdownOpen"
                                    class="absolute top-full start-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
                                >
                                    <div class="p-3">
                                        <Link
                                            v-for="child in link.children"
                                            :key="child.href"
                                            :href="child.href"
                                            class="flex items-start gap-3 p-3 rounded-xl hover:bg-light-blue transition-colors group"
                                            @click="isProductDropdownOpen = false"
                                        >
                                            <div>
                                                <p class="text-sm font-semibold text-dark group-hover:text-primary transition-colors">{{ child.label }}</p>
                                                <p class="text-xs text-gray mt-0.5">{{ child.desc }}</p>
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
