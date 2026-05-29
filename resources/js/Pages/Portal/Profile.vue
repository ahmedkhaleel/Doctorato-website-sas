<script setup>
/**
 * Portal profile + preferences. Two independent forms on one page:
 *
 *   1. Profile — name, clinic, phone, country, specialty. Email is
 *      shown read-only because it's the magic-link identifier.
 *   2. Preferences — single marketing-opt-in toggle. The note under
 *      it explicitly states that transactional emails (invoices,
 *      dunning) keep coming regardless, so customers understand
 *      what they're opting out of.
 */
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    customer: Object,
});

const page = usePage();
const portalMessage = computed(() => page.props.flash?.portalMessage ?? null);

const profileForm = useForm({
    full_name: props.customer.full_name ?? '',
    clinic_name: props.customer.clinic_name ?? '',
    phone: props.customer.phone ?? '',
    country: props.customer.country ?? '',
    specialty: props.customer.specialty ?? '',
});

const prefsForm = useForm({
    marketing_opt_in: props.customer.marketing_opt_in,
});

function saveProfile() { profileForm.put('/portal/profile'); }
function savePrefs() { prefsForm.put('/portal/preferences'); }

const logoutForm = useForm({});
function logout() { logoutForm.post('/portal/logout'); }
</script>

<template>
    <Head title="Profile — Doctorato Portal" />

    <div class="min-h-screen bg-[#F4F1EA]">
        <!-- Top bar -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link href="/portal/dashboard" class="flex items-center gap-4">
                        <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-32 h-auto" />
                        <span class="text-xs uppercase tracking-widest text-[#C4A265] font-bold hidden sm:inline">Portal</span>
                    </Link>
                </div>
                <button @click="logout" class="text-sm text-[#5A6C7D] hover:text-[#0A1628] transition font-medium">
                    Sign out
                </button>
            </div>
        </header>

        <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
            <header class="mb-8">
                <Link href="/portal/dashboard" class="text-xs text-[#5A6C7D] hover:text-[#0A1628] transition mb-3 inline-flex items-center gap-1">
                    ← Back to dashboard
                </Link>
                <h1 class="text-2xl font-bold text-[#0A1628]">Profile &amp; preferences</h1>
            </header>

            <!-- Flash message -->
            <div v-if="portalMessage" class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm text-emerald-900">{{ portalMessage }}</p>
            </div>

            <!-- Profile form -->
            <section class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-5">Your details</h2>

                <form @submit.prevent="saveProfile" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-[#8B9BAC] font-semibold mb-1.5">Full name</label>
                            <input v-model="profileForm.full_name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72] outline-none transition" />
                            <p v-if="profileForm.errors.full_name" class="text-rose-500 text-xs mt-1">{{ profileForm.errors.full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-[#8B9BAC] font-semibold mb-1.5">Clinic name</label>
                            <input v-model="profileForm.clinic_name" type="text" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72] outline-none transition" />
                            <p v-if="profileForm.errors.clinic_name" class="text-rose-500 text-xs mt-1">{{ profileForm.errors.clinic_name }}</p>
                        </div>
                        <div>
                            <label class="block text-xs text-[#8B9BAC] font-semibold mb-1.5">Phone</label>
                            <input v-model="profileForm.phone" type="tel" dir="ltr" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72] outline-none transition" />
                        </div>
                        <div>
                            <label class="block text-xs text-[#8B9BAC] font-semibold mb-1.5">Country</label>
                            <input v-model="profileForm.country" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72] outline-none transition" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs text-[#8B9BAC] font-semibold mb-1.5">Specialty</label>
                            <input v-model="profileForm.specialty" type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-[#1B4F72] outline-none transition" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs text-[#8B9BAC] font-semibold mb-1.5">Email <span class="text-gray-400 font-normal">(read-only — contact support to change)</span></label>
                            <input :value="customer.email" type="email" readonly dir="ltr" class="w-full border border-gray-200 bg-gray-50 rounded-lg px-3 py-2.5 text-sm text-gray-500 cursor-not-allowed" />
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" :disabled="profileForm.processing" class="bg-[#0A1628] hover:bg-[#1B4F72] text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition disabled:opacity-60">
                            {{ profileForm.processing ? 'Saving…' : 'Save changes' }}
                        </button>
                    </div>
                </form>
            </section>

            <!-- Preferences form -->
            <section class="bg-white rounded-2xl border border-gray-200 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-5">Email preferences</h2>

                <form @submit.prevent="savePrefs">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input v-model="prefsForm.marketing_opt_in" type="checkbox" class="mt-1 w-5 h-5 rounded border-gray-300 text-[#1B4F72] focus:ring-[#1B4F72]" />
                        <div>
                            <p class="font-semibold text-[#0A1628]">Product updates &amp; tips</p>
                            <p class="text-xs text-[#5A6C7D] mt-1 leading-relaxed max-w-md">
                                Occasional emails about new features, clinic management tips, and customer stories. Roughly once a month, never more.
                            </p>
                        </div>
                    </label>

                    <div class="mt-5 pt-5 border-t border-gray-100 text-xs text-[#8B9BAC] leading-relaxed">
                        <strong class="text-[#1C2833]">Note:</strong>
                        invoices, payment receipts, and account notices are sent regardless of this setting — those are part of running your subscription.
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit" :disabled="prefsForm.processing" class="bg-[#0A1628] hover:bg-[#1B4F72] text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition disabled:opacity-60">
                            {{ prefsForm.processing ? 'Saving…' : 'Save preferences' }}
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</template>
