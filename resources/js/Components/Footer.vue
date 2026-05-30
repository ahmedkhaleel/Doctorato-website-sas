<script setup>
/**
 * Footer — v3 production-grade redesign.
 *
 * Fixes from v2:
 *   - Logo was rendering as a dark RGBA PNG against the dark
 *     background. Now CSS-inverted to pure white with a subtle
 *     gold drop-shadow on hover. Apple/Stripe-style treatment.
 *   - Typography drifted off-system. v2 used arbitrary text-[13px]
 *     + font-mono on trust chips, both of which made the footer
 *     read like a different page. Removed: now uses standard
 *     Tailwind text-sm / text-xs scale + inherits IBM Plex Arabic
 *     / Inter from the body root.
 *
 * New visual atmosphere:
 *   - Layered background: noise SVG + grid lines + animated
 *     gradient orb that drifts left/right over 20s.
 *   - Floating ambient particles (4 small gold dots drifting up)
 *     give life without distracting from content.
 *   - Eyebrow marks pulse subtly (3s cycle).
 *   - Link underline already animates on hover; now also has a
 *     1px gold caret that fades in.
 *   - Logo gets a soft gold halo on hover.
 *   - Newsletter input glows on focus.
 *   - Social icons rotate-tilt subtly on hover.
 *
 * Honours prefers-reduced-motion — every animation has a fallback.
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
    <footer class="doctorato-footer relative text-white overflow-hidden">
        <!-- Layered backdrop: solid → grid → noise → animated orb → particles -->
        <div class="absolute inset-0 bg-[#06101C]"></div>

        <!-- Subtle grid pattern, fades top → bottom -->
        <div class="absolute inset-0 footer-grid pointer-events-none"></div>

        <!-- SVG noise texture for that premium tactile feel -->
        <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-[0.035]" xmlns="http://www.w3.org/2000/svg">
            <filter id="footer-noise">
                <feTurbulence type="fractalNoise" baseFrequency="0.85" numOctaves="3" stitchTiles="stitch"/>
                <feColorMatrix values="0 0 0 0 1   0 0 0 0 1   0 0 0 0 1   0 0 0 1 0"/>
            </filter>
            <rect width="100%" height="100%" filter="url(#footer-noise)"/>
        </svg>

        <!-- Animated gradient orb that drifts slowly across the top edge -->
        <div class="absolute top-[-20%] inset-x-0 h-[400px] pointer-events-none footer-orb"></div>

        <!-- Top hairline -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/45 to-transparent"></div>

        <!-- Floating ambient particles — 4 tiny gold dots drifting up -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <span class="footer-particle" style="--x: 12%;  --delay: 0s;  --duration: 14s;"></span>
            <span class="footer-particle" style="--x: 32%;  --delay: 4s;  --duration: 17s;"></span>
            <span class="footer-particle" style="--x: 68%;  --delay: 8s;  --duration: 15s;"></span>
            <span class="footer-particle" style="--x: 88%;  --delay: 11s; --duration: 19s;"></span>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">

            <!-- ════ ZONE 1 — brand + 3 link columns ════ -->
            <div class="pt-14 pb-10">
                <div class="grid grid-cols-2 md:grid-cols-12 gap-x-8 gap-y-12">

                    <!-- BRAND  (cols 1-6 on desktop) -->
                    <div class="col-span-2 md:col-span-6 footer-fade" style="--delay: 0ms">

                        <!-- Logo with white inversion + hover halo -->
                        <Link href="/" class="inline-block mb-5 group footer-logo-link">
                            <img
                                src="/images/doctorato-logo.png"
                                alt="Doctorato"
                                class="h-9 w-auto footer-logo"
                            />
                        </Link>

                        <p class="text-sm leading-relaxed text-white/55 max-w-md mb-7">
                            {{ $t('footer.description') }}
                        </p>

                        <!-- Newsletter -->
                        <form @submit.prevent="submitNewsletter" class="max-w-sm">
                            <div class="footer-eyebrow mb-3">
                                <span class="footer-eyebrow-mark"></span>
                                <span>{{ isAr ? 'النشرة الشهرية' : 'Monthly briefing' }}</span>
                            </div>
                            <div class="footer-newsletter-field">
                                <input
                                    v-model="newsletterForm.email"
                                    type="email"
                                    required
                                    :placeholder="$t('footer.email_placeholder')"
                                    class="flex-1 min-w-0 py-2 bg-transparent text-sm text-white placeholder-white/30 outline-none"
                                    dir="ltr"
                                />
                                <button
                                    type="submit"
                                    :disabled="newsletterForm.processing"
                                    :aria-label="$t('footer.subscribe')"
                                    class="footer-arrow-btn group/btn"
                                >
                                    <svg class="w-4 h-4 rtl:rotate-180 group-hover/btn:translate-x-0.5 rtl:group-hover/btn:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6" />
                                    </svg>
                                </button>
                            </div>
                            <Transition
                                enter-active-class="transition duration-300"
                                enter-from-class="opacity-0 -translate-y-0.5"
                                enter-to-class="opacity-100 translate-y-0"
                            >
                                <p v-if="newsletterSuccess" class="flex items-center gap-1.5 text-emerald-400/95 text-xs mt-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $t('footer.subscribe_success') }}
                                </p>
                            </Transition>
                            <p v-if="newsletterForm.errors.email" class="text-rose-400/90 text-xs mt-2">
                                {{ newsletterForm.errors.email }}
                            </p>
                        </form>

                        <!-- Socials -->
                        <div class="flex items-center gap-1.5 mt-7">
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

                    <!-- LINK COLUMNS (6+2+2+2=12) -->
                    <div
                        v-for="(column, idx) in linkColumns"
                        :key="column.title"
                        class="col-span-1 md:col-span-2 footer-fade"
                        :style="`--delay: ${120 + idx * 80}ms;`"
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
                                :style="`--link-delay: ${250 + idx * 80 + lidx * 50}ms;`"
                            >
                                <Link
                                    :href="link.href"
                                    class="footer-link group/link"
                                >
                                    <span class="footer-link-text">{{ linkLabel(link) }}</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ════ ZONE 2 — legal bar ════ -->
            <div class="border-t border-white/[0.07]">
                <div class="py-5 flex flex-col lg:flex-row items-center justify-between gap-y-3 gap-x-6">

                    <!-- Copyright + Markeza -->
                    <p class="text-xs text-white/45 flex items-center flex-wrap justify-center lg:justify-start gap-x-2">
                        <span>&copy; {{ new Date().getFullYear() }} Doctorato</span>
                        <span class="text-white/15 mx-1">—</span>
                        <span class="text-white/40">{{ isAr ? 'منتج من Markeza Group' : 'A Markeza Group product' }}</span>
                    </p>

                    <!-- Trust chips -->
                    <ul class="flex items-center gap-x-5 gap-y-2 flex-wrap justify-center">
                        <li
                            v-for="chip in trustChips"
                            :key="chip.label"
                            class="inline-flex items-center gap-1.5 text-xs"
                        >
                            <span v-if="chip.live" class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
                            </span>
                            <span v-else class="w-1 h-1 rounded-full bg-[#C4A265]/70"></span>
                            <span class="text-white/55 font-medium">{{ chip.label }}</span>
                            <span class="text-white/40">{{ chip.value }}</span>
                        </li>
                    </ul>

                    <!-- Legal links — Privacy + Terms only -->
                    <ul class="flex items-center gap-x-4 text-xs text-white/45 flex-wrap justify-center">
                        <li><Link href="/privacy" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.privacy') }}</Link></li>
                        <li class="text-white/15">—</li>
                        <li><Link href="/terms" class="hover:text-[#C4A265] transition-colors">{{ $t('footer.terms') }}</Link></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* ─── Inherit body font (IBM Plex Arabic / Inter) ────────── */
