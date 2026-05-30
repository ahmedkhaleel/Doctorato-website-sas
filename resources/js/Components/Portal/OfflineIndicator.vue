<script setup>
/**
 * Portal — connectivity banner.
 *
 * Listens to navigator's online/offline events and surfaces a
 * thin sticky banner at the top of the portal when the network
 * drops. Two states:
 *   - online → component renders nothing
 *   - offline → amber banner with explanation
 *   - reconnected (newly online, was offline) → emerald banner that
 *     auto-dismisses after 3s so the customer sees the transition
 *
 * Why we don't auto-reload on reconnect:
 *   - The customer may be reading something; surprise reload is
 *     annoying.
 *   - The PWA service worker's network-first strategy already
 *     pulls fresh data on the next navigation.
 *
 * Edge case: navigator.onLine reports the OS's view of "do I have a
 * link" — it can be true while we still can't reach the origin (eg
 * captive portal). The banner is best-effort and we accept a small
 * false-negative rate; the alternative (HEAD probe every 5s) is
 * worse for battery and bandwidth.
 */
import { ref, onMounted, onUnmounted } from 'vue';

const isOffline = ref(false);
const showReconnect = ref(false);
let reconnectTimer = null;

function onOnline() {
    if (isOffline.value) {
        showReconnect.value = true;
        clearTimeout(reconnectTimer);
        reconnectTimer = setTimeout(() => { showReconnect.value = false; }, 3000);
    }
    isOffline.value = false;
}

function onOffline() {
    isOffline.value = true;
    showReconnect.value = false;
}

onMounted(() => {
    // navigator.onLine starts true if there's any active interface,
    // false if all are down. Read once at mount so a customer who
    // opens the app already offline sees the banner immediately.
    isOffline.value = !navigator.onLine;
    window.addEventListener('online', onOnline);
    window.addEventListener('offline', onOffline);
});

onUnmounted(() => {
    window.removeEventListener('online', onOnline);
    window.removeEventListener('offline', onOffline);
    clearTimeout(reconnectTimer);
});
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition-all duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div
            v-if="isOffline"
            class="sticky top-0 z-40 bg-amber-50 border-b border-amber-200 text-amber-900 text-sm py-2 px-4 text-center"
            role="status"
            aria-live="polite"
        >
            <span class="inline-flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                لا يوجد اتصال. ستظل تتصفح آخر بيانات شاهدتها — التحديثات تنتظر عودة الشبكة.
            </span>
        </div>
        <div
            v-else-if="showReconnect"
            class="sticky top-0 z-40 bg-emerald-50 border-b border-emerald-200 text-emerald-900 text-sm py-2 px-4 text-center"
            role="status"
            aria-live="polite"
        >
            <span class="inline-flex items-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                عاد الاتصال.
            </span>
        </div>
    </Transition>
</template>
