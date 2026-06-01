<script setup>
/**
 * /city/{city} — local-SEO landing page per Egyptian governorate.
 *
 * Targets "نظام إدارة عيادات في القاهرة / الإسكندرية / الجيزة …"
 * — the dominant pattern of buyer-intent searches that include a city.
 *
 * Each city ships its own LocalBusiness schema with geo-coordinates so
 * Google routes the page into the local pack on city-scoped queries.
 * Content is fully bilingual; the city name is interpolated into title,
 * H1, intro, and the FAQ to maximise keyword density without keyword-
 * stuffing.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const props = defineProps({
    city: { type: String, required: true },
});

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');

// Verified geo-coordinates from OpenStreetMap. Lat/lng anchor the
// LocalBusiness schema to the governorate centroid.
const CITIES = {
    cairo: {
        nameAr: 'القاهرة', nameEn: 'Cairo', region: 'Cairo Governorate',
        lat: 30.0444, lng: 31.2357,
        intro: { ar: 'القاهرة عاصمة الطب في مصر — أكثر من 8,000 عيادة وأكثر من 60 مستشفى خاص.',
                 en: 'Cairo is Egypt\'s medical capital — over 8,000 clinics and 60+ private hospitals.' },
    },
    alexandria: {
        nameAr: 'الإسكندرية', nameEn: 'Alexandria', region: 'Alexandria Governorate',
        lat: 31.2001, lng: 29.9187,
        intro: { ar: 'الإسكندرية ثاني أكبر سوق طبي في مصر — أكثر من 2,500 عيادة منها 400 في طب الأسنان.',
                 en: 'Alexandria is Egypt\'s second-largest medical market — 2,500+ clinics, 400 dental practices.' },
    },
    giza: {
        nameAr: 'الجيزة', nameEn: 'Giza', region: 'Giza Governorate',
        lat: 30.0131, lng: 31.2089,
        intro: { ar: 'الجيزة نمو سريع في القطاع الطبي الخاص — أكثر من 3,200 عيادة وأكثر من 80 مركز طبي.',
                 en: 'Giza shows rapid growth in private healthcare — 3,200+ clinics and 80+ medical centers.' },
    },
    mansoura: {
        nameAr: 'المنصورة', nameEn: 'Mansoura', region: 'Dakahlia Governorate',
        lat: 31.0411, lng: 31.3805,
        intro: { ar: 'المنصورة مركز طبي إقليمي يخدم الدلتا — أكثر من 1,800 عيادة و 30 مركز متخصص.',
                 en: 'Mansoura is a regional medical hub serving the Delta — 1,800+ clinics and 30 specialized centers.' },
    },
    tanta: {
        nameAr: 'طنطا', nameEn: 'Tanta', region: 'Gharbia Governorate',
        lat: 30.7865, lng: 31.0004,
        intro: { ar: 'طنطا قلب الغربية الطبي — أكثر من 1,200 عيادة في مختلف التخصصات.',
                 en: 'Tanta is Gharbia\'s medical heart — 1,200+ clinics across all specialties.' },
    },
    asyut: {
        nameAr: 'أسيوط', nameEn: 'Asyut', region: 'Asyut Governorate',
        lat: 27.1809, lng: 31.1837,
        intro: { ar: 'أسيوط أكبر مركز طبي في الصعيد — أكثر من 1,500 عيادة تخدم المنطقة كاملة.',
                 en: 'Asyut is Upper Egypt\'s largest medical hub — 1,500+ clinics serving the entire region.' },
    },
};

const data = computed(() => CITIES[props.city] || CITIES.cairo);
const cityName = computed(() => isAr.value ? data.value.nameAr : data.value.nameEn);

const seoTitle = computed(() => isAr.value
    ? `نظام إدارة عيادات في ${data.value.nameAr} | دكتوراتو ${new Date().getFullYear()}`
    : `Clinic Management System in ${data.value.nameEn} | Doctorato ${new Date().getFullYear()}`);

const seoDesc = computed(() => isAr.value
    ? `نظام إدارة عيادات احترافي للعيادات في ${data.value.nameAr}: EMR، فواتير، إيصال إلكتروني، WhatsApp، تأمين. تركيب مجاني وتجربة 30 يوم بدون بطاقة ائتمان.`
    : `Professional clinic management system for ${data.value.nameEn} clinics: EMR, billing, e-receipt, WhatsApp, insurance. Free setup and 30-day trial — no credit card.`);

const cityJsonLd = computed(() => [
    {
        '@context': 'https://schema.org',
        '@type': 'LocalBusiness',
        '@id': `https://doctorato.com/city/${props.city}#localbusiness`,
        name: `Doctorato — ${data.value.nameEn}`,
        description: seoDesc.value,
        url: `https://doctorato.com/city/${props.city}`,
        image: 'https://doctorato.com/images/og-cover.jpg',
        priceRange: 'EGP 1,990–6,990 per month',
        address: {
            '@type': 'PostalAddress',
            addressLocality: data.value.nameEn,
            addressRegion: data.value.region,
            addressCountry: 'EG',
        },
        geo: {
            '@type': 'GeoCoordinates',
            latitude: data.value.lat,
            longitude: data.value.lng,
        },
        areaServed: {
            '@type': 'City',
            name: data.value.nameEn,
            containedInPlace: { '@type': 'Country', name: 'Egypt' },
        },
        aggregateRating: {
            '@type': 'AggregateRating',
            ratingValue: '4.8',
            reviewCount: '210',
        },
    },
    {
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: [
            {
                '@type': 'Question',
                name: isAr.value
                    ? `هل دكتوراتو يدعم العيادات في ${data.value.nameAr}؟`
                    : `Does Doctorato support clinics in ${data.value.nameEn}?`,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: isAr.value
                        ? `نعم، دكتوراتو يخدم العيادات في ${data.value.nameAr} بدعم محلي بالعربي، تركيب مجاني، وترحيل بيانات من أي نظام موجود — كله عن بُعد بدون الحاجة لزيارة العيادة.`
                        : `Yes, Doctorato serves clinics in ${data.value.nameEn} with Arabic local support, free setup, and data migration from any existing system — all remote, no on-site visit needed.`,
                },
            },
            {
                '@type': 'Question',
                name: isAr.value
                    ? `كم سعر النظام للعيادات في ${data.value.nameAr}؟`
                    : `How much does the system cost for ${data.value.nameEn} clinics?`,
                acceptedAnswer: {
                    '@type': 'Answer',
                    text: isAr.value
                        ? `الأسعار شفافة بالجنيه المصري: المبتدئ 1,990 ج.م، النمو 3,990 ج.م، الاحترافي 6,990 ج.م شهريًا، مع تركيب مجاني على باقتي Starter و Growth.`
                        : `Transparent pricing in EGP: Starter 1,990, Growth 3,990, Professional 6,990 per month, with free setup on Starter + Growth plans.`,
                },
            },
        ],
    },
]);
</script>

<template>
    <SeoHead
        :title="seoTitle"
        :description="seoDesc"
        :json-ld="cityJsonLd"
        :breadcrumbs="[
            { name: isAr ? 'الرئيسية' : 'Home', url: '/' },
            { name: cityName, url: `/city/${city}` },
        ]"
    />
    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-28 pb-16 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            <div class="absolute top-0 end-0 w-96 h-96 bg-[#C4A265]/10 rounded-full blur-[120px]"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm ring-1 ring-[#C4A265]/30 text-[11px] font-bold uppercase tracking-widest text-[#C4A265] mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C4A265] animate-pulse"></span>
                    📍 {{ cityName }}, {{ isAr ? 'مصر' : 'Egypt' }}
                </span>
                <h1 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white mb-4 leading-tight">
                    {{ isAr ? `نظام إدارة عيادات احترافي في ${data.nameAr}` : `Professional Clinic Management in ${data.nameEn}` }}
                </h1>
                <p class="text-base sm:text-lg text-white/70 max-w-2xl mx-auto leading-relaxed mb-8">
                    {{ data.intro[isAr ? 'ar' : 'en'] }}
                </p>

                <!-- 4-benefits chip row -->
                <div class="flex flex-wrap justify-center gap-2 mb-8">
                    <span v-for="(b, i) in [
                        isAr ? 'تركيب مجاني' : 'Free setup',
                        isAr ? 'دعم بالعربي' : 'Arabic support',
                        isAr ? 'إيصال إلكتروني' : 'E-receipt',
                        isAr ? 'تجربة 30 يوم' : '30-day trial',
                    ]" :key="i"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/[0.06] backdrop-blur-sm border border-white/[0.1] text-xs sm:text-sm text-white/85">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ b }}
                    </span>
                </div>

                <Link
                    href="/demo"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold bg-gradient-to-r from-[#C4A265] to-[#D4B876] text-white shadow-xl shadow-[#C4A265]/25 hover:-translate-y-0.5 transition-all duration-300"
                >
                    {{ isAr ? `احجز عرضك في ${data.nameAr} اليوم` : `Book your demo in ${data.nameEn} today` }}
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </Link>
            </div>
        </section>

        <!-- Why for this city -->
        <section class="py-16 sm:py-20 bg-gradient-to-b from-gray-50 to-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-[#1C2833] mb-3">
                        {{ isAr ? `ليه عيادات ${data.nameAr} تختار دكتوراتو؟` : `Why ${data.nameEn} clinics choose Doctorato` }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <article v-for="(item, i) in [
                        { icon: '⚡', titleAr: 'تشغيل سريع', titleEn: 'Fast deployment', bodyAr: 'تركيب وترحيل البيانات خلال 24 ساعة — كله عن بُعد بدون الحاجة لزيارة عيادتك.', bodyEn: 'Setup and data migration in 24 hours — fully remote, no on-site visit needed.' },
                        { icon: '🛡️', titleAr: 'متوافق مع القانون المصري', titleEn: 'Egypt-compliant', bodyAr: 'إيصال إلكتروني، فاتورة ضريبية، تقارير مصلحة الضرائب — معتمد رسميًا.', bodyEn: 'E-receipt, tax invoice, Tax Authority reports — officially certified.' },
                        { icon: '📞', titleAr: 'دعم محلي بالعربي', titleEn: 'Local Arabic support', bodyAr: 'فريق دعم يفهم تخصصك ويرد على واتساب وهاتف خلال ساعات العمل.', bodyEn: 'Support team that understands your specialty and replies on WhatsApp + phone in business hours.' },
                        { icon: '💰', titleAr: 'سعر بالجنيه المصري', titleEn: 'EGP-denominated pricing', bodyAr: 'أسعار شفافة بدون تقلبات سعر الصرف — يبدأ من 1,990 ج.م/شهر.', bodyEn: 'Transparent pricing without exchange-rate volatility — from 1,990 EGP/month.' },
                        { icon: '🏥', titleAr: '6 تخصصات طبية', titleEn: '6 medical specialties', bodyAr: 'أسنان، جلدية، أطفال، نساء، باطنة، تليميديسن — قوالب جاهزة لكل تخصص.', bodyEn: 'Dental, derma, pediatrics, gyn, internal medicine, telemedicine — templates per specialty.' },
                        { icon: '📱', titleAr: 'WhatsApp للمرضى', titleEn: 'WhatsApp for patients', bodyAr: 'تأكيدات، تذكيرات، ووصفات تلقائية — قلّل الـ no-show بنسبة 65%.', bodyEn: 'Confirmations, reminders, prescriptions — cut no-shows by 65%.' },
                    ]" :key="i"
                             class="group bg-white rounded-3xl p-6 shadow-sm ring-1 ring-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="text-3xl mb-3">{{ item.icon }}</div>
                        <h3 class="text-base sm:text-lg font-extrabold text-[#1C2833] mb-2">
                            {{ isAr ? item.titleAr : item.titleEn }}
                        </h3>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ isAr ? item.bodyAr : item.bodyEn }}
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 sm:py-20 bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold mb-4">
                    {{ isAr ? `جرّب دكتوراتو في عيادتك في ${data.nameAr} مجانًا` : `Try Doctorato free in your ${data.nameEn} clinic` }}
                </h2>
                <p class="text-white/70 mb-8 max-w-xl mx-auto">
                    {{ isAr ? '30 يوم تجربة كاملة. بدون بطاقة ائتمان. تركيب مجاني.' : '30-day full trial. No credit card. Free setup.' }}
                </p>
                <Link
                    href="/demo"
                    class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl font-bold text-[#1B4F72] bg-white hover:bg-[#C4A265] hover:text-white transition-all duration-300 shadow-xl"
                >
                    {{ isAr ? 'احجز عرضك المجاني' : 'Book your free demo' }}
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </Link>
            </div>
        </section>
    </MainLayout>
</template>
