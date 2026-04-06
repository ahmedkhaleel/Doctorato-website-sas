<script setup>
import { ref, onMounted, onUnmounted } from 'vue';

const isVisible = ref(false);

function checkScroll() {
    isVisible.value = window.scrollY > 400;
}

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

onMounted(() => window.addEventListener('scroll', checkScroll));
onUnmounted(() => window.removeEventListener('scroll', checkScroll));
</script>

<template>
    <Transition enter-active-class="transition-all duration-300" enter-from-class="opacity-0 translate-y-4" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition-all duration-200" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-4">
        <button
            v-if="isVisible"
            @click="scrollToTop"
            class="fixed bottom-6 end-6 z-50 bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg hover:bg-primary-light transition-colors"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
            </svg>
        </button>
    </Transition>
</template>
