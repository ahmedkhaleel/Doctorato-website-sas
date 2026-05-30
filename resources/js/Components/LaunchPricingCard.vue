<script setup>
/**
 * Plan card for the launch-pricing era (post-Phase-32).
 *
 * Renders the strikethrough anchor + the launch price + the setup
 * fee block + the 3-instalment option for annual subscribers, with
 * a brand-coloured 'most popular' state for the highlighted tier.
 *
 * The component reads everything off the `plan` prop the
 * PublicContentCache.plans() builder surfaces (Phase A). It doesn't
 * format currency itself — that comes from the parent via the
 * formatPrice prop, so the same component works under any country
 * currency.
 *
 * Two emitted events:
 *   choose-monthly  — clicked the monthly checkout button
 *   choose-yearly   — clicked the annual checkout button (with the
 *                     installment-vs-upfront preference)
 */
import { computed, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    plan: { type: Object, required: true },
    isYearly: { type: Boolean, default: false },
    // Format a numeric EGP value to the active country's currency.
    formatPrice: { type: Function, default: (v) => String(v) },
    // Highlights the 'most popular' card with a stronger frame.
    highlight: { type: Boolean, default: false },
});

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

// ─── Pricing helpers — single source of truth ─────────────
const isCustom = computed(() => Boolean(props.plan.is_custom));
const launchActive = computed(() => Boolean(props.plan.is_launch_offer_active));

const regularPrice = computed(() =>
    props.isYearly ? props.plan.yearly_price : props.plan.monthly_price
);
const launchPrice = computed(() =>
    props.isYearly ? props.plan.yearly_price_launch : props.plan.monthly_price_launch
);
const activePrice = computed(() => launchActive.value ? launchPrice.value : regularPrice.value);

// Per-month figure for annual customers — important for the comparison.
const yearlyAsMonthly = computed(() => {
    if (!props.isYearly) return null;
    const yearly = activePrice.value;
    if (!yearly) return null;
    return Math.round(yearly / 12);
});

const setupFee = computed(() => {
    if (isCustom.value) return 0;
    const launch = props.plan.setup_fee_launch ?? props.plan.setup_fee;
    if (props.isYearly) {
        // Annual subscribers get the 50%-off setup price the model
        // pre-computed for us.
        return props.plan.setup_fee_yearly ?? Math.round(launch * 0.5);
    }
    return launch;
});
const setupFeeFull = computed(() => props.plan.setup_fee || 0);

// 3-instalment view for the annual customer
const showInstallments = ref(false);
const installments = computed(() => props.plan.installments || []);
const installmentsTotal = computed(() =>
    installments.value.reduce((sum, x) => sum + (x.amount || 0), 0)
);

// Specialties pill — labels per tier so the visitor knows what they
// can choose from BEFORE buying.
const specialtiesLabel = computed(() => {
    const c = props.plan.included_specialties_count;
    if (c === 'one') return tr('تخصص واحد (تختار من 6)', '1 specialty (pick from 6)');
    if (c === 'three') return tr('3 تخصصات (تختار من 6)', '3 specialties (pick from 6)');
    if (c === 'all_plus_early') return tr('كل التخصصات + Early access', 'All + early access');
    return tr('كل التخصصات (6)', 'All 6 specialties');
});

// Limits row — each item only shows when it's a meaningful number.
const limits = computed(() => {
    const out = [];
    if (props.plan.max_doctors != null) {
        out.push({
            icon: 'doctor',
            label: `${props.plan.max_doctors} ${tr('طبيب', props.plan.max_doctors > 1 ? 'doctors' : 'doctor')}`,
        });
    } else if (!isCustom.value) {
        out.push({ icon: 'doctor', label: tr('أطباء بلا حدود', 'Unlimited doctors') });
    }
    if (props.plan.max_patients != null) {
        out.push({
            icon: 'patients',
            label: `${new Intl.NumberFormat(isAr.value ? 'ar-EG' : 'en-US').format(props.plan.max_patients)} ${tr('مريض', 'patients')}`,
        });
    } else if (!isCustom.value) {
        out.push({ icon: 'patients', label: tr('مرضى بلا حدود', 'Unlimited patients') });
    }
    if (props.plan.max_branches != null) {
        out.push({
            icon: 'branch',
            label: `${props.plan.max_branches} ${tr('فرع', props.plan.max_branches > 1 ? 'branches' : 'branch')}`,
        });
    } else if (!isCustom.value) {
        out.push({ icon: 'branch', label: tr('فروع بلا حدود', 'Unlimited branches') });
    }
    if (props.plan.storage_gb) {
        out.push({ icon: 'storage', label: `${props.plan.storage_gb} GB` });
    }
    return out;
});

