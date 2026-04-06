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
    <section class="relative py-20 overflow-hidden">
        <!-- Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#1B4F72] via-[#1B4F72] to-[#1C2833]"></div>
        <!-- Decorative pattern -->
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 32px 32px;"></div>
        <!-- Glow orbs -->
        <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 start-0 w-80 h-80 bg-[#2471A3]/20 rounded-full blur-[100px]"></div>

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16 animate-fade-up">
                <!-- Left: Text Content -->
                <div class="flex-1 text-center lg:text-start">
                    <!-- Icon -->
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10 mb-6">
                        <svg class="w-7 h-7 text-[#C4A265]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </div>

                    <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-3">
                        {{ $t('newsletter.title') }}
                    </h2>
                    <p class="text-white/60 text-sm md:text-base leading-relaxed max-w-md mx-auto lg:mx-0">
                        {{ $t('newsletter.subtitle') }}
                    </p>
                </div>

                <!-- Right: Form -->
                <div class="w-full lg:w-auto lg:min-w-[420px]">
                    <form @submit.prevent="submitNewsletter">
                        <div class="flex gap-3 bg-white/10 backdrop-blur-sm border border-white/15 rounded-2xl p-2">
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="flex-1 px-5 py-3.5 bg-transparent text-white placeholder-white/40 outline-none text-sm min-w-0"
                                :placeholder="$t('newsletter.email_placeholder')"
                            />
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3.5 bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-semibold rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-[#C4A265]/25 hover:-translate-y-0.5 disabled:opacity-50 shrink-0 flex items-center gap-2 text-sm"
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
                        <p v-if="form.errors.email" class="text-red-300 text-xs mt-3 ps-3">{{ form.errors.email }}</p>

                        <!-- Success Message -->
                        <Transition
                            enter-active-class="transition duration-300 ease-out"
                            enter-from-class="opacity-0 translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-200 ease-in"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <div v-if="showSuccess" class="mt-3 ps-3 flex items-center gap-2 text-emerald-300 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>{{ $t('newsletter.success') }}</span>
                            </div>
                        </Transition>
                    </form>

                    <!-- Privacy Note -->
                    <p class="text-white/30 text-xs mt-4 ps-3">{{ $t('newsletter.privacy_note') }}</p>
                </div>
            </div>
        </div>
    </section>
</template>
