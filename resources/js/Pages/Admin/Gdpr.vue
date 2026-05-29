<script setup>
/**
 * /admin/gdpr — Data Subject Rights console.
 *
 * Two actions for the DPO / privacy officer:
 *   1. Export — POSTs and streams a JSON download of everything
 *      tied to the email (demo + subscriptions + invoices + payments
 *      + audit-log mentions).
 *   2. Erase  — irreversible. PII fields overwritten, financial
 *      records preserved (Egyptian 5y tax retention), tombstone
 *      activity log row created. Requires typing ERASE to confirm.
 */
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash?.gdprMessage ?? null);

const exportForm = useForm({ email: '' });
const eraseForm = useForm({ email: '', reason: '', confirm: '' });
const showEraseConfirm = ref(false);

function downloadExport() {
    if (!exportForm.email) return;
    // Standard form POST so the response can stream a JSON download —
    // Inertia would intercept and parse it as a page payload.
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/gdpr/export';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (csrf) {
        const t = document.createElement('input');
        t.type = 'hidden';
        t.name = '_token';
        t.value = csrf;
        form.appendChild(t);
    }
    const e = document.createElement('input');
    e.type = 'hidden';
    e.name = 'email';
    e.value = exportForm.email;
    form.appendChild(e);
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

function submitErase() {
    eraseForm.post('/admin/gdpr/erase', {
        preserveScroll: true,
        onSuccess: () => {
            eraseForm.reset();
            showEraseConfirm.value = false;
        },
    });
}
</script>

<template>
    <Head title="GDPR — Doctorato" />
    <AdminLayout>
        <div class="max-w-3xl mx-auto py-8 px-4">
            <h1 class="text-2xl font-bold text-[#0A1628] mb-1">طلبات الخصوصية (GDPR / PDPL)</h1>
            <p class="text-sm text-[#5A6C7D] mb-8">
                تنفيذ حقوق العميل: الوصول (المادة 15) والمسح (المادة 17).
                السجلات المالية تبقى محفوظة للامتثال الضريبي مع إخفاء PII.
            </p>

            <div v-if="flash" class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-sm text-emerald-900">
                {{ flash }}
            </div>

            <!-- Export -->
            <section class="bg-white rounded-2xl border border-gray-200 p-6 mb-8">
                <h2 class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-4">تصدير البيانات</h2>
                <p class="text-sm text-[#5A6C7D] mb-4">
                    يحضّر ملف JSON بكامل البيانات المرتبطة بالعميل، جاهز للإرسال إليه.
                </p>
                <form @submit.prevent="downloadExport" class="flex gap-2">
                    <input
                        v-model="exportForm.email"
                        type="email"
                        placeholder="customer@example.com"
                        required
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:border-[#0A1628] focus:ring-1 focus:ring-[#0A1628] outline-none"
                    />
                    <button type="submit" class="bg-[#0A1628] hover:bg-[#1C2833] text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition">
                        تنزيل
                    </button>
                </form>
            </section>

            <!-- Erase -->
            <section class="bg-white rounded-2xl border border-rose-200 p-6">
                <h2 class="text-xs font-bold uppercase tracking-widest text-rose-700 mb-4">مسح البيانات (لا يمكن التراجع)</h2>
                <p class="text-sm text-[#5A6C7D] mb-4">
                    يستبدل اسم وبريد وهاتف العميل بقيم محذوفة. يحتفظ بسجلات الاشتراك/الفاتورة/الدفع (مطلوبة قانونياً لمدة 5 سنوات) مع إخفاء PII.
                </p>
                <form @submit.prevent="submitErase" class="space-y-3">
                    <input
                        v-model="eraseForm.email"
                        type="email"
                        placeholder="بريد العميل"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm"
                    />
                    <input
                        v-model="eraseForm.reason"
                        type="text"
                        placeholder="سبب الطلب (يُحفظ للتدقيق)"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm"
                    />
                    <div>
                        <label class="block text-xs font-semibold text-rose-700 mb-1.5">اكتب ERASE للتأكيد</label>
                        <input
                            v-model="eraseForm.confirm"
                            type="text"
                            placeholder="ERASE"
                            required
                            class="w-full border border-rose-300 rounded-lg px-4 py-2.5 text-sm font-mono"
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="eraseForm.confirm !== 'ERASE' || eraseForm.processing"
                        class="bg-rose-700 hover:bg-rose-800 disabled:bg-gray-300 text-white text-sm font-semibold rounded-lg px-5 py-2.5 transition w-full"
                    >
                        تنفيذ المسح
                    </button>
                </form>
            </section>
        </div>
    </AdminLayout>
</template>
