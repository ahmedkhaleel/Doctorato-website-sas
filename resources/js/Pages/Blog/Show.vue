<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useLocale } from '@/composables/useLocale';
import { useI18n } from 'vue-i18n';
import { Head, Link } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import { computed } from 'vue';

const { t } = useI18n();
const { localizedField, isRtl } = useLocale();
useScrollAnimation();

const props = defineProps({
    post: Object,
    relatedPosts: { type: Array, default: () => [] },
});

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString(isRtl.value ? 'ar-SA' : 'en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

const pageTitle = computed(() => localizedField(props.post, 'title'));
const content = computed(() => localizedField(props.post, 'content') || localizedField(props.post, 'body') || '');
const excerpt = computed(() => localizedField(props.post, 'excerpt') || '');

const shareUrl = computed(() => {
    if (typeof window !== 'undefined') {
        return encodeURIComponent(window.location.href);
    }
    return '';
});

const shareTitle = computed(() => encodeURIComponent(pageTitle.value || ''));

const shareLinks = computed(() => [
    {
        name: 'Twitter',
        url: `https://twitter.com/intent/tweet?url=${shareUrl.value}&text=${shareTitle.value}`,
        icon: 'M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z',
        color: 'hover:bg-[#1DA1F2]',
    },
    {
        name: 'LinkedIn',
        url: `https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl.value}`,
        icon: 'M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2zM4 6a2 2 0 100-4 2 2 0 000 4z',
        color: 'hover:bg-[#0077B5]',
    },
    {
        name: 'WhatsApp',
        url: `https://wa.me/?text=${shareTitle.value}%20${shareUrl.value}`,
        icon: 'M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z',
        color: 'hover:bg-[#25D366]',
    },
]);

function getExcerpt(post) {
    const ex = localizedField(post, 'excerpt');
    if (ex) return ex;
    const body = localizedField(post, 'content') || localizedField(post, 'body') || '';
    // Strip HTML tags for clean preview text
    return body.replace(/<[^>]*>/g, '').substring(0, 120) + '...';
}

// Strip HTML and count words for the schema's wordCount (Google uses
// this as a quality signal — long-form content ranks better).
const wordCount = computed(() => {
    const plain = (content.value || '').replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
    return plain ? plain.split(' ').length : 0;
});

// Estimated reading time. Arabic readers are slightly slower (~180 wpm)
// vs English (~225 wpm) for technical content; round up to 1 minute min.
const readingTime = computed(() => {
    if (props.post.reading_time) return props.post.reading_time;
    const wpm = isRtl.value ? 180 : 225;
    return Math.max(1, Math.ceil(wordCount.value / wpm));
});

// Build a slug from a heading's text — used as both the anchor id and
// the TOC link target. Strips HTML, normalises whitespace, and keeps
// Arabic letters intact.
function slugifyHeading(text) {
    return (text || '')
        .replace(/<[^>]*>/g, '')
        .trim()
        .toLowerCase()
        .replace(/[\s ]+/g, '-')
        .replace(/[^؀-ۿa-z0-9-]/g, '')
        .replace(/-+/g, '-')
        .replace(/^-|-$/g, '')
        .slice(0, 80) || 'heading';
}

// Walk the rendered HTML, extract every H2, and inject a deterministic
// id so the table of contents can scroll-link to it. We compute both
// the anchored HTML and the TOC list in one pass so they stay in sync.
const processedContent = computed(() => {
    const raw = content.value || '';
    if (!raw) return { html: '', toc: [] };
    const seen = new Map();
    const toc = [];
    const html = raw.replace(/<h2(\s[^>]*)?>([\s\S]*?)<\/h2>/gi, (match, attrs = '', inner) => {
        let slug = slugifyHeading(inner);
        const count = seen.get(slug) || 0;
        seen.set(slug, count + 1);
        if (count > 0) slug = `${slug}-${count + 1}`;
        toc.push({ id: slug, text: inner.replace(/<[^>]*>/g, '').trim() });
        // Preserve any pre-existing attributes (e.g. class) and add id.
        const existingAttrs = (attrs || '').replace(/\sid="[^"]*"/i, '');
        return `<h2${existingAttrs} id="${slug}">${inner}</h2>`;
    });
    return { html, toc };
});

const renderedContent = computed(() => processedContent.value.html);
const tableOfContents = computed(() => processedContent.value.toc);

// Resolve absolute URLs at SSR-safe time. window only exists in the
// browser; on the server we fall back to the canonical app URL via
// the inertia page props (set by app.blade.php as a meta tag).
const origin = typeof window !== 'undefined' ? window.location.origin : 'https://doctorato.com';
const pageUrl = typeof window !== 'undefined' ? window.location.href : `${origin}/blog/${props.post.slug}`;

const articleJsonLd = computed(() => ({
    '@context': 'https://schema.org',
    '@graph': [
        {
            '@type': 'BlogPosting',
            '@id': `${pageUrl}#article`,
            headline: pageTitle.value,
            description: excerpt.value,
            image: props.post.featured_image
                ? (props.post.featured_image.startsWith('http')
                    ? props.post.featured_image
                    : origin + props.post.featured_image)
                : `${origin}/images/og-cover.jpg`,
            datePublished: props.post.published_at || props.post.created_at,
            dateModified: props.post.updated_at || props.post.published_at,
            wordCount: wordCount.value,
            inLanguage: isRtl.value ? 'ar' : 'en',
            articleSection: props.post.category ? localizedField(props.post.category, 'name') : 'Healthcare',
            author: {
                '@type': 'Organization',
                '@id': `${origin}#organization`,
                name: 'Doctorato',
                url: origin,
            },
            publisher: {
                '@type': 'Organization',
                '@id': `${origin}#organization`,
                name: 'Doctorato',
                logo: {
                    '@type': 'ImageObject',
                    url: `${origin}/images/doctorato-logo.png`,
                },
            },
            mainEntityOfPage: {
                '@type': 'WebPage',
                '@id': pageUrl,
            },
        },
        {
            '@type': 'BreadcrumbList',
            itemListElement: [
                { '@type': 'ListItem', position: 1, name: t('blog.breadcrumb_home'), item: origin + '/' },
                { '@type': 'ListItem', position: 2, name: t('blog.breadcrumb_blog'), item: origin + '/blog' },
                { '@type': 'ListItem', position: 3, name: pageTitle.value, item: pageUrl },
            ],
        },
    ],
}));
</script>

<template>
    <SeoHead
        :title="pageTitle"
        :description="excerpt"
        :image="post.featured_image"
        :json-ld="articleJsonLd"
    />
    <MainLayout>
        <!-- Article Header -->
        <section class="relative py-20 bg-gradient-to-br from-primary via-primary-dark to-primary overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 start-20 w-72 h-72 bg-secondary rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="relative container mx-auto px-4">
                <div class="max-w-4xl mx-auto text-center">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center justify-center gap-2 text-sm text-white/60 mb-6 animate-fade-up">
                        <Link href="/" class="hover:text-white transition-colors">{{ t('blog.breadcrumb_home') }}</Link>
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <Link href="/blog" class="hover:text-white transition-colors">{{ t('blog.breadcrumb_blog') }}</Link>
                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        <span class="text-white/80 truncate max-w-xs">{{ pageTitle }}</span>
                    </nav>

                    <!-- Category -->
                    <span
                        v-if="post.category"
                        class="inline-block px-4 py-1.5 bg-white/10 text-white text-sm font-medium rounded-full mb-4 backdrop-blur-sm border border-white/20 animate-fade-up"
                    >
                        {{ localizedField(post.category, 'name') }}
                    </span>

                    <!-- Title -->
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight animate-fade-up">
                        {{ pageTitle }}
                    </h1>

                    <!-- Meta -->
                    <div class="flex items-center justify-center gap-4 text-white/60 text-sm animate-fade-up">
                        <span v-if="post.author" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {{ post.author.name }}
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ formatDate(post.published_at || post.created_at) }}
                        </span>
                        <span class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ readingTime }} {{ t('blog.min_read') }}
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Image -->
        <div v-if="post.featured_image" class="container mx-auto px-4 -mt-8 relative z-10">
            <div class="max-w-4xl mx-auto">
                <img
                    :src="post.featured_image"
                    :alt="pageTitle"
                    class="w-full rounded-2xl shadow-xl object-cover max-h-[500px]"
                />
            </div>
        </div>

        <!-- Article Content -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-4xl mx-auto">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <!-- Content -->
                        <article class="lg:col-span-8">
                            <!-- Inline TOC for mobile (desktop has the sidebar version) -->
                            <nav
                                v-if="tableOfContents.length > 1"
                                class="lg:hidden mb-8 p-5 bg-light-blue rounded-xl"
                                :aria-label="t('blog.toc_title')"
                            >
                                <p class="text-sm font-bold text-dark mb-3">{{ t('blog.toc_title') }}</p>
                                <ol class="space-y-1.5 text-sm">
                                    <li v-for="(item, i) in tableOfContents" :key="item.id">
                                        <a
                                            :href="`#${item.id}`"
                                            class="text-secondary hover:text-primary transition-colors flex items-start gap-2"
                                        >
                                            <span class="text-gray text-xs mt-0.5">{{ i + 1 }}.</span>
                                            <span>{{ item.text }}</span>
                                        </a>
                                    </li>
                                </ol>
                            </nav>
                            <div
                                class="prose prose-lg max-w-none text-dark
                                    prose-headings:text-dark prose-headings:font-bold
                                    prose-h2:scroll-mt-24
                                    prose-a:text-secondary prose-a:no-underline hover:prose-a:underline
                                    prose-img:rounded-xl prose-img:shadow-md
                                    prose-blockquote:border-s-4 prose-blockquote:border-secondary prose-blockquote:bg-light-blue prose-blockquote:rounded-e-xl prose-blockquote:py-2 prose-blockquote:px-4
                                    prose-code:bg-light-blue prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                                    prose-table:w-full prose-th:bg-light-blue prose-th:p-3 prose-td:p-3 prose-td:border-t prose-td:border-gray-light/20
                                    animate-fade-up"
                                v-html="renderedContent"
                            ></div>

                            <!-- Inline CTA — fires after the article body to convert engaged readers -->
                            <div class="mt-12 p-6 md:p-8 bg-gradient-to-br from-primary to-primary-dark rounded-2xl text-white animate-fade-up">
                                <h3 class="text-xl md:text-2xl font-bold mb-3">{{ t('blog.cta_title') }}</h3>
                                <p class="text-white/85 mb-5 text-sm md:text-base">{{ t('blog.cta_subtitle') }}</p>
                                <div class="flex flex-wrap gap-3">
                                    <Link
                                        href="/demo"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-white text-primary font-bold rounded-full hover:bg-light-blue transition-colors"
                                    >
                                        {{ t('blog.cta_demo') }}
                                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </Link>
                                    <Link
                                        href="/roi-calculator"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-white/10 text-white font-bold rounded-full border border-white/30 hover:bg-white/20 transition-colors"
                                    >
                                        {{ t('blog.cta_roi') }}
                                    </Link>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div v-if="post.tags?.length" class="mt-10 pt-6 border-t border-gray-light/20 animate-fade-up">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-medium text-gray">{{ t('blog.tags') }}:</span>
                                    <span
                                        v-for="tag in post.tags"
                                        :key="tag.id || tag"
                                        class="px-3 py-1 bg-light-blue text-gray text-xs font-medium rounded-full"
                                    >
                                        #{{ typeof tag === 'string' ? tag : localizedField(tag, 'name') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Share (mobile + tablet) -->
                            <div class="lg:hidden mt-8 pt-6 border-t border-gray-light/20 animate-fade-up">
                                <p class="text-sm font-medium text-dark mb-3">{{ t('blog.share') }}</p>
                                <div class="flex gap-3">
                                    <a
                                        v-for="link in shareLinks"
                                        :key="link.name"
                                        :href="link.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        :aria-label="link.name"
                                        class="w-10 h-10 bg-light-blue text-gray rounded-xl flex items-center justify-center transition-all duration-300 hover:text-white"
                                        :class="link.color"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" :d="link.icon" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </article>

                        <!-- Sidebar (desktop only): TOC + share + sticky CTA -->
                        <aside class="hidden lg:block lg:col-span-4">
                            <div class="sticky top-24 space-y-6">
                                <!-- Table of contents -->
                                <nav
                                    v-if="tableOfContents.length > 1"
                                    class="p-5 bg-light-blue rounded-xl"
                                    :aria-label="t('blog.toc_title')"
                                >
                                    <p class="text-sm font-bold text-dark mb-3 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        {{ t('blog.toc_title') }}
                                    </p>
                                    <ol class="space-y-1.5 text-sm max-h-[40vh] overflow-y-auto pr-1">
                                        <li v-for="(item, i) in tableOfContents" :key="item.id">
                                            <a
                                                :href="`#${item.id}`"
                                                class="text-secondary hover:text-primary transition-colors flex items-start gap-2 leading-snug"
                                            >
                                                <span class="text-gray text-xs mt-0.5 shrink-0">{{ i + 1 }}.</span>
                                                <span>{{ item.text }}</span>
                                            </a>
                                        </li>
                                    </ol>
                                </nav>

                                <!-- Sticky CTA: convert engaged readers without disrupting flow -->
                                <div class="p-5 bg-gradient-to-br from-primary to-primary-dark rounded-xl text-white">
                                    <p class="font-bold mb-1.5 text-base">{{ t('blog.sticky_cta_title') }}</p>
                                    <p class="text-white/85 text-xs mb-4 leading-relaxed">{{ t('blog.sticky_cta_subtitle') }}</p>
                                    <Link
                                        href="/demo"
                                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-white text-primary text-sm font-bold rounded-full hover:bg-light-blue transition-colors"
                                    >
                                        {{ t('blog.cta_demo') }}
                                    </Link>
                                </div>

                                <!-- Share -->
                                <div>
                                    <p class="text-xs text-gray font-medium mb-2">{{ t('blog.share') }}</p>
                                    <div class="flex gap-2">
                                        <a
                                            v-for="link in shareLinks"
                                            :key="link.name"
                                            :href="link.url"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            :aria-label="link.name"
                                            class="w-10 h-10 bg-light-blue text-gray rounded-xl flex items-center justify-center transition-all duration-300 hover:text-white"
                                            :class="link.color"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" :d="link.icon" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related Posts -->
        <section v-if="relatedPosts.length" class="py-20 bg-light-blue">
            <div class="container mx-auto px-4">
                <div class="max-w-6xl mx-auto">
                    <h2 class="text-2xl md:text-3xl font-bold text-dark mb-10 text-center animate-fade-up">
                        {{ t('blog.related_posts') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <article
                            v-for="related in relatedPosts"
                            :key="related.id"
                            class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 animate-fade-up"
                        >
                            <!-- Image -->
                            <Link :href="'/blog/' + related.slug" class="block overflow-hidden aspect-video bg-gradient-to-br from-primary/10 to-secondary/10">
                                <img
                                    v-if="related.featured_image"
                                    :src="related.featured_image"
                                    :alt="localizedField(related, 'title')"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center">
                                    <svg class="w-12 h-12 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </Link>

                            <!-- Content -->
                            <div class="p-5">
                                <div class="flex items-center gap-3 mb-2">
                                    <span
                                        v-if="related.category"
                                        class="px-2.5 py-0.5 bg-primary/10 text-primary text-xs font-medium rounded-full"
                                    >
                                        {{ localizedField(related.category, 'name') }}
                                    </span>
                                    <span class="text-gray text-xs">{{ formatDate(related.published_at || related.created_at) }}</span>
                                </div>
                                <h3 class="font-bold text-dark group-hover:text-primary transition-colors line-clamp-2 mb-2">
                                    <Link :href="'/blog/' + related.slug">
                                        {{ localizedField(related, 'title') }}
                                    </Link>
                                </h3>
                                <p class="text-gray text-sm leading-relaxed line-clamp-2">
                                    {{ getExcerpt(related) }}
                                </p>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <!-- Back to Blog CTA -->
        <section class="py-12 bg-white">
            <div class="container mx-auto px-4 text-center animate-fade-up">
                <Link
                    href="/blog"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-primary hover:bg-primary-dark text-white font-bold rounded-full transition-all duration-300"
                >
                    <svg class="w-5 h-5 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                    </svg>
                    {{ t('blog.back_to_blog') }}
                </Link>
            </div>
        </section>
    </MainLayout>
</template>
