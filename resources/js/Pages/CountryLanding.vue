<script setup>
/**
 * Per-country landing page: /sa, /ae, /eg, ...
 *
 * One Vue template driven by the $market prop the controller sends.
 * Locks visuals to the country (flag in hero, local insurers, local
 * phone number) while reusing the pricing + testimonial data that's
 * already keyed per country in the rest of the site.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, ref } from 'vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';

const { t, locale } = useI18n();
useScrollAnimation();

const props = defineProps({
    market: { type: Object, required: true },
    plans: { type: Array, default: () => [] },
    testimonials: { type: Array, default: () => [] },
});

const billing = ref('monthly');
const isYearly = computed(() => billing.value === 'yearly');

const name = computed(() => locale.value === 'ar' ? props.market.name_ar : props.market.name_en);
const heroTitle = computed(() => locale.value === 'ar' ? props.market.hero_title_ar : props.market.hero_title_en);
const heroSubtitle = computed(() => locale.value === 'ar' ? props.market.hero_subtitle_ar : props.market.hero_subtitle_en);
const highlights = computed(() => locale.value === 'ar' ? (props.market.highlights_ar || []) : (props.market.highlights_en || []));

function fmt(n) {
    return new Intl.NumberFormat(locale.value === 'ar' ? 'ar-EG' : 'en-US').format(Math.round(n));
}
function priceFor(plan) {
    if (!plan) return 0;
    return isYearly.value ? plan.yearly_price : plan.monthly_price;
}
function symbolFor(plan) {
    return plan?.currency_symbol || plan?.currency || '';
}

// Cheapest plan for the hero teaser ("ابدأ من 297 ر.س")
const startingPrice = computed(() => {
    if (!props.plans.length) return null;
    const sorted = [...props.plans].sort((a, b) => (a.monthly_price || 0) - (b.monthly_price || 0));
    return sorted[0];
});

// Structured data — a LocalBusiness schema tied to the country
// improves local-SEO rankings and enables rich cards in SERPs.
const ldJson = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'SoftwareApplication',
    name: 'Doctorato',
    applicationCategory: 'BusinessApplication',
    operatingSystem: 'Web',
    areaServed: {
        '@type': 'Country',
        name: props.market.name_en,
        identifier: props.market.country_code,
    },
    offers: props.plans.map(p => ({
        '@type': 'Offer',
        name: locale.value === 'ar' ? p.name_ar : p.name_en,
        price: String(p.monthly_price),
        priceCurrency: p.currency,
    })),
    aggregateRating: props.testimonials.length ? {
        '@type': 'AggregateRating',
        ratingValue: (props.testimonials.reduce((s, t) => s + Number(t.rating || 5), 0) / props.testimonials.length).toFixed(1),
        reviewCount: props.testimonials.length,
    } : undefined,
}));
</script>

<template>
    <SeoHead
        :title="`${heroTitle} — Doctorato ${market.flag}`"
        :description="heroSubtitle"
        :json-ld="ldJson"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-32 pb-20 md:pt-40 md:pb-24 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-20 -start-20 w-96 h-96 bg-[#C4A265]/15 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-10 -end-20 w-[28rem] h-[28rem] bg-[#2471A3]/20 rounded-full blur-[120px]"></div>

            <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <!-- Flag + country pill -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/[0.08] backdrop-blur-sm border border-white/[0.12] mb-6 animate-fade-up">
                        <span class="text-lg leading-none">{{ market.flag }}</span>
                        <span class="text-sm text-white/80 font-semibold">{{ name }}</span>
                    </div>

                    <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white mb-6 animate-fade-up leading-tight">
                        {{ heroTitle }}
                    </h1>
                    <p class="text-base md:text-xl text-white/70 max-w-3xl mx-auto leading-relaxed mb-10 animate-fade-up">
                        {{ heroSubtitle }}
                    </p>

                    <!-- Starting-from teaser -->
                    <div v-if="startingPrice" class="inline-flex items-baseline gap-2 mb-8 animate-fade-up">
                        <span class="text-sm text-white/50">{{ locale === 'ar' ? 'ابدأ من' : 'Starting at' }}</span>
                        <span class="text-3xl md:text-4xl font-extrabold text-[#C4A265]">{{ fmt(startingPrice.monthly_price) }}</span>
                        <span class="text-sm text-white/60">{{ symbolFor(startingPrice) }} {{ locale === 'ar' ? '/شهر' : '/month' }}</span>
                    </div>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 animate-fade-up">
                        <Link
                            href="/demo"
                            class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold text-sm shadow-lg shadow-[#C4A265]/25 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300"
                        >
                            {{ locale === 'ar' ? 'احجز عرضاً تجريبياً' : 'Book a demo' }}
                            <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                        <a
                            :href="`https://wa.me/${market.whatsapp}`"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full border border-white/20 text-white font-semibold text-sm hover:bg-white/10 transition-all duration-300"
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            {{ locale === 'ar' ? 'واتساب مباشر' : 'WhatsApp us' }}
                        </a>
                    </div>

                    <!-- Phone -->
                    <div class="mt-6 animate-fade-up">
                        <a :href="`tel:${market.phone.replace(/\s/g, '')}`" class="inline-flex items-center gap-2 text-sm text-white/60 hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span dir="ltr">{{ market.phone }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Local highlights -->
        <section class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 animate-fade-up">
                    <h2 class="text-2xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ locale === 'ar' ? `لماذا ${name}؟` : `Built for ${name}` }}
                    </h2>
                    <p class="text-gray-500 max-w-2xl mx-auto">
                        {{ locale === 'ar' ? 'دكتوراتو مُعدّ خصيصاً لسوقك المحلي — ليس مجرد ترجمة.' : 'Doctorato is adapted for your local market — not just translated.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 animate-stagger">
                    <div
                        v-for="(highlight, idx) in highlights"
                        :key="idx"
                        class="flex items-start gap-4 p-6 rounded-2xl bg-gradient-to-br from-light-blue/40 to-white border border-gray-100 hover:shadow-lg transition-shadow"
                    >
                        <div class="shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-[#C4A265] to-[#D4B876] flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <p class="text-sm md:text-base text-dark leading-relaxed font-medium">{{ highlight }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Insurers strip -->
        <section v-if="market.insurers && market.insurers.length" class="py-14 bg-light-blue/40">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-6">
                    {{ locale === 'ar' ? 'متكامل مع أكبر شركات التأمين' : 'Integrated with leading insurers' }}
                </p>
                <div class="flex flex-wrap items-center justify-center gap-x-8 gap-y-4">
                    <span
                        v-for="ins in market.insurers"
                        :key="ins"
                        class="inline-flex items-center px-4 py-2 rounded-xl bg-white border border-gray-100 shadow-sm text-sm font-semibold text-dark"
                    >
                        🛡️ {{ ins }}
                    </span>
                </div>
            </div>
        </section>

        <!-- Pricing teaser -->
        <section v-if="plans.length" class="py-20 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 animate-fade-up">
                    <h2 class="text-2xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ locale === 'ar' ? `أسعار ${name}` : `${name} Pricing` }}
                    </h2>
                    <p class="text-gray-500">
                        {{ locale === 'ar' ? 'كل الأسعار بالعملة المحلية — بدون مفاجآت.' : 'All prices in your local currency — no surprises.' }}
                    </p>
                </div>

                <div class="flex items-center justify-center gap-4 mb-8">
                    <button
                        @click="billing = 'monthly'"
                        :class="billing === 'monthly' ? 'bg-dark text-white' : 'bg-light-blue text-gray'"
                        class="px-5 py-2 rounded-full text-sm font-semibold transition"
                    >{{ locale === 'ar' ? 'شهري' : 'Monthly' }}</button>
                    <button
                        @click="billing = 'yearly'"
                        :class="billing === 'yearly' ? 'bg-dark text-white' : 'bg-light-blue text-gray'"
                        class="px-5 py-2 rounded-full text-sm font-semibold transition"
                    >
                        {{ locale === 'ar' ? 'سنوي' : 'Yearly' }}
                        <span class="ms-1 text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-500 text-white">-20%</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div
                        v-for="plan in plans"
                        :key="plan.id"
                        class="rounded-2xl p-6 border-2 transition-all flex flex-col"
                        :class="plan.is_popular ? 'bg-gradient-to-b from-[#1B4F72] to-[#0A1628] text-white border-[#C4A265] shadow-xl' : 'bg-white border-gray-100'"
                    >
                        <h3 class="font-bold mb-2" :class="plan.is_popular ? 'text-white' : 'text-dark'">
                            {{ locale === 'ar' ? plan.name_ar : plan.name_en }}
                        </h3>
                        <div class="flex items-baseline gap-1 mb-4">
                            <span class="text-3xl font-extrabold" :class="plan.is_popular ? 'text-[#C4A265]' : 'text-[#1B4F72]'">{{ fmt(priceFor(plan)) }}</span>
                            <span class="text-sm font-semibold" :class="plan.is_popular ? 'text-white/70' : 'text-gray'">{{ symbolFor(plan) }}</span>
                            <span class="text-xs" :class="plan.is_popular ? 'text-white/50' : 'text-gray'">/ {{ isYearly ? (locale === 'ar' ? 'سنة' : 'yr') : (locale === 'ar' ? 'شهر' : 'mo') }}</span>
                        </div>
                        <p v-if="Number(plan.setup_fee) > 0" class="text-xs mb-4" :class="plan.is_popular ? 'text-white/60' : 'text-gray'">
                            + {{ fmt(plan.setup_fee) }} {{ symbolFor(plan) }} {{ locale === 'ar' ? 'رسوم إعداد' : 'setup' }}
                        </p>
                        <Link
                            :href="`/checkout/${plan.slug}?cycle=${billing}`"
                            class="mt-auto block text-center py-2.5 rounded-xl font-semibold text-sm transition"
                            :class="plan.is_popular ? 'bg-[#C4A265] text-white hover:bg-[#D4B876]' : 'bg-dark text-white hover:bg-primary'"
                        >
                            {{ locale === 'ar' ? 'ابدأ الآن' : 'Get started' }}
                        </Link>
                    </div>
                </div>

                <div class="text-center mt-8">
                    <Link href="/pricing" class="inline-flex items-center gap-2 text-sm font-semibold text-[#1B4F72] hover:text-[#C4A265] transition-colors">
                        {{ locale === 'ar' ? 'شاهد كل التفاصيل' : 'See full pricing' }}
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Local testimonials -->
        <section v-if="testimonials.length" class="py-20 bg-light-blue/40">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-2xl md:text-4xl font-extrabold text-center text-[#1C2833] mb-10 animate-fade-up">
                    {{ locale === 'ar' ? 'ماذا يقول عملاؤنا في ' + name : 'What our customers say' }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <article v-for="t in testimonials" :key="t.id" class="bg-white rounded-2xl p-6 shadow-md">
                        <div class="flex gap-0.5 mb-3">
                            <svg v-for="n in 5" :key="n" class="w-4 h-4" :class="n <= t.rating ? 'text-[#C4A265]' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed mb-4">"{{ locale === 'ar' ? t.review_ar : (t.review_en || t.review_ar) }}"</p>
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-sm font-bold text-dark">{{ locale === 'ar' ? t.client_name_ar : (t.client_name_en || t.client_name_ar) }}</p>
                            <p class="text-xs text-[#C4A265] font-semibold">{{ locale === 'ar' ? t.clinic_name_ar : (t.clinic_name_en || t.clinic_name_ar) }}</p>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Final CTA -->
        <section class="py-20 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <span class="text-4xl mb-4 inline-block">{{ market.flag }}</span>
                <h2 class="text-2xl md:text-4xl font-extrabold mb-4">
                    {{ locale === 'ar' ? `انضم لأكبر عيادات ${name}` : `Join the leading clinics in ${name}` }}
                </h2>
                <p class="text-white/60 mb-8 leading-relaxed">
                    {{ locale === 'ar' ? 'تجربة مجانية 14 يوم — بدون بطاقة ائتمان — إعداد كامل لفريقنا.' : '14-day free trial — no credit card — full team onboarding.' }}
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <Link href="/demo" class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white font-bold shadow-xl hover:-translate-y-0.5 transition">
                        {{ locale === 'ar' ? 'ابدأ التجربة المجانية' : 'Start free trial' }}
                    </Link>
                    <Link href="/pricing" class="inline-flex items-center gap-2 px-8 py-4 rounded-full border-2 border-white/20 font-semibold hover:bg-white/10 transition">
                        {{ locale === 'ar' ? 'شاهد الأسعار' : 'View pricing' }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
