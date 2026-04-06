<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const { t } = useI18n();
useScrollAnimation();

const showSuccess = ref(false);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const subjects = computed(() => [
    { value: 'general', label: t('contact.subject_general') },
    { value: 'sales', label: t('contact.subject_sales') },
    { value: 'support', label: t('contact.subject_support') },
    { value: 'partnership', label: t('contact.subject_partnership') },
    { value: 'other', label: t('contact.subject_other') },
]);

const contactInfo = computed(() => [
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />`,
        label: t('contact.info_email'),
        value: 'info@doctorato.com',
        href: 'mailto:info@doctorato.com',
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />`,
        label: t('contact.info_phone'),
        value: '+966 50 000 0000',
        href: 'tel:+966500000000',
    },
    {
        icon: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />`,
        label: t('contact.info_address'),
        value: t('contact.address_value'),
        href: null,
    },
]);

const socialLinks = computed(() => [
    { name: 'Twitter', icon: 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z', href: '#' },
    { name: 'LinkedIn', icon: 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 6a2 2 0 100-4 2 2 0 000 4z', href: '#' },
    { name: 'Instagram', icon: 'M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 01-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 017.8 2m-.2 2A3.6 3.6 0 004 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 003.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5M12 7a5 5 0 110 10 5 5 0 010-10m0 2a3 3 0 100 6 3 3 0 000-6z', href: '#' },
]);

function submitForm() {
    form.post(route('contact.store'), {
        onSuccess: () => {
            showSuccess.value = true;
            form.reset();
        },
    });
}
</script>

<template>
    <Head :title="t('contact.page_title')" />
    <MainLayout>
        <!-- Hero Section -->
        <section class="relative py-24 bg-gradient-to-br from-primary via-primary-dark to-primary overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 start-20 w-72 h-72 bg-secondary rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="relative container mx-auto px-4 text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 animate-fade-up">
                    {{ t('contact.hero_title') }}
                </h1>
                <p class="text-xl text-white/80 max-w-3xl mx-auto animate-fade-up">
                    {{ t('contact.hero_subtitle') }}
                </p>
            </div>
        </section>

        <!-- Contact Content -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">
                    <!-- Contact Form -->
                    <div class="lg:col-span-2 animate-fade-up">
                        <!-- Success Message -->
                        <Transition
                            enter-active-class="transition duration-500 ease-out"
                            enter-from-class="opacity-0 scale-90"
                            enter-to-class="opacity-100 scale-100"
                        >
                            <div
                                v-if="showSuccess"
                                class="text-center py-12 bg-light-blue rounded-2xl"
                            >
                                <div class="w-20 h-20 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <h3 class="text-2xl font-bold text-dark mb-2">{{ t('contact.success_title') }}</h3>
                                <p class="text-gray">{{ t('contact.success_message') }}</p>
                                <button
                                    @click="showSuccess = false"
                                    class="mt-6 px-6 py-2 bg-primary text-white rounded-full hover:bg-primary-dark transition-colors"
                                >
                                    {{ t('contact.send_another') }}
                                </button>
                            </div>
                        </Transition>

                        <form v-if="!showSuccess" @submit.prevent="submitForm" class="bg-light-blue rounded-2xl p-6 sm:p-8 shadow-sm">
                            <h2 class="text-2xl font-bold text-dark mb-6">{{ t('contact.form_title') }}</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <!-- Name -->
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1.5">{{ t('contact.name') }} *</label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                        :placeholder="t('contact.name_placeholder')"
                                    />
                                    <p v-if="form.errors.name" class="text-danger text-xs mt-1">{{ form.errors.name }}</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1.5">{{ t('contact.email') }} *</label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                        :placeholder="t('contact.email_placeholder')"
                                    />
                                    <p v-if="form.errors.email" class="text-danger text-xs mt-1">{{ form.errors.email }}</p>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1.5">{{ t('contact.phone') }}</label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                        :placeholder="t('contact.phone_placeholder')"
                                    />
                                    <p v-if="form.errors.phone" class="text-danger text-xs mt-1">{{ form.errors.phone }}</p>
                                </div>

                                <!-- Subject -->
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1.5">{{ t('contact.subject') }} *</label>
                                    <select
                                        v-model="form.subject"
                                        required
                                        class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all"
                                    >
                                        <option value="">{{ t('contact.select_subject') }}</option>
                                        <option v-for="s in subjects" :key="s.value" :value="s.value">{{ s.label }}</option>
                                    </select>
                                    <p v-if="form.errors.subject" class="text-danger text-xs mt-1">{{ form.errors.subject }}</p>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="mt-5">
                                <label class="block text-sm font-medium text-dark mb-1.5">{{ t('contact.message') }} *</label>
                                <textarea
                                    v-model="form.message"
                                    rows="6"
                                    required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-light/50 bg-white focus:border-secondary focus:ring-2 focus:ring-secondary/20 outline-none transition-all resize-none"
                                    :placeholder="t('contact.message_placeholder')"
                                ></textarea>
                                <p v-if="form.errors.message" class="text-danger text-xs mt-1">{{ form.errors.message }}</p>
                            </div>

                            <!-- Submit -->
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="mt-6 w-full sm:w-auto px-10 py-4 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all duration-300 hover:shadow-lg hover:shadow-secondary/25 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="w-5 h-5 animate-spin"
                                    fill="none" viewBox="0 0 24 24"
                                >
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                </svg>
                                <span>{{ form.processing ? t('contact.sending') : t('contact.send') }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8 animate-fade-up">
                        <!-- Contact Info -->
                        <div class="bg-light-blue rounded-2xl p-6 shadow-sm">
                            <h3 class="text-xl font-bold text-dark mb-6">{{ t('contact.info_title') }}</h3>
                            <div class="space-y-5">
                                <div
                                    v-for="(info, idx) in contactInfo"
                                    :key="idx"
                                    class="flex items-start gap-4"
                                >
                                    <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" v-html="info.icon"></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray mb-0.5">{{ info.label }}</p>
                                        <a
                                            v-if="info.href"
                                            :href="info.href"
                                            class="text-dark font-medium hover:text-secondary transition-colors"
                                        >
                                            {{ info.value }}
                                        </a>
                                        <p v-else class="text-dark font-medium">{{ info.value }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="bg-light-blue rounded-2xl p-6 shadow-sm">
                            <h3 class="text-xl font-bold text-dark mb-4">{{ t('contact.hours_title') }}</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray text-sm">{{ t('contact.hours_weekdays') }}</span>
                                    <span class="text-dark font-medium text-sm">9:00 - 18:00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray text-sm">{{ t('contact.hours_saturday') }}</span>
                                    <span class="text-dark font-medium text-sm">10:00 - 14:00</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray text-sm">{{ t('contact.hours_friday') }}</span>
                                    <span class="text-danger font-medium text-sm">{{ t('contact.hours_closed') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="bg-light-blue rounded-2xl p-6 shadow-sm">
                            <h3 class="text-xl font-bold text-dark mb-4">{{ t('contact.social_title') }}</h3>
                            <div class="flex gap-3">
                                <a
                                    v-for="social in socialLinks"
                                    :key="social.name"
                                    :href="social.href"
                                    :aria-label="social.name"
                                    class="w-10 h-10 bg-primary/10 hover:bg-primary hover:text-white text-primary rounded-xl flex items-center justify-center transition-all duration-300"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" :d="social.icon" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Map Placeholder -->
                        <div class="bg-light-blue rounded-2xl overflow-hidden shadow-sm">
                            <div class="aspect-video bg-gradient-to-br from-primary/5 to-secondary/5 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-12 h-12 text-primary/30 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-gray text-sm">{{ t('contact.map_placeholder') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