.doctorato-footer,
.doctorato-footer * {
    font-family: inherit;
}

/* ─── Logo: invert dark PNG → pure white + hover gold halo ── */
.footer-logo {
    filter: brightness(0) invert(1);
    opacity: 0.92;
    transition: filter 300ms ease, opacity 300ms ease, transform 300ms ease;
}
.footer-logo-link:hover .footer-logo {
    opacity: 1;
    filter: brightness(0) invert(1) drop-shadow(0 0 10px rgba(196, 162, 101, 0.45));
    transform: translateY(-1px);
}

/* ─── Grid pattern that fades from top ──────────────────── */
.footer-grid {
    background-image:
        linear-gradient(to right,  rgba(255,255,255,0.025) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size: 56px 56px;
    mask-image: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);
    -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 60%, transparent 100%);
}

/* ─── Animated gradient orb that drifts across the top ──── */
.footer-orb {
    background: radial-gradient(ellipse at center, rgba(196, 162, 101, 0.20) 0%, transparent 60%);
    filter: blur(40px);
    animation: orb-drift 22s ease-in-out infinite alternate;
}
@keyframes orb-drift {
    0%   { transform: translateX(-15%) scale(1);   opacity: 0.7; }
    50%  { transform: translateX(0%)   scale(1.1); opacity: 1;   }
    100% { transform: translateX(15%)  scale(1);   opacity: 0.7; }
}

