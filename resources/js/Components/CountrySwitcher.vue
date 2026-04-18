<script setup>
/**
 * Country dropdown in the public navbar.
 *
 * Reads the active country + supported list from Inertia's shared props
 * (populated by HandleInertiaRequests → CountryDetector). Picking a new
 * country hits GET /country/{code} which persists the choice in session
 * and reloads the page so Pricing re-resolves with the new country's
 * PlanPrice rows.
 *
 * Renders differently based on the `variant` prop:
 *   - "default": pill with flag + country code + chevron (desktop)
 *   - "compact": icon-only flag for mobile
 */
import { usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';

defineProps({
    variant: { type: String, default: 'default' },
});

const page = usePage();
const { locale } = useI18n();

const isOpen = ref(false);
const dropdownRef = ref(null);

const activeCode = computed(() => page.props.activeCountry || 'EG');
const countries = computed(() => page.props.supportedCountries || []);

const active = computed(() =>
    countries.value.find((c) => c.country_code === activeCode.value) || {
        country_code: 'EG',
        country_flag: '🇪🇬',
        country_name_ar: 'مصر',
        country_name_en: 'Egypt',
    }
);

function label(c) {
    return locale.value === 'ar' ? c.country_name_ar : c.country_name_en;
}

function select(code) {
    isOpen.value = false;
    if (code === activeCode.value) return;
    router.get(`/country/${code}`, {}, { preserveScroll: false });
}

function handleClickOutside(e) {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        isOpen.value = false;
    }
}

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>

<template>
    <div ref="dropdownRef" class="relative">
        <button
            @click="isOpen = !isOpen"
            :class="[
                'inline-flex items-center gap-1.5 rounded-full transition-all',
                variant === 'compact'
                    ? 'px-2 py-1.5 text-base hover:bg-gray-100'
                    : 'px-3 py-1.5 text-sm font-medium border border-gray-light/30 text-dark hover:border-secondary hover:text-secondary',
            ]"
            :title="label(active)"
        >
            <span class="text-base leading-none">{{ active.country_flag }}</span>
            <span v-if="variant !== 'compact'" class="font-mono text-xs font-bold">{{ active.country_code }}</span>
            <svg v-if="variant !== 'compact'" class="w-3 h-3 transition-transform" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-2"
        >
            <div
                v-if="isOpen && countries.length"
                class="absolute top-full end-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50 max-h-96 overflow-y-auto"
            >
                <div class="px-3 py-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100">
                    {{ locale === 'ar' ? 'اختر دولتك' : 'Choose your country' }}
                </div>
                <button
                    v-for="c in countries"
                    :key="c.country_code"
                    type="button"
                    @click="select(c.country_code)"
                    :class="[
                        'w-full flex items-center gap-3 px-4 py-2.5 text-start transition-colors',
                        c.country_code === activeCode
                            ? 'bg-light-blue/60 text-primary font-bold'
                            : 'hover:bg-gray-50 text-dark',
                    ]"
                >
                    <span class="text-lg leading-none">{{ c.country_flag }}</span>
                    <span class="flex-1 text-sm">{{ label(c) }}</span>
                    <span class="text-[10px] font-mono text-gray-400">{{ c.currency_code }}</span>
                    <svg v-if="c.country_code === activeCode" class="w-4 h-4 text-primary" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </Transition>
    </div>
</template>
