<script setup>
/**
 * RelatedPages — internal-linking block for the bottom of high-traffic
 * landing pages. Pure SEO play: more relevant internal links = more
 * crawl signal + better topic-cluster ranking + lower bounce rate.
 *
 * Usage:
 *   <RelatedPages :section="'specialty'" :exclude="'/dental'" />
 *
 * Sections are predefined keyword groups. The `exclude` prop hides the
 * current page from its own related-list so we don't link a page to
 * itself.
 */
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    section: { type: String, required: true },
    exclude: { type: String, default: '' },
});

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');

const SECTIONS = {
    // Specialty pages — link from one specialty to the others to build a topic cluster.
    specialty: [
        { href: '/dental', titleAr: 'نظام إدارة عيادة أسنان', titleEn: 'Dental clinic system', emoji: '🦷' },
        { href: '/dermatology', titleAr: 'نظام عيادة جلدية وتجميل', titleEn: 'Dermatology & cosmetic system', emoji: '🧴' },
        { href: '/pediatrics', titleAr: 'نظام عيادة أطفال', titleEn: 'Pediatrics clinic system', emoji: '👶' },
        { href: '/obstetrics', titleAr: 'نظام عيادة نساء وتوليد', titleEn: 'OB-GYN clinic system', emoji: '🤰' },
        { href: '/telemedicine', titleAr: 'استشارات أونلاين (Telemed)', titleEn: 'Telemedicine', emoji: '🎥' },
    ],

    // Use-case pages — links from facility-type queries to product/pricing.
    usecase: [
        { href: '/for/solo-doctor', titleAr: 'طبيب فرد', titleEn: 'Solo doctor', emoji: '🩺' },
        { href: '/for/small-clinic', titleAr: 'عيادة صغيرة', titleEn: 'Small clinic', emoji: '🏥' },
        { href: '/for/polyclinic', titleAr: 'مركز طبي / بولي كلينك', titleEn: 'Polyclinic', emoji: '🏢' },
        { href: '/for/hospital', titleAr: 'مستشفى', titleEn: 'Hospital', emoji: '🏨' },
        { href: '/for/dental-clinic', titleAr: 'عيادة أسنان', titleEn: 'Dental clinic', emoji: '🦷' },
        { href: '/for/derma-clinic', titleAr: 'عيادة جلدية وتجميل', titleEn: 'Derma clinic', emoji: '🧴' },
    ],

    // City landing pages — local-SEO cluster.
    city: [
        { href: '/city/cairo', titleAr: 'عيادات القاهرة', titleEn: 'Cairo clinics', emoji: '📍' },
        { href: '/city/alexandria', titleAr: 'عيادات الإسكندرية', titleEn: 'Alexandria clinics', emoji: '📍' },
        { href: '/city/giza', titleAr: 'عيادات الجيزة', titleEn: 'Giza clinics', emoji: '📍' },
        { href: '/city/mansoura', titleAr: 'عيادات المنصورة', titleEn: 'Mansoura clinics', emoji: '📍' },
        { href: '/city/tanta', titleAr: 'عيادات طنطا', titleEn: 'Tanta clinics', emoji: '📍' },
        { href: '/city/asyut', titleAr: 'عيادات أسيوط', titleEn: 'Asyut clinics', emoji: '📍' },
    ],

    // Versus pages — comparison cluster.
    versus: [
        { href: '/vs/vezeeta', titleAr: 'دكتوراتو vs فيزيتا', titleEn: 'Doctorato vs Vezeeta', emoji: '⚖️' },
        { href: '/vs/cliniko', titleAr: 'دكتوراتو vs Cliniko', titleEn: 'Doctorato vs Cliniko', emoji: '⚖️' },
        { href: '/vs/clinicgateway', titleAr: 'دكتوراتو vs ClinicGateway', titleEn: 'Doctorato vs ClinicGateway', emoji: '⚖️' },
        { href: '/vs/drsoft', titleAr: 'دكتوراتو vs دكتور سوفت', titleEn: 'Doctorato vs Dr. Soft', emoji: '⚖️' },
        { href: '/vs/practo', titleAr: 'دكتوراتو vs Practo', titleEn: 'Doctorato vs Practo', emoji: '⚖️' },
    ],

    // Core product pages — shown on landing/use-case/city pages.
    product: [
        { href: '/features', titleAr: 'كل المميزات', titleEn: 'All features', emoji: '✨' },
        { href: '/pricing', titleAr: 'الأسعار', titleEn: 'Pricing', emoji: '💰' },
        { href: '/portals', titleAr: '6 بوابات منفصلة', titleEn: '6 role-based portals', emoji: '🚪' },
        { href: '/reports', titleAr: 'التقارير والتحليلات', titleEn: 'Reports & analytics', emoji: '📊' },
        { href: '/demo', titleAr: 'احجز عرضًا مجانيًا', titleEn: 'Book a free demo', emoji: '📅' },
    ],
};

const items = computed(() => {
    const list = SECTIONS[props.section] || [];
    return list.filter(item => item.href !== props.exclude);
});

const heading = computed(() => {
    const map = {
        specialty: { ar: 'حلول لكل تخصص طبي', en: 'Solutions for every medical specialty' },
        usecase: { ar: 'الحل المناسب لحجم منشأتك', en: 'The right fit for your facility size' },
        city: { ar: 'دكتوراتو في محافظتك', en: 'Doctorato in your governorate' },
        versus: { ar: 'مقارنات مع المنافسين', en: 'Compare with competitors' },
        product: { ar: 'استكشف المزيد', en: 'Explore more' },
    };
    const m = map[props.section] || map.product;
    return isAr.value ? m.ar : m.en;
});
</script>

<template>
    <section v-if="items.length" class="py-12 sm:py-16 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl sm:text-2xl font-extrabold text-[#1C2833] mb-6 sm:mb-8 text-center">
                {{ heading }}
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                <Link
                    v-for="item in items"
                    :key="item.href"
                    :href="item.href"
                    class="group flex flex-col items-center gap-2 p-4 sm:p-5 bg-white rounded-2xl ring-1 ring-gray-100 hover:ring-[#C4A265] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300"
                >
                    <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform duration-300">{{ item.emoji }}</span>
                    <span class="text-xs sm:text-sm font-semibold text-[#1C2833] text-center leading-tight group-hover:text-[#1B4F72] transition-colors">
                        {{ isAr ? item.titleAr : item.titleEn }}
                    </span>
                </Link>
            </div>
        </div>
    </section>
</template>
