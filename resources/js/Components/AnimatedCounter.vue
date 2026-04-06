<script setup>
import { ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';

const props = defineProps({
    target: { type: Number, required: true },
    duration: { type: Number, default: 2000 },
    suffix: { type: String, default: '' },
    prefix: { type: String, default: '' },
});

const count = ref(0);
const el = ref(null);
const hasAnimated = ref(false);

function animate() {
    if (hasAnimated.value) return;
    hasAnimated.value = true;
    const start = performance.now();
    const step = (now) => {
        const progress = Math.min((now - start) / props.duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        count.value = Math.floor(eased * props.target);
        if (progress < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
}

const { stop } = useIntersectionObserver(el, ([{ isIntersecting }]) => {
    if (isIntersecting) { animate(); stop(); }
}, { threshold: 0.3 });
</script>

<template>
    <span ref="el">{{ prefix }}{{ count.toLocaleString() }}{{ suffix }}</span>
</template>
