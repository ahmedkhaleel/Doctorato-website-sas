<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import WhatsAppButton from '@/Components/WhatsAppButton.vue';
import ScrollToTop from '@/Components/ScrollToTop.vue';
import TrackingScripts from '@/Components/TrackingScripts.vue';
import ExitIntentModal from '@/Components/ExitIntentModal.vue';
import ClientCountryDetector from '@/Components/ClientCountryDetector.vue';
import CookieConsent from '@/Components/CookieConsent.vue';
import LaunchCountdown from '@/Components/LaunchCountdown.vue';

const { locale } = useI18n();
const dir = computed(() => locale.value === 'ar' ? 'rtl' : 'ltr');
</script>

<template>
    <div :dir="dir" :lang="locale">
        <LaunchCountdown sticky :ends-at="'2026-12-31T23:59:59'" />
        <Navbar />
        <main class="pt-20">
            <slot />
        </main>
        <Footer />
        <WhatsAppButton />
        <ScrollToTop />
        <TrackingScripts />
        <ExitIntentModal />
        <ClientCountryDetector />
        <CookieConsent />
    </div>
</template>
