<script setup>
/**
 * Granular cookie consent banner.
 *
 * Three categories the visitor can opt in/out of independently:
 *
 *   necessary   — session, CSRF, country detection, referral capture.
 *                 Cannot be declined; the site doesnt work without them.
 *                 Shown to acknowledge legal basis (legitimate interest).
 *   analytics   — Google Analytics 4, Tag Manager.
 *   marketing   — Future retargeting tags. Off by default; only
 *                 enabled if the visitor explicitly opts in.
 *
 * Choice is stored in `doc_cookie_consent` cookie as a JSON blob
 * with the three flags + version + timestamp:
 *   { v: 1, necessary: true, analytics: true, marketing: false, ts: 1717... }
 *
 * The version field lets us re-prompt visitors when the categories
 * change (incrementing v invalidates older consent and re-shows
 * the banner).
 *
 * Events:
 *   - 'cookie-consent-changed' on window with the consent payload
 *     in detail. Analytics/marketing tag loaders listen for this
 *     to gate themselves.
 *
 * Visual states:
 *   - First-time visitor → compact banner with Accept / Reject / Customise.
 *   - Customise open → expanded panel with three category toggles.
 *   - Already consented → component renders nothing.
 *
 * Note: the install-prompt cookie + the referral cookie pre-date
 * this component and are already labelled 'necessary' (session +
 * attribution). We don't surface them as toggles because the
 * customer either gets the portal experience or they don't.
 */
import { ref, computed, onMounted } from 'vue';

const COOKIE_NAME = 'doc_cookie_consent';
const CONSENT_VERSION = 1;
const COOKIE_DAYS = 180;

const visible = ref(false);
const showCustomise = ref(false);

// Defaults — necessary always true (can't be toggled off), the
// others off until explicit opt-in.
const choices = ref({ analytics: false, marketing: false });

function readConsent() {
    const raw = document.cookie.split(';').map((c) => c.trim())
        .find((c) => c.startsWith(`${COOKIE_NAME}=`));
    if (!raw) return null;
    try {
        const value = decodeURIComponent(raw.split('=')[1]);
        const parsed = JSON.parse(value);
        if (parsed?.v !== CONSENT_VERSION) return null;  // re-prompt on version bump
        return parsed;
    } catch (_) { return null; }
}

function writeConsent(state) {
    const payload = JSON.stringify({
        v: CONSENT_VERSION,
        necessary: true,
        analytics: !!state.analytics,
        marketing: !!state.marketing,
        ts: Math.floor(Date.now() / 1000),
    });
    const expires = new Date(Date.now() + COOKIE_DAYS * 86400 * 1000).toUTCString();
    document.cookie = `${COOKIE_NAME}=${encodeURIComponent(payload)};expires=${expires};path=/;SameSite=Lax`;
    window.dispatchEvent(new CustomEvent('cookie-consent-changed', { detail: JSON.parse(payload) }));
}

function acceptAll() {
    choices.value = { analytics: true, marketing: true };
    writeConsent(choices.value);
    visible.value = false;
}

function rejectNonNecessary() {
    choices.value = { analytics: false, marketing: false };
    writeConsent(choices.value);
    visible.value = false;
}

function saveCustom() {
    writeConsent(choices.value);
    visible.value = false;
}

onMounted(() => {
    const existing = readConsent();
    if (!existing) {
        visible.value = true;
    }
});
</script>

