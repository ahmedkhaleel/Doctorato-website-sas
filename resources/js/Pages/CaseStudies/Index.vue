<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    cases: { type: Array, default: () => [] },
});

const { locale, t } = useI18n();

const featured = computed(() => props.cases.find((c) => c.is_featured) || props.cases[0]);
const others = computed(() => {
    if (!featured.value) return props.cases;
    return props.cases.filter((c) => c.id !== featured.value.id);
});

function localized(item, field) {
    return locale.value === 'ar' ? item[`${field}_ar`] : item[`${field}_en`] || item[`${field}_ar`];
}

function metricLabel(metric) {
    return locale.value === 'ar' ? metric.label_ar : metric.label_en;
}
</script>

<template>
    <SeoHead
        :title="t('case_studies.page_title') || 'دراسات الحالة'"
        :description="t('case_studies.page_description') || 'قصص نجاح حقيقية لعيادات ومراكز طبية تستخدم دكتوراتو'"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-32 pb-20 overflow-hidden bg-[#070F1B]">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#070F1B]"></div>
            <div class="absolute inset-0">
                <div class="absolute top-[-15%] start-[10%] w-[600px] h-[600px] bg-[#1B4F72]/40 rounded-full blur-[140px] animate-aurora"></div>
                <div class="absolute bottom-[-10%] end-[10%] w-[600px] h-[600px] bg-[#C4A265]/15 rounded-full blur-[140px] animate-aurora" style="animation-delay: -6s"></div>
            </div>
            <div
                class="absolute inset-0 opacity-[0.05] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.5) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.5) 1px, transparent 1px); background-size: 80px 80px;"
            ></div>
            <svg class="absolute inset-0 w-full h-full opacity-[0.035] pointer-events-none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="cs-hex" x="0" y="0" width="56" height="64" patternUnits="userSpaceOnUse">
                        <polygon points="28,2 52,16 52,48 28,62 4,48 4,16" fill="none" stroke="white" stroke-width="1" />
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#cs-hex)" />
            </svg>

            <div class="relative max-w-5xl mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-6 border border-white/10 animate-fade-up">
                    <span class="flex w-2 h-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#C4A265]"></span>
                    </span>
                    <span class="text-sm font-semibold text-white tracking-wide">قصص نجاح حقيقية</span>
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white via-white to-[#C4A265] bg-clip-text text-transparent">
                        كيف غيّر دكتوراتو طريقة عمل العيادات
                    </span>
                </h1>
                <p class="text-lg text-white/70 max-w-2xl mx-auto animate-fade-up">
                    أرقام وقصص حقيقية من عيادات ومراكز طبية اعتمدت دكتوراتو وحققت نتائج ملموسة في أشهر معدودة.
                </p>
            </div>
        </section>

        <!-- Featured case study -->
        <section v-if="featured" class="py-20 bg-light-blue/30">
            <div class="max-w-6xl mx-auto px-4">
                <Link
                    :href="`/case-studies/${featured.slug}`"
                    class="group block bg-white rounded-3xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-500 hover:-translate-y-1"
                >
                    <div class="grid lg:grid-cols-2 gap-0">
                        <!-- Image / accent side -->
                        <div class="relative bg-gradient-to-br from-[#0D2B45] via-[#1B4F72] to-[#0D2B45] p-10 md:p-12 text-white overflow-hidden">
                            <div class="absolute top-0 end-0 w-64 h-64 bg-[#C4A265]/15 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 animate-pulse-slow"></div>
                            <div class="absolute inset-0 opacity-[0.04] pointer-events-none" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>

                            <div class="relative">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C4A265]/15 border border-[#C4A265]/25 text-[#C4A265] text-xs font-semibold mb-6">
                                    ⭐ مقالة مميزة
                                </div>
                                <p class="text-white/60 text-sm mb-2">{{ localized(featured, 'industry') }} · {{ featured.location }}</p>
                                <h3 class="text-3xl md:text-4xl font-extrabold mb-4 leading-tight">{{ localized(featured, 'title') }}</h3>
                                <p class="text-white/70 leading-relaxed mb-6">{{ localized(featured, 'summary') }}</p>

                                <!-- Top metrics -->
                                <div class="grid grid-cols-2 gap-4 mt-8">
                                    <div v-for="(metric, idx) in (featured.metrics || []).slice(0, 4)" :key="idx" class="bg-white/[0.06] backdrop-blur-sm rounded-xl border border-white/10 p-4">
                                        <div class="text-3xl font-extrabold text-[#C4A265] tabular-nums">{{ metric.value }}{{ metric.suffix }}</div>
                                        <div class="text-[11px] text-white/60 mt-1">{{ metricLabel(metric) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Text side -->
                        <div class="p-10 md:p-12 flex flex-col justify-center">
                            <div class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-2">العميل</div>
                            <h4 class="text-2xl font-bold text-dark mb-4">{{ localized(featured, 'client_name') }}</h4>

                            <div v-if="featured.testimonial_ar" class="bg-light-blue/40 rounded-2xl p-6 mb-6 border-s-4 border-secondary">
                                <p class="text-dark italic leading-relaxed mb-3">
                                    "{{ locale === 'ar' ? featured.testimonial_ar : featured.testimonial_en || featured.testimonial_ar }}"
                                </p>
                                <p class="text-sm font-semibold text-primary">— {{ featured.testimonial_author }}</p>
                                <p class="text-xs text-gray">{{ featured.testimonial_role }}</p>
                            </div>

                            <span class="inline-flex items-center gap-2 text-primary font-bold group-hover:gap-3 transition-all">
                                اقرأ القصة الكاملة
                                <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </Link>
            </div>
        </section>

        <!-- Other case studies grid -->
        <section v-if="others.length" class="py-20">
            <div class="max-w-6xl mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-dark mb-3">المزيد من النجاحات</h2>
                    <p class="text-gray">عيادات ومراكز اختارت دكتوراتو وحققت نتائج ملموسة</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 animate-stagger">
                    <Link
                        v-for="c in others"
                        :key="c.id"
                        :href="`/case-studies/${c.slug}`"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-xl transition-all duration-500 hover:-translate-y-1 overflow-hidden relative"
                    >
                        <!-- Top accent -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#1B4F72] via-[#C4A265] to-[#1B4F72] scale-x-0 group-hover:scale-x-100 origin-center transition-transform duration-500"></div>

                        <p class="text-xs text-gray mb-2">{{ localized(c, 'industry') }} · {{ c.location }}</p>
                        <h3 class="text-lg font-bold text-dark mb-3 group-hover:text-primary transition-colors leading-snug">{{ localized(c, 'title') }}</h3>
                        <p class="text-sm text-gray leading-relaxed line-clamp-3 mb-4">{{ localized(c, 'summary') }}</p>

                        <!-- Mini metrics -->
                        <div class="grid grid-cols-2 gap-2 pt-4 border-t border-gray-100">
                            <div v-for="(m, i) in (c.metrics || []).slice(0, 2)" :key="i">
                                <div class="text-xl font-extrabold text-primary tabular-nums">{{ m.value }}{{ m.suffix }}</div>
                                <div class="text-[10px] text-gray">{{ metricLabel(m) }}</div>
                            </div>
                        </div>

                        <span class="inline-flex items-center gap-1 text-primary text-sm font-bold mt-4 group-hover:gap-2 transition-all">
                            اقرأ القصة
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 bg-gradient-to-br from-[#0D2B45] to-[#1B4F72] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">جاهز لتكون قصة النجاح القادمة؟</h2>
                <p class="text-white/70 mb-8">احسب عائدك المتوقع من دكتوراتو في أقل من دقيقة</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link href="/roi-calculator" class="px-8 py-3.5 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all hover:-translate-y-0.5">
                        احسب عائدك المتوقع
                    </Link>
                    <Link href="/demo" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-full backdrop-blur-sm border border-white/20 transition-all">
                        اطلب عرضاً تجريبياً
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
