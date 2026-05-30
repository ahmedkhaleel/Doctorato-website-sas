<script setup>
/**
 * /admin/webhooks/{id} — single webhook event detail + replay.
 *
 * Shows:
 *   - Metadata (timing, ids, HMAC validity, response code)
 *   - Pretty-printed payload (the original POST body, byte-for-byte)
 *   - Response body the controller returned
 *   - Replay button (disabled when HMAC failed or row is still received)
 *   - Lineage: if this event was a replay, link back to the origin;
 *     if it has replays, list them.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    event: Object,
    replays: { type: Array, default: () => [] },
});

const page = usePage();
const flash = computed(() => page.props.flash?.success ?? null);
const errors = computed(() => page.props.errors ?? {});

const replayForm = useForm({});
function replay() {
    replayForm.post(`/admin/webhooks/${props.event.id}/replay`);
}

const canReplay = computed(() =>
    props.event.hmac_valid && ['processed', 'failed'].includes(props.event.status)
);

function statusBadge(s) {
    switch (s) {
        case 'received':  return 'bg-blue-100 text-blue-800';
        case 'processed': return 'bg-emerald-100 text-emerald-800';
        case 'failed':    return 'bg-rose-100 text-rose-800';
        case 'replayed':  return 'bg-amber-100 text-amber-800';
        default:          return 'bg-gray-100 text-gray-600';
    }
}

function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
    });
}

const prettyPayload = computed(() => JSON.stringify(props.event.payload, null, 2));
</script>

<template>
    <Head :title="`حدث #${event.id} — Webhooks`" />
    <AdminLayout :page-title="`حدث Webhook #${event.id}`">
        <Link href="/admin/webhooks" class="inline-flex items-center gap-1 text-sm text-[#1B4F72] hover:text-[#0A1628] mb-4">
            ← العودة للقائمة
        </Link>

        <div v-if="flash" class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-900">
            {{ flash }}
        </div>
        <div v-if="errors.replay" class="mb-6 bg-rose-50 border border-rose-200 rounded-xl p-4 text-sm text-rose-900">
            {{ errors.replay }}
        </div>

        <!-- Metadata grid -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-xs text-gray-500 mb-1">المصدر</p>
                    <p class="text-sm font-semibold text-gray-800">{{ event.source }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">النوع</p>
                    <p class="text-sm font-semibold text-gray-800">{{ event.event_type || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">الحالة</p>
                    <span :class="statusBadge(event.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                        {{ event.status }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">HMAC</p>
                    <span :class="event.hmac_valid ? 'text-emerald-600' : 'text-rose-600'" class="font-bold">
                        {{ event.hmac_valid ? '✓ valid' : '✗ invalid' }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Order ID</p>
                    <p class="font-mono text-xs text-gray-700 break-all">{{ event.order_id || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Transaction ID</p>
                    <p class="font-mono text-xs text-gray-700 break-all">{{ event.gateway_id || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Response Code</p>
                    <p class="font-mono text-sm font-semibold text-gray-800">{{ event.response_code || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">IP</p>
                    <p class="font-mono text-xs text-gray-700">{{ event.ip || '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Received</p>
                    <p class="text-xs text-gray-700">{{ formatDate(event.received_at) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Processed</p>
                    <p class="text-xs text-gray-700">{{ formatDate(event.processed_at) }}</p>
                </div>
            </div>

            <!-- Replay action -->
            <div class="border-t border-gray-100 pt-5 flex items-center justify-between flex-wrap gap-3">
                <div class="text-sm text-gray-600">
                    إعادة التشغيل تُنشئ <strong>سجلاً جديداً</strong> مرتبطاً بهذا الحدث ولا تعدّل الأصلي. حاجز idempotency داخل المعالج يمنع المعالجة المزدوجة.
                </div>
                <button
                    @click="replay"
                    :disabled="!canReplay || replayForm.processing"
                    class="bg-[#0A1628] hover:bg-[#1C2833] disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition inline-flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    إعادة التشغيل
                </button>
            </div>
        </div>

        <!-- Lineage -->
        <div v-if="event.origin || replays.length" class="bg-white rounded-2xl p-6 border border-gray-100 mb-6">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">Lineage</h2>
            <div v-if="event.origin" class="mb-3 text-sm">
                <span class="text-gray-500">إعادة تشغيل لـ </span>
                <Link :href="`/admin/webhooks/${event.origin.id}`" class="font-mono text-[#1B4F72] hover:underline">
                    #{{ event.origin.id }}
                </Link>
                <span class="text-gray-400 mr-2">({{ formatDate(event.origin.received_at) }})</span>
            </div>
            <div v-if="replays.length">
                <p class="text-xs text-gray-500 mb-2">إعادات تشغيل لاحقة:</p>
                <ul class="space-y-1.5 text-sm">
                    <li v-for="r in replays" :key="r.id" class="flex items-center gap-3">
                        <Link :href="`/admin/webhooks/${r.id}`" class="font-mono text-[#1B4F72] hover:underline">
                            #{{ r.id }}
                        </Link>
                        <span :class="statusBadge(r.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">{{ r.status }}</span>
                        <span class="text-xs text-gray-500">{{ formatDate(r.received_at) }}</span>
                        <span v-if="r.error" class="text-xs text-rose-600">{{ r.error }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Payload -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 mb-6">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">Payload (الجسم الأصلي)</h2>
            <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-xs font-mono overflow-x-auto leading-relaxed">{{ prettyPayload }}</pre>
        </div>

        <!-- Response + error -->
        <div v-if="event.response_body || event.error" class="bg-white rounded-2xl p-6 border border-gray-100">
            <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">استجابة المعالج</h2>
            <div v-if="event.error" class="mb-3 bg-rose-50 border border-rose-200 rounded-lg p-3 text-sm text-rose-900">
                <strong>Error:</strong> {{ event.error }}
            </div>
            <pre v-if="event.response_body" class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-xs font-mono overflow-x-auto">{{ event.response_body }}</pre>
        </div>
    </AdminLayout>
</template>
