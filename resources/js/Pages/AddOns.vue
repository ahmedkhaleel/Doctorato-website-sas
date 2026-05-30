<script setup>
/**
 * /add-ons — public Add-Ons catalogue.
 *
 * Renders the 9 launch add-ons as a 3-column grid (2 on tablet,
 * 1 on mobile). Each card:
 *   - Icon glyph + name + description
 *   - Strikethrough anchor + launch price (when is_launch_active)
 *   - 'مجاناً مع Pro+' bundle ribbon when included_in_plans set
 *   - 'Most popular' / featured badge
 *
 * The page is purely informational — the actual checkout for an
 * add-on happens from inside the customer portal after they pick
 * a base plan, so each card links to /demo (sales) rather than a
 * cart.
 */
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

useScrollAnimation();

const { locale } = useI18n();
const isAr = computed(() => locale.value === 'ar');
const tr = (ar, en) => (isAr.value ? ar : en);

const props = defineProps({
    addons: { type: Array, default: () => [] },
});

function fmtMoney(v) {
    return new Intl.NumberFormat(isAr.value ? 'ar-EG' : 'en-US').format(Math.round(v || 0));
}

function periodLabel(p) {
    if (p === 'yearly') return tr('سنوياً', '/ year');
    if (p === 'one_time') return tr('مرة واحدة', '/ one-time');
    return tr('شهرياً', '/ month');
}

function iconPath(icon) {
    // Single SVG path per icon name. Falls back to a default circle.
    const lib = {
        doctor: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        branch: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
        whatsapp: 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z',
        sms: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
        video: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
        shield: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
        phone: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
        flask: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z',
        box: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4',
    };
    return lib[icon] || 'M5 13l4 4L19 7';
}

// Group add-ons: featured first, then the rest.
const featured = computed(() => props.addons.filter((a) => a.is_featured));
const others = computed(() => props.addons.filter((a) => !a.is_featured));

const addonsJsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@type': 'ItemList',
    name: tr('إضافات Doctorato', 'Doctorato Add-Ons'),
    description: tr(
        'إضافات اختيارية لباقات Doctorato — WhatsApp Business API، تكامل تأمين، تطبيق mobile مخصص، باقات SMS، وحدة تليميديسين.',
        'Optional add-ons for Doctorato plans — WhatsApp Business API, insurance integration, custom mobile app, SMS bundles, telemedicine module.'
    ),
    itemListElement: props.addons.map((a, i) => ({
        '@type': 'ListItem',
        position: i + 1,
        name: isAr.value ? a.name_ar : a.name_en,
        description: isAr.value ? a.description_ar : a.description_en,
    })),
}));
</script>

