<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const props = defineProps({
    faqs: { type: Array, default: () => [] },
});

const { t } = useI18n();
const { localizedField } = useLocale();
useScrollAnimation();

const activeCategory = ref('all');
const openIndex = ref(null);

const categories = computed(() => [
    { key: 'all', label: t('faq.categories.all') },
    { key: 'general', label: t('faq.categories.general') },
    { key: 'pricing', label: t('faq.categories.pricing') },
    { key: 'technical', label: t('faq.categories.technical') },
]);

const filteredFaqs = computed(() => {
    if (activeCategory.value === 'all') return props.faqs;
    return props.faqs.filter(faq => faq.category === activeCategory.value);
});

function toggle(index) {
    openIndex.value = openIndex.value === index ? null : index;
}

function setCategory(key) {
    activeCategory.value = key;
    openIndex.value = null;
}
</script>

<template>
    <div class="max-w-4xl mx-auto">
        <!-- Category Tabs -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-10 animate-fade-up">
            <button
                v-for="cat in categories"
                :key="cat.key"
                @click="setCategory(cat.key)"
                class="px-5 py-2.5 rounded-full text-sm font-medium transition-all duration-300"
                :class="activeCategory === cat.key
                    ? 'bg-secondary text-white shadow-lg shadow-secondary/20'
                    : 'bg-white text-gray border border-gray-200 hover:border-secondary/30 hover:text-secondary hover:shadow-sm'"
            >
                {{ cat.label }}
            </button>
        </div>

        <!-- FAQ Items -->
        <div class="space-y-3 animate-stagger">
            <div
                v-for="(faq, index) in filteredFaqs"
                :key="faq.id || index"
                class="rounded-2xl transition-all duration-300 overflow-hidden"
                :class="openIndex === index
                    ? 'bg-white shadow-lg shadow-primary/5 border border-secondary/20 ring-1 ring-secondary/10'
                    : 'bg-white border border-gray-100 hover:border-gray-200 hover:shadow-md'"
            >
                <!-- Question -->
                <button
                    @click="toggle(index)"
                    class="w-full flex items-center gap-4 px-6 py-5 text-start group"
                >
                    <!-- Question icon -->
                    <div
                        class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-all duration-300"
                        :class="openIndex === index
                            ? 'bg-secondary/10 text-secondary'
                            : 'bg-gray-100 text-gray group-hover:bg-secondary/5 group-hover:text-secondary'"
                    >
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01" />
                        </svg>
                    </div>

                    <span
                        class="flex-1 text-[15px] font-semibold transition-colors duration-200"
                        :class="openIndex === index ? 'text-primary' : 'text-dark group-hover:text-primary'"
                    >
                        {{ localizedField(faq, 'question') }}
                    </span>

                    <!-- Expand icon -->
                    <div
                        class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-all duration-300"
                        :class="openIndex === index
                            ? 'bg-secondary text-white rotate-180'
                            : 'bg-gray-100 text-gray group-hover:bg-gray-200'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </button>

                <!-- Answer -->
                <Transition
                    enter-active-class="transition-all duration-300 ease-out overflow-hidden"
                    enter-from-class="max-h-0 opacity-0"
                    enter-to-class="max-h-96 opacity-100"
                    leave-active-class="transition-all duration-200 ease-in overflow-hidden"
                    leave-from-class="max-h-96 opacity-100"
                    leave-to-class="max-h-0 opacity-0"
                >
                    <div v-if="openIndex === index" class="overflow-hidden">
                        <div class="px-6 pb-6 ps-[4.25rem]">
                            <div class="w-8 h-px bg-secondary/30 mb-4"></div>
                            <p class="text-gray leading-relaxed text-sm">
                                {{ localizedField(faq, 'answer') }}
                            </p>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredFaqs.length === 0" class="text-center py-16 text-gray">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="font-medium">{{ $t('faq.no_results') }}</p>
        </div>
    </div>
</template>
