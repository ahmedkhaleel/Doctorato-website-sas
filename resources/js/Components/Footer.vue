<script setup>
/**
 * Footer — editorial-restraint redesign.
 *
 * Compact two-zone composition:
 *
 *   ZONE 1 (~280px)
 *   ┌──────────────────────────────────────────────────────────┐
 *   │ Brand col (logo + tagline + inline newsletter + socials) │ Product │ Resources │ Company │
 *   └──────────────────────────────────────────────────────────┘
 *
 *   ZONE 2 (~60px)
 *   ┌──────────────────────────────────────────────────────────┐
 *   │ © · Markeza · [SSL] [GDPR] [99.9%]  ·  Privacy · Terms   │
 *   └──────────────────────────────────────────────────────────┘
 *
 * Why this is shorter than the previous 5-section stack:
 *   - Trust badges + newsletter + brand row + link grid were 4
 *     stacked sections with their own padding. Each cost ~110px.
 *   - Folding newsletter inline under the brand saves a full row.
 *   - Folding trust badges into the legal bar saves another row.
 *   - Single bottom hairline instead of section dividers everywhere.
 *
 * The brand col carries all the "extras" (newsletter, social,
 * compliance tone). Link columns stay clean — uniform grid of names
 * with a single gold-dot hover accent.
 *
 * Restraint discipline:
 *   - Gold (#C4A265) reserved for eyebrows, hover dots, top hairline,
 *     single trust glyph. Nothing else.
 *   - No aurora orbs; one quiet top-left radial vignette.
 *   - Typography: tight tracking on eyebrows (0.22em), generous
 *     leading on body. Display weight only on the brand wordmark.
 *   - Stagger reveal limited to brand + 3 link columns. No per-link
 *     reveal — that's where the previous footer felt fussy.
 */
import { ref, computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale, te } = useI18n();
const isAr = computed(() => locale.value === 'ar');

const page = usePage();

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

const productLinks = [
    { label: 'nav.features', href: '/features' },
    { label: 'nav.pricing', href: '/pricing' },
    { label: 'footer.link_dental', href: '/dental', fallback: { ar: 'عيادات الأسنان', en: 'Dental' } },
    { label: 'footer.link_pediatrics', href: '/pediatrics', fallback: { ar: 'طب الأطفال', en: 'Pediatrics' } },
    { label: 'footer.link_telemedicine', href: '/telemedicine', fallback: { ar: 'الاستشارات الأون لاين', en: 'Telemedicine' } },
];

const resourcesLinks = [
    { label: 'nav.blog', href: '/blog' },
    { label: 'footer.link_glossary', href: '/glossary', fallback: { ar: 'قاموس المصطلحات', en: 'Glossary' } },
    { label: 'footer.link_compare', href: '/compare', fallback: { ar: 'مقارنة الأنظمة', en: 'Compare' } },
    { label: 'nav.faq', href: '/faq', fallback: { ar: 'الأسئلة الشائعة', en: 'FAQ' } },
    { label: 'nav.reports', href: '/reports', fallback: { ar: 'التقارير', en: 'Reports' } },
];

const companyLinks = [
    { label: 'nav.about', href: '/about', fallback: { ar: 'عن دكتوراتو', en: 'About' } },
    { label: 'footer.contact_us', href: '/contact' },
    { label: 'footer.request_demo', href: '/demo' },
    { label: 'nav.technology', href: '/technology' },
    { label: '_status', href: '/status', fallback: { ar: 'حالة الخدمة', en: 'Status' } },
];

