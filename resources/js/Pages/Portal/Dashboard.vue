<script setup>
/**
 * Portal dashboard. Sections:
 *   1. Customer header (name + clinic + email + sign-out)
 *   2. Active subscriptions (status, next billing date, cancel button)
 *   3. Invoice history (most recent 20)
 *
 * Cancellation uses soft-cancel: subscription stays active until
 * ends_at, then expires. Customer gets a confirmation modal so an
 * accidental click doesn't kill an active sub.
 */
import { Head, useForm, usePage, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    customer: Object,
    trial: { type: Object, default: null },
    subscriptions: Array,
    invoices: Array,
});

const page = usePage();
const portalMessage = computed(() => page.props.flash?.portalMessage ?? null);

const logoutForm = useForm({});
const cancelForm = useForm({});
const resumeForm = useForm({});
const showCancelFor = ref(null);

function logout() { logoutForm.post('/portal/logout'); }
function confirmCancel(id) {
    cancelForm.post(`/portal/subscriptions/${id}/cancel`, {
        onFinish: () => showCancelFor.value = null,
    });
}
function resumeSubscription(id) { resumeForm.post(`/portal/subscriptions/${id}/resume`); }

function statusBadgeClass(status) {
    switch (status) {
        case 'active':    return 'bg-emerald-100 text-emerald-800';
        case 'past_due':  return 'bg-amber-100 text-amber-800';
        case 'canceled':  return 'bg-gray-100 text-gray-600';
        case 'paid':      return 'bg-emerald-100 text-emerald-800';
        case 'failed':    return 'bg-rose-100 text-rose-800';
        case 'pending':   return 'bg-blue-100 text-blue-800';
        default:          return 'bg-gray-100 text-gray-600';
    }
}

function formatMoney(amount, currency) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency || 'USD',
        minimumFractionDigits: 2,
    }).format(amount);
}

function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
    });
}
</script>

