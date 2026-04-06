<script setup>
import { useLocale } from '@/composables/useLocale';

const { locale, switchLocale } = useLocale();

defineProps({
    variant: { type: String, default: 'default' }, // 'default' | 'light'
});
</script>

<template>
    <button
        @click="switchLocale"
        class="px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-300"
        :class="variant === 'light'
            ? 'border border-white/30 text-white hover:bg-white/10'
            : 'border border-gray-light/30 text-dark hover:border-secondary hover:text-secondary'"
    >
        {{ locale === 'ar' ? 'EN' : 'عربي' }}
    </button>
</template>
