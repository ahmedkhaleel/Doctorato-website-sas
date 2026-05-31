<script setup>
/**
 * Footer — v4 brand-aligned redesign.
 *
 * Previous attempts (v1-v3) built a dark navy footer with gold
 * accents — disconnected from the site identity. The Doctorato
 * brand system actually is:
 *   --color-primary    #1B4F72  (medical blue, body links + CTAs)
 *   --color-secondary  #C4A265  (champagne gold, accents + CTAs)
 *   --color-dark       #1C2833
 *   --color-light-blue #EBF5FB
 *   Fonts: IBM Plex Sans Arabic (AR) / Inter (EN)
 *
 * v4 lives in that brand palette: a quiet cream-tinted background
 * with primary blue text + gold accents, modelled after Stripe,
 * Plaid, and Notion's footers. The logo renders in its original
 * colour (no inversion hack needed). Typography inherits from
 * the body root so the footer reads as the same page, not a
 * separate dark zone.
 *
 * Atmosphere is restrained — a single primary-blue radial wash
 * top-start + a tiny dot grid at 0.04 opacity. No animated orbs,
 * no floating particles. International B2B SaaS footers don't
 * need atmospheric tricks; they need precise typography and
 * crisp use of brand colour.
 */
import { ref, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t, locale, te } = useI18n();
const isAr = computed(() => locale.value === 'ar');

function linkLabel(link) {
    if (link.label && te(link.label)) return t(link.label);
    if (link.fallback) return isAr.value ? link.fallback.ar : link.fallback.en;
    return link.label;
}

