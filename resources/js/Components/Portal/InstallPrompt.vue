<script setup>
/**
 * Portal — Add-to-Home-Screen banner.
 *
 * Listens for the `beforeinstallprompt` event that Chromium-based
 * browsers fire when the page meets PWA install criteria (HTTPS,
 * manifest, SW registered, scope match). We hold the event and
 * show our own banner so the customer sees branded copy explaining
 * what they're installing.
 *
 * iOS Safari quirks:
 *   - Safari NEVER fires beforeinstallprompt — Apple's install flow
 *     is manual (Share menu → Add to Home Screen). For Safari we
 *     detect by user-agent and show a one-line hint instead of the
 *     interactive button.
 *   - matchMedia('(display-mode: standalone)') AND
 *     window.navigator.standalone (Safari-specific) both tell us
 *     when we're ALREADY running as a PWA — hide the banner.
 *
 * Dismissal:
 *   - Closing the banner stores a 30-day cookie so we don't nag
 *     repeatedly.
 *   - 'Install' choice (accepted or dismissed) also stores the
 *     cookie — beforeinstallprompt only fires once per session,
 *     no point re-showing the banner.
 */
import { ref, onMounted, computed } from 'vue';

const promptEvent = ref(null);
const visible = ref(false);
const isSafariIos = ref(false);

const DISMISS_COOKIE = 'doc_pwa_dismissed';
const DISMISS_DAYS = 30;

function setDismissCookie() {
    const expires = new Date(Date.now() + DISMISS_DAYS * 86400 * 1000).toUTCString();
    document.cookie = `${DISMISS_COOKIE}=1; expires=${expires}; path=/portal; SameSite=Lax`;
}

function isDismissed() {
    return document.cookie.split(';').some((c) => c.trim().startsWith(`${DISMISS_COOKIE}=`));
}

function isAlreadyInstalled() {
    // Chrome/Edge/Android.
    if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) return true;
    // iOS Safari home-screen apps.
    if (window.navigator.standalone === true) return true;
    return false;
}

async function install() {
    if (!promptEvent.value) return;
    promptEvent.value.prompt();
    try {
        await promptEvent.value.userChoice;
    } catch (_) { /* ignore */ }
    visible.value = false;
    setDismissCookie();
    promptEvent.value = null;
}

function dismiss() {
    visible.value = false;
    setDismissCookie();
}

onMounted(() => {
    if (isAlreadyInstalled() || isDismissed()) return;

    // Chromium handler — the standard install flow.
    window.addEventListener('beforeinstallprompt', (e) => {
        // Suppress the native mini-infobar; we'll trigger prompt()
        // ourselves from our own button.
        e.preventDefault();
        promptEvent.value = e;
        visible.value = true;
    });

    // Safari hint — UA sniffing is the only signal Safari gives us.
    // Mobile Safari + not standalone + not in-app browser.
    const ua = navigator.userAgent || '';
    const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
    const isSafari = /^((?!chrome|android|crios|fxios|edgios).)*safari/i.test(ua);
    if (isIOS && isSafari && !isAlreadyInstalled()) {
        isSafariIos.value = true;
        visible.value = true;
    }
});

const supportsButton = computed(() => promptEvent.value !== null);
</script>

<template>
    <div v-if="visible"
        class="fixed bottom-4 inset-x-4 sm:inset-x-auto sm:right-4 sm:max-w-sm bg-white rounded-2xl border border-gray-200 shadow-lg p-4 z-50"
        role="dialog"
        aria-labelledby="install-prompt-title"
    >
        <div class="flex items-start gap-3">
            <div class="shrink-0 w-10 h-10 rounded-xl bg-[#0A1628] flex items-center justify-center">
                <svg class="w-5 h-5 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v12m0 0l-4-4m4 4l4-4M4 20h16" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 id="install-prompt-title" class="text-sm font-bold text-[#0A1628] mb-0.5">
                    ثبّت Doctorato Portal
                </h3>
                <p v-if="supportsButton" class="text-xs text-[#5A6C7D] leading-relaxed">
                    وصول سريع لاشتراكاتك وفواتيرك من الشاشة الرئيسية، بدون فتح المتصفح.
                </p>
                <p v-else-if="isSafariIos" class="text-xs text-[#5A6C7D] leading-relaxed">
                    اضغط <strong>زر المشاركة</strong> أسفل Safari ثم اختر
                    <strong>“Add to Home Screen”</strong>.
                </p>
                <div class="mt-3 flex items-center gap-2">
                    <button
                        v-if="supportsButton"
                        @click="install"
                        class="bg-[#0A1628] hover:bg-[#1C2833] text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition"
                    >
                        ثبّت الآن
                    </button>
                    <button
                        @click="dismiss"
                        class="text-xs text-[#8B9BAC] hover:text-[#1C2833] px-3 py-1.5"
                    >
                        لاحقاً
                    </button>
                </div>
            </div>
            <button
                @click="dismiss"
                class="shrink-0 text-[#8B9BAC] hover:text-[#1C2833] -m-1 p-1"
                aria-label="إغلاق"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</template>
