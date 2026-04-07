<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import CurrencySwitcher from '@/Components/CurrencySwitcher.vue';

const { t, locale } = useI18n();

const isScrolled = ref(false);
const isMobileMenuOpen = ref(false);
const isProductDropdownOpen = ref(false);

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
                <Link href="/" class="flex items-center gap-2 shrink-0">
                    <span
                        class="text-xl lg:text-2xl font-bold tracking-tight transition-colors duration-300 text-primary"
                    >
                        <span class="bg-gradient-to-r from-secondary to-secondary-light bg-clip-text text-transparent">D</span>octorato
                    </span>
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

                <!-- Mobile Hamburger -->
                <button
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    class="lg:hidden p-2 rounded-lg transition-colors text-dark hover:bg-gray-100"
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

        <!-- Mobile Menu Overlay -->
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
                        <!-- Dropdown section in mobile -->
                        <template v-if="link.dropdown">
                            <p class="px-4 pt-4 pb-2 text-xs font-bold text-gray uppercase tracking-wider">{{ link.label }}</p>
                            <Link
                                v-for="child in link.children"
                                :key="child.href"
                                :href="child.href"
                                @click="closeMobileMenu"
                                class="block px-4 py-2.5 rounded-xl text-dark hover:bg-light-blue hover:text-primary font-medium transition-colors text-sm"
                            >
                                {{ child.label }}
                            </Link>
                        </template>

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
    </nav>
</template>