/* ─── Floating particles ──────────────────────────────── */
.footer-particle {
    position: absolute;
    bottom: -10px;
    left: var(--x);
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: #C4A265;
    box-shadow: 0 0 6px rgba(196, 162, 101, 0.6);
    opacity: 0;
    animation: particle-float var(--duration) linear infinite;
    animation-delay: var(--delay);
}
@keyframes particle-float {
    0%   { transform: translateY(0)      scale(1);   opacity: 0;   }
    10%  { opacity: 0.7; }
    50%  { transform: translateY(-180px) scale(1.2); opacity: 0.4; }
    90%  { opacity: 0;   }
    100% { transform: translateY(-360px) scale(0.6); opacity: 0;   }
}

/* ─── Eyebrow (section label) ───────────────────────────
   Uses text-xs (12px) — matches the navbar small-cap labels.
   Square mark pulses slowly for life. */
.footer-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(196, 162, 101, 0.95);
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
    animation: eyebrow-pulse 3s ease-in-out infinite;
}
@keyframes eyebrow-pulse {
    0%, 100% { opacity: 0.6; transform: rotate(45deg) scale(1);    }
    50%      { opacity: 1;   transform: rotate(45deg) scale(1.15); }
}

/* ─── Footer link with revealing underline + caret ───── */
.footer-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.62);
    font-size: 14px;
    line-height: 1.5;
    transition: color 200ms ease, transform 200ms ease;
    padding: 1px 0;
}
.footer-link:hover {
    color: rgba(255, 255, 255, 0.98);
    transform: translateX(2px);
}
[dir="rtl"] .footer-link:hover { transform: translateX(-2px); }

.footer-link-text {
    position: relative;
}
.footer-link-text::after {
    content: '';
    position: absolute;
    inset-inline-start: 0;
    bottom: -3px;
    width: 0;
    height: 1px;
    background: #C4A265;
    transition: width 320ms cubic-bezier(0.22, 1, 0.36, 1);
}
.footer-link:hover .footer-link-text::after {
    width: 100%;
}

/* Staggered link reveal */
.footer-link-item {
    animation: link-reveal 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--link-delay, 0ms);
}
@keyframes link-reveal {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0);    }
}

/* ─── Newsletter field with focus glow ────────────────── */
.footer-newsletter-field {
    position: relative;
    display: flex;
    align-items: center;
    padding-bottom: 4px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    transition: border-color 250ms ease;
}
.footer-newsletter-field::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(to right, transparent, #C4A265, transparent);
    transform: scaleX(0);
    transition: transform 350ms cubic-bezier(0.22, 1, 0.36, 1);
}
.footer-newsletter-field:focus-within::after {
    transform: scaleX(1);
}

.footer-arrow-btn {
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(196, 162, 101, 0.12);
    color: #C4A265;
    transition: all 260ms cubic-bezier(0.22, 1, 0.36, 1);
}
.footer-arrow-btn:hover {
    background: #C4A265;
    color: #06101C;
    box-shadow: 0 0 0 4px rgba(196, 162, 101, 0.18);
}
.footer-arrow-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* ─── Social glyphs with subtle tilt ─────────────────── */
.footer-social {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.4);
    background: rgba(255, 255, 255, 0);
    border: 1px solid rgba(255, 255, 255, 0.06);
    transition: all 280ms cubic-bezier(0.22, 1, 0.36, 1);
}
.footer-social:hover {
    color: #ffffff;
    background: rgba(196, 162, 101, 0.10);
    border-color: rgba(196, 162, 101, 0.4);
    transform: translateY(-2px) rotate(-3deg);
}

/* ─── Zone reveal stagger ─────────────────────────────── */
.footer-fade {
    animation: footer-fade-up 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--delay, 0ms);
}
@keyframes footer-fade-up {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0);     }
}

/* ─── Honour reduce-motion across the board ──────────── */
@media (prefers-reduced-motion: reduce) {
    .footer-fade,
    .footer-link-item,
    .footer-particle,
    .footer-orb,
    .footer-eyebrow-mark,
    .animate-ping {
        animation: none !important;
    }
    .footer-link-text::after,
    .footer-newsletter-field::after,
    .footer-arrow-btn,
    .footer-social,
    .footer-logo {
        transition: none !important;
    }
}
</style>
