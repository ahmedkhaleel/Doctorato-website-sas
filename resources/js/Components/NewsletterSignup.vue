<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const { t } = useI18n();
useScrollAnimation();

const showSuccess = ref(false);
const showError = ref(false);

const form = useForm({ email: '' });

function submitNewsletter() {
    showError.value = false;
    form.post(route('newsletter.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccess.value = true;
            form.reset();
            setTimeout(() => { showSuccess.value = false; }, 5000);
        },
        onError: () => {
            showError.value = true;
        },
    });
}
</script>

<template>
    <section class="relative py-20 overflow-hidden bg-gradient-to-b from-white via-[#FAF7F0] to-[#F5EFE0]">
        <!-- Soft decorative pattern -->
        <div class="absolute inset-0 opacity-[0.035]" style="background-image: radial-gradient(circle at 1px 1px, #1B4F72 1px, transparent 0); background-size: 32px 32px;"></div>

        <!-- Decorative orbs -->
        <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px] -translate-y-1/3 translate-x-1/3"></div>
        <div class="absolute bottom-0 start-0 w-80 h-80 bg-[#1B4F72]/[0.06] rounded-full blur-[100px] translate-y-1/3 -translate-x-1/3"></div>

        <!-- Top gradient divider -->
        <div class="absolute top-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-[#C4A265]/30 to-transparent" />

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Card container -->
            <div class="bg-white rounded-3xl shadow-xl shadow-[#1B4F72]/5 border border-[#C4A265]/20 overflow-hidden animate-fade-up">
                <div class="relative p-8 md:p-12">
                    <!-- Gold accent corner -->
                    <div class="absolute top-0 end-0 w-40 h-40 bg-gradient-to-br from-[#C4A265]/10 to-transparent rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 start-0 w-32 h-32 bg-gradient-to-tr from-[#1B4F72]/5 to-transparent rounded-full blur-2xl"></div>

                    <div class="relative flex flex-col lg:flex-row items-center gap-10 lg:gap-12">
                        <!-- Left: Text Content -->
                        <div class="flex-1 text-center lg:text-start">
                            <!-- Icon badge -->
                            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] shadow-lg shadow-[#C4A265]/25 mb-5">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>

                            <h2 class="text-2xl md:text-3xl font-extrabold text-[#0D2B45] mb-3">
                                {{ $t('newsletter.title') }}
                            </h2>
                            <p class="text-gray-600 text-sm md:text-base leading-relaxed max-w-md mx-auto lg:mx-0">
                                {{ $t('newsletter.subtitle') }}
                            </p>
                        </div>

                        <!-- Right: Form -->
                        <div class="w-full lg:w-auto lg:min-w-[420px]">
                            <form @submit.prevent="submitNewsletter">
                                <div class="flex gap-2 bg-[#FAF7F0] border border-[#C4A265]/25 rounded-2xl p-2 focus-within:border-[#C4A265]/60 focus-within:shadow-lg focus-within:shadow-[#C4A265]/10 transition-all">
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="flex-1 px-5 py-3.5 bg-transparent text-[#0D2B45] placeholder-gray-400 outline-none text-sm min-w-0"
                                        :placeholder="$t('newsletter.email_placeholder')"
                                    />
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="px-6 py-3.5 bg-gradient-to-r from-[#1B4F72] to-[#0D2B45] text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-[#1B4F72]/25 hover:-translate-y-0.5 disabled:opacity-50 shrink-0 flex items-center gap-2 text-sm"
                                    >
                                        <svg
                                            v-if="form.processing"
                                            class="w-4 h-4 animate-spin"
                                            fill="none" viewBox="0 0 24 24"
                                        >
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        <span>{{ $t('newsletter.subscribe') }}</span>
                                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Validation Error -->
                                <p v-if="form.errors.email" class="text-red-500 text-xs mt-3 ps-3">{{ form.errors.email }}</p>

                                <!-- Success Message -->
                                <Transition
                                    enter-active-class="transition duration-300 ease-out"
                                    enter-from-class="opacity-0 translate-y-2"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition duration-200 ease-in"
                                    leave-from-class="opacity-100"
                                    leave-to-class="opacity-0"
                                >
                                    <div v-if="showSuccess" class="mt-3 ps-3 flex items-center gap-2 text-emerald-600 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ $t('newsletter.success') }}</span>
                                    </div>
                                </Transition>
                            </form>

                            <!-- Privacy Note -->
                            <p class="text-gray-400 text-xs mt-4 ps-3 flex items-center gap-1.5 justify-center lg:justify-start">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                {{ $t('newsletter.privacy_note') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
