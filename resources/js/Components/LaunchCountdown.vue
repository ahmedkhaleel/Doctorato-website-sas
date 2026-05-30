<script setup>
/**
 * Launch-offer countdown banner.
 *
 * Renders a sticky-feel ribbon with:
 *   - 🔥 'Launch offer' label
 *   - countdown DD : HH : MM : SS to the offer's end date
 *   - CTA to view plans
 *
 * Reads the end date from the prop OR from the first launch-active
 * plan in the shared props. Self-disables when:
 *   - prop endsAt is missing
 *   - the deadline has elapsed
 *
 * Dismissible via a cookie (doc_launch_dismissed) so a returning
 * visitor isn't nagged. Cookie life: 7 days so the banner re-appears
 * weekly to recapture attention as the deadline approaches.
 */
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    endsAt: { type: String, default: null },
    /** When true, banner is full-width sticky at the top of the page. */
    sticky: { type: Boolean, default: false },
});

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

const page = usePage();

// Resolve the deadline: explicit prop wins, else the first plan with
// an active launch offer in shared props.
const resolvedEndsAt = computed(() => {
    if (props.endsAt) return props.endsAt;
    const plans = page.props.plans || page.props.launchOffer?.plans || [];
    const first = plans.find?.((p) => p?.launch_offer_ends_at);
    return first?.launch_offer_ends_at ?? null;
});

const COOKIE = 'doc_launch_dismissed';
const dismissed = ref(false);
function readCookie() {
    if (typeof document === 'undefined') return false;
    return document.cookie.split(';').some((c) => c.trim().startsWith(`${COOKIE}=`));
}
function dismiss() {
    const expires = new Date(Date.now() + 7 * 86400 * 1000).toUTCString();
    document.cookie = `${COOKIE}=1; expires=${expires}; path=/; SameSite=Lax`;
    dismissed.value = true;
}

// Tick state — refs because Date.now() at module scope would break
// SSR + the Phase 29 workflow guidance (no Date.now in setup).
const now = ref(typeof window === 'undefined' ? 0 : Date.now());
let tickId = null;

const remainingMs = computed(() => {
    if (!resolvedEndsAt.value) return 0;
    const end = new Date(resolvedEndsAt.value).getTime();
    return Math.max(0, end - now.value);
});

const visible = computed(() => !dismissed.value && remainingMs.value > 0);

const parts = computed(() => {
    const ms = remainingMs.value;
    const d = Math.floor(ms / 86400000);
    const h = Math.floor((ms % 86400000) / 3600000);
    const m = Math.floor((ms % 3600000) / 60000);
    const s = Math.floor((ms % 60000) / 1000);
    return { d, h, m, s };
});

const pad = (n) => String(n).padStart(2, '0');

onMounted(() => {
    dismissed.value = readCookie();
    now.value = Date.now();
    tickId = setInterval(() => { now.value = Date.now(); }, 1000);
});
onUnmounted(() => { if (tickId) clearInterval(tickId); });
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-500 ease-out"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-300 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div
            v-if="visible"
            :class="[
                'relative overflow-hidden',
                sticky ? 'sticky top-0 z-40' : '',
            ]"
        >
            <div class="relative bg-gradient-to-r from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] text-white">
                <!-- Subtle gold shimmer line at top -->
                <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/60 to-transparent"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm">
                    <!-- Launch label with pulsing dot -->
                    <span class="inline-flex items-center gap-2 font-bold">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#C4A265]"></span>
                        </span>
                        <span>🔥 {{ tr('عرض الإطلاق ينتهي خلال', 'Launch offer ends in') }}</span>
                    </span>

                    <!-- Countdown -->
                    <div class="inline-flex items-center gap-2 font-mono tabular-nums">
                        <div class="bg-white/[0.10] backdrop-blur-sm rounded-md px-2 py-1 min-w-[42px] text-center">
                            <span class="text-base font-extrabold">{{ pad(parts.d) }}</span>
                            <span class="block text-[9px] uppercase tracking-wider opacity-70 mt-0.5">{{ tr('يوم', 'd') }}</span>
                        </div>
                        <span class="text-[#C4A265]">:</span>
                        <div class="bg-white/[0.10] backdrop-blur-sm rounded-md px-2 py-1 min-w-[42px] text-center">
                            <span class="text-base font-extrabold">{{ pad(parts.h) }}</span>
                            <span class="block text-[9px] uppercase tracking-wider opacity-70 mt-0.5">{{ tr('س', 'h') }}</span>
                        </div>
                        <span class="text-[#C4A265]">:</span>
                        <div class="bg-white/[0.10] backdrop-blur-sm rounded-md px-2 py-1 min-w-[42px] text-center">
                            <span class="text-base font-extrabold">{{ pad(parts.m) }}</span>
                            <span class="block text-[9px] uppercase tracking-wider opacity-70 mt-0.5">{{ tr('د', 'm') }}</span>
                        </div>
                        <span class="text-[#C4A265]">:</span>
                        <div class="bg-white/[0.10] backdrop-blur-sm rounded-md px-2 py-1 min-w-[42px] text-center">
                            <span class="text-base font-extrabold">{{ pad(parts.s) }}</span>
                            <span class="block text-[9px] uppercase tracking-wider opacity-70 mt-0.5">{{ tr('ث', 's') }}</span>
                        </div>
                    </div>

                    <!-- CTA -->
                    <Link href="/pricing" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-[#C4A265] hover:bg-[#A88B4A] text-white font-bold text-xs transition-colors">
                        {{ tr('شاهد العروض', 'View offers') }}
                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </Link>

                    <button
                        @click="dismiss"
                        :aria-label="tr('إخفاء', 'Dismiss')"
                        class="absolute end-3 top-1/2 -translate-y-1/2 text-white/50 hover:text-white p-1"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
