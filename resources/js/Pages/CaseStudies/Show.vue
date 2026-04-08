<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    study: { type: Object, required: true },
    related: { type: Array, default: () => [] },
});

const { locale } = useI18n();

function localized(field) {
    const c = props.study;
    return locale.value === 'ar' ? c[`${field}_ar`] : c[`${field}_en`] || c[`${field}_ar`];
}

function metricLabel(m) {
    return locale.value === 'ar' ? m.label_ar : m.label_en;
}

const jsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'Article',
    headline: localized('title'),
    description: localized('summary'),
    author: { '@type': 'Organization', name: 'Doctorato' },
    publisher: {
        '@type': 'Organization',
        name: 'Doctorato',
        logo: { '@type': 'ImageObject', url: (typeof window !== 'undefined' ? window.location.origin : '') + '/images/doctorato-logo.png' },
    },
    about: { '@type': 'Organization', name: localized('client_name') },
}));
</script>

<template>
    <SeoHead
        :title="study.seo_title || localized('title')"
        :description="study.seo_description || localized('summary')"
        :image="study.hero_image"
        :json-ld="jsonLd"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-32 pb-16 overflow-hidden bg-[#070F1B] text-white">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#070F1B]"></div>
            <div class="absolute top-[-10%] start-[10%] w-[500px] h-[500px] bg-[#1B4F72]/40 rounded-full blur-[140px] animate-aurora"></div>
            <div class="absolute bottom-[-10%] end-[10%] w-[500px] h-[500px] bg-[#C4A265]/15 rounded-full blur-[140px] animate-aurora" style="animation-delay: -6s"></div>
            <div
                class="absolute inset-0 opacity-[0.05] animate-grid-drift"
                style="background-image: linear-gradient(45deg, rgba(196,162,101,0.5) 1px, transparent 1px), linear-gradient(-45deg, rgba(196,162,101,0.5) 1px, transparent 1px); background-size: 80px 80px;"
            ></div>

            <div class="relative max-w-4xl mx-auto px-4">
                <Link href="/case-studies" class="inline-flex items-center gap-2 text-white/60 hover:text-white text-sm mb-8 transition-colors">
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة لكل دراسات الحالة
                </Link>

                <div class="flex flex-wrap items-center gap-2 mb-4">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C4A265]/15 border border-[#C4A265]/25 text-[#C4A265] text-xs font-bold">
                        {{ localized('industry') }}
                    </span>
                    <span class="text-white/40 text-xs">·</span>
                    <span class="text-white/60 text-xs">{{ study.location }}</span>
                </div>

                <h1 class="text-3xl md:text-5xl font-extrabold mb-4 leading-tight">
                    <span class="bg-gradient-to-br from-white to-[#C4A265] bg-clip-text text-transparent">
                        {{ localized('title') }}
                    </span>
                </h1>

                <p class="text-lg text-white/70 leading-relaxed">{{ localized('summary') }}</p>

                <div class="mt-8 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#C4A265] to-[#D4B87A] flex items-center justify-center shadow-lg shadow-[#C4A265]/30 text-2xl font-bold">
                        {{ localized('client_name')[0] }}
                    </div>
                    <div>
                        <p class="font-bold">{{ localized('client_name') }}</p>
                        <p class="text-xs text-white/50">{{ study.location }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Metrics row -->
        <section v-if="study.metrics && study.metrics.length" class="py-12 bg-light-blue/30">
            <div class="max-w-5xl mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div v-for="(m, i) in study.metrics" :key="i" class="bg-white rounded-2xl border border-gray-100 p-6 text-center hover:shadow-xl transition-all duration-500 hover:-translate-y-1">
                        <div class="text-4xl md:text-5xl font-black text-primary tabular-nums">{{ m.value }}{{ m.suffix }}</div>
                        <div class="text-xs text-gray mt-2 font-medium">{{ metricLabel(m) }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content sections -->
        <section class="py-16">
            <div class="max-w-3xl mx-auto px-4 space-y-12">
                <div v-if="localized('challenge')">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <h2 class="text-2xl font-extrabold text-dark">التحدي</h2>
                    </div>
                    <p class="text-gray leading-relaxed text-lg">{{ localized('challenge') }}</p>
                </div>

                <div v-if="localized('solution')">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        </div>
                        <h2 class="text-2xl font-extrabold text-dark">الحل</h2>
                    </div>
                    <p class="text-gray leading-relaxed text-lg">{{ localized('solution') }}</p>
                </div>

                <div v-if="localized('results')">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <h2 class="text-2xl font-extrabold text-dark">النتائج</h2>
                    </div>
                    <p class="text-gray leading-relaxed text-lg">{{ localized('results') }}</p>
                </div>

                <!-- Modules used -->
                <div v-if="study.modules_used && study.modules_used.length">
                    <h3 class="text-sm font-bold text-gray uppercase tracking-widest mb-4">الوحدات المستخدمة</h3>
                    <div class="flex flex-wrap gap-2">
                        <span v-for="m in study.modules_used" :key="m" class="px-4 py-2 rounded-full bg-light-blue text-primary text-sm font-semibold border border-primary/10">
                            {{ m }}
                        </span>
                    </div>
                </div>

                <!-- Testimonial -->
                <div v-if="study.testimonial_ar" class="bg-gradient-to-br from-light-blue/40 to-light-gold/30 rounded-3xl p-8 md:p-10 border-s-4 border-secondary">
                    <svg class="w-10 h-10 text-secondary mb-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z"/></svg>
                    <p class="text-xl md:text-2xl text-dark italic font-medium leading-relaxed mb-4">
                        "{{ locale === 'ar' ? study.testimonial_ar : study.testimonial_en || study.testimonial_ar }}"
                    </p>
                    <div>
                        <p class="font-bold text-primary">{{ study.testimonial_author }}</p>
                        <p class="text-sm text-gray">{{ study.testimonial_role }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related cases -->
        <section v-if="related.length" class="py-16 bg-light-blue/30">
            <div class="max-w-6xl mx-auto px-4">
                <h2 class="text-2xl md:text-3xl font-extrabold text-dark text-center mb-10">دراسات حالة أخرى</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    <Link
                        v-for="r in related"
                        :key="r.id"
                        :href="`/case-studies/${r.slug}`"
                        class="group bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-500"
                    >
                        <p class="text-xs text-gray mb-2">{{ locale === 'ar' ? r.industry_ar : r.industry_en }}</p>
                        <h3 class="font-bold text-dark mb-2 group-hover:text-primary transition-colors">{{ locale === 'ar' ? r.title_ar : r.title_en }}</h3>
                        <p class="text-sm text-gray line-clamp-2">{{ locale === 'ar' ? r.summary_ar : r.summary_en }}</p>
                    </Link>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-20 bg-gradient-to-br from-[#0D2B45] to-[#1B4F72] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-3xl md:text-4xl font-extrabold mb-4">احسب عائدك المتوقع</h2>
                <p class="text-white/70 mb-8">في أقل من دقيقة، اعرف كم ستوفّر باستخدام دكتوراتو</p>
                <Link href="/roi-calculator" class="inline-block px-8 py-3.5 bg-secondary hover:bg-secondary-dark text-white font-bold rounded-full transition-all hover:-translate-y-0.5">
                    حاسبة العائد
                </Link>
            </div>
        </section>
    </MainLayout>
</template>
