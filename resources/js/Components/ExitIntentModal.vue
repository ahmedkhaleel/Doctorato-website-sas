<script setup>
/**
 * Exit-intent modal — the last-chance conversion grab.
 *
 * Fires once per session when the visitor's cursor darts toward
 * the tab bar (y < 10px with upward velocity) on desktop, or when
 * they try to close the tab via the back gesture on mobile (popstate).
 * Shown only on pages where it makes sense: pricing + plan pages +
 * home + demo. Skipped on checkout/success/admin routes.
 *
 * Offers a bumped discount ("15% extra على أول شهر") and a one-click
 * route to /start-trial so the visitor doesn't have to retype
 * anything they might already have open in the demo form.
 *
 * Sets a sessionStorage flag so the modal never nags the same
 * visitor twice in the same session.
 */
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, onMounted, onBeforeUnmount, computed } from 'vue';

const { locale } = useI18n();
const page = usePage();

const STORAGE_KEY = 'doctorato_exit_intent_shown';
const TRIGGER_DELAY_MS = 8000; // minimum time on page before we're willing to intercept
const SKIP_ROUTES = ['/checkout', '/start-trial', '/admin', '/payment', '/privacy', '/terms'];

const isOpen = ref(false);
const mountedAt = Date.now();

const currentPath = computed(() => (typeof window !== 'undefined' ? window.location.pathname : ''));
const shouldSkip = computed(() => SKIP_ROUTES.some(prefix => currentPath.value.startsWith(prefix)));

function trigger() {
    if (isOpen.value) return;
    if (shouldSkip.value) return;
    if (Date.now() - mountedAt < TRIGGER_DELAY_MS) return;
    if (typeof sessionStorage !== 'undefined' && sessionStorage.getItem(STORAGE_KEY)) return;

    isOpen.value = true;
    try { sessionStorage.setItem(STORAGE_KEY, '1'); } catch (_) {}
}

function close() {
    isOpen.value = false;
}

function handleMouseLeave(e) {
    // Only fire when the pointer exits through the top of the viewport
    // — classic exit-intent signal. Ignore side and bottom exits.
    if (e.clientY < 10 && !e.relatedTarget && !e.toElement) {
        trigger();
    }
}

// Mobile fallback: back-gesture on the first visit triggers the modal too.
function handleBackGesture() {
    trigger();
}

onMounted(() => {
    if (typeof document === 'undefined') return;
    document.addEventListener('mouseleave', handleMouseLeave);
    window.addEventListener('popstate', handleBackGesture, { once: true });
});

onBeforeUnmount(() => {
    document.removeEventListener('mouseleave', handleMouseLeave);
    window.removeEventListener('popstate', handleBackGesture);
});
</script>

<template>
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
                v-if="isOpen"
                class="fixed inset-0 z-[100] bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
                @click.self="close"
            >
                <Transition
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 scale-90 translate-y-4"
                    enter-to-class="opacity-100 scale-100 translate-y-0"
                >
                    <div v-if="isOpen" class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden">
                        <!-- Close button -->
                        <button
                            @click="close"
                            class="absolute top-4 end-4 z-10 w-9 h-9 rounded-full bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white flex items-center justify-center transition"
                            :aria-label="locale === 'ar' ? 'إغلاق' : 'Close'"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Dark header with the hook -->
                        <div class="relative bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] p-8 text-white overflow-hidden">
                            <div class="absolute -top-10 -end-10 w-40 h-40 bg-[#C4A265]/20 rounded-full blur-3xl"></div>
                            <div class="absolute -bottom-10 -start-10 w-40 h-40 bg-[#2471A3]/20 rounded-full blur-3xl"></div>

                            <div class="relative">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 ring-1 ring-amber-300/40 mb-4">
                                    <span class="text-xl">🎁</span>
                                    <span class="text-xs text-amber-200 font-bold uppercase tracking-wide">
                                        {{ locale === 'ar' ? 'عرض خاص قبل ما تروح' : 'Wait — before you go' }}
                                    </span>
                                </div>
                                <h2 class="text-2xl md:text-3xl font-extrabold leading-tight mb-2">
                                    {{ locale === 'ar' ? 'خصم إضافي 15% على أول شهر' : 'An extra 15% off your first month' }}
                                </h2>
                                <p class="text-white/70 text-sm leading-relaxed">
                                    {{ locale === 'ar'
                                        ? 'ابدأ تجربتك المجانية الآن وسنعطيك كود يخصم 15% من أول اشتراك مدفوع — فوق خصم عرض الإطلاق.'
                                        : 'Start your free trial now and we\'ll hand you a code that shaves 15% off your first paid month — on top of the launch offer.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="p-8">
                            <!-- The coupon -->
                            <div class="bg-gradient-to-r from-[#C4A265]/10 via-[#D4B876]/10 to-[#C4A265]/10 border-2 border-dashed border-[#C4A265]/40 rounded-2xl p-4 mb-6 text-center">
                                <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mb-2">
                                    {{ locale === 'ar' ? 'استخدم الكود' : 'Use coupon' }}
                                </p>
                                <p class="text-2xl md:text-3xl font-extrabold font-mono tracking-wider text-[#1B4F72]">
                                    STAY15
                                </p>
                                <p class="text-[11px] text-gray-400 mt-2">
                                    {{ locale === 'ar' ? 'صالح لمدة 24 ساعة' : 'Valid for 24 hours' }}
                                </p>
                            </div>

                            <!-- CTAs -->
                            <div class="space-y-3">
                                <Link
                                    href="/start-trial"
                                    class="w-full flex items-center justify-center gap-2 py-4 rounded-xl bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition"
                                    @click="close"
                                >
                                    {{ locale === 'ar' ? 'ابدأ تجربتي المجانية الآن' : 'Start my free trial now' }}
                                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                    </svg>
                                </Link>
                                <button
                                    @click="close"
                                    class="w-full py-3 text-sm text-gray-500 hover:text-dark transition"
                                >
                                    {{ locale === 'ar' ? 'لا شكراً، سأكمل الاستكشاف' : 'No thanks, I\'ll keep browsing' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
