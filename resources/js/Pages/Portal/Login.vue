<script setup>
/**
 * Portal login — single email field, magic-link flow.
 * After submit the page shows a "check your inbox" confirmation
 * whether or not the email matched a real customer, so attackers
 * can't enumerate the user list from response timing.
 */
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({ email: '' });
const page = usePage();
const sent = computed(() => Boolean(page.props.flash?.portalLinkSent));

function submit() { form.post('/portal/login'); }
</script>

<template>
    <Head title="Sign in — Doctorato Portal" />

    <div class="min-h-screen bg-gradient-to-br from-[#F4F1EA] to-[#E8E2D0] flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-44 h-auto mx-auto mb-3" />
                <p class="text-[#5A6C7D] text-sm">Customer portal</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <!-- State: link sent -->
                <div v-if="sent" class="text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-[#0A1628] mb-2">Check your inbox</h2>
                    <p class="text-sm text-[#5A6C7D] leading-relaxed">
                        If the email you entered matches a Doctorato customer, we just sent a sign-in link. It expires in 15 minutes.
                    </p>
                </div>

                <!-- State: enter email -->
                <form v-else @submit.prevent="submit" class="space-y-5">
                    <div class="text-center mb-2">
                        <h2 class="text-xl font-bold text-[#0A1628] mb-1">Sign in</h2>
                        <p class="text-sm text-[#5A6C7D]">Enter the email you used at signup. We'll mail you a one-click link.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email address</label>
                        <input
                            v-model="form.email"
                            type="email"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#1B4F72] focus:border-transparent outline-none transition"
                            placeholder="you@clinic.com"
                            required
                            autofocus
                            dir="ltr"
                        />
                        <p v-if="form.errors.email" class="text-rose-500 text-xs mt-2">{{ form.errors.email }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-[#0A1628] hover:bg-[#1B4F72] text-white font-semibold py-3 rounded-lg transition disabled:opacity-60"
                    >
                        {{ form.processing ? 'Sending…' : 'Email me the sign-in link' }}
                    </button>

                    <p class="text-center text-xs text-[#8B9BAC] pt-2">
                        No password to remember. We email a one-time link instead.
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>
