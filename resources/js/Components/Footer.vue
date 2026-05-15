<script setup>
/**
 * Footer — rebuilt for visual balance and professional hierarchy.
 *
 * Layout (top to bottom):
 *   1. Brand row    : logo + description + social, single column,
 *                     spans the full width so the brand reads big.
 *   2. Link grid    : four equal-weight columns (Product, Resources,
 *                     Company, From the blog). Same width = visually
 *                     balanced; no column dominates.
 *   3. Newsletter   : centered card with the form, given its own
 *                     row instead of being crammed into a sidebar.
 *                     Acts as the conversion focal point.
 *   4. Trust strip  : compact row of compliance badges.
 *   5. Legal bar    : copyright + privacy + terms, evenly split.
 *
 * Background is single subtle aurora + dot grid — the previous
 * version had three overlapping orbs and a diagonal grid, which
 * fought for attention.
 */
import { ref, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale, te } = useI18n();
const isAr = computed(() => locale.value === 'ar');

const page = usePage();
const latestPosts = computed(() => page.props.footerLatestPosts || []);
function postTitle(post) {
    return isAr.value ? (post.title_ar || post.title_en) : (post.title_en || post.title_ar);
}

// Render a footer link label. When a translation key exists we use
// it; otherwise fall back to the inline ar/en pair on the link.
function linkLabel(link) {
    if (link.label && te(link.label)) return t(link.label);
    if (link.fallback) return isAr.value ? link.fallback.ar : link.fallback.en;
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

// Four sibling link columns — each kept to ~5 items so columns are
// visually the same height, which is what made the old footer feel
// lopsided when one column had 7 links and another had 4.
const productLinks = [
    { label: 'nav.features', href: '/features' },
    { label: 'nav.portals', href: '/portals' },
    { label: 'footer.link_dental', href: '/dental', fallback: { ar: 'عيادات الأسنان', en: 'Dental' } },
    { label: 'footer.link_pediatrics', href: '/pediatrics', fallback: { ar: 'طب الأطفال', en: 'Pediatrics' } },
    { label: 'footer.link_telemedicine', href: '/telemedicine', fallback: { ar: 'الاستشارات الأون لاين', en: 'Telemedicine' } },
];

const resourcesLinks = [
    { label: 'nav.pricing', href: '/pricing' },
    { label: 'nav.blog', href: '/blog' },
    { label: 'footer.link_glossary', href: '/glossary', fallback: { ar: 'قاموس المصطلحات', en: 'Glossary' } },
    { label: 'footer.link_compare', href: '/compare', fallback: { ar: 'مقارنة الأنظمة', en: 'Compare systems' } },
    { label: 'nav.faq', href: '/faq', fallback: { ar: 'الأسئلة الشائعة', en: 'FAQ' } },
];

const companyLinks = [
    { label: 'nav.about', href: '/about', fallback: { ar: 'عن دكتوراتو', en: 'About' } },
    { label: 'footer.contact_us', href: '/contact' },
    { label: 'footer.request_demo', href: '/demo' },
    { label: 'nav.technology', href: '/technology' },
    { label: 'nav.reports', href: '/reports' },
];

const socialLinks = [
    { name: 'Twitter',   href: '#', color: '#1DA1F2', icon: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' },
    { name: 'Instagram', href: '#', color: '#E4405F', icon: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z' },
    { name: 'LinkedIn',  href: '#', color: '#0A66C2', icon: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' },
    { name: 'YouTube',   href: '#', color: '#FF0000', icon: 'M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' },
    { name: 'TikTok',    href: '#', color: '#FF0050', icon: 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z' },
];

const trustBadges = computed(() => [
    { label: 'SSL 256-bit', icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' },
    { label: 'PCI DSS',     icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
    { label: isAr.value ? 'GDPR متوافق' : 'GDPR-ready',     icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
    { label: isAr.value ? 'HIPAA متوافق' : 'HIPAA-aligned', icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z' },
    { label: isAr.value ? '99.9% استقرار' : '99.9% uptime', live: true },
    { label: isAr.value ? 'دعم 24/7'      : '24/7 support', icon: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM12 7v5l3 2' },
]);

const linkColumns = computed(() => [
    { title: t('footer.quick_links'),                       links: productLinks },
    { title: isAr.value ? 'الموارد'   : 'Resources',         links: resourcesLinks },
    { title: t('footer.support'),                           links: companyLinks },
]);
</script>

<template>
    <footer class="relative text-white overflow-hidden bg-[#070F1B]">
        <!-- Top hairline — soft gold gradient, no shimmer; subtle by design. -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/50 to-transparent"></div>

        <!-- Single quiet background — base gradient + one aurora orb +
             dot grid. The previous version stacked three orbs and a
             diagonal grid that fought for attention. -->
        <div class="absolute inset-0 bg-gradient-to-b from-[#0A1726] via-[#0A1320] to-[#070F1B]"></div>
        <div class="absolute inset-x-0 top-0 h-96 bg-[#1B4F72]/15 blur-[140px] pointer-events-none"></div>
        <div
            class="absolute inset-0 opacity-[0.04] pointer-events-none"
            style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"
        ></div>

        <div class="relative">
            <!-- ───────── 1. Brand row ───────── -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 sm:pt-16 lg:pt-20 pb-10 sm:pb-12">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                    <!-- Logo + description -->
                    <div class="md:col-span-7 lg:col-span-8 text-center md:text-start footer-fade" style="--delay: 0ms">
                        <Link href="/" class="inline-block mb-5 group">
                            <img
                                src="/images/doctorato-logo.png"
                                alt="Doctorato"
                                class="w-36 sm:w-40 h-auto logo-white group-hover:scale-105 transition-transform duration-300"
                            />
                        </Link>
                        <p class="text-white/60 text-sm sm:text-base leading-relaxed max-w-xl mx-auto md:mx-0">
                            {{ $t('footer.description') }}
                        </p>
                    </div>

                    <!-- Social + back-to-top, right-aligned on desktop -->
                    <div class="md:col-span-5 lg:col-span-4 footer-fade flex flex-col items-center md:items-end gap-5" style="--delay: 100ms">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.2em] text-[#C4A265] font-bold mb-3 text-center md:text-end">
                                {{ isAr ? 'تابعنا' : 'Follow us' }}
                            </p>
                            <div class="flex items-center gap-2">
                                <a
                                    v-for="social in socialLinks"
                                    :key="social.name"
                                    :href="social.href"
                                    :aria-label="social.name"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group relative w-10 h-10 rounded-xl bg-white/[0.04] border border-white/[0.08] flex items-center justify-center transition-all duration-300 hover:-translate-y-1 hover:border-white/20 overflow-hidden"
                                >
                                    <span
                                        class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                        :style="{ background: social.color }"
                                    ></span>
                                    <svg class="relative w-4 h-4 fill-current text-white/70 group-hover:text-white transition-colors" viewBox="0 0 24 24">
                                        <path :d="social.icon" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <button
                            @click="scrollTop"
                            class="group inline-flex items-center gap-1.5 text-xs font-semibold text-white/50 hover:text-white transition-colors"
                        >
                            <svg class="w-3.5 h-3.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            {{ isAr ? 'العودة لأعلى' : 'Back to top' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- ───────── 2. Link grid — 4 equal-weight columns ───────── -->
            <div class="border-t border-white/[0.05]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12 lg:py-14">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-10">
                        <!-- 3 link columns rendered from the same template — visually identical. -->
                        <div
                            v-for="(column, idx) in linkColumns"
                            :key="column.title"
                            class="footer-fade"
                            :style="`--delay: ${150 + idx * 80}ms;`"
                        >
                            <h4 class="text-xs font-bold tracking-[0.18em] uppercase text-[#C4A265] mb-5">
                                {{ column.title }}
                            </h4>
                            <ul class="space-y-3">
                                <li v-for="link in column.links" :key="link.href">
                                    <Link
                                        :href="link.href"
                                        class="group inline-flex items-center gap-1.5 text-sm text-white/60 hover:text-white transition-colors"
                                    >
                                        <span class="w-0 group-hover:w-3 h-px bg-[#C4A265] transition-all duration-300"></span>
                                        <span>{{ linkLabel(link) }}</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <!-- 4th column: latest posts -->
                        <div v-if="latestPosts.length" class="footer-fade" style="--delay: 390ms">
                            <h4 class="text-xs font-bold tracking-[0.18em] uppercase text-[#C4A265] mb-5">
                                {{ isAr ? 'من المدوّنة' : 'From the blog' }}
                            </h4>
                            <ul class="space-y-3">
                                <li v-for="post in latestPosts.slice(0, 4)" :key="post.id">
                                    <Link
                                        :href="`/blog/${post.slug}`"
                                        class="group inline-flex items-start gap-1.5 text-sm text-white/60 hover:text-white transition-colors leading-snug"
                                        :title="postTitle(post)"
                                    >
                                        <span class="w-0 group-hover:w-3 h-px bg-[#C4A265] transition-all duration-300 mt-2 shrink-0"></span>
                                        <span class="line-clamp-2">{{ postTitle(post) }}</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ───────── 3. Newsletter — its own focal row ───────── -->
            <div class="border-t border-white/[0.05]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-12">
                    <div class="max-w-3xl mx-auto text-center footer-fade" style="--delay: 0ms">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-[#C4A265] font-bold mb-3">
                            {{ isAr ? 'نشرة بريدية' : 'Newsletter' }}
                        </p>
                        <h3 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-white mb-2">
                            {{ $t('footer.newsletter') }}
                        </h3>
                        <p class="text-sm sm:text-base text-white/55 mb-6 max-w-xl mx-auto leading-relaxed">
                            {{ $t('footer.newsletter_desc') }}
                        </p>

                        <form @submit.prevent="submitNewsletter" class="max-w-md mx-auto">
                            <div class="relative flex flex-col sm:flex-row gap-2 p-1 sm:p-1.5 rounded-2xl sm:rounded-full bg-white/[0.04] border border-white/[0.08] focus-within:border-[#C4A265]/40 transition-colors">
                                <input
                                    v-model="newsletterForm.email"
                                    type="email"
                                    required
                                    :placeholder="$t('footer.email_placeholder')"
                                    class="flex-1 min-w-0 px-4 py-3 sm:py-2.5 bg-transparent text-white placeholder-white/35 text-sm outline-none"
                                    dir="ltr"
                                />
                                <button
                                    type="submit"
                                    :disabled="newsletterForm.processing"
                                    class="group inline-flex items-center justify-center gap-1.5 px-5 sm:px-6 py-3 sm:py-2.5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white text-sm font-bold rounded-xl sm:rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-[#C4A265]/30 disabled:opacity-50"
                                >
                                    <span>{{ $t('footer.subscribe') }}</span>
                                    <svg class="w-4 h-4 rtl:rotate-180 group-hover:translate-x-0.5 rtl:group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="newsletterForm.errors.email" class="text-rose-400 text-xs mt-2">{{ newsletterForm.errors.email }}</p>
                            <Transition
                                enter-active-class="transition duration-300"
                                enter-from-class="opacity-0 -translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition duration-200"
                                leave-from-class="opacity-100"
                                leave-to-class="opacity-0"
                            >
                                <p v-if="newsletterSuccess" class="flex items-center justify-center gap-1.5 text-emerald-400 text-xs font-semibold mt-3">
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

            <!-- ───────── 4. Trust strip ───────── -->
            <div class="border-t border-white/[0.05] bg-black/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    <div class="grid grid-cols-3 sm:grid-cols-6 gap-x-4 gap-y-3 items-center">
                        <div
                            v-for="badge in trustBadges"
                            :key="badge.label"
                            class="flex items-center justify-center gap-1.5 text-white/55"
                        >
                            <span v-if="badge.live" class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                            </span>
                            <svg
                                v-else
                                class="w-3.5 h-3.5 shrink-0 text-[#C4A265]/80"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" :d="badge.icon" />
                            </svg>
                            <span class="text-[10px] sm:text-xs font-semibold tracking-wide whitespace-nowrap">{{ badge.label }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ───────── 5. Legal bar ───────── -->
            <div class="border-t border-white/[0.05]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-xs sm:text-sm text-white/45">
                        <p class="text-center sm:text-start">
                            &copy; {{ new Date().getFullYear() }} Doctorato
                            <span class="text-white/20 mx-1.5">·</span>
                            <span>{{ isAr ? 'منتج من Markeza Group' : 'A Markeza Group product' }}</span>
                        </p>
                        <div class="flex items-center gap-4 sm:gap-5">
                            <Link href="/privacy" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.privacy') }}</Link>
                            <span class="w-px h-3 bg-white/15"></span>
                            <Link href="/terms" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.terms') }}</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* Stagger reveal — each block fades up in sequence via --delay */
.footer-fade {
    animation: footer-fade-up 0.9s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--delay, 0ms);
}
@keyframes footer-fade-up {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Logo color tweak — assumes the white logo PNG; if the file is
   dark, swap to filter: brightness(0) invert(1). */
.logo-white {
    /* No-op marker so we can target it for future tweaks. */
}

/* Honor reduce-motion */
@media (prefers-reduced-motion: reduce) {
    .footer-fade { animation: none; }
}
</style>
