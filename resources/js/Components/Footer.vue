<script setup>
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale, te } = useI18n();
const isAr = computed(() => locale.value === 'ar');

// Render a footer link label. When a translation key exists we use it;
// otherwise fall back to the inline ar/en pair defined on the link —
// this avoids having to round-trip to ar.json for brand-new pages.
function linkLabel(link) {
    if (link.label && te(link.label)) return t(link.label);
    if (link.fallback) return locale.value === 'ar' ? link.fallback.ar : link.fallback.en;
    return link.label;
}

const newsletterForm = useForm({ email: '' });
const newsletterSuccess = ref(false);

function submitNewsletter() {
    newsletterForm.post(route('newsletter.store'), {
        preserveScroll: true,
        onSuccess: () => {
            newsletterSuccess.value = true;
            newsletterForm.reset();
            setTimeout(() => { newsletterSuccess.value = false; }, 4000);
        },
    });
}

function scrollTop() {
    if (typeof window !== 'undefined') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

const productLinks = [
    { label: 'nav.features', href: '/features' },
    { label: 'nav.portals', href: '/portals' },
    { label: 'footer.link_dental', href: '/dental', fallback: { ar: 'نظام عيادات الأسنان', en: 'Dental clinic system' } },
    { label: 'footer.link_pediatrics', href: '/pediatrics', fallback: { ar: 'طب الأطفال', en: 'Pediatrics' } },
    { label: 'footer.link_telemedicine', href: '/telemedicine', fallback: { ar: 'الاستشارات الأون لاين', en: 'Online consultations' } },
    { label: 'nav.solutions', href: '/solutions' },
    { label: 'nav.pricing', href: '/pricing' },
];

const companyLinks = [
    { label: 'footer.contact_us', href: '/contact' },
    { label: 'footer.request_demo', href: '/demo' },
    { label: 'nav.technology', href: '/technology' },
    { label: 'nav.reports', href: '/reports' },
    { label: 'nav.blog', href: '/blog' },
    { label: 'footer.link_glossary', href: '/glossary', fallback: { ar: 'قاموس المصطلحات', en: 'Glossary' } },
    { label: 'footer.link_compare', href: '/compare', fallback: { ar: 'مقارنة بالمنافسين', en: 'Compare alternatives' } },
];

const socialLinks = [
    { name: 'Twitter',   href: '#', color: '#1DA1F2', icon: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' },
    { name: 'Instagram', href: '#', color: '#E4405F', icon: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z' },
    { name: 'LinkedIn',  href: '#', color: '#0A66C2', icon: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' },
    { name: 'YouTube',   href: '#', color: '#FF0000', icon: 'M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' },
    { name: 'TikTok',    href: '#', color: '#FF0050', icon: 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z' },
];

const trustBadges = computed(() => [
    { label: 'SSL 256-bit',                                color: 'text-emerald-400', icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' },
    { label: 'PCI DSS',                                    color: 'text-blue-400',    icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
    { label: isAr.value ? 'GDPR متوافق' : 'GDPR-ready',     color: 'text-amber-400',   icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
    { label: isAr.value ? 'HIPAA متوافق' : 'HIPAA-aligned', color: 'text-rose-400',    icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
    { label: isAr.value ? '99.9% استقرار' : '99.9% uptime', color: 'text-emerald-400', live: true },
    { label: isAr.value ? 'دعم 24/7'      : '24/7 support', color: 'text-[#C4A265]',   icon: 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
]);
</script>

<template>
    <footer class="relative text-white overflow-hidden bg-[#070F1B]">
        <!-- ─── Layer 1 — animated gold gradient line at the very top ─── -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/60 to-transparent footer-shimmer"></div>

        <!-- ─── Layer 2 — base radial gradient bed ─── -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0F1923] to-[#070F1B]"></div>

        <!-- ─── Layer 3 — dot grid texture ─── -->
        <div
            class="absolute inset-0 opacity-[0.06] pointer-events-none"
            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 36px 36px;"
        ></div>

        <!-- ─── Layer 4 — floating aurora orbs (drift slowly) ─── -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-40 -start-20 w-[28rem] h-[28rem] rounded-full bg-[#1B4F72]/30 blur-[140px] footer-aurora"></div>
            <div class="absolute -bottom-32 -end-32 w-[36rem] h-[36rem] rounded-full bg-[#C4A265]/[0.10] blur-[160px] footer-aurora" style="animation-delay: -8s"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[24rem] h-[24rem] rounded-full bg-[#2E86C1]/15 blur-[120px] footer-aurora" style="animation-delay: -16s"></div>
        </div>

        <!-- ─── Layer 5 — diagonal grid (drifts) — desktop only to save mobile battery + reduce visual noise on small screens ─── -->
        <div class="absolute inset-0 footer-diagonal-grid pointer-events-none hidden sm:block"></div>

        <!-- ──────────── Content ──────────── -->
        <div class="relative">
            <!-- Tagline bar — centered stack on mobile, side-by-side on desktop -->
            <div class="border-b border-white/[0.06]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 sm:gap-6">
                        <div class="flex flex-col sm:flex-row items-center text-center sm:text-start gap-3 sm:gap-4 w-full sm:w-auto">
                            <div class="relative shrink-0">
                                <div class="absolute inset-0 rounded-2xl bg-[#C4A265]/20 blur-xl footer-pulse"></div>
                                <div class="relative w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center shadow-lg shadow-[#C4A265]/20">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 18v-6a9 9 0 0118 0v6m-3 0a3 3 0 11-6 0 3 3 0 016 0zm0 0v-6m-12 6a3 3 0 11-6 0 3 3 0 016 0zm0 0v-6" />
                                    </svg>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] sm:text-xs uppercase tracking-[0.2em] text-[#C4A265] font-bold mb-0.5">{{ isAr ? 'مهمتنا' : 'Our mission' }}</p>
                                <p class="text-xs sm:text-base font-semibold text-white/90 leading-snug">
                                    {{ isAr ? 'تحويل العيادات الطبية للعصر الرقمي — بكفاءة وأمان' : 'Clinics in the digital age — efficient, secure, beautiful' }}
                                </p>
                            </div>
                        </div>
                        <button
                            @click="scrollTop"
                            class="group hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/[0.05] hover:bg-white/[0.1] border border-white/[0.08] text-xs font-semibold text-white/70 hover:text-white transition-all"
                        >
                            <span>{{ isAr ? 'العودة لأعلى' : 'Back to top' }}</span>
                            <svg class="w-3.5 h-3.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main columns — 2-col on mobile (link blocks side-by-side),
                 12-col on md+ for full layout flexibility. -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 lg:py-16">
                <div class="grid grid-cols-2 md:grid-cols-12 gap-8 sm:gap-10 lg:gap-12">
                    <!-- Brand + about + social — full width on mobile, centered. -->
                    <div class="col-span-2 md:col-span-12 lg:col-span-4 footer-fade text-center md:text-start" style="--delay: 0ms">
                        <Link href="/" class="inline-block mb-4 sm:mb-5 group">
                            <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-36 sm:w-40 h-auto logo-white group-hover:scale-105 transition-transform duration-300" />
                        </Link>
                        <p class="text-white/55 text-sm leading-relaxed mb-5 sm:mb-6 max-w-md mx-auto md:mx-0">
                            {{ $t('footer.description') }}
                        </p>

                        <!-- Social icons — centered on mobile -->
                        <div class="flex items-center justify-center md:justify-start gap-2 sm:gap-2.5">
                            <a
                                v-for="social in socialLinks"
                                :key="social.name"
                                :href="social.href"
                                :aria-label="social.name"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group relative w-10 h-10 rounded-xl bg-white/[0.04] border border-white/[0.08] flex items-center justify-center transition-all duration-300 hover:-translate-y-1 hover:scale-110 overflow-hidden"
                                :style="{ '--brand-color': social.color }"
                            >
                                <span class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300" :style="{ background: social.color }"></span>
                                <svg class="relative w-4 h-4 fill-current text-white/70 group-hover:text-white transition-colors" viewBox="0 0 24 24">
                                    <path :d="social.icon" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Product links — 1/2 width on mobile (side-by-side with Support) -->
                    <div class="col-span-1 md:col-span-4 lg:col-span-2 footer-fade" style="--delay: 100ms">
                        <h4 class="relative inline-flex items-center gap-2 mb-5 text-base font-bold text-white">
                            <span class="w-1 h-4 rounded-full bg-gradient-to-b from-[#C4A265] to-[#D4B876]"></span>
                            {{ $t('footer.quick_links') }}
                        </h4>
                        <ul class="space-y-2.5">
                            <li v-for="link in productLinks" :key="link.href">
                                <Link
                                    :href="link.href"
                                    class="group inline-flex items-center gap-1.5 text-sm text-white/55 hover:text-white transition-colors"
                                >
                                    <span class="w-0 group-hover:w-2 h-px bg-[#C4A265] transition-all duration-300"></span>
                                    <span>{{ linkLabel(link) }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Company links — 1/2 width on mobile -->
                    <div class="col-span-1 md:col-span-4 lg:col-span-2 footer-fade" style="--delay: 200ms">
                        <h4 class="relative inline-flex items-center gap-2 mb-5 text-base font-bold text-white">
                            <span class="w-1 h-4 rounded-full bg-gradient-to-b from-[#C4A265] to-[#D4B876]"></span>
                            {{ $t('footer.support') }}
                        </h4>
                        <ul class="space-y-2.5">
                            <li v-for="link in companyLinks" :key="link.href">
                                <Link
                                    :href="link.href"
                                    class="group inline-flex items-center gap-1.5 text-sm text-white/55 hover:text-white transition-colors"
                                >
                                    <span class="w-0 group-hover:w-2 h-px bg-[#C4A265] transition-all duration-300"></span>
                                    <span>{{ linkLabel(link) }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Newsletter — full width on mobile, 1/3 on md, 1/3 on lg -->
                    <div class="col-span-2 md:col-span-4 lg:col-span-4 footer-fade" style="--delay: 300ms">
                        <div class="relative rounded-2xl p-5 sm:p-6 bg-white/[0.03] border border-white/[0.08] overflow-hidden footer-newsletter-card">
                            <!-- Soft glow behind card -->
                            <div class="absolute -top-10 -end-10 w-32 h-32 rounded-full bg-[#C4A265]/15 blur-3xl pointer-events-none"></div>

                            <h4 class="relative inline-flex items-center gap-2 mb-2 text-base font-bold text-white">
                                <span class="w-1 h-4 rounded-full bg-gradient-to-b from-[#C4A265] to-[#D4B876]"></span>
                                {{ $t('footer.newsletter') }}
                            </h4>
                            <p class="text-xs sm:text-sm text-white/55 mb-4 leading-relaxed">{{ $t('footer.newsletter_desc') }}</p>

                            <form @submit.prevent="submitNewsletter" class="space-y-2.5">
                                <!-- Stack vertically on mobile so each touch target is full-width
                                     and easy to tap with a thumb. Inline on sm+ for compactness. -->
                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-2 rounded-xl">
                                    <input
                                        v-model="newsletterForm.email"
                                        type="email"
                                        required
                                        :placeholder="$t('footer.email_placeholder')"
                                        class="w-full sm:flex-1 sm:min-w-0 px-3.5 py-3 sm:py-2.5 rounded-lg bg-white/[0.06] border border-white/[0.12] text-white placeholder-white/35 text-sm focus:border-[#C4A265]/60 focus:bg-white/[0.10] focus:ring-2 focus:ring-[#C4A265]/20 outline-none transition-all"
                                        dir="ltr"
                                    />
                                    <button
                                        type="submit"
                                        :disabled="newsletterForm.processing"
                                        class="group inline-flex items-center justify-center gap-1.5 w-full sm:w-auto px-4 sm:px-5 py-3 sm:py-2.5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] hover:shadow-lg hover:shadow-[#C4A265]/25 text-white text-sm font-bold rounded-lg transition-all duration-300 disabled:opacity-50 hover:-translate-y-0.5"
                                    >
                                        <span>{{ $t('footer.subscribe') }}</span>
                                        <svg class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </button>
                                </div>
                                <p v-if="newsletterForm.errors.email" class="text-rose-400 text-xs">{{ newsletterForm.errors.email }}</p>
                                <Transition
                                    enter-active-class="transition duration-300"
                                    enter-from-class="opacity-0 -translate-y-1"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition duration-200"
                                    leave-from-class="opacity-100"
                                    leave-to-class="opacity-0"
                                >
                                    <p v-if="newsletterSuccess" class="flex items-center gap-1.5 text-emerald-400 text-xs font-semibold">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $t('footer.subscribe_success') }}
                                    </p>
                                </Transition>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trust strip — 3-col grid on mobile (2 rows of 3), inline on desktop -->
            <div class="border-t border-white/[0.06] bg-black/30 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
                    <div class="grid grid-cols-3 sm:flex sm:flex-wrap items-center justify-center gap-x-3 sm:gap-x-5 gap-y-3">
                        <div
                            v-for="badge in trustBadges"
                            :key="badge.label"
                            class="group flex items-center justify-center gap-1.5 text-white/55 hover:text-white/95 transition-colors"
                        >
                            <span v-if="badge.live" class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            <svg v-else class="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" :class="badge.color" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="badge.icon" />
                            </svg>
                            <span class="text-[10px] sm:text-xs font-semibold tracking-wide whitespace-nowrap">{{ badge.label }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom bar — center on mobile, split on desktop -->
            <div class="border-t border-white/[0.06]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-5">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-white/45">
                        <!-- Privacy/terms shown FIRST on mobile (smaller, but more important
                             for visitors looking for legal). Copyright second, broken across
                             two lines on small screens to fit. -->
                        <div class="flex items-center gap-4 sm:gap-5 order-1 sm:order-2">
                            <Link href="/privacy" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.privacy') }}</Link>
                            <span class="w-px h-3 bg-white/15"></span>
                            <Link href="/terms" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.terms') }}</Link>
                        </div>
                        <p class="text-center sm:text-start text-[11px] sm:text-sm leading-relaxed order-2 sm:order-1">
                            <span class="block sm:inline">&copy; {{ new Date().getFullYear() }} Doctorato</span>
                            <span class="hidden sm:inline text-white/20"> · </span>
                            <span class="block sm:inline">{{ isAr ? 'منتج من Markeza Group' : 'A Markeza Group product' }}</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </footer>
</template>

<style scoped>
/* Shimmer along the gold top line — looks like light sweeping across */
.footer-shimmer {
    background-size: 200% 100%;
    animation: footer-shimmer-anim 6s linear infinite;
}
@keyframes footer-shimmer-anim {
    0%   { background-position: 100% 0; }
    100% { background-position: -100% 0; }
}

/* Slow drifting aurora orbs */
.footer-aurora {
    animation: footer-aurora-drift 18s ease-in-out infinite;
}
@keyframes footer-aurora-drift {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33%      { transform: translate(28px, -20px) scale(1.08); }
    66%      { transform: translate(-22px, 18px) scale(0.95); }
}

/* Subtle pulse behind the mission icon */
.footer-pulse {
    animation: footer-pulse-anim 3.5s ease-in-out infinite;
}
@keyframes footer-pulse-anim {
    0%, 100% { transform: scale(1);   opacity: 0.6; }
    50%      { transform: scale(1.2); opacity: 0.9; }
}

/* Diagonal grid that slowly drifts (background texture) */
.footer-diagonal-grid {
    background-image:
        linear-gradient(45deg, rgba(196, 162, 101, 0.04) 25%, transparent 25%),
        linear-gradient(-45deg, rgba(196, 162, 101, 0.04) 25%, transparent 25%);
    background-size: 60px 60px;
    animation: footer-grid-drift 22s linear infinite;
    opacity: 0.5;
}
@keyframes footer-grid-drift {
    0%   { background-position: 0 0, 0 0; }
    100% { background-position: 60px 60px, -60px 60px; }
}

/* Stagger reveal for the four columns */
.footer-fade {
    animation: footer-column-fade 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--delay, 0ms);
}
@keyframes footer-column-fade {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Newsletter card — soft animated glowing border */
.footer-newsletter-card::before {
    content: '';
    position: absolute;
    inset: -1px;
    padding: 1px;
    border-radius: 1rem;
    background: linear-gradient(135deg, rgba(196, 162, 101, 0.45), transparent 30%, transparent 70%, rgba(196, 162, 101, 0.30));
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
            mask-composite: exclude;
    pointer-events: none;
    opacity: 0.7;
}

/* Respect "reduce motion" — stop animations for users who opt out */
@media (prefers-reduced-motion: reduce) {
    .footer-shimmer,
    .footer-aurora,
    .footer-pulse,
    .footer-diagonal-grid,
    .footer-fade {
        animation: none;
    }
}
</style>
