<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { ref, computed } from 'vue';

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');

// Tiny bilingual string helper used throughout the page.
// Using inline pairs keeps the page self-contained and avoids dragging
// i18n keys into the global translation files for a single page.
const tr = (ar, en) => (isAr.value ? ar : en);

// === User inputs ===
const monthlyPatients = ref(500);
const avgTicket = ref(400);          // EGP per visit
const adminHoursPerWeek = ref(20);   // staff hours spent on admin
const hourlyCost = ref(40);          // EGP per staff hour
const noShowRate = ref(15);          // %

// === Doctorato impact assumptions (conservative, based on real customer data) ===
const ASSUMPTIONS = {
    timeSavedPct: 0.55,        // 55% reduction in admin hours
    noShowReductionPct: 0.65,  // 65% reduction in no-shows (e.g. 15% → 5.25%)
    revenueUpliftPct: 0.18,    // 18% revenue uplift via patient retention + repeat visits
    monthlyCostEgp: 1599,      // Pro plan default
};

// === Computed metrics ===
const adminHoursMonthly = computed(() => adminHoursPerWeek.value * 4.33);
const adminCostMonthly = computed(() => adminHoursMonthly.value * hourlyCost.value);

const hoursSavedMonthly = computed(() => Math.round(adminHoursMonthly.value * ASSUMPTIONS.timeSavedPct));
const adminCostSavedMonthly = computed(() => Math.round(adminCostMonthly.value * ASSUMPTIONS.timeSavedPct));

// Lost revenue from no-shows currently
const lostRevenueMonthly = computed(() =>
    Math.round(monthlyPatients.value * (noShowRate.value / 100) * avgTicket.value)
);
// Recovered after Doctorato
const recoveredRevenueMonthly = computed(() =>
    Math.round(lostRevenueMonthly.value * ASSUMPTIONS.noShowReductionPct)
);

// Additional revenue from retention
const additionalRevenueMonthly = computed(() =>
    Math.round(monthlyPatients.value * avgTicket.value * ASSUMPTIONS.revenueUpliftPct)
);

const totalMonthlyImpact = computed(() =>
    adminCostSavedMonthly.value + recoveredRevenueMonthly.value + additionalRevenueMonthly.value
);

const monthlyCost = ASSUMPTIONS.monthlyCostEgp;
const netMonthlyImpact = computed(() => totalMonthlyImpact.value - monthlyCost);
const annualNetImpact = computed(() => netMonthlyImpact.value * 12);

const roiPercent = computed(() => {
    if (monthlyCost === 0) return 0;
    return Math.round((netMonthlyImpact.value / monthlyCost) * 100);
});

const paybackDays = computed(() => {
    if (totalMonthlyImpact.value <= 0) return null;
    return Math.max(1, Math.round((monthlyCost / totalMonthlyImpact.value) * 30));
});

function fmtMoney(v) {
    return new Intl.NumberFormat(isAr.value ? 'ar-EG' : 'en-US').format(Math.round(v || 0));
}

const currencyLabel = computed(() => tr('ج.م', 'EGP'));
</script>

<template>
    <SeoHead
        :title="tr('حاسبة العائد على الاستثمار', 'ROI Calculator')"
        :description="tr('احسب كم ستوفّر عيادتك وكم ستربح إضافياً عند استخدام دكتوراتو — في أقل من دقيقة', 'Calculate your clinic savings and extra revenue with Doctorato — in under a minute')"
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
                    {{ tr('أدخل أرقام عيادتك الحالية، واحصل فوراً على تقدير علمي لعائد الاستثمار خلال شهر و سنة.', 'Enter your current clinic numbers and get an instant data-backed estimate of your monthly and annual ROI.') }}
                </p>
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
                            <p class="text-sm text-gray mb-6">{{ tr('حرّك المؤشرات لرؤية النتيجة فوراً', 'Move the sliders to see the result instantly') }}</p>

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

                                <div class="mt-6 grid grid-cols-2 gap-4">
                                    <div class="bg-white/[0.06] backdrop-blur-sm rounded-2xl border border-white/10 p-4">
                                        <p class="text-xs text-white/60">{{ tr('ROI شهري', 'Monthly ROI') }}</p>
                                        <p class="text-3xl font-extrabold text-[#C4A265] tabular-nums">{{ roiPercent }}%</p>
                                    </div>
                                    <div class="bg-white/[0.06] backdrop-blur-sm rounded-2xl border border-white/10 p-4">
                                        <p class="text-xs text-white/60">{{ tr('فترة الاسترداد', 'Payback period') }}</p>
                                        <p class="text-3xl font-extrabold text-[#C4A265] tabular-nums">
                                            <span v-if="paybackDays !== null">{{ paybackDays }}<span class="text-base">d</span></span>
                                            <span v-else>—</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Breakdown -->
                        <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm">
                            <h3 class="font-bold text-dark mb-5">{{ tr('تفصيل التوفيرات الشهرية', 'Monthly savings breakdown') }}</h3>

                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-dark text-sm">{{ tr('توفير وقت الإدارة (55٪)', 'Admin time saved (55%)') }}</p>
                                        <p class="text-xs text-gray">{{ tr(`${hoursSavedMonthly} ساعة موفّرة شهرياً`, `${hoursSavedMonthly} hours saved per month`) }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-600 tabular-nums">+{{ fmtMoney(adminCostSavedMonthly) }} {{ currencyLabel }}</p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-dark text-sm">{{ tr('تقليل عدم الحضور (65٪)', 'No-show reduction (65%)') }}</p>
                                        <p class="text-xs text-gray">{{ tr(`من إجمالي ${fmtMoney(lostRevenueMonthly)} ج.م مفقودة`, `Out of ${fmtMoney(lostRevenueMonthly)} EGP currently lost`) }}</p>
                                    </div>
                                    <p class="font-bold text-emerald-600 tabular-nums">+{{ fmtMoney(recoveredRevenueMonthly) }} {{ currencyLabel }}</p>
                                </div>

                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-dark text-sm">{{ tr('نمو الإيرادات (18٪)', 'Revenue growth (18%)') }}</p>
                                        <p class="text-xs text-gray">{{ tr('احتفاظ بالمرضى وزيارات متكررة', 'Patient retention and repeat visits') }}</p>
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

                        <!-- CTA -->
                        <div class="bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] rounded-3xl p-8 text-white text-center">
                            <h3 class="text-2xl font-bold mb-3">{{ tr('جاهز لتحقيق هذه الأرقام؟', 'Ready to hit these numbers?') }}</h3>
                            <p class="text-white/70 mb-5">{{ tr('احجز عرضاً تجريبياً مجانياً وسنريك كيف تطبّق هذه الأرقام على عيادتك', 'Book a free demo and we’ll show you exactly how to apply these numbers to your clinic') }}</p>
                            <Link href="/demo" class="inline-block px-8 py-3.5 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all hover:-translate-y-0.5">
                                {{ tr('اطلب عرضاً تجريبياً مجانياً', 'Request a free demo') }}
                            </Link>
                        </div>

                        <!-- Disclaimer -->
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
    </MainLayout>
</template>