const newsletterForm = useForm({ email: '' });
const newsletterSuccess = ref(false);
function submitNewsletter() {
    newsletterForm.post('/newsletter', {
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
    { label: '_addons', href: '/add-ons', fallback: { ar: 'الإضافات', en: 'Add-ons' } },
    { label: 'footer.link_dental', href: '/dental', fallback: { ar: 'عيادات الأسنان', en: 'Dental' } },
    { label: 'footer.link_pediatrics', href: '/pediatrics', fallback: { ar: 'طب الأطفال', en: 'Pediatrics' } },
    { label: 'footer.link_telemedicine', href: '/telemedicine', fallback: { ar: 'الاستشارات الأون لاين', en: 'Telemedicine' } },
    { label: 'footer.link_obstetrics', href: '/obstetrics', fallback: { ar: 'النساء والتوليد', en: 'Obstetrics & Gynecology' } },
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

const trustChips = computed(() => [
    { label: 'SSL', value: '256-bit' },
    { label: 'GDPR', value: isAr.value ? 'متوافق' : 'ready' },
    { label: isAr.value ? 'استقرار' : 'Uptime', value: '99.9%', live: true },
]);

const linkColumns = computed(() => [
    { title: isAr.value ? 'المنتج' : 'Product',    links: productLinks },
    { title: isAr.value ? 'الموارد' : 'Resources', links: resourcesLinks },
    { title: isAr.value ? 'الشركة' : 'Company',    links: companyLinks },
]);
</script>

<template>
    <footer class="doctorato-footer relative overflow-hidden">

        <!-- Quiet warm cream base — sits between #fff (page bg) and the
             site's existing #EBF5FB. Reads as 'end of page' without
             jumping out as a dark slab. -->
        <div class="absolute inset-0 bg-[#FBFAF6]"></div>

        <!-- Soft primary-blue wash top-start, mirrored for RTL -->
        <div
            class="absolute top-0 w-[640px] h-[480px] pointer-events-none opacity-[0.05]"
            :class="isAr ? 'right-0 translate-x-1/3' : 'left-0 -translate-x-1/3'"
            style="background: radial-gradient(circle at top, #1B4F72 0%, transparent 65%);"
        ></div>

        <!-- Tiny dot grid in primary-blue (very low opacity for tactile feel) -->
        <div
            class="absolute inset-0 pointer-events-none"
            style="background-image: radial-gradient(circle at 1px 1px, rgba(27,79,114,0.10) 1px, transparent 0); background-size: 28px 28px; opacity: 0.4;"
        ></div>

        <!-- Top hairline — gold gradient, signals 'section start' -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/40 to-transparent"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">

            <!-- ════ ZONE 1 — brand + 3 link columns ════ -->
            <div class="pt-14 pb-10">
                <div class="grid grid-cols-2 md:grid-cols-12 gap-x-8 gap-y-12">

                    <!-- BRAND COLUMN (6/12) -->
                    <div class="col-span-2 md:col-span-6 footer-fade" style="--delay: 0ms">

                        <!-- Logo — natural colour, no inversion hack needed -->
                        <Link href="/" class="inline-block mb-5 group footer-logo-link">
                            <img
                                src="/images/doctorato-logo.png"
                                alt="Doctorato"
                                class="h-10 w-auto footer-logo"
                            />
                        </Link>

                        <p class="text-sm leading-relaxed text-[#5A6C7D] max-w-md mb-7">
                            {{ $t('footer.description') }}
                        </p>

                        <!-- Newsletter — boxed input matching the site's CTA aesthetic -->
                        <form @submit.prevent="submitNewsletter" class="max-w-md">
                            <div class="footer-eyebrow mb-3">
                                <span class="footer-eyebrow-mark"></span>
                                <span>{{ isAr ? 'النشرة الشهرية' : 'Monthly briefing' }}</span>
                            </div>
                            <div class="footer-newsletter-field group/news">
                                <input
                                    v-model="newsletterForm.email"
                                    type="email"
                                    required
                                    :placeholder="$t('footer.email_placeholder')"
                                    class="flex-1 min-w-0 px-4 py-2.5 bg-transparent text-sm text-[#1C2833] placeholder-[#8B9BAC] outline-none"
                                    dir="ltr"
                                />
                                <button
                                    type="submit"
                                    :disabled="newsletterForm.processing"
                                    class="footer-cta group/btn"
                                >
                                    <span>{{ $t('footer.subscribe') }}</span>
                                    <svg class="w-3.5 h-3.5 rtl:rotate-180 group-hover/btn:translate-x-0.5 rtl:group-hover/btn:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6" />
                                    </svg>
                                </button>
                            </div>
                            <Transition
                                enter-active-class="transition duration-300"
                                enter-from-class="opacity-0"
                                enter-to-class="opacity-100"
                            >
                                <p v-if="newsletterSuccess" class="flex items-center gap-1.5 text-emerald-700 text-xs font-medium mt-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $t('footer.subscribe_success') }}
                                </p>
                            </Transition>
                            <p v-if="newsletterForm.errors.email" class="text-rose-600 text-xs mt-2">
                                {{ newsletterForm.errors.email }}
                            </p>
                        </form>

                        <!-- Socials -->
                        <div class="flex items-center gap-2 mt-8">
                            <a
                                v-for="social in socialLinks"
                                :key="social.name"
                                :href="social.href"
                                :aria-label="social.name"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="footer-social"
                            >
                                <svg class="w-[15px] h-[15px] fill-current" viewBox="0 0 24 24">
                                    <path :d="social.icon" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- LINK COLUMNS (6+2+2+2) -->
                    <div
                        v-for="(column, idx) in linkColumns"
                        :key="column.title"
                        class="col-span-1 md:col-span-2 footer-fade"
                        :style="`--delay: ${100 + idx * 70}ms;`"
                    >
                        <div class="footer-eyebrow mb-5">
                            <span class="footer-eyebrow-mark"></span>
                            <span>{{ column.title }}</span>
                        </div>
                        <ul class="space-y-3">
                            <li
                                v-for="(link, lidx) in column.links"
                                :key="link.href"
                                class="footer-link-item"
                                :style="`--link-delay: ${220 + idx * 70 + lidx * 40}ms;`"
                            >
                                <Link :href="link.href" class="footer-link group/link">
                                    <span class="footer-link-text">{{ linkLabel(link) }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ════ ZONE 2 — legal bar ════ -->
            <div class="border-t border-[#1B4F72]/8">
                <div class="py-5 flex flex-col lg:flex-row items-center justify-between gap-y-3 gap-x-6">

                    <!-- Copyright + Markeza -->
                    <p class="text-xs text-[#8B9BAC] flex items-center flex-wrap justify-center lg:justify-start gap-x-2">
                        <span>&copy; {{ new Date().getFullYear() }}</span>
                        <span class="font-semibold text-[#1B4F72]">Doctorato</span>
                        <span class="text-[#C4A265]/50 mx-1">—</span>
                        <span>{{ isAr ? 'منتج من Markeza Group' : 'A Markeza Group product' }}</span>
                    </p>

                    <!-- Trust chips -->
                    <ul class="flex items-center gap-x-5 gap-y-2 flex-wrap justify-center">
                        <li
                            v-for="chip in trustChips"
                            :key="chip.label"
                            class="inline-flex items-center gap-1.5 text-xs"
                        >
                            <span v-if="chip.live" class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-500 opacity-50"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                            </span>
                            <span v-else class="w-1 h-1 rounded-full bg-[#C4A265]"></span>
                            <span class="text-[#1B4F72] font-semibold">{{ chip.label }}</span>
                            <span class="text-[#8B9BAC]">{{ chip.value }}</span>
                        </li>
                    </ul>

                    <!-- Legal links — classic two-link bar -->
                    <ul class="flex items-center gap-x-4 text-xs text-[#8B9BAC] flex-wrap justify-center">
                        <li><Link href="/privacy" class="hover:text-[#1B4F72] transition-colors">{{ $t('footer.privacy') }}</Link></li>
                        <li class="text-[#C4A265]/40">—</li>
                        <li><Link href="/terms" class="hover:text-[#1B4F72] transition-colors">{{ $t('footer.terms') }}</Link></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* ─── Inherit body font (IBM Plex Arabic / Inter) ──────── */
.doctorato-footer,
.doctorato-footer * {
    font-family: inherit;
}

/* ─── Logo: natural colour + subtle lift on hover ──────── */
.footer-logo {
    transition: opacity 250ms ease, transform 250ms ease;
    opacity: 0.95;
}
.footer-logo-link:hover .footer-logo {
    opacity: 1;
    transform: translateY(-1px);
}

/* ─── Eyebrow (section label) ──────────────────────────
   Gold square mark + uppercase title using the site's brand
   colour. Inherits Inter/IBM Plex from <body>. */
.footer-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #A88B4A;  /* secondary-dark — better contrast on cream */
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.22em;
    line-height: 1;
}
.footer-eyebrow-mark {
    display: inline-block;
    width: 6px;
    height: 6px;
    background: #C4A265;
    transform: rotate(45deg);
    flex-shrink: 0;
    animation: eyebrow-pulse 3.2s ease-in-out infinite;
}
@keyframes eyebrow-pulse {
    0%, 100% { opacity: 0.6;  transform: rotate(45deg) scale(1);    }
    50%      { opacity: 1;    transform: rotate(45deg) scale(1.15); }
}

/* ─── Footer link with primary-blue hover + gold underline ── */
.footer-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    color: #4A5C6D;
    font-size: 14px;
    line-height: 1.5;
    transition: color 220ms ease, transform 220ms ease;
    padding: 1px 0;
}
.footer-link:hover {
    color: #1B4F72;            /* primary blue, brand */
    transform: translateX(2px);
}
[dir="rtl"] .footer-link:hover { transform: translateX(-2px); }

.footer-link-text { position: relative; }
.footer-link-text::after {
    content: '';
    position: absolute;
    inset-inline-start: 0;
    bottom: -3px;
    width: 0;
    height: 1px;
    background: #C4A265;       /* gold underline */
    transition: width 320ms cubic-bezier(0.22, 1, 0.36, 1);
}
.footer-link:hover .footer-link-text::after { width: 100%; }

/* Staggered link reveal */
.footer-link-item {
    animation: link-reveal 0.55s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--link-delay, 0ms);
}
@keyframes link-reveal {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0);    }
}

