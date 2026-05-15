<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useLocale } from '@/composables/useLocale';
import { computed } from 'vue';

const { t, locale } = useI18n();
const { localizedField, isRtl } = useLocale();

const props = defineProps({
    suggestedPosts: { type: Array, default: () => [] },
    requestedPath: { type: String, default: '' },
});

const isAr = computed(() => locale.value === 'ar');

// Top destinations to send a lost visitor back into. Keeps them on
// the site instead of bouncing back to Google.
const popularDestinations = computed(() => [
    {
        href: '/',
        title: isAr.value ? 'الصفحة الرئيسية' : 'Homepage',
        desc: isAr.value ? 'ابدأ من هنا' : 'Start here',
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    },
    {
        href: '/features',
        title: isAr.value ? 'المميزات' : 'Features',
        desc: isAr.value ? '800+ خاصية' : '800+ features',
        icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    },
    {
        href: '/pricing',
        title: isAr.value ? 'الأسعار' : 'Pricing',
        desc: isAr.value ? 'خطط من 297 ر.س / 799 ج.م' : 'Plans from SAR 297 / EGP 799',
        icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    },
    {
        href: '/blog',
        title: isAr.value ? 'المدوّنة' : 'Blog',
        desc: isAr.value ? 'مقالات وأدلة' : 'Articles & guides',
        icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    },
    {
        href: '/demo',
        title: isAr.value ? 'احجز عرض' : 'Book a demo',
        desc: isAr.value ? '15 دقيقة مجاناً' : '15 minutes, free',
        icon: 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
    },
    {
        href: '/contact',
        title: isAr.value ? 'تواصل معنا' : 'Contact',
        desc: isAr.value ? 'فريقنا جاهز للمساعدة' : 'Our team is here to help',
        icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    },
]);

function postTitle(post) {
    return localizedField(post, 'title');
}
</script>

<template>
    <!-- noindex the 404 page itself so Google doesn't index error URLs -->
    <SeoHead
        :title="isAr ? 'الصفحة غير موجودة (404)' : 'Page not found (404)'"
        :description="isAr
            ? 'الصفحة اللي بتدوّر عليها مش موجودة، لكن في 6 وجهات شائعة + أحدث المقالات تحت.'
            : 'The page you are looking for does not exist — but here are 6 popular destinations and our latest articles.'"
        :noindex="true"
    />

    <MainLayout>
        <section class="relative py-20 lg:py-28 bg-gradient-to-br from-[#0A1628] via-[#1B4F72] to-[#0A1628] overflow-hidden">
            <div class="absolute inset-0 opacity-10 pointer-events-none">
                <div class="absolute top-20 start-20 w-72 h-72 bg-secondary rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="relative container mx-auto px-4 text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-8">
                    <span class="text-5xl font-black text-white">404</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-white mb-4 max-w-3xl mx-auto leading-tight">
                    {{ isAr ? 'الصفحة دي مش موجودة' : "This page doesn't exist" }}
                </h1>
                <p class="text-lg md:text-xl text-white/75 max-w-2xl mx-auto mb-8">
                    {{ isAr
                        ? 'يا إما الرابط فيه خطأ، يا إما الصفحة اتنقلت أو اتشالت. خلّينا نوصلك لحاجة مفيدة.'
                        : 'The link is broken, moved, or never existed. Let us get you somewhere useful.' }}
                </p>
                <div v-if="requestedPath" class="inline-block px-4 py-2 bg-white/5 border border-white/10 rounded-full text-white/50 text-sm font-mono mb-8 max-w-full truncate">
                    {{ requestedPath }}
                </div>
                <div class="flex flex-wrap gap-3 justify-center">
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary font-bold rounded-full hover:bg-light-blue transition-colors"
                    >
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ isAr ? 'الرئيسية' : 'Go home' }}
                    </Link>
                    <Link
                        href="/blog"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 text-white font-bold rounded-full border border-white/30 hover:bg-white/20 transition-colors"
                    >
                        {{ isAr ? 'تصفّح المقالات' : 'Browse articles' }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- Popular destinations grid -->
        <section class="py-16 lg:py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-5xl mx-auto">
                    <h2 class="text-2xl md:text-3xl font-bold text-dark mb-2 text-center">
                        {{ isAr ? 'وجهات مشهورة' : 'Popular destinations' }}
                    </h2>
                    <p class="text-gray text-center mb-10">
                        {{ isAr ? 'الأماكن اللي بيدخلها معظم الزوار' : 'Where most visitors land' }}
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link
                            v-for="dest in popularDestinations"
                            :key="dest.href"
                            :href="dest.href"
                            class="group p-5 rounded-2xl border border-gray-200 hover:border-primary hover:shadow-lg transition-all duration-300 flex items-start gap-4"
                        >
                            <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0 group-hover:bg-primary group-hover:text-white transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" :d="dest.icon" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-dark mb-0.5 group-hover:text-primary transition-colors">{{ dest.title }}</h3>
                                <p class="text-gray text-sm">{{ dest.desc }}</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Suggested articles -->
        <section v-if="suggestedPosts.length" class="py-16 lg:py-20 bg-light-blue">
            <div class="container mx-auto px-4">
                <div class="max-w-5xl mx-auto">
                    <h2 class="text-2xl md:text-3xl font-bold text-dark mb-2 text-center">
                        {{ isAr ? 'أو اقرأ آخر المقالات' : 'Or read our latest articles' }}
                    </h2>
                    <p class="text-gray text-center mb-10">
                        {{ isAr ? 'أدلة عملية لإدارة عيادتك' : 'Practical guides for running your clinic' }}
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Link
                            v-for="post in suggestedPosts"
                            :key="post.slug"
                            :href="`/blog/${post.slug}`"
                            class="group bg-white p-6 rounded-2xl hover:shadow-lg transition-all duration-300"
                        >
                            <span class="inline-block px-2.5 py-1 bg-primary/10 text-primary text-xs font-semibold rounded-full mb-3">
                                {{ isAr ? 'مقال' : 'Article' }}
                            </span>
                            <h3 class="font-bold text-dark line-clamp-3 group-hover:text-primary transition-colors leading-snug">
                                {{ postTitle(post) }}
                            </h3>
                            <div class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-secondary">
                                {{ isAr ? 'اقرأ' : 'Read' }}
                                <svg class="w-3.5 h-3.5 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
