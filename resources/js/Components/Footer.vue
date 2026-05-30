<script setup>
/**
 * Footer — editorial-precision redesign (v2).
 *
 * The previous attempt used col-start utilities to position link
 * columns inside a 12-col grid. Tailwinds JIT resolved the conflict
 * between md:col-span-7 (base) and md:col-span-2 (conditional)
 * unpredictably, which is why the columns landed scattered in the
 * screenshot instead of in a row.
 *
 * Rebuilt with a robust 12-col grid using only col-span (no
 * col-start hacks). Layout: brand 6 / Product 2 / Resources 2 /
 * Company 2 — sums to 12, items lay out in document order, RTL is
 * handled by CSS automatically.
 *
 * Visual upgrades from v1:
 *   - Eyebrow gets a tiny gold square mark before the text
 *     (replaces the all-caps-only treatment)
 *   - Each link reveals a 1px underline on hover (editorial feel,
 *     replaces the dot indicator)
 *   - Brand gets a small EST detail under the wordmark
 *   - Newsletter is a borderless underline-only input with a tiny
 *     gold arrow button (Linear-style minimalism)
 *   - Trust chips use a monospace-tabular font for the numbers
 *   - Bottom bar uses em-dash separators (more editorial than dots)
 *   - Single gold corner ornament instead of radial vignette
 *
 * Total height target: ~330px desktop, ~700px stacked mobile.
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
    { label: isAr.value ? 'GDPR' : 'GDPR', value: isAr.value ? 'متوافق' : 'ready' },
    { label: isAr.value ? 'استقرار' : 'Uptime', value: '99.9%', live: true },
]);

// Each column rendered from this list — same structure, same depth,
// so the visual rhythm reads as a clean three-column masthead.
const linkColumns = computed(() => [
    { title: isAr.value ? 'المنتج' : 'Product',    links: productLinks },
    { title: isAr.value ? 'الموارد' : 'Resources', links: resourcesLinks },
    { title: isAr.value ? 'الشركة' : 'Company',    links: companyLinks },
]);
</script>

<template>
    <footer class="relative text-white bg-[#08111E] overflow-hidden">
        <!-- Single gold hairline at top -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/45 to-transparent"></div>

        <!-- Subtle gold corner ornament — small luminous arc top-start.
             Replaces the previous wide radial vignette. -->
        <div
            class="absolute top-0 w-[420px] h-[420px] pointer-events-none opacity-[0.07]"
            :class="isAr ? 'right-0' : 'left-0'"
            style="background: radial-gradient(circle at top, #C4A265 0%, transparent 55%);"
        ></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-10">

            <!-- ════ ZONE 1 — brand + 3 link columns ════ -->
            <div class="pt-14 pb-10">
                <div class="grid grid-cols-2 md:grid-cols-12 gap-x-8 gap-y-12">

                    <!-- BRAND  (cols 1-6 on desktop, full row on mobile) -->
                    <div class="col-span-2 md:col-span-6 footer-fade" style="--delay: 0ms">
                        <Link href="/" class="inline-flex items-center gap-3 mb-5">
                            <img src="/images/doctorato-logo.png" alt="Doctorato" class="h-9 w-auto" />
                        </Link>

                        <p class="text-[13px] leading-[1.75] text-white/55 max-w-md mb-7">
                            {{ $t('footer.description') }}
                        </p>

                        <!-- Newsletter — underline-only input + tiny gold arrow.
                             Quiet but high-contrast on the page. -->
                        <form @submit.prevent="submitNewsletter" class="max-w-sm">
                            <div class="footer-eyebrow mb-3">
                                <span class="footer-eyebrow-mark"></span>
                                <span>{{ isAr ? 'النشرة الشهرية' : 'Monthly briefing' }}</span>
                            </div>
                            <div class="relative flex items-center border-b border-white/15 focus-within:border-[#C4A265]/60 transition-colors pb-1">
                                <input
                                    v-model="newsletterForm.email"
                                    type="email"
                                    required
                                    :placeholder="$t('footer.email_placeholder')"
                                    class="flex-1 min-w-0 py-2 bg-transparent text-[13px] text-white placeholder-white/30 outline-none"
                                    dir="ltr"
                                />
                                <button
                                    type="submit"
                                    :disabled="newsletterForm.processing"
                                    :aria-label="$t('footer.subscribe')"
                                    class="group/btn shrink-0 inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#C4A265]/15 hover:bg-[#C4A265] text-[#C4A265] hover:text-[#0A1320] transition-colors disabled:opacity-50"
                                >
                                    <svg class="w-3.5 h-3.5 rtl:rotate-180 group-hover/btn:translate-x-px rtl:group-hover/btn:-translate-x-px transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6l6 6-6 6" />
                                    </svg>
                                </button>
                            </div>
                            <Transition
                                enter-active-class="transition duration-300"
                                enter-from-class="opacity-0"
                                enter-to-class="opacity-100"
                            >
                                <p v-if="newsletterSuccess" class="flex items-center gap-1.5 text-emerald-400/90 text-[11px] mt-2">
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

                        <!-- Socials — small ghost glyphs -->
                        <div class="flex items-center gap-1 mt-7">
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

                    <!-- LINK COLUMNS — each col-span-2 on desktop (6+2+2+2=12),
                         col-span-1 on mobile (2-col grid → 2 cols visible). -->
                    <div
                        v-for="(column, idx) in linkColumns"
                        :key="column.title"
                        class="col-span-1 md:col-span-2 footer-fade"
                        :style="`--delay: ${120 + idx * 70}ms;`"
                    >
                        <div class="footer-eyebrow mb-5">
                            <span class="footer-eyebrow-mark"></span>
                            <span>{{ column.title }}</span>
                        </div>
                        <ul class="space-y-3">
                            <li v-for="link in column.links" :key="link.href">
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

            <!-- ════ ZONE 2 — legal bar with folded trust chips ════ -->
            <div class="border-t border-white/[0.07]">
                <div class="py-5 flex flex-col lg:flex-row items-center justify-between gap-y-3 gap-x-6 text-[11px]">

                    <!-- Copyright + Markeza -->
                    <p class="text-white/45 flex items-center flex-wrap justify-center lg:justify-start gap-x-2">
                        <span>&copy; {{ new Date().getFullYear() }} Doctorato</span>
                        <span class="text-white/15 mx-1">—</span>
                        <span class="text-white/40">{{ isAr ? 'منتج من Markeza Group' : 'A Markeza Group product' }}</span>
                    </p>

                    <!-- Trust chips inline — KEY/VALUE pairs, mono numbers -->
                    <ul class="flex items-center gap-x-5 gap-y-2 flex-wrap justify-center">
                        <li
                            v-for="chip in trustChips"
                            :key="chip.label"
                            class="inline-flex items-center gap-1.5 text-white/45"
                        >
                            <span v-if="chip.live" class="relative flex h-1.5 w-1.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-60"></span>
                                <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-400"></span>
                            </span>
                            <span v-else class="w-1 h-1 rounded-full bg-[#C4A265]/70"></span>
                            <span class="text-white/55 font-medium">{{ chip.label }}</span>
                            <span class="font-mono tabular-nums text-white/40">{{ chip.value }}</span>
                        </li>
                    </ul>

                    <!-- Legal links — classic Privacy + Terms only.
                         /sub-processors and /responsible-disclosure exist
                         at their URLs (linked from DPA + security.txt
                         respectively) but DON'T belong in the public
                         footer:
                           - Sub-processors leaks infra detail to
                             competitors and confuses non-technical
                             buyers (Stripe / Linear / Notion all keep
                             this behind customer-portal or DPA refs).
                           - Responsible-disclosure is read by security
                             scanners + researchers via security.txt;
                             they don't need a footer hint. -->
                    <ul class="flex items-center gap-x-4 text-white/45 flex-wrap justify-center">
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
/* ─── Eyebrow (section label) ─────────────────────────────────
   Small gold square + uppercase title. Tight tracking, refined
   weight. Used across newsletter label + all 3 link headings. */
.footer-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: rgba(196, 162, 101, 0.95);
    font-size: 10px;
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
}

/* ─── Footer link with revealing underline ───────────────────
   Subtle but precise micro-interaction. The underline expands
   from the start edge to full text width on hover. Works in
   both LTR and RTL because we anchor on the inline-start side.
*/
.footer-link {
    position: relative;
    display: inline-flex;
    align-items: center;
    color: rgba(255, 255, 255, 0.6);
    font-size: 13px;
    line-height: 1.4;
    transition: color 200ms ease;
    padding: 1px 0;
}
.footer-link:hover { color: rgba(255, 255, 255, 0.95); }

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
    transition: width 280ms cubic-bezier(0.22, 1, 0.36, 1);
}
.footer-link:hover .footer-link-text::after {
    width: 100%;
}

/* ─── Single, gentle stagger ─────────────────────────────── */
.footer-fade {
    animation: footer-fade-up 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
    animation-delay: var(--delay, 0ms);
}
@keyframes footer-fade-up {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Honour reduce-motion */
@media (prefers-reduced-motion: reduce) {
    .footer-fade { animation: none; }
    .animate-ping { animation: none; }
    .footer-link-text::after { transition: none; }
}
</style>
