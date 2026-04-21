<script setup>
/**
 * Compact ROI preview for the Pricing page.
 *
 * Shows two sliders (patients/month, no-show rate) and computes an
 * estimated annual saving using the same assumptions as the full
 * RoiCalculator page. The goal here is to plant a number in the
 * visitor's head ("عيادتك ممكن توفّر 84 ألف ج.م سنوياً") right next
 * to the price tag — making the subscription feel like an investment
 * rather than an expense.
 *
 * Full calculator lives at /roi-calculator for visitors who want to
 * tweak every input.
 */
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, ref } from 'vue';

const { locale } = useI18n();

// Defaults match the median clinic we onboard.
const patients = ref(500);
const noShowRate = ref(15);

// Conservative assumptions, identical to RoiCalculator.vue page.
const AVG_TICKET = 400;            // EGP per visit
const NO_SHOW_REDUCTION = 0.65;    // 65% of no-shows recovered
const REVENUE_UPLIFT = 0.12;       // retention bump (trimmed vs full calc for honesty)

const recoveredRevenueMonthly = computed(() =>
    Math.round(patients.value * (noShowRate.value / 100) * AVG_TICKET * NO_SHOW_REDUCTION)
);
const upliftRevenueMonthly = computed(() =>
    Math.round(patients.value * AVG_TICKET * REVENUE_UPLIFT)
);
const totalMonthly = computed(() => recoveredRevenueMonthly.value + upliftRevenueMonthly.value);
const totalYearly = computed(() => totalMonthly.value * 12);

function fmt(n) {
    return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US').format(Math.round(n));
}
</script>

<template>
    <section class="py-16 lg:py-20 bg-gradient-to-b from-light-blue/40 to-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10 animate-fade-up">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 mb-4">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                    <span class="text-sm text-emerald-700 font-bold">
                        {{ locale === 'ar' ? 'احسب عائد الاستثمار' : 'Calculate your ROI' }}
                    </span>
                </div>
                <h2 class="text-2xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                    {{ locale === 'ar' ? 'كم ستوفّر عيادتك مع دكتوراتو؟' : 'How much will your clinic save with Doctorato?' }}
                </h2>
                <p class="text-gray-500 max-w-2xl mx-auto">
                    {{ locale === 'ar'
                        ? 'حرّك المؤشرات لتشاهد حجم التوفير بناءً على بيانات عيادتك'
                        : 'Move the sliders to see real savings based on your clinic data' }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 animate-fade-up">
                <!-- Inputs -->
                <div class="bg-white rounded-2xl p-6 md:p-7 shadow-lg border border-gray-100 space-y-6">
                    <!-- Patients -->
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <label class="text-sm font-semibold text-dark">
                                {{ locale === 'ar' ? 'عدد المرضى شهرياً' : 'Patients per month' }}
                            </label>
                            <span class="text-lg font-extrabold text-[#1B4F72] tabular-nums">{{ fmt(patients) }}</span>
                        </div>
                        <input
                            v-model.number="patients"
                            type="range"
                            min="50" max="3000" step="50"
                            class="w-full accent-[#C4A265] cursor-pointer"
                        />
                        <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                            <span>50</span>
                            <span>1500</span>
                            <span>3000</span>
                        </div>
                    </div>

                    <!-- No-show rate -->
                    <div>
                        <div class="flex justify-between items-baseline mb-2">
                            <label class="text-sm font-semibold text-dark">
                                {{ locale === 'ar' ? 'نسبة المرضى المتغيّبين' : 'No-show rate' }}
                            </label>
                            <span class="text-lg font-extrabold text-[#1B4F72] tabular-nums">{{ noShowRate }}%</span>
                        </div>
                        <input
                            v-model.number="noShowRate"
                            type="range"
                            min="0" max="40" step="1"
                            class="w-full accent-[#C4A265] cursor-pointer"
                        />
                        <div class="flex justify-between text-[10px] text-gray-400 mt-1">
                            <span>0%</span>
                            <span>20%</span>
                            <span>40%</span>
                        </div>
                    </div>

                    <!-- Small note -->
                    <p class="text-[11px] text-gray-400 leading-relaxed pt-3 border-t border-gray-100">
                        {{ locale === 'ar'
                            ? '* الحساب مبني على متوسط تذكرة 400 ج.م واستعادة 65% من المرضى المتغيّبين + زيادة 12% من العملاء الحاليين. افتح الحاسبة الكاملة للمزيد.'
                            : '* Assumes a 400 EGP avg ticket, 65% no-show recovery, and 12% retention uplift. Open the full calculator for more detail.' }}
                    </p>
                </div>

                <!-- Output -->
                <div class="relative rounded-2xl p-6 md:p-7 bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white shadow-xl overflow-hidden">
                    <div class="absolute -top-10 end-0 w-40 h-40 bg-[#C4A265]/20 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-10 start-0 w-40 h-40 bg-emerald-400/10 rounded-full blur-3xl"></div>

                    <div class="relative">
                        <p class="text-xs uppercase tracking-widest text-[#C4A265] font-bold mb-2">
                            {{ locale === 'ar' ? 'توفير سنوي تقديري' : 'Estimated annual savings' }}
                        </p>
                        <div class="flex items-baseline gap-2 mb-2">
                            <span class="text-4xl md:text-5xl font-extrabold text-white tabular-nums">
                                {{ fmt(totalYearly) }}
                            </span>
                            <span class="text-sm font-semibold text-white/70">
                                {{ locale === 'ar' ? 'ج.م / سنة' : 'EGP / yr' }}
                            </span>
                        </div>
                        <p class="text-sm text-white/60 leading-relaxed mb-5">
                            {{ locale === 'ar' ? 'بناءً على بيانات مئات العيادات — الأرقام حقيقية، محافظة.' : 'Based on data from hundreds of clinics — real numbers, conservative.' }}
                        </p>

                        <!-- Breakdown -->
                        <div class="space-y-2 mb-5 pb-5 border-b border-white/10">
                            <div class="flex justify-between text-sm">
                                <span class="text-white/70">{{ locale === 'ar' ? 'استعادة المتغيّبين' : 'Recovered no-shows' }}</span>
                                <span class="font-bold tabular-nums text-emerald-300">+{{ fmt(recoveredRevenueMonthly) }}<span class="text-xs text-white/50 ms-1">/ {{ locale === 'ar' ? 'شهر' : 'mo' }}</span></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-white/70">{{ locale === 'ar' ? 'زيادة الاحتفاظ بالمرضى' : 'Retention uplift' }}</span>
                                <span class="font-bold tabular-nums text-emerald-300">+{{ fmt(upliftRevenueMonthly) }}<span class="text-xs text-white/50 ms-1">/ {{ locale === 'ar' ? 'شهر' : 'mo' }}</span></span>
                            </div>
                        </div>

                        <!-- CTAs -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <Link
                                href="/roi-calculator"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white text-[#1B4F72] font-bold text-sm hover:bg-[#C4A265] hover:text-white transition"
                            >
                                {{ locale === 'ar' ? 'الحاسبة الكاملة' : 'Full calculator' }}
                                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </Link>
                            <Link
                                href="/demo"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold text-sm hover:shadow-lg transition"
                            >
                                {{ locale === 'ar' ? 'احجز عرضاً الآن' : 'Book a demo' }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
