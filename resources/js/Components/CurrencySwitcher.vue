<script setup>
import { ref } from 'vue';
import { useCurrency } from '@/composables/useCurrency';
import { useI18n } from 'vue-i18n';

const { currencies, currentCurrency, switchCurrency, getCurrencyName } = useCurrency();
const { locale } = useI18n();
const isOpen = ref(false);

function selectCurrency(code) {
    switchCurrency(code);
    isOpen.value = false;
}
</script>

<template>
    <div class="relative">
        <button
            @click="isOpen = !isOpen"
            class="flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-light/30 hover:border-secondary transition-colors text-sm"
        >
            <span v-if="currentCurrency?.flag_emoji">{{ currentCurrency.flag_emoji }}</span>
            <span class="font-medium">{{ currentCurrency?.code }}</span>
            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div
                v-if="isOpen"
                class="absolute top-full mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 z-50 max-h-80 overflow-y-auto"
                :class="locale === 'ar' ? 'left-0' : 'right-0'"
            >
                <div class="p-2">
                    <button
                        v-for="currency in currencies"
                        :key="currency.code"
                        @click="selectCurrency(currency.code)"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-light-blue transition-colors text-start"
                        :class="{ 'bg-light-blue font-bold': currency.code === currentCurrency?.code }"
                    >
                        <span class="text-lg">{{ currency.flag_emoji }}</span>
                        <span class="flex-1">
                            <span class="block text-sm font-medium text-dark">{{ getCurrencyName(currency) }}</span>
                            <span class="block text-xs text-gray">{{ currency.code }} — {{ currency.symbol }}</span>
                        </span>
                        <span v-if="currency.code === currentCurrency?.code" class="text-secondary text-lg">&#10003;</span>
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>
