<script setup>
/**
 * /portal/refer — the customer's referral hub.
 *
 * Three states the page handles:
 *   1. No active sub → empty state pointing to /pricing.
 *   2. Active sub, no referrals yet → big "copy link" CTA + how-it-works.
 *   3. Active sub with referrals → credit balance + list of referred clinics.
 *
 * Copy-link uses the modern Clipboard API with a fallback for the
 * cPanel shared environment (older Safari versions). Toast clears
 * after 2 seconds — no library, just a ref + setTimeout.
 */
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    hasActiveSubscription: Boolean,
    referralCode: String,
    shareUrl: String,
    creditCents: Number,
    creditFormatted: String,
    currency: String,
    rewardBps: Number,
    referrals: Array,
});

const logoutForm = useForm({});
function logout() { logoutForm.post('/portal/logout'); }

const copied = ref(false);
function copyShareUrl() {
    if (!props.shareUrl) return;
    const url = props.shareUrl;
    const reset = () => {
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    };
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(reset).catch(fallbackCopy);
    } else {
        fallbackCopy();
    }
    function fallbackCopy() {
        const ta = document.createElement('textarea');
        ta.value = url;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try { document.execCommand('copy'); reset(); } catch (_) {}
        document.body.removeChild(ta);
    }
}

const rewardPct = computed(() => Math.round((props.rewardBps || 0) / 100));
const whatsappShare = computed(() => {
    if (!props.shareUrl) return '#';
    const msg = `I've been using Doctorato to manage my clinic — try it free for 14 days: ${props.shareUrl}`;
    return `https://wa.me/?text=${encodeURIComponent(msg)}`;
});
const emailShare = computed(() => {
    if (!props.shareUrl) return '#';
    const subject = encodeURIComponent('Doctorato — clinic management I think you should try');
    const body = encodeURIComponent(`Hey,\n\nI've been using Doctorato to run my clinic and thought you might find it useful. They give a 14-day free trial — use this link so we both get a credit:\n\n${props.shareUrl}\n\n`);
    return `mailto:?subject=${subject}&body=${body}`;
});

function statusBadgeClass(status) {
    switch (status) {
        case 'active':   return 'bg-emerald-100 text-emerald-800';
        case 'past_due': return 'bg-amber-100 text-amber-800';
        case 'canceled': return 'bg-gray-100 text-gray-600';
        default:         return 'bg-gray-100 text-gray-600';
    }
}
function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="Refer a clinic — Doctorato" />

    <div class="min-h-screen bg-[#F4F1EA]">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
                <Link href="/portal/dashboard" class="flex items-center gap-4">
                    <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-32 h-auto" />
                    <span class="text-xs uppercase tracking-widest text-[#C4A265] font-bold hidden sm:inline">Portal</span>
                </Link>
                <div class="flex items-center gap-5">
                    <Link href="/portal/dashboard" class="text-sm text-[#5A6C7D] hover:text-[#0A1628] font-medium">Dashboard</Link>
                    <Link href="/portal/profile" class="text-sm text-[#5A6C7D] hover:text-[#0A1628] font-medium">Profile</Link>
                    <button @click="logout" class="text-sm text-[#5A6C7D] hover:text-[#0A1628] font-medium">Sign out</button>
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
            <div class="mb-10">
                <h1 class="text-2xl font-bold text-[#0A1628] mb-1">Refer a clinic, earn credit.</h1>
                <p class="text-sm text-[#5A6C7D]">
                    For every clinic that signs up through your link and starts a paid plan, you get
                    <strong class="text-[#0A1628]">{{ rewardPct }}% of their first payment</strong> as
                    credit toward your next invoice.
                </p>
            </div>

            <!-- Empty state — no active sub yet -->
            <div v-if="!hasActiveSubscription" class="bg-white rounded-2xl border border-gray-200 p-8 text-center">
                <div class="w-12 h-12 rounded-full bg-[#FAF8F3] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-[#0A1628] mb-2">Activate a plan to unlock referrals</h2>
                <p class="text-sm text-[#5A6C7D] mb-6 max-w-md mx-auto">
                    Your referral link is generated once you start a paid subscription — that way every
                    credit you earn lands on a real invoice.
                </p>
                <Link href="/pricing" class="inline-flex items-center gap-2 bg-[#0A1628] hover:bg-[#1C2833] text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition">
                    See plans →
                </Link>
            </div>

            <template v-else>
                <!-- Credit summary -->
                <section class="grid sm:grid-cols-2 gap-4 mb-8">
                    <article class="bg-white rounded-2xl border border-gray-200 p-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-2">Credit balance</p>
                        <p class="text-3xl font-bold text-[#0A1628] leading-none">{{ creditFormatted }} <span class="text-base font-medium text-[#5A6C7D]">{{ currency }}</span></p>
                        <p class="text-xs text-[#8B9BAC] mt-3">Applied automatically to your next renewal invoice.</p>
                    </article>
                    <article class="bg-white rounded-2xl border border-gray-200 p-6">
                        <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-2">Clinics referred</p>
                        <p class="text-3xl font-bold text-[#0A1628] leading-none">{{ referrals.length }}</p>
                        <p class="text-xs text-[#8B9BAC] mt-3">Counted once they activate a paid plan.</p>
                    </article>
                </section>

                <!-- Share card -->
                <section class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-7 mb-8">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">Your share link</h2>
                    <div class="flex flex-col sm:flex-row gap-2 mb-4">
                        <input
                            type="text"
                            :value="shareUrl"
                            readonly
                            class="flex-1 bg-[#F4F1EA] border border-gray-200 rounded-lg px-4 py-2.5 text-sm text-[#0A1628] font-mono"
                            @focus="$event.target.select()"
                        />
                        <button
                            @click="copyShareUrl"
                            class="inline-flex items-center justify-center gap-2 bg-[#0A1628] hover:bg-[#1C2833] text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition min-w-[120px]"
                        >
                            <svg v-if="!copied" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" /></svg>
                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            {{ copied ? 'Copied!' : 'Copy link' }}
                        </button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a :href="whatsappShare" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-[#FAF8F3] hover:bg-[#F4F1EA] border border-gray-200 text-[#0A1628] text-sm font-medium rounded-lg px-4 py-2 transition">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        <a :href="emailShare" class="inline-flex items-center gap-2 bg-[#FAF8F3] hover:bg-[#F4F1EA] border border-gray-200 text-[#0A1628] text-sm font-medium rounded-lg px-4 py-2 transition">
                            <svg class="w-4 h-4 text-[#5A6C7D]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            Email
                        </a>
                    </div>
                    <p class="text-xs text-[#8B9BAC] mt-4">
                        Your code: <span class="font-mono font-semibold text-[#0A1628]">{{ referralCode }}</span>
                    </p>
                </section>

                <!-- Referrals list -->
                <section v-if="referrals.length">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">Your referrals</h2>
                    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-[#FAF8F3] border-b border-gray-200">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-[#5A6C7D]">
                                    <th class="px-5 py-3">Clinic</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Activated</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="r in referrals" :key="r.id">
                                    <td class="px-5 py-3 text-[#0A1628] font-medium">{{ r.clinic_name }}</td>
                                    <td class="px-5 py-3">
                                        <span :class="statusBadgeClass(r.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize">{{ r.status }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right text-[#5A6C7D]">{{ formatDate(r.activated_at) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </template>
        </main>
    </div>
</template>
