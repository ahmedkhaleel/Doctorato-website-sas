<script setup>
/**
 * Cross-link strip for specialty / module pages.
 *
 * Drop at the bottom of Dental / Pediatrics / Dermatology /
 * Telemedicine / EMR / Medical CRM pages. Pass the `current` slug so
 * the component shows the OTHER three modules — keeps SEO juice
 * flowing between sibling topical pages without duplicating code on
 * every page.
 *
 * Usage:
 *   <RelatedModules current="dental" />
 */
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

const props = defineProps({
    current: { type: String, required: true },
    /** Limit to N items (default 3 for a clean grid). */
    max: { type: Number, default: 3 },
});

const ALL_MODULES = [
    {
        slug: 'dental',
        href: '/dental',
        icon: '🦷',
        gradient: 'from-[#C4A265]/10 to-white border-[#C4A265]/15',
        title_ar: 'عيادات الأسنان', title_en: 'Dental clinics',
        body_ar: 'مخطط أسنان FDI، خطط علاج، أشعة، مختبرات، وتقويم',
        body_en: 'FDI tooth chart, treatment plans, X-rays, labs, and orthodontics',
    },
    {
        slug: 'dermatology',
        href: '/dermatology',
        icon: '✨',
        gradient: 'from-rose-100/40 to-white border-rose-200/50',
        title_ar: 'الجلدية والتجميل', title_en: 'Derma & Cosmetics',
        body_ar: 'خرائط جلد، أرشيف صور قبل/بعد، بروتوكولات بوتكس وفيلر',
        body_en: 'Skin maps, before/after archive, Botox + filler protocols',
    },
    {
        slug: 'pediatrics',
        href: '/pediatrics',
        icon: '👶',
        gradient: 'from-sky-100/40 to-white border-sky-200/50',
        title_ar: 'طب الأطفال', title_en: 'Pediatrics',
        body_ar: 'منحنيات نمو WHO، تطعيمات، حسابات أدوية بالوزن، بوابة ولي الأمر',
        body_en: 'WHO growth charts, vaccinations, weight-based dosing, parent portal',
    },
    {
        slug: 'telemedicine',
        href: '/telemedicine',
        icon: '📹',
        gradient: 'from-emerald-100/40 to-white border-emerald-200/50',
        title_ar: 'الاستشارات عن بُعد', title_en: 'Telemedicine',
        body_ar: 'فيديو HD، وصفات إلكترونية، دفع قبل الجلسة، تكامل EMR',
        body_en: 'HD video, e-prescriptions, pre-session payment, EMR integration',
    },
    {
        slug: 'emr',
        href: '/emr',
        icon: '📋',
        gradient: 'from-blue-100/40 to-white border-blue-200/50',
        title_ar: 'EMR / EHR', title_en: 'EMR / EHR',
        body_ar: 'سجلات طبية إلكترونية، قوالب لكل تخصص، وصفات رقمية، تكامل مختبر',
        body_en: 'Electronic records, per-specialty templates, e-prescribing, lab integration',
    },
    {
        slug: 'medical-crm',
        href: '/medical-crm',
        icon: '🎯',
        gradient: 'from-amber-100/40 to-white border-amber-200/50',
        title_ar: 'CRM طبي', title_en: 'Medical CRM',
        body_ar: 'تتبع العملاء المحتملين، حملات SMS وواتساب، متابعات تلقائية',
        body_en: 'Lead tracking, SMS + WhatsApp campaigns, automated follow-ups',
    },
];

const others = computed(() =>
    ALL_MODULES.filter(m => m.slug !== props.current).slice(0, props.max)
);
</script>

<template>
    <section class="py-12 sm:py-16 bg-gradient-to-b from-white to-light-blue/30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8 sm:mb-10 animate-fade-up">
                <p class="text-xs sm:text-sm font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                    {{ tr('استكشف المزيد', 'Explore more') }}
                </p>
                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-[#1C2833]">
                    {{ tr('وحدات أخرى قد تهمك', 'Other modules you might like') }}
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                <Link
                    v-for="m in others"
                    :key="m.slug"
                    :href="m.href"
                    class="group relative bg-gradient-to-br border rounded-2xl p-5 sm:p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
                    :class="m.gradient"
                >
                    <div class="text-3xl sm:text-4xl mb-3">{{ m.icon }}</div>
                    <h3 class="text-base sm:text-lg font-bold text-[#1C2833] mb-2 group-hover:text-[#1B4F72] transition">
                        {{ isAr ? m.title_ar : m.title_en }}
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mb-4">
                        {{ isAr ? m.body_ar : m.body_en }}
                    </p>
                    <span class="inline-flex items-center gap-1 text-xs sm:text-sm font-semibold text-[#C4A265] group-hover:gap-2 transition-all">
                        {{ tr('شاهد التفاصيل', 'Learn more') }}
                        <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </span>
                </Link>
            </div>
        </div>
    </section>
</template>