const featuresList = computed(() => isAr.value ? props.plan.features_ar : props.plan.features_en);
const planName = computed(() => isAr.value ? props.plan.name_ar : props.plan.name_en);
const planDesc = computed(() => isAr.value ? props.plan.description_ar : props.plan.description_en);

// Saving vs the regular yearly anchor — the headline number on the
// annual card. Format: 'وفّر 9,975 ج.م'
const yearlySavings = computed(() => {
    if (!props.isYearly || isCustom.value) return null;
    const regular = props.plan.yearly_price;
    const launch = props.plan.yearly_price_launch ?? regular;
    const setupSaving = (props.plan.setup_fee || 0) - (props.plan.setup_fee_yearly || 0);
    return Math.round((regular - launch) + setupSaving);
});

// Checkout route
const checkoutUrl = computed(() => {
    if (isCustom.value) return '/contact';
    const cycle = props.isYearly ? 'yearly' : 'monthly';
    return `/checkout/${props.plan.slug}?cycle=${cycle}`;
});
</script>

<template>
    <article
        :class="[
            'relative bg-white rounded-3xl border transition-all duration-500 overflow-hidden',
            highlight
                ? 'border-[#C4A265]/60 shadow-2xl shadow-[#C4A265]/15 lg:-translate-y-2'
                : 'border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1',
        ]"
    >
        <!-- Most-popular ribbon -->
        <div v-if="highlight" class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#C4A265] via-[#D4B876] to-[#C4A265]"></div>
        <div v-if="highlight" class="absolute top-4 inset-x-0 flex justify-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white text-[10px] font-bold uppercase tracking-widest shadow-md">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.539 1.118L10 13.347l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.363-1.118L3.567 7.82c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                {{ tr('الأكثر طلباً', 'Most popular') }}
            </span>
        </div>

        <div class="p-6 sm:p-7" :class="highlight ? 'pt-12' : 'pt-6'">

            <!-- Plan name + description -->
            <div class="mb-5">
                <h3 class="text-lg sm:text-xl font-bold text-[#1C2833]">{{ planName }}</h3>
                <p class="text-xs text-[#5A6C7D] mt-1 leading-relaxed">{{ planDesc }}</p>
            </div>

            <!-- Specialties pill -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#FBFAF6] border border-[#C4A265]/20 mb-5">
                <svg class="w-3.5 h-3.5 text-[#C4A265]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                <span class="text-[11px] font-bold text-[#1B4F72]">{{ specialtiesLabel }}</span>
            </div>

            <!-- ───── PRICE BLOCK ───── -->
            <div v-if="!isCustom" class="mb-6">
                <!-- Strikethrough regular price (only when launch is active) -->
                <div v-if="launchActive && regularPrice !== launchPrice" class="text-sm text-gray-400 line-through tabular-nums mb-0.5">
                    {{ formatPrice(regularPrice) }}{{ isYearly ? ` / ${tr('سنة', 'yr')}` : ` / ${tr('شهر', 'mo')}` }}
                </div>

                <!-- Active price -->
                <div class="flex items-baseline gap-2">
                    <span class="text-4xl sm:text-5xl font-black text-[#1C2833] tabular-nums leading-none">
                        {{ formatPrice(activePrice) }}
                    </span>
                    <span class="text-sm text-[#5A6C7D] font-medium">
                        / {{ isYearly ? tr('سنة', 'year') : tr('شهر', 'month') }}
                    </span>
                </div>

                <!-- Annual: per-month equivalent -->
                <p v-if="isYearly && yearlyAsMonthly" class="text-xs text-[#5A6C7D] mt-1.5">
                    {{ tr('يعادل', 'Equivalent to') }} <strong class="text-[#1B4F72]">{{ formatPrice(yearlyAsMonthly) }}</strong> / {{ tr('شهر', 'month') }}
                </p>

                <!-- Launch / saving badges -->
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span v-if="launchActive" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold uppercase tracking-wider border border-rose-100">
                        🔥 {{ tr('سعر الإطلاق', 'Launch price') }}
                    </span>
                    <span v-if="yearlySavings && yearlySavings > 0" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider border border-emerald-100">
                        💰 {{ tr('وفّر', 'Save') }} {{ formatPrice(yearlySavings) }}
                    </span>
                </div>
            </div>

            <!-- Custom price block -->
            <div v-else class="mb-6">
                <div class="text-3xl font-black text-[#1B4F72]">{{ tr('اتصل بنا', 'Contact us') }}</div>
                <p class="text-xs text-[#5A6C7D] mt-1">{{ tr('عرض مخصّص لاحتياجاتك', 'Tailored to your needs') }}</p>
            </div>

            <!-- ───── SETUP FEE BLOCK ───── -->
            <div v-if="!isCustom && setupFee !== undefined" class="bg-[#FBFAF6] border border-[#C4A265]/15 rounded-2xl p-3 mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-[#A88B4A]">
                            {{ tr('رسوم التشغيل', 'Setup fee') }}
                        </p>
                        <p class="text-[10px] text-[#5A6C7D] mt-0.5">{{ tr('(مرة واحدة)', '(one-time)') }}</p>
                    </div>
                    <div class="text-end">
                        <div v-if="isYearly && setupFeeFull > setupFee" class="text-[11px] text-gray-400 line-through tabular-nums">
                            {{ formatPrice(setupFeeFull) }}
                        </div>
                        <div class="text-lg font-extrabold text-[#1B4F72] tabular-nums">
                            <span v-if="setupFee > 0">{{ formatPrice(setupFee) }}</span>
                            <span v-else class="text-emerald-600">{{ tr('مجاناً', 'Free') }}</span>
                        </div>
                        <p v-if="isYearly && setupFeeFull > setupFee" class="text-[10px] text-emerald-700 font-bold mt-0.5">
                            {{ tr('خصم 50% مع السنوي', '50% off with annual') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- ───── INSTALMENT OPTION (annual only) ───── -->
            <div v-if="!isCustom && isYearly && plan.supports_installments" class="mb-5">
                <button
                    type="button"
                    @click="showInstallments = !showInstallments"
                    class="flex items-center gap-2 w-full text-start text-xs font-semibold text-[#1B4F72] hover:text-[#0D2B45] transition"
                >
                    <svg class="w-4 h-4 transition-transform" :class="showInstallments ? 'rotate-90' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span>{{ tr('متاح التقسيط على 3 دفعات بدون فوائد', 'Available: 3-instalment plan, no interest') }}</span>
                </button>
                <Transition
                    enter-active-class="transition-all duration-300 ease-out"
                    enter-from-class="opacity-0 max-h-0"
                    enter-to-class="opacity-100 max-h-64"
                    leave-active-class="transition-all duration-200 ease-in"
                    leave-from-class="opacity-100 max-h-64"
                    leave-to-class="opacity-0 max-h-0"
                >
                    <ul v-if="showInstallments" class="mt-3 space-y-1.5 text-xs">
                        <li
                            v-for="inst in installments"
                            :key="inst.index"
                            class="flex items-center justify-between bg-white border border-gray-100 rounded-lg p-2"
                        >
                            <span class="text-[#5A6C7D]">
                                {{ tr(`الدفعة ${inst.index}`, `Payment ${inst.index}`) }}
                                <span class="text-[10px] text-[#8B9BAC] ms-1">
                                    ({{ inst.due_after_months === 0 ? tr('عند الاشتراك', 'at signup') : tr(`بعد ${inst.due_after_months} شهور`, `after ${inst.due_after_months} months`) }})
                                </span>
                            </span>
                            <span class="font-bold text-[#1C2833] tabular-nums">{{ formatPrice(inst.amount) }}</span>
                        </li>
                    </ul>
                </Transition>
            </div>

            <!-- ───── LIMITS STRIP ───── -->
            <div v-if="!isCustom" class="grid grid-cols-2 gap-2 mb-5">
                <div
                    v-for="(lim, idx) in limits"
                    :key="idx"
                    class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2"
                >
                    <svg class="w-3.5 h-3.5 text-[#1B4F72] shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path v-if="lim.icon === 'doctor'" stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        <path v-else-if="lim.icon === 'patients'" stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        <path v-else-if="lim.icon === 'branch'" stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        <path v-else-if="lim.icon === 'storage'" stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                    <span class="text-[11px] font-semibold text-[#1C2833] truncate">{{ lim.label }}</span>
                </div>
            </div>

            <!-- ───── CTA BUTTON ───── -->
            <Link
                :href="checkoutUrl"
                :class="[
                    'block w-full text-center py-3 rounded-2xl font-bold text-sm transition-all',
                    highlight
                        ? 'bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white hover:shadow-xl hover:shadow-[#C4A265]/35 hover:-translate-y-0.5'
                        : 'bg-[#1B4F72] hover:bg-[#0D2B45] text-white hover:-translate-y-0.5',
                ]"
            >
                {{ isCustom ? tr('تواصل مع المبيعات', 'Talk to sales') : tr('ابدأ التجربة المجانية', 'Start free trial') }}
            </Link>

            <p v-if="!isCustom" class="text-center text-[10px] text-[#8B9BAC] mt-2">
                {{ tr('14 يوم مجاناً • بدون بطاقة ائتمان', '14-day free trial • no credit card') }}
            </p>

            <!-- ───── FEATURES LIST ───── -->
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[#A88B4A] mb-3">
                    {{ tr('ما المتضمن', 'What\'s included') }}
                </p>
                <ul class="space-y-2">
                    <li
                        v-for="(feat, idx) in featuresList"
                        :key="idx"
                        class="flex items-start gap-2"
                    >
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-xs text-[#1C2833] leading-snug">{{ feat }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </article>
</template>
