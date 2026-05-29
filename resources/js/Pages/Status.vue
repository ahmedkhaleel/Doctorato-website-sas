<script setup>
/**
 * Branded /status page. Renders an at-a-glance "everything is fine"
 * (or not) summary plus a row per subsystem. Designed to be useful
 * during outages without being so detailed that it leaks internals.
 *
 * Polls the underlying /healthz/deep every 30s so the page stays
 * current even if a viewer leaves the tab open.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    overall: String,
    checks: Object,
    lastChecked: String,
});

const liveChecks = ref({ ...props.checks });
const liveOverall = ref(props.overall);
const lastChecked = ref(props.lastChecked);
let pollHandle = null;

async function pollHealth() {
    try {
        const res = await fetch('/healthz/deep', { headers: { Accept: 'application/json' } });
        const data = await res.json();
        const remap = {
            ok: 'operational',
            degraded: 'degraded',
            down: 'down',
        };
        liveChecks.value = {
            website:  { name: 'Website',          status: 'operational', description: 'doctorato.com is responding normally.' },
            database: { name: 'Database',         status: remap[data.checks?.database?.status] || 'down', description: '' },
            queue:    { name: 'Background jobs',  status: remap[data.checks?.queue?.status]    || 'down', description: '' },
            storage:  { name: 'File storage',     status: remap[data.checks?.storage?.status]  || 'down', description: '' },
        };
        liveOverall.value = Object.values(liveChecks.value).every(c => c.status === 'operational') ? 'operational' : 'degraded';
        lastChecked.value = data.time || new Date().toISOString();
    } catch (e) {
        // Network error → leave last state, increment a soft warning
    }
}

onMounted(() => { pollHandle = setInterval(pollHealth, 30_000); });
onUnmounted(() => { if (pollHandle) clearInterval(pollHandle); });

const statusColor = computed(() => {
    if (liveOverall.value === 'operational') return 'bg-emerald-50 border-emerald-200 text-emerald-900';
    if (liveOverall.value === 'degraded')    return 'bg-amber-50 border-amber-200 text-amber-900';
    return 'bg-rose-50 border-rose-200 text-rose-900';
});

const statusHeadline = computed(() => {
    if (liveOverall.value === 'operational') return 'All systems operational';
    if (liveOverall.value === 'degraded')    return 'Some systems are experiencing issues';
    return 'A major outage is in progress';
});

function dotColor(status) {
    if (status === 'operational') return 'bg-emerald-500';
    if (status === 'degraded')    return 'bg-amber-500';
    return 'bg-rose-500';
}

function statusLabel(status) {
    if (status === 'operational') return 'Operational';
    if (status === 'degraded')    return 'Degraded';
    return 'Down';
}

function statusBadgeClass(status) {
    if (status === 'operational') return 'bg-emerald-100 text-emerald-800';
    if (status === 'degraded')    return 'bg-amber-100 text-amber-800';
    return 'bg-rose-100 text-rose-800';
}

const lastCheckedHuman = computed(() => {
    try {
        return new Date(lastChecked.value).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    } catch { return ''; }
});
</script>

<template>
    <SeoHead
        title="System status"
        description="Real-time status for Doctorato — website, database, background jobs, and file storage."
        :breadcrumbs="[
            { name: 'Home', url: '/' },
            { name: 'Status', url: '/status' },
        ]"
    />
    <MainLayout>
        <section class="bg-[#F4F1EA] min-h-[60vh] py-16">
            <div class="max-w-3xl mx-auto px-4 sm:px-6">
                <header class="text-center mb-10">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#C4A265] mb-3">System status</p>
                    <h1 class="text-3xl sm:text-4xl font-bold text-[#0A1628] mb-2">Doctorato status</h1>
                    <p class="text-sm text-[#5A6C7D]">
                        Last checked at {{ lastCheckedHuman }} · auto-refreshes every 30 seconds
                    </p>
                </header>

                <!-- Overall banner -->
                <div :class="statusColor" class="rounded-2xl border p-6 flex items-center gap-4 mb-8">
                    <span class="relative flex h-3 w-3 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-50" :class="dotColor(liveOverall === 'operational' ? 'operational' : 'degraded')"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3" :class="dotColor(liveOverall === 'operational' ? 'operational' : 'degraded')"></span>
                    </span>
                    <div>
                        <p class="font-bold text-lg">{{ statusHeadline }}</p>
                    </div>
                </div>

                <!-- Per-subsystem rows -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <div
                        v-for="(check, key) in liveChecks"
                        :key="key"
                        class="px-6 py-5 border-b border-gray-100 last:border-0 flex items-start justify-between gap-4"
                    >
                        <div class="flex items-start gap-3">
                            <span class="w-2.5 h-2.5 rounded-full mt-2 shrink-0" :class="dotColor(check.status)"></span>
                            <div>
                                <p class="font-bold text-[#0A1628]">{{ check.name }}</p>
                                <p v-if="check.description" class="text-xs text-[#8B9BAC] mt-0.5">{{ check.description }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] uppercase tracking-widest font-bold px-2.5 py-1 rounded-full shrink-0" :class="statusBadgeClass(check.status)">
                            {{ statusLabel(check.status) }}
                        </span>
                    </div>
                </div>

                <p class="text-xs text-[#8B9BAC] text-center mt-8">
                    Need to report an issue?
                    <a href="mailto:info@doctorato.com" class="text-[#C4A265] font-semibold hover:underline">info@doctorato.com</a>
                </p>
            </div>
        </section>
    </MainLayout>
</template>
