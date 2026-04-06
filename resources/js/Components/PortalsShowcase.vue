<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const { t } = useI18n();
const { localizedField } = useLocale();
useScrollAnimation();

const portals = computed(() => [
    {
        key: 'admin',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        name: t('portals.admin_name'),
        description: t('portals.admin_desc'),
        features: 39,
        color: 'primary',
    },
    {
        key: 'doctor',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        name: t('portals.doctor_name'),
        description: t('portals.doctor_desc'),
        features: 25,
        color: 'accent',
    },
    {
        key: 'secretary',
        icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
        name: t('portals.secretary_name'),
        description: t('portals.secretary_desc'),
        features: 18,
        color: 'secondary',
    },
    {
        key: 'patient',
        icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
        name: t('portals.patient_name'),
        description: t('portals.patient_desc'),
        features: 15,
        color: 'success',
    },
    {
        key: 'webmaster',
        icon: 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
        name: t('portals.webmaster_name'),
        description: t('portals.webmaster_desc'),
        features: 12,
        color: 'warning',
    },
    {
        key: 'website',
        icon: 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
        name: t('portals.website_name'),
        description: t('portals.website_desc'),
        features: 10,
        color: 'accent-light',
    },
]);

function getColorClasses(color) {
    const map = {
        primary: { bg: 'bg-primary/10', text: 'text-primary', border: 'hover:border-primary/30' },
        accent: { bg: 'bg-accent/10', text: 'text-accent', border: 'hover:border-accent/30' },
        secondary: { bg: 'bg-secondary/10', text: 'text-secondary', border: 'hover:border-secondary/30' },
        success: { bg: 'bg-success/10', text: 'text-success', border: 'hover:border-success/30' },
        warning: { bg: 'bg-warning/10', text: 'text-warning', border: 'hover:border-warning/30' },
        'accent-light': { bg: 'bg-accent-light/10', text: 'text-accent-light', border: 'hover:border-accent-light/30' },
    };
    return map[color] || map.primary;
}
</script>

<template>
    <section id="portals" class="py-20 bg-light-blue">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center mb-12 animate-fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-dark mb-4">
                    {{ $t('portals.title') }}
                </h2>
                <p class="text-lg text-gray max-w-2xl mx-auto">
                    {{ $t('portals.subtitle') }}
                </p>
                <div class="w-20 h-1 bg-secondary mx-auto mt-6 rounded-full"></div>
            </div>

            <!-- Portals Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-stagger">
                <div
                    v-for="portal in portals"
                    :key="portal.key"
                    class="bg-white rounded-2xl p-6 sm:p-8 border border-gray-light/20 shadow-sm transition-all duration-300 hover:shadow-xl hover:scale-[1.03] cursor-default"
                    :class="getColorClasses(portal.color).border"
                >
                    <!-- Icon -->
                    <div
                        class="w-14 h-14 rounded-xl flex items-center justify-center mb-5"
                        :class="getColorClasses(portal.color).bg"
                    >
                        <svg
                            class="w-7 h-7"
                            :class="getColorClasses(portal.color).text"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="portal.icon" />
                        </svg>
                    </div>

                    <!-- Name -->
                    <h3 class="text-lg font-bold text-dark mb-2">{{ portal.name }}</h3>

                    <!-- Description -->
                    <p class="text-sm text-gray leading-relaxed mb-4">{{ portal.description }}</p>

                    <!-- Feature Count -->
                    <div class="flex items-center gap-2">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                            :class="[getColorClasses(portal.color).bg, getColorClasses(portal.color).text]"
                        >
                            {{ portal.features }}+ {{ $t('portals.features') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