<template>
    <div
        v-if="visible"
        class="fixed inset-x-0 bottom-0 z-50 bg-white border-t border-gray-200 shadow-2xl"
        role="dialog"
        aria-labelledby="cookie-consent-title"
        aria-describedby="cookie-consent-body"
    >
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 sm:py-5">
            <!-- Compact view -->
            <div v-if="!showCustomise" class="flex flex-col sm:flex-row sm:items-start gap-4">
                <div class="flex-1 min-w-0">
                    <h2 id="cookie-consent-title" class="text-sm font-bold text-[#0A1628] mb-1">
                        ملفات تعريف الارتباط
                    </h2>
                    <p id="cookie-consent-body" class="text-xs text-[#5A6C7D] leading-relaxed">
                        نستخدم ملفات تعريف ارتباط ضرورية لتشغيل الموقع، واختيارية لقياس الاستخدام والتسويق.
                        يمكنك القبول، الرفض، أو تخصيص اختيارك.
                        <a href="/privacy" class="text-[#0A1628] underline">سياسة الخصوصية</a>.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2 shrink-0">
                    <button @click="showCustomise = true" class="text-xs font-semibold text-[#5A6C7D] hover:text-[#0A1628] px-4 py-2 border border-gray-200 rounded-lg transition">
                        تخصيص
                    </button>
                    <button @click="rejectNonNecessary" class="text-xs font-semibold text-[#5A6C7D] hover:text-[#0A1628] px-4 py-2 border border-gray-200 rounded-lg transition">
                        ضرورية فقط
                    </button>
                    <button @click="acceptAll" class="text-xs font-semibold bg-[#0A1628] hover:bg-[#1C2833] text-white px-4 py-2 rounded-lg transition">
                        قبول الكل
                    </button>
                </div>
            </div>

            <!-- Customise view -->
            <div v-else>
                <div class="flex items-start justify-between gap-4 mb-4">
                    <h2 class="text-sm font-bold text-[#0A1628]">تخصيص الاختيار</h2>
                    <button @click="showCustomise = false" class="text-xs text-[#8B9BAC] hover:text-[#0A1628]">
                        إلغاء
                    </button>
                </div>
                <div class="space-y-3">
                    <!-- Necessary — always on -->
                    <div class="flex items-start gap-3 p-3 bg-[#FAF8F3] rounded-lg">
                        <input type="checkbox" checked disabled class="mt-1 w-4 h-4 text-[#C4A265] rounded" aria-label="ضرورية" />
                        <div>
                            <p class="text-xs font-bold text-[#0A1628]">ضرورية (لا يمكن تعطيلها)</p>
                            <p class="text-[11px] text-[#5A6C7D]">
                                جلسة الدخول، CSRF، حفظ العملة، اكتشاف الدولة، التقاط رمز الإحالة. الموقع لا يعمل بدونها.
                            </p>
                        </div>
                    </div>
                    <!-- Analytics -->
                    <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-[#FAF8F3]">
                        <input type="checkbox" v-model="choices.analytics" class="mt-1 w-4 h-4 text-[#C4A265] rounded" />
                        <div>
                            <p class="text-xs font-bold text-[#0A1628]">قياس الاستخدام (Analytics)</p>
                            <p class="text-[11px] text-[#5A6C7D]">
                                Google Analytics 4 لقياس الصفحات الأكثر زيارة. لا نُرسل بيانات شخصية.
                            </p>
                        </div>
                    </label>
                    <!-- Marketing -->
                    <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-[#FAF8F3]">
                        <input type="checkbox" v-model="choices.marketing" class="mt-1 w-4 h-4 text-[#C4A265] rounded" />
                        <div>
                            <p class="text-xs font-bold text-[#0A1628]">تسويق وإعادة استهداف</p>
                            <p class="text-[11px] text-[#5A6C7D]">
                                Tag Manager + بكسلات المنصات لعرض إعلانات أكثر صلة. مُعطّل افتراضياً.
                            </p>
                        </div>
                    </label>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button @click="rejectNonNecessary" class="text-xs font-semibold text-[#5A6C7D] hover:text-[#0A1628] px-4 py-2 border border-gray-200 rounded-lg transition">
                        رفض الاختيارية
                    </button>
                    <button @click="saveCustom" class="text-xs font-semibold bg-[#0A1628] hover:bg-[#1C2833] text-white px-4 py-2 rounded-lg transition">
                        حفظ اختياري
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
