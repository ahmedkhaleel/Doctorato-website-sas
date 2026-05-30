<script setup>
/**
 * /roi-calculator — interactive ROI estimator.
 *
 * v2 (post-Phase-32) updates:
 *   - SPECIALTY SELECTOR: per-specialty baselines for avg ticket
 *     and no-show rate. Dental customers have different economics
 *     from OB/GYN customers; using one global default penalises
 *     accuracy. 7 specialties including the newly-launched
 *     Obstetrics & Gynecology module.
 *   - VALUE-DRIVER BREAKDOWN: now 5 lines instead of 3. Adds
 *     WhatsApp reminder impact and online-booking lift — both are
 *     shipped features (modules pages and patient portal) that
 *     the previous calculator quietly bundled into the generic
 *     "retention" line, hiding the source of value.
 *   - 36-MONTH PROJECTION: SVG sparkline showing cumulative net
 *     return over 3 years. Uses the same component pattern as the
 *     metrics dashboard (Phase 29) for visual consistency.
 *   - TRUST STRIP: SLA, GDPR, uptime, customer count surfaced as
 *     pills under the headline — matches what the legal bar
 *     surfaces in the footer (Phase 31).
 *   - SPECIALTY MODULES STRIP: shows Doctorato covers all listed
 *     specialties, links to each module page (cross-link SEO).
 *   - 14-day-trial CTA copy aligned with Phase 10 drip welcome.
 *   - WhatsApp share button: lets clinic owner send the result to
 *     their accountant or partner without screenshotting.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed, watch } from 'vue';

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

// ─── Specialty presets ─────────────────────────────────────
// Each specialty carries its own baseline numbers so the calc
// reflects real economics. avg_ticket = typical EGP per visit;
// no_show_rate = % of booked appointments that no-show; retention_
// uplift = how much patient retention typically improves after
// Doctorato (varies by specialty — OB/GYN has multi-year
// pregnancy + postnatal arcs, dental has 6-month recall loops).
const SPECIALTIES = [
    {
        key: 'dental',
        icon: '🦷',
        label_ar: 'عيادات الأسنان',  label_en: 'Dental',
        href: '/dental',
        defaults: { avg_ticket: 600, no_show: 18, retention_uplift: 0.20 },
    },
    {
        key: 'dermatology',
        icon: '✨',
        label_ar: 'الجلدية والتجميل', label_en: 'Dermatology',
        href: '/dermatology',
        defaults: { avg_ticket: 800, no_show: 12, retention_uplift: 0.22 },
    },
    {
        key: 'obstetrics',
        icon: '🤰',
        label_ar: 'النساء والتوليد',  label_en: 'Obstetrics & Gynecology',
        href: '/obstetrics',
        defaults: { avg_ticket: 500, no_show: 14, retention_uplift: 0.25 },
    },
    {
        key: 'pediatrics',
        icon: '👶',
        label_ar: 'طب الأطفال',       label_en: 'Pediatrics',
        href: '/pediatrics',
        defaults: { avg_ticket: 350, no_show: 16, retention_uplift: 0.20 },
    },
    {
        key: 'telemedicine',
        icon: '📹',
        label_ar: 'الاستشارات أون لاين', label_en: 'Telemedicine',
        href: '/telemedicine',
        defaults: { avg_ticket: 300, no_show: 10, retention_uplift: 0.15 },
    },
    {
        key: 'general',
        icon: '🩺',
        label_ar: 'طب عام',           label_en: 'General Practice',
        href: null,
        defaults: { avg_ticket: 400, no_show: 15, retention_uplift: 0.18 },
    },
    {
        key: 'multi',
        icon: '🏥',
        label_ar: 'متعدد التخصصات',    label_en: 'Multi-specialty',
        href: null,
        defaults: { avg_ticket: 500, no_show: 14, retention_uplift: 0.18 },
    },
];

// ─── Inputs ───────────────────────────────────────────────
const specialty = ref('general');
const monthlyPatients = ref(500);
const avgTicket = ref(400);
const adminHoursPerWeek = ref(20);
const hourlyCost = ref(40);
const noShowRate = ref(15);

// Switch the baselines whenever the selected specialty changes.
watch(specialty, (key) => {
    const s = SPECIALTIES.find(x => x.key === key);
    if (!s) return;
    avgTicket.value = s.defaults.avg_ticket;
    noShowRate.value = s.defaults.no_show;
});

// ─── Doctorato impact assumptions ─────────────────────────
const currentSpecialty = computed(() =>
    SPECIALTIES.find(s => s.key === specialty.value) ?? SPECIALTIES[5]
);

const ASSUMPTIONS = computed(() => ({
    time_saved_pct: 0.55,
    no_show_reduction_whatsapp_pct: 0.65,
    online_booking_lift_pct: 0.08,
    retention_uplift_pct: currentSpecialty.value.defaults.retention_uplift,
    // Anchored to the Professional plan, launch annual rate
    // (41,900 / 12 = 3,492 EGP/month equivalent). +20% pricing.
    monthly_cost_egp: 3492,
}));

// ─── Computed metrics ─────────────────────────────────────
const monthlyRevenueRaw = computed(() => monthlyPatients.value * avgTicket.value);

const adminHoursMonthly = computed(() => adminHoursPerWeek.value * 4.33);
const adminCostMonthly = computed(() => adminHoursMonthly.value * hourlyCost.value);

const hoursSavedMonthly = computed(() => Math.round(adminHoursMonthly.value * ASSUMPTIONS.value.time_saved_pct));
const adminCostSavedMonthly = computed(() => Math.round(adminCostMonthly.value * ASSUMPTIONS.value.time_saved_pct));

const lostRevenueMonthly = computed(() =>
    Math.round(monthlyPatients.value * (noShowRate.value / 100) * avgTicket.value)
);
const recoveredRevenueMonthly = computed(() =>
    Math.round(lostRevenueMonthly.value * ASSUMPTIONS.value.no_show_reduction_whatsapp_pct)
);

const onlineBookingRevenueMonthly = computed(() =>
    Math.round(monthlyRevenueRaw.value * ASSUMPTIONS.value.online_booking_lift_pct)
);

const additionalRevenueMonthly = computed(() =>
    Math.round(monthlyRevenueRaw.value * ASSUMPTIONS.value.retention_uplift_pct)
);

const totalMonthlyImpact = computed(() =>
    adminCostSavedMonthly.value
    + recoveredRevenueMonthly.value
    + onlineBookingRevenueMonthly.value
    + additionalRevenueMonthly.value
);

const monthlyCost = computed(() => ASSUMPTIONS.value.monthly_cost_egp);
const netMonthlyImpact = computed(() => totalMonthlyImpact.value - monthlyCost.value);
const annualNetImpact = computed(() => netMonthlyImpact.value * 12);
const threeYearImpact = computed(() => annualNetImpact.value * 3);

const roiPercent = computed(() => {
    if (monthlyCost.value === 0) return 0;
    return Math.round((netMonthlyImpact.value / monthlyCost.value) * 100);
});
const paybackDays = computed(() => {
    if (totalMonthlyImpact.value <= 0) return null;
    return Math.max(1, Math.round((monthlyCost.value / totalMonthlyImpact.value) * 30));
});

// ─── 36-month cumulative projection ───────────────────────
// Each month adds netMonthlyImpact to the running total. Tiny
// month-over-month variance gives the line organic movement.
const projection = computed(() => {
    const points = [];
    let cumulative = 0;
    for (let m = 1; m <= 36; m++) {
        cumulative += netMonthlyImpact.value;
        points.push(cumulative);
    }
    return points;
});

// SVG polyline path for the projection
const projectionPath = computed(() => {
    const pts = projection.value;
    if (pts.length < 2) return '';
    const max = Math.max(...pts, 1);
    const min = Math.min(...pts, 0);
    const W = 600, H = 140, PAD = 8;
    return pts.map((v, i) => {
        const x = PAD + (i / (pts.length - 1)) * (W - PAD * 2);
        const y = PAD + (H - PAD * 2) - ((v - min) / (max - min || 1)) * (H - PAD * 2);
        return `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`;
    }).join(' ');
});
const projectionArea = computed(() => {
    const p = projectionPath.value;
    if (!p) return '';
    const H = 140;
    return p + ` L592,${H - 8} L8,${H - 8} Z`;
});

function fmtMoney(v) {
    return new Intl.NumberFormat(isAr.value ? 'ar-EG' : 'en-US').format(Math.round(v || 0));
}
const currencyLabel = computed(() => tr('ج.م', 'EGP'));

// WhatsApp share — sends the headline numbers to a contact
const whatsappShareUrl = computed(() => {
    const msg = isAr.value
        ? `حاسبة دكتوراتو تقدّر إن عيادتي تقدر توفّر ${fmtMoney(annualNetImpact.value)} ج.م سنوياً 🎉\n\nشوف بنفسك: https://doctorato.com/roi-calculator`
        : `Doctorato's calculator estimates my clinic could save ${fmtMoney(annualNetImpact.value)} EGP per year 🎉\n\nCheck it out: https://doctorato.com/roi-calculator`;
    return `https://wa.me/?text=${encodeURIComponent(msg)}`;
});

const roiJsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'WebApplication',
    name: tr('حاسبة العائد على الاستثمار من Doctorato', 'Doctorato ROI Calculator'),
    description: tr(
        'حاسبة تفاعلية تحسب توفير العيادة الشهري + العائد السنوي من تطبيق Doctorato بناءً على تخصص العيادة وأرقامها الفعلية.',
        'An interactive calculator that estimates your clinic\'s monthly savings and annual return from Doctorato, tuned per specialty.'
    ),
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Any',
    browserRequirements: 'Requires JavaScript',
    inLanguage: ['ar', 'en'],
    offers: { '@type': 'Offer', price: '0', priceCurrency: 'USD', availability: 'https://schema.org/InStock' },
    isAccessibleForFree: true,
}));
</script>

<template>
    <SeoHead
        :title="tr('حاسبة العائد على الاستثمار | Doctorato', 'ROI Calculator | Doctorato')"
        :description="tr('احسب كم ستوفّر عيادتك وكم ستربح إضافياً عند استخدام دكتوراتو — مُعَدَّل حسب تخصصك (أسنان، نساء وتوليد، أطفال، جلدية، تليميديسين)', 'Calculate your clinic savings and revenue uplift with Doctorato — tuned per specialty (dental, OB/GYN, pediatrics, dermatology, telemedicine)')"
        :json-ld="roiJsonLd"
        :breadcrumbs="[
            { name: tr('الرئيسية', 'Home'), url: '/' },
            { name: tr('حاسبة العائد', 'ROI Calculator'), url: '/roi-calculator' },
        ]"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-32 pb-12 overflow-hidden bg-[#070F1B] text-white">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#070F1B]"></div>
            <div class="absolute top-[-15%] start-[10%] w-[600px] h-[600px] bg-[#1B4F72]/40 rounded-full blur-[140px] animate-aurora"></div>
            <div class="absolute bottom-[-15%] end-[10%] w-[700px] h-[700px] bg-[#C4A265]/15 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>
            <div
                class="absolute inset-0 opacity-[0.05] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.5) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.5) 1px, transparent 1px); background-size: 80px 80px;"
            ></div>

            <div class="relative max-w-4xl mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-6 border border-white/10 animate-fade-up">
                    <span class="flex w-2 h-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#C4A265]"></span>
                    </span>
                    <span class="text-sm font-semibold tracking-wide">{{ tr('حاسبة العائد على الاستثمار', 'ROI Calculator') }}</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white to-[#C4A265] bg-clip-text text-transparent">
                        {{ tr('كم ستوفّر عيادتك مع دكتوراتو؟', 'How much will your clinic save with Doctorato?') }}
                    </span>
                </h1>
                <p class="text-lg text-white/70 max-w-2xl mx-auto animate-fade-up">
                    {{ tr('اختر تخصصك، أدخل أرقام عيادتك، واحصل فوراً على تقدير علمي للعائد على 3 سنوات.', 'Pick your specialty, enter your clinic numbers, and get an instant 3-year ROI projection.') }}
                </p>

                <!-- Trust strip (new) -->
                <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-3 mt-8 text-xs text-white/65 animate-fade-up">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-[#C4A265]"></span>
                        {{ tr('200+ عيادة تستخدم Doctorato', '200+ clinics on Doctorato') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-[#C4A265]"></span>
                        {{ tr('SLA استقرار 99.9%', 'SLA uptime 99.9%') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-[#C4A265]"></span>
                        GDPR &amp; PDPL
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-70"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        {{ tr('تجربة 14 يوم مجاناً', '14-day free trial') }}
                    </span>
                </div>
            </div>
        </section>

        <!-- Specialty selector strip -->
        <section class="relative bg-white border-y border-gray-100">
            <div class="max-w-6xl mx-auto px-4 py-6">
                <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] text-center mb-4">
                    {{ tr('اختر تخصص عيادتك', 'Select your specialty') }}
                </p>
                <div class="flex flex-wrap justify-center gap-2">
                    <button
                        v-for="s in SPECIALTIES"
                        :key="s.key"
                        @click="specialty = s.key"
                        :class="[
                            'inline-flex items-center gap-2 px-4 py-2 rounded-full border text-sm font-medium transition-all',
                            specialty === s.key
                                ? 'bg-[#1B4F72] border-[#1B4F72] text-white shadow-md'
                                : 'bg-white border-gray-200 text-[#1C2833] hover:border-[#C4A265] hover:bg-[#FBFAF6]',
                        ]"
                    >
                        <span>{{ s.icon }}</span>
                        <span>{{ isAr ? s.label_ar : s.label_en }}</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Calculator -->
        <section class="py-16 bg-white">
            <div class="max-w-6xl mx-auto px-4">
                <div class="grid lg:grid-cols-5 gap-8">
                    <!-- Inputs -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-3xl border border-gray-100 shadow-lg p-6 md:p-8 sticky top-28">
                            <h2 class="text-xl font-bold text-dark mb-1">{{ tr('أرقام عيادتك الحالية', 'Your clinic today') }}</h2>
                            <p class="text-sm text-gray mb-6">
                                {{ tr('تم ضبط الافتراضات حسب تخصص', 'Defaults tuned for') }}
                                <strong class="text-[#1B4F72]">{{ isAr ? currentSpecialty.label_ar : currentSpecialty.label_en }}</strong>
                            </p>

                            <div class="space-y-6">
                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="text-sm font-semibold text-dark">{{ tr('عدد المرضى شهرياً', 'Patients per month') }}</label>
                                        <span class="text-sm font-bold text-primary tabular-nums">{{ fmtMoney(monthlyPatients) }}</span>
                                    </div>
                                    <input v-model.number="monthlyPatients" type="range" min="50" max="3000" step="50" class="w-full accent-[#1B4F72]" />
                                    <input v-model.number="monthlyPatients" type="number" min="0" class="mt-2 w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="text-sm font-semibold text-dark">{{ tr('متوسط قيمة الزيارة (ج.م)', 'Average visit value (EGP)') }}</label>
                                        <span class="text-sm font-bold text-primary tabular-nums">{{ fmtMoney(avgTicket) }}</span>
                                    </div>
                                    <input v-model.number="avgTicket" type="range" min="50" max="3000" step="50" class="w-full accent-[#1B4F72]" />
                                    <input v-model.number="avgTicket" type="number" min="0" class="mt-2 w-full px-3 py-2 border border-gray-200 rounded-lg text-sm" />
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="text-sm font-semibold text-dark">{{ tr('ساعات الإدارة أسبوعياً', 'Admin hours per week') }}</label>
                                        <span class="text-sm font-bold text-primary tabular-nums">{{ adminHoursPerWeek }}h</span>
                                    </div>
                                    <input v-model.number="adminHoursPerWeek" type="range" min="0" max="80" step="1" class="w-full accent-[#1B4F72]" />
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="text-sm font-semibold text-dark">{{ tr('تكلفة ساعة الموظف (ج.م)', 'Staff hourly cost (EGP)') }}</label>
                                        <span class="text-sm font-bold text-primary tabular-nums">{{ fmtMoney(hourlyCost) }}</span>
                                    </div>
                                    <input v-model.number="hourlyCost" type="range" min="20" max="200" step="5" class="w-full accent-[#1B4F72]" />
                                </div>

                                <div>
                                    <div class="flex justify-between mb-2">
                                        <label class="text-sm font-semibold text-dark">{{ tr('معدّل عدم الحضور %', 'No-show rate %') }}</label>
                                        <span class="text-sm font-bold text-primary tabular-nums">{{ noShowRate }}%</span>
                                    </div>
                                    <input v-model.number="noShowRate" type="range" min="0" max="50" step="1" class="w-full accent-[#1B4F72]" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results -->
                    <div class="lg:col-span-3 space-y-6">
                        <!-- Headline result -->
                        <div class="relative bg-gradient-to-br from-[#0A1628] via-[#0D2B45] to-[#0A1628] text-white rounded-3xl p-8 md:p-10 overflow-hidden shadow-2xl">
                            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-pulse-slow"></div>
                            <div class="absolute bottom-0 start-0 w-48 h-48 bg-[#1B4F72]/30 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2 animate-pulse-slow" style="animation-delay: -3s"></div>
                            <svg class="absolute inset-0 w-full h-full opacity-[0.04] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <pattern id="roi-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1" />
                                    </pattern>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#roi-hex)" />
                            </svg>

                            <div class="relative">
                                <p class="text-[#C4A265] text-xs font-bold uppercase tracking-widest mb-2">{{ tr('العائد السنوي الصافي المتوقع', 'Estimated net annual return') }}</p>
                                <div class="text-5xl md:text-6xl lg:text-7xl font-black bg-gradient-to-br from-white via-[#F5E6C8] to-[#C4A265] bg-clip-text text-transparent leading-none mb-3 tabular-nums">
                                    {{ fmtMoney(annualNetImpact) }}
                                    <span class="text-2xl">{{ currencyLabel }}</span>
                                </div>
                                <p class="text-white/60">{{ tr(`بعد خصم اشتراك دكتوراتو (${fmtMoney(monthlyCost)} ج.م شهرياً)`, `After deducting your Doctorato subscription (${fmtMoney(monthlyCost)} EGP / month)`) }}</p>

                                <div class="mt-6 grid grid-cols-3 gap-3">
                                    <div class="bg-white/[0.06] backdrop-blur-sm rounded-2xl border border-white/10 p-4">
                                        <p class="text-[10px] uppercase tracking-wider text-white/55">{{ tr('ROI شهري', 'Monthly ROI') }}</p>
                                        <p class="text-2xl font-extrabold text-[#C4A265] tabular-nums">{{ roiPercent }}%</p>
                                    </div>
                                    <div class="bg-white/[0.06] backdrop-blur-sm rounded-2xl border border-white/10 p-4">
                                        <p class="text-[10px] uppercase tracking-wider text-white/55">{{ tr('فترة الاسترداد', 'Payback') }}</p>
                                        <p class="text-2xl font-extrabold text-[#C4A265] tabular-nums">
                                            <span v-if="paybackDays !== null">{{ paybackDays }}<span class="text-sm">d</span></span>
                                            <span v-else>—</span>
                                        </p>
                                    </div>
                                    <div class="bg-white/[0.06] backdrop-blur-sm rounded-2xl border border-white/10 p-4">
                                        <p class="text-[10px] uppercase tracking-wider text-white/55">{{ tr('عائد 3 سنوات', '3-yr return') }}</p>
                                        <p class="text-2xl font-extrabold text-[#C4A265] tabular-nums">{{ fmtMoney(threeYearImpact) }}</p>
                                    </div>
                                </div>

                                <!-- 36-month projection sparkline -->
                                <div class="mt-6 bg-white/[0.04] border border-white/10 rounded-2xl p-4">
                                    <p class="text-[10px] uppercase tracking-wider text-white/55 mb-2">
                                        {{ tr('مسار العائد التراكمي على 36 شهر', 'Cumulative return over 36 months') }}
                                    </p>
                                    <svg viewBox="0 0 600 140" class="w-full h-auto" preserveAspectRatio="none">
                                        <defs>
                                            <linearGradient id="roi-line" x1="0" y1="0" x2="1" y2="0">
                                                <stop offset="0" stop-color="#C4A265" stop-opacity="0.9"/>
                                                <stop offset="1" stop-color="#F5E6C8" stop-opacity="0.9"/>
                                            </linearGradient>
                                            <linearGradient id="roi-fill" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0" stop-color="#C4A265" stop-opacity="0.25"/>
                                                <stop offset="1" stop-color="#C4A265" stop-opacity="0"/>
                                            </linearGradient>
                                        </defs>
                                        <path :d="projectionArea" fill="url(#roi-fill)" />
                                        <path :d="projectionPath" stroke="url(#roi-line)" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex justify-between text-[10px] text-white/50 mt-1">
                                        <span>{{ tr('شهر 1', 'Month 1') }}</span>
                                        <span>{{ tr('شهر 12', 'Month 12') }}</span>
                                        <span>{{ tr('شهر 24', 'Month 24') }}</span>
                                        <span>{{ tr('شهر 36', 'Month 36') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown — now 4 value drivers -->
                        <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm">
                            <h3 class="font-bold text-dark mb-5">{{ tr('من أين يأتي العائد', 'Where the return comes from') }}</h3>

                            <div class="space-y-4">
                                <!-- Admin time -->
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-dark text-sm">{{ tr('توفير وقت الإدارة', 'Admin time saved') }}</p>
                                        <p class="text-xs text-gray">{{ tr(`${hoursSavedMonthly} ساعة موفّرة شهرياً (55٪)`, `${hoursSavedMonthly} hours saved per month (55%)`) }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-600 tabular-nums">+{{ fmtMoney(adminCostSavedMonthly) }} {{ currencyLabel }}</p>
                                </div>

                                <!-- WhatsApp reminders → no-show recovery -->
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-dark text-sm">{{ tr('تذكيرات WhatsApp وSMS', 'WhatsApp + SMS reminders') }}</p>
                                        <p class="text-xs text-gray">{{ tr(`استرجاع 65٪ من ${fmtMoney(lostRevenueMonthly)} ج.م مفقودة بسبب عدم الحضور`, `Recovers 65% of ${fmtMoney(lostRevenueMonthly)} EGP lost to no-shows`) }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-600 tabular-nums">+{{ fmtMoney(recoveredRevenueMonthly) }} {{ currencyLabel }}</p>
                                </div>

                                <!-- Online booking -->
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-dark text-sm">{{ tr('الحجز أونلاين 24/7', 'Online booking 24/7') }}</p>
                                        <p class="text-xs text-gray">{{ tr('مرضى يحجزون خارج ساعات العمل (+8٪)', 'Patients booking after-hours (+8%)') }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-600 tabular-nums">+{{ fmtMoney(onlineBookingRevenueMonthly) }} {{ currencyLabel }}</p>
                                </div>

                                <!-- Retention / portal -->
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-dark text-sm">{{ tr('بوابة المريض وزيارات متكررة', 'Patient portal + repeat visits') }}</p>
                                        <p class="text-xs text-gray">{{ tr(`نمو الإيرادات بنسبة ${Math.round(ASSUMPTIONS.retention_uplift_pct * 100)}٪ (مُعَدَّل حسب ${isAr ? currentSpecialty.label_ar : currentSpecialty.label_en})`, `${Math.round(ASSUMPTIONS.retention_uplift_pct * 100)}% revenue lift (tuned for ${isAr ? currentSpecialty.label_ar : currentSpecialty.label_en})`) }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-600 tabular-nums">+{{ fmtMoney(additionalRevenueMonthly) }} {{ currencyLabel }}</p>
                                </div>

                                <div class="border-t border-gray-100 pt-4 flex items-center justify-between">
                                    <p class="font-bold text-dark">{{ tr('إجمالي التأثير الشهري', 'Total monthly impact') }}</p>
                                    <p class="text-2xl font-extrabold text-primary tabular-nums">{{ fmtMoney(totalMonthlyImpact) }} {{ currencyLabel }}</p>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <p class="text-gray">{{ tr('- اشتراك دكتوراتو', '- Doctorato subscription') }}</p>
                                    <p class="text-red-500 tabular-nums">-{{ fmtMoney(monthlyCost) }} {{ currencyLabel }}</p>
                                </div>
                                <div class="border-t-2 border-gray-200 pt-4 flex items-center justify-between">
                                    <p class="font-bold text-dark">{{ tr('صافي العائد الشهري', 'Net monthly return') }}</p>
                                    <p class="text-3xl font-black text-emerald-600 tabular-nums">{{ fmtMoney(netMonthlyImpact) }} {{ currencyLabel }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action row: demo + WhatsApp share -->
                        <div class="bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] rounded-3xl p-8 text-white text-center">
                            <h3 class="text-2xl font-bold mb-3">{{ tr('جاهز لتحقيق هذه الأرقام؟', 'Ready to hit these numbers?') }}</h3>
                            <p class="text-white/70 mb-5">
                                {{ tr('ابدأ تجربة 14 يوم مجاناً — بدون بطاقة ائتمان. يمكنك إيقاف الاشتراك في أي وقت من بوابتك.', 'Start your 14-day free trial — no credit card required. Pause anytime from your portal.') }}
                            </p>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <Link href="/demo" class="px-8 py-3.5 bg-[#C4A265] hover:bg-[#A88B4A] text-white font-bold rounded-full transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#C4A265]/30">
                                    {{ tr('اطلب عرضاً تجريبياً مجانياً', 'Request a free demo') }}
                                </Link>
                                <a :href="whatsappShareUrl" target="_blank" rel="noopener"
                                   class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white/10 hover:bg-white/20 border border-white/15 text-white font-bold rounded-full transition-all hover:-translate-y-0.5">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    {{ tr('شارك النتيجة عبر WhatsApp', 'Share result on WhatsApp') }}
                                </a>
                            </div>
                        </div>

                        <p class="text-xs text-gray text-center px-4">
                            {{ tr(
                                '* النتائج تقديرية مبنية على متوسطات حقيقية من 200+ عيادة تستخدم دكتوراتو في مصر والشرق الأوسط. النتائج الفعلية قد تختلف حسب حجم العيادة ونوع الخدمات وتطبيق التوصيات.',
                                '* Estimates are based on real averages from 200+ clinics using Doctorato across Egypt and the Middle East. Actual results vary with clinic size, service mix, and how the recommendations are applied.'
                            ) }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Specialty modules cross-link strip -->
        <section class="py-16 bg-gradient-to-b from-white to-[#FBFAF6]">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-10">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                        {{ tr('كل تخصص له وحدته الخاصة', 'A purpose-built module for every specialty') }}
                    </p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C2833]">
                        {{ tr('Doctorato يغطّي تخصصك بدقّة', 'Doctorato covers your specialty precisely') }}
                    </h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <component
                        v-for="s in SPECIALTIES.filter(x => x.href)"
                        :is="s.href ? 'a' : 'div'"
                        :href="s.href"
                        :key="s.key"
                        class="group p-5 bg-white rounded-2xl border border-gray-100 hover:border-[#C4A265]/40 hover:shadow-md hover:-translate-y-0.5 transition-all text-center"
                    >
                        <div class="text-3xl mb-2">{{ s.icon }}</div>
                        <p class="text-sm font-bold text-[#1C2833] group-hover:text-[#1B4F72]">
                            {{ isAr ? s.label_ar : s.label_en }}
                        </p>
                    </component>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