/* ─── Newsletter — pill field with embedded gold CTA ─── */
.footer-newsletter-field {
    display: flex;
    align-items: center;
    gap: 0;
    padding: 4px;
    background: #ffffff;
    border: 1px solid rgba(27, 79, 114, 0.12);
    border-radius: 999px;
    transition: border-color 260ms ease, box-shadow 260ms ease;
    box-shadow: 0 1px 2px rgba(27, 79, 114, 0.04);
}
.footer-newsletter-field:focus-within {
    border-color: rgba(196, 162, 101, 0.55);
    box-shadow:
        0 0 0 4px rgba(196, 162, 101, 0.10),
        0 4px 12px rgba(27, 79, 114, 0.06);
}

/* The button uses the site's secondary-CTA shape from Navbar.vue
   (bg-secondary rounded-full text-white). Matches the 'Request demo'
   button so the footer CTA reads as a sibling. */
.footer-cta {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.55rem 1.1rem;
    background: #C4A265;
    color: #ffffff;
    font-size: 13px;
    font-weight: 700;
    border-radius: 999px;
    transition: all 280ms cubic-bezier(0.22, 1, 0.36, 1);
    white-space: nowrap;
}
.footer-cta:hover {
    background: #A88B4A;       /* secondary-dark */
    box-shadow: 0 6px 18px rgba(196, 162, 101, 0.35);
    transform: translateY(-1px);
}
.footer-cta:disabled {
    opacity: 0.55;
    cursor: not-allowed;
    transform: none;
}

/* ─── Social glyphs — primary-blue tinted on hover ──── */
.footer-social {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #5A6C7D;
    background: #ffffff;
    border: 1px solid rgba(27, 79, 114, 0.10);
    transition: all 260ms cubic-bezier(0.22, 1, 0.36, 1);
}
.footer-social:hover {
    color: #ffffff;
    background: #1B4F72;
    border-color: #1B4F72;
    box-shadow: 0 4px 12px rgba(27, 79, 114, 0.18);
    transform: translateY(-2px);
}

/* ─── Zone fade-up stagger ─────────────────────────── */
.footer-fade {
    animation: footer-fade-up 0.75s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--delay, 0ms);
}
@keyframes footer-fade-up {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0);    }
}

/* ─── Honour reduce-motion across the board ────────── */
@media (prefers-reduced-motion: reduce) {
    .footer-fade,
    .footer-link-item,
    .footer-eyebrow-mark,
    .animate-ping {
        animation: none !important;
    }
    .footer-link,
    .footer-link-text::after,
    .footer-newsletter-field,
    .footer-cta,
    .footer-social,
    .footer-logo {
        transition: none !important;
    }
}
</style>