const socialLinks = [
    { name: 'Twitter',   href: '#', icon: 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z' },
    { name: 'Instagram', href: '#', icon: 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z' },
    { name: 'LinkedIn',  href: '#', icon: 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z' },
    { name: 'YouTube',   href: '#', icon: 'M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z' },
];

// Compact compliance pills folded into the bottom bar.
// Live-uptime gets the only animated element on the page (a slow pulse).
const trustChips = computed(() => [
    { label: 'SSL · 256-bit' },
    { label: isAr.value ? 'GDPR متوافق' : 'GDPR-ready' },
    { label: isAr.value ? '99.9% استقرار' : '99.9% uptime', live: true },
]);

const linkColumns = computed(() => [
    { title: isAr.value ? 'المنتج' : 'Product',    links: productLinks },
    { title: isAr.value ? 'الموارد' : 'Resources', links: resourcesLinks },
    { title: isAr.value ? 'الشركة' : 'Company',    links: companyLinks },
]);
</script>

<template>
    <footer class="relative text-white bg-[#0A1320] overflow-hidden">
        <!-- Top hairline — single gold gradient as a quiet section break. -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/40 to-transparent"></div>

        <!-- One quiet radial vignette top-start. No aurora orbs, no dot grid.
             The previous version stacked three glow elements. -->
        <div
            class="absolute pointer-events-none w-[680px] h-[680px] rounded-full opacity-[0.18] -translate-y-1/2"
            :class="isAr ? 'right-0 translate-x-1/3' : 'left-0 -translate-x-1/3'"
            style="background: radial-gradient(circle, rgba(196,162,101,0.20) 0%, rgba(196,162,101,0) 70%);"
        ></div>

        <div class="relative">
            <!-- ════ ZONE 1 — brand + link columns ════ -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-14 pb-10">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-x-8 gap-y-10">

                    <!-- Brand column (5 of 12) — logo + tagline + inline newsletter + socials -->
                    <div class="md:col-span-5 footer-fade" style="--delay: 0ms">
                        <Link href="/" class="inline-block mb-4 group">
                            <img
                                src="/images/doctorato-logo.png"
                                alt="Doctorato"
                                class="h-9 w-auto group-hover:opacity-90 transition-opacity"
                            />
                        </Link>
                        <p class="text-[13px] leading-[1.7] text-white/50 max-w-md mb-5">
                            {{ $t('footer.description') }}
                        </p>

                        <!-- Compact inline newsletter — pill input, arrow button -->
                        <form @submit.prevent="submitNewsletter" class="max-w-md">
                            <p class="text-[10px] uppercase tracking-[0.22em] text-[#C4A265]/90 font-bold mb-2">
                                {{ isAr ? 'النشرة الشهرية' : 'Monthly briefing' }}
                            </p>
                            <div class="relative flex items-center bg-white/[0.03] border border-white/[0.08] rounded-full focus-within:border-[#C4A265]/45 transition-colors h-11">
                                <input
                                    v-model="newsletterForm.email"
                                    type="email"
                                    required
                                    :placeholder="$t('footer.email_placeholder')"
                                    class="flex-1 min-w-0 px-4 bg-transparent text-[13px] text-white placeholder-white/30 outline-none"
                                    dir="ltr"
                                />
                                <button
                                    type="submit"
                                    :disabled="newsletterForm.processing"
                                    :aria-label="$t('footer.subscribe')"
                                    class="group/btn flex items-center justify-center w-9 h-9 mx-1 rounded-full bg-[#C4A265] hover:bg-[#D4B876] text-[#0A1320] transition-colors disabled:opacity-50"
                                >
                                    <svg class="w-3.5 h-3.5 rtl:rotate-180 group-hover/btn:translate-x-0.5 rtl:group-hover/btn:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6" />
                                    </svg>
                                </button>
                            </div>
                            <Transition
                                enter-active-class="transition duration-300"
                                enter-from-class="opacity-0 -translate-y-0.5"
                                enter-to-class="opacity-100 translate-y-0"
                            >
                                <p v-if="newsletterSuccess" class="flex items-center gap-1.5 text-emerald-400/90 text-[11px] font-medium mt-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $t('footer.subscribe_success') }}
                                </p>
                            </Transition>
                            <p v-if="newsletterForm.errors.email" class="text-rose-400/90 text-[11px] mt-2">
                                {{ newsletterForm.errors.email }}
                            </p>
                        </form>

                        <!-- Socials — inline, smaller than the previous 40px buttons. -->
                        <div class="flex items-center gap-1 mt-6">
                            <a
                                v-for="social in socialLinks"
                                :key="social.name"
                                :href="social.href"
                                :aria-label="social.name"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group/social w-8 h-8 rounded-md flex items-center justify-center text-white/35 hover:text-white hover:bg-white/[0.04] transition-colors"
                            >
                                <svg class="w-[14px] h-[14px] fill-current" viewBox="0 0 24 24">
                                    <path :d="social.icon" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Link columns (7 of 12, split 3-way) — visually identical mini-mastheads. -->
                    <div
                        v-for="(column, idx) in linkColumns"
                        :key="column.title"
                        class="md:col-span-7 md:col-start-auto"
                        :class="[
                            'footer-fade',
                            idx === 0 ? 'md:col-span-2 md:col-start-7' : '',
                            idx === 1 ? 'md:col-span-2 md:col-start-9' : '',
                            idx === 2 ? 'md:col-span-2 md:col-start-11' : '',
                        ]"
                        :style="`--delay: ${120 + idx * 70}ms;`"
                    >
                        <h4 class="text-[10px] font-bold uppercase tracking-[0.22em] text-[#C4A265]/90 mb-4">
                            {{ column.title }}
                        </h4>
                        <ul class="space-y-2.5">
                            <li v-for="link in column.links" :key="link.href">
                                <Link
                                    :href="link.href"
                                    class="group/link flex items-center gap-2 text-[13px] text-white/55 hover:text-white transition-colors"
                                >
                                    <span class="w-1 h-1 rounded-full bg-[#C4A265] opacity-0 group-hover/link:opacity-100 transition-opacity"></span>
                                    <span class="leading-snug">{{ linkLabel(link) }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ════ ZONE 2 — single thin legal bar with folded-in trust chips ════ -->
            <div class="border-t border-white/[0.06]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex flex-col lg:flex-row items-center justify-between gap-3 text-[11px]">

                        <!-- Left: copyright + Markeza badge -->
                        <p class="text-white/40 flex items-center flex-wrap justify-center lg:justify-start gap-x-2 gap-y-1">
                            <span>&copy; {{ new Date().getFullYear() }} Doctorato</span>
                            <span class="text-white/15">·</span>
                            <span>{{ isAr ? 'منتج من Markeza Group' : 'A Markeza Group product' }}</span>
                        </p>

                        <!-- Centre: trust chips inline (replaces the old 6-icon row) -->
                        <ul class="flex items-center gap-x-3 gap-y-1.5 flex-wrap justify-center">
                            <li
                                v-for="chip in trustChips"
                                :key="chip.label"
                                class="inline-flex items-center gap-1.5 text-white/45 font-medium tracking-wide"
                            >
                                <span v-if="chip.live" class="relative flex h-1.5 w-1.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
                                </span>
                                <span v-else class="w-1 h-1 rounded-full bg-[#C4A265]/70"></span>
                                <span>{{ chip.label }}</span>
                            </li>
                        </ul>

                        <!-- Right: legal -->
                        <div class="flex items-center gap-3 text-white/40">
                            <Link href="/privacy" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.privacy') }}</Link>
                            <span class="w-px h-2.5 bg-white/15"></span>
                            <Link href="/terms" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.terms') }}</Link>
                            <span class="w-px h-2.5 bg-white/15"></span>
                            <Link href="/sub-processors" class="hover:text-[#C4A265] transition-colors">
                                {{ isAr ? 'الموزّعون الفرعيون' : 'Sub-processors' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* Single, gentle stagger. No per-link reveal. */
.footer-fade {
    animation: footer-fade-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--delay, 0ms);
}
@keyframes footer-fade-up {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Honour reduce-motion. */
@media (prefers-reduced-motion: reduce) {
    .footer-fade { animation: none; }
    .animate-ping { animation: none; }
}
</style>