<template>
    <SeoHead
        :title="tr('الإضافات | Doctorato', 'Add-Ons | Doctorato')"
        :description="tr('إضافات اختيارية لتخصيص باقتك: WhatsApp Business API، تكامل التأمين، تطبيق mobile مخصص، باقات SMS، وحدة تليميديسين — بأسعار الإطلاق', 'Optional add-ons to customise your plan: WhatsApp Business API, insurance integration, custom mobile app, SMS bundles, telemedicine — at launch prices')"
        :json-ld="addonsJsonLd"
        :breadcrumbs="[
            { name: tr('الرئيسية', 'Home'), url: '/' },
            { name: tr('الإضافات', 'Add-ons'), url: '/add-ons' },
        ]"
    />

    <MainLayout>
        <!-- Hero -->
        <section class="relative pt-32 pb-12 overflow-hidden bg-[#070F1B] text-white">
            <div class="absolute inset-0 bg-gradient-to-br from-[#0A1628] via-[#0D2F45] to-[#070F1B]"></div>
            <div class="absolute top-[-15%] start-[10%] w-[500px] h-[500px] bg-[#1B4F72]/35 rounded-full blur-[140px] animate-aurora"></div>
            <div class="absolute bottom-[-15%] end-[10%] w-[600px] h-[600px] bg-[#C4A265]/20 rounded-full blur-[160px] animate-aurora" style="animation-delay: -6s"></div>

            <div class="relative max-w-4xl mx-auto px-4 text-center">
                <div class="inline-flex items-center gap-2 px-5 py-2 bg-white/[0.06] backdrop-blur-md rounded-full mb-6 border border-white/10 animate-fade-up">
                    <span class="flex w-2 h-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C4A265] opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-[#C4A265]"></span>
                    </span>
                    <span class="text-sm font-semibold tracking-wide">{{ tr('إضافات الباقات', 'Plan Add-Ons') }}</span>
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-4 animate-fade-up leading-tight">
                    <span class="bg-gradient-to-br from-white to-[#C4A265] bg-clip-text text-transparent">
                        {{ tr('خصّص باقتك بإضافات تنافسية', 'Customise your plan with launch-priced add-ons') }}
                    </span>
                </h1>
                <p class="text-lg text-white/70 max-w-2xl mx-auto animate-fade-up">
                    {{ tr('كل إضافة بأسعار الإطلاق حتى نهاية 2026 — بعضها مجاناً مع باقتي Professional و Enterprise', 'All add-ons at launch pricing through end of 2026 — several are bundled FREE in Professional and Enterprise') }}
                </p>
            </div>
        </section>

        <!-- Featured add-ons -->
        <section v-if="featured.length" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                        ⭐ {{ tr('الأكثر طلباً', 'Most popular') }}
                    </p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C2833]">
                        {{ tr('الإضافات التي تختارها العيادات أولاً', 'The add-ons clinics pick first') }}
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 animate-stagger">
                    <article
                        v-for="addon in featured"
                        :key="addon.id"
                        class="group relative bg-white rounded-3xl border border-[#C4A265]/25 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-500 p-6 overflow-hidden"
                    >
                        <!-- Top gold accent bar -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#C4A265] via-[#D4B876] to-[#C4A265]"></div>

                        <!-- Badge top-end -->
                        <div v-if="addon.badge_ar || addon.badge_en" class="absolute top-4 end-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider border border-emerald-100">
                                {{ isAr ? addon.badge_ar : addon.badge_en }}
                            </span>
                        </div>

                        <!-- Icon -->
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#1B4F72] to-[#0D2B45] flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath(addon.icon)"/>
                            </svg>
                        </div>

                        <h3 class="text-base font-extrabold text-[#1C2833] mb-1">{{ isAr ? addon.name_ar : addon.name_en }}</h3>
                        <p class="text-xs text-[#5A6C7D] leading-relaxed mb-5">{{ isAr ? addon.description_ar : addon.description_en }}</p>

                        <!-- Pricing -->
                        <div class="border-t border-gray-100 pt-4">
                            <div v-if="addon.is_launch_active && addon.price_egp_launch < addon.price_egp" class="text-xs text-gray-400 line-through tabular-nums">
                                {{ fmtMoney(addon.price_egp) }} ج.م
                            </div>
                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-[#1B4F72] tabular-nums">{{ fmtMoney(addon.active_price ?? addon.price_egp) }}</span>
                                <span class="text-xs text-[#5A6C7D]">ج.م {{ periodLabel(addon.period) }}</span>
                            </div>
                            <div v-if="addon.is_launch_active && addon.price_egp_launch < addon.price_egp" class="mt-1.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold uppercase tracking-wider border border-rose-100">
                                🔥 {{ tr('سعر الإطلاق', 'Launch price') }}
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- All other add-ons -->
        <section v-if="others.length" class="py-16 bg-[#FBFAF6]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10">
                    <p class="text-xs font-bold uppercase tracking-widest text-[#C4A265] mb-2">
                        {{ tr('باقي الإضافات', 'More add-ons') }}
                    </p>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-[#1C2833]">
                        {{ tr('كل ما تحتاجه لتخصيص الباقة', 'Everything else for fine-tuning your plan') }}
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5 animate-stagger">
                    <article
                        v-for="addon in others"
                        :key="addon.id"
                        class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all p-5"
                    >
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#1B4F72]/8 to-[#C4A265]/8 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#1B4F72]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="iconPath(addon.icon)"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 mb-1">
                                    <h3 class="text-sm font-bold text-[#1C2833]">{{ isAr ? addon.name_ar : addon.name_en }}</h3>
                                    <span v-if="addon.badge_ar || addon.badge_en" class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[9px] font-bold uppercase tracking-wider">
                                        {{ isAr ? addon.badge_ar : addon.badge_en }}
                                    </span>
                                </div>
                                <p class="text-xs text-[#5A6C7D] leading-relaxed mb-3">{{ isAr ? addon.description_ar : addon.description_en }}</p>
                                <div class="flex items-baseline gap-1.5">
                                    <span v-if="addon.is_launch_active && addon.price_egp_launch < addon.price_egp" class="text-xs text-gray-400 line-through tabular-nums">
                                        {{ fmtMoney(addon.price_egp) }}
                                    </span>
                                    <span class="text-base font-extrabold text-[#1B4F72] tabular-nums">{{ fmtMoney(addon.active_price ?? addon.price_egp) }}</span>
                                    <span class="text-[10px] text-[#5A6C7D]">ج.م {{ periodLabel(addon.period) }}</span>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <!-- Footnote + CTA -->
        <section class="py-16 bg-gradient-to-br from-[#0D2B45] to-[#1B4F72] text-white">
            <div class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-2xl md:text-3xl font-extrabold mb-3">
                    {{ tr('غير متأكد أيها يناسبك؟', 'Not sure which add-ons fit?') }}
                </h2>
                <p class="text-white/70 mb-6 max-w-2xl mx-auto">
                    {{ tr('احجز جلسة استشارية مجانية مع فريقنا. نقترح عليك الباقة + الإضافات المناسبة بناءً على حجم عيادتك واحتياجاتك.', 'Book a free consultation. We will recommend the right plan + add-ons based on your clinic size and needs.') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <Link href="/demo" class="px-8 py-3.5 bg-[#C4A265] hover:bg-[#A88B4A] text-white font-bold rounded-full transition-all hover:-translate-y-0.5 hover:shadow-xl hover:shadow-[#C4A265]/30">
                        {{ tr('اطلب استشارة مجانية', 'Book a free consultation') }}
                    </Link>
                    <Link href="/pricing" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 border border-white/15 text-white font-bold rounded-full transition-all hover:-translate-y-0.5">
                        {{ tr('شاهد الباقات', 'See plans') }}
                    </Link>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
