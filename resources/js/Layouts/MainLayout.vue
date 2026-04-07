<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Navbar from '@/Components/Navbar.vue';
import Footer from '@/Components/Footer.vue';
import WhatsAppButton from '@/Components/WhatsAppButton.vue';
import ScrollToTop from '@/Components/ScrollToTop.vue';
import TrackingScripts from '@/Components/TrackingScripts.vue';

const { locale } = useI18n();
const dir = computed(() => locale.value === 'ar' ? 'rtl' : 'ltr');
</script>

<template>
    <div :dir="dir" :lang="locale">
        <Navbar />
        <main class="pt-20">
            <slot />
        </main>
        <Footer />
        <WhatsAppButton />
        <ScrollToTop />
        <TrackingScripts />
    </div>
</template>