<template>
    <Head title="Portal — Doctorato" />

    <div class="min-h-screen bg-[#F4F1EA]">
        <!-- Top bar -->
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-32 h-auto" />
                    <span class="text-xs uppercase tracking-widest text-[#C4A265] font-bold hidden sm:inline">Portal</span>
                </div>
                <div class="flex items-center gap-5">
                    <Link href="/portal/profile" class="text-sm text-[#5A6C7D] hover:text-[#0A1628] transition font-medium">
                        Profile
                    </Link>
                    <button @click="logout" class="text-sm text-[#5A6C7D] hover:text-[#0A1628] transition font-medium">
                        Sign out
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
            <!-- Greeting -->
            <div class="mb-10">
                <h1 class="text-2xl font-bold text-[#0A1628] mb-1">Welcome back, {{ customer.name }}.</h1>
                <p class="text-sm text-[#5A6C7D]">{{ customer.clinic }} · {{ customer.email }}</p>
            </div>

            <!-- Success message flash -->
            <div v-if="portalMessage" class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm text-emerald-900">{{ portalMessage }}</p>
            </div>

            <!-- Trial status panel — visible only while the customer
                 is on their free trial and hasn't activated a paid sub. -->
            <section v-if="trial" class="mb-10">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">Your trial</h2>
                <article class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-7">
                    <div class="flex items-start justify-between gap-4 flex-wrap mb-4">
                        <div>
                            <p class="text-sm text-[#5A6C7D] mb-1">Days remaining</p>
                            <p class="text-3xl font-bold text-[#0A1628] leading-none">
                                {{ trial.days_left }}<span class="text-base font-medium text-[#5A6C7D] ml-2">/ {{ trial.total_days }}</span>
                            </p>
                            <p class="text-xs text-[#8B9BAC] mt-2">Ends {{ formatDate(trial.ends_at) }}</p>
                        </div>
                        <a
                            v-if="trial.subdomain"
                            :href="`https://${trial.subdomain}.doctorato.app`"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2 bg-[#0A1628] hover:bg-[#1C2833] text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition"
                        >
                            Open my clinic
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </a>
                        <Link
                            v-else
                            href="/pricing"
                            class="inline-flex items-center gap-2 bg-[#0A1628] hover:bg-[#1C2833] text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition"
                        >
                            See plans
                        </Link>
                    </div>

                    <!-- Progress bar -->
                    <div class="h-1.5 bg-[#F4F1EA] rounded-full overflow-hidden mb-5">
                        <div class="h-full bg-[#C4A265] transition-all duration-500" :style="{ width: trial.progress_pct + '%' }"></div>
                    </div>

                    <!-- Onboarding checklist driven by drip_step (1=welcome sent, 2=tour sent, 3=case study sent) -->
                    <ul class="space-y-2.5">
                        <li class="flex items-start gap-3">
                            <span :class="trial.drip_step >= 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400'" class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                <svg v-if="trial.drip_step >= 1" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                <span v-else class="text-xs font-bold">1</span>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-[#0A1628]">Welcome guide</p>
                                <p class="text-xs text-[#8B9BAC]">Sent to your inbox the day you signed up.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span :class="trial.drip_step >= 2 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400'" class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                <svg v-if="trial.drip_step >= 2" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                <span v-else class="text-xs font-bold">2</span>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-[#0A1628]">Feature tour</p>
                                <p class="text-xs text-[#8B9BAC]">Day 3 — the three workflows worth setting up first.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span :class="trial.drip_step >= 3 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-400'" class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                <svg v-if="trial.drip_step >= 3" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                <span v-else class="text-xs font-bold">3</span>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-[#0A1628]">Case study + ROI</p>
                                <p class="text-xs text-[#8B9BAC]">Day 7 — how a Riyadh clinic doubled bookings.</p>
                            </div>
                        </li>
                    </ul>
                </article>
            </section>

            <!-- Subscriptions -->
            <section class="mb-10">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">Subscriptions</h2>

                <div v-if="!subscriptions.length" class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                    <p class="text-[#5A6C7D] text-sm">No active subscriptions on this account.</p>
                </div>

                <div v-else class="space-y-3">
                    <article
                        v-for="sub in subscriptions"
                        :key="sub.id"
                        class="bg-white rounded-2xl border border-gray-200 p-6"
                    >
                        <div class="flex items-start justify-between gap-4 mb-4">
                            <div>
                                <h3 class="font-bold text-[#0A1628] text-lg">{{ sub.plan_name }}</h3>
                                <p class="text-sm text-[#8B9BAC] capitalize">{{ sub.billing_cycle }} billing</p>
                            </div>
                            <span class="text-[10px] uppercase tracking-widest font-bold px-3 py-1 rounded-full" :class="statusBadgeClass(sub.status)">
                                {{ sub.status }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-4">
                            <div>
                                <dt class="text-xs text-[#8B9BAC] uppercase tracking-wider mb-1">Started</dt>
                                <dd class="text-[#1C2833] font-medium">{{ formatDate(sub.starts_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-[#8B9BAC] uppercase tracking-wider mb-1">
                                    {{ sub.status === 'canceled' ? 'Access ends' : 'Renews' }}
                                </dt>
                                <dd class="text-[#1C2833] font-medium">{{ formatDate(sub.next_billing_date || sub.ends_at) }}</dd>
                            </div>
                        </dl>

                        <!-- Cancel UI -->
                        <div v-if="sub.status === 'active' || sub.status === 'past_due'" class="mt-6 pt-6 border-t border-gray-100">
                            <button
                                v-if="showCancelFor !== sub.id"
                                @click="showCancelFor = sub.id"
                                class="text-sm font-semibold text-rose-700 hover:text-rose-800 transition"
                            >
                                Cancel subscription
                            </button>
                            <div v-else class="space-y-3">
                                <p class="text-sm text-[#5A6C7D]">
                                    Are you sure? You'll keep access until <strong>{{ formatDate(sub.ends_at) }}</strong>, but no further renewals will be billed.
                                </p>
                                <div class="flex gap-2">
                                    <button @click="confirmCancel(sub.id)" :disabled="cancelForm.processing" class="bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition disabled:opacity-60">
                                        Yes, cancel
                                    </button>
                                    <button @click="showCancelFor = null" class="text-sm text-[#5A6C7D] hover:text-[#0A1628] px-4 py-2">
                                        Keep subscription
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Resume UI — appears for soft-canceled subs still in grace period -->
                        <div v-else-if="sub.status === 'canceled' && sub.ends_at && new Date(sub.ends_at) > new Date()" class="mt-6 pt-6 border-t border-gray-100">
                            <p class="text-sm text-[#5A6C7D] mb-3">
                                Your subscription is scheduled to end on <strong>{{ formatDate(sub.ends_at) }}</strong>. Changed your mind?
                            </p>
                            <button @click="resumeSubscription(sub.id)" :disabled="resumeForm.processing" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition disabled:opacity-60">
                                {{ resumeForm.processing ? 'Resuming…' : 'Resume subscription' }}
                            </button>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Invoices -->
            <section>
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">Recent invoices</h2>

                <div v-if="!invoices.length" class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                    <p class="text-[#5A6C7D] text-sm">No invoices yet.</p>
                </div>

                <div v-else class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-[#FAF8F3] border-b border-gray-200">
                            <tr>
                                <th class="text-left text-xs uppercase tracking-wider text-[#8B9BAC] font-bold px-5 py-3">Invoice</th>
                                <th class="text-left text-xs uppercase tracking-wider text-[#8B9BAC] font-bold px-5 py-3">Date</th>
                                <th class="text-left text-xs uppercase tracking-wider text-[#8B9BAC] font-bold px-5 py-3">Amount</th>
                                <th class="text-left text-xs uppercase tracking-wider text-[#8B9BAC] font-bold px-5 py-3">Status</th>
                                <th class="text-right text-xs uppercase tracking-wider text-[#8B9BAC] font-bold px-5 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="inv in invoices" :key="inv.id" class="border-b border-gray-100 last:border-0 hover:bg-gray-50 transition">
                                <td class="px-5 py-4 font-mono text-xs text-[#0A1628]">{{ inv.number }}</td>
                                <td class="px-5 py-4 text-[#5A6C7D]">{{ formatDate(inv.paid_at || inv.created_at) }}</td>
                                <td class="px-5 py-4 font-semibold text-[#0A1628]">{{ formatMoney(inv.total, inv.currency) }}</td>
                                <td class="px-5 py-4">
                                    <span class="text-[10px] uppercase tracking-widest font-bold px-2.5 py-1 rounded-full" :class="statusBadgeClass(inv.status)">
                                        {{ inv.status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a :href="`/portal/invoices/${inv.id}`" target="_blank" rel="noopener" class="text-xs font-semibold text-[#1B4F72] hover:text-[#0A1628] transition inline-flex items-center gap-1 me-3" title="View invoice">
                                        View
                                    </a>
                                    <a :href="`/portal/invoices/${inv.id}?print=1`" target="_blank" rel="noopener" class="text-xs font-semibold text-[#C4A265] hover:text-[#0A1628] transition inline-flex items-center gap-1" title="Open in print dialog">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        PDF
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</template>
