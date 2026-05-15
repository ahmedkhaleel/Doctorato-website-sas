<script setup>
/**
 * Per-page SEO component.
 *
 * Usage:
 *   <SeoHead
 *     title="الأسعار"
 *     description="خطط واضحة بالجنيه المصري..."
 *     :jsonLd="{ '@context': 'https://schema.org', ...}"
 *   />
 *
 * Automatically builds absolute canonical, og:url, and overrides the head tags
 * defined in app.blade.php (matched via head-key).
 */
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    image: { type: String, default: null },        // absolute or path under /images
    noindex: { type: Boolean, default: false },
    jsonLd: { type: [Object, Array], default: null },
    // Pass a breadcrumb trail and we'll emit BreadcrumbList JSON-LD
    // alongside the page-specific schema. Example:
    //   :breadcrumbs="[{name:'Home', url:'/'}, {name:'EMR', url:'/emr'}]"
    breadcrumbs: { type: Array, default: () => [] },
});

const page = usePage();

const origin = computed(() => {
    if (typeof window !== 'undefined') return window.location.origin;
    return '';
});

const canonical = computed(() => {
    if (typeof window === 'undefined') return '';
    const url = new URL(window.location.href);
    url.search = ''; // drop query params
    url.hash = '';
    return url.toString();
});

const ogImage = computed(() => {
    if (!props.image) return `${origin.value}/images/og-cover.jpg`;
    if (props.image.startsWith('http')) return props.image;
    return `${origin.value}${props.image.startsWith('/') ? '' : '/'}${props.image}`;
});

// NOTE: Inertia's createInertiaApp title callback already appends " — Doctorato"
// so we pass the raw title here.
const fullTitleForMeta = computed(() => `${props.title} — Doctorato`);

// Auto-build a BreadcrumbList block when the page passed a trail.
// Each item gets its absolute URL resolved against the current origin.
const breadcrumbJsonLd = computed(() => {
    if (!props.breadcrumbs?.length) return null;
    const base = origin.value || 'https://doctorato.com';
    return {
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: props.breadcrumbs.map((crumb, i) => ({
            '@type': 'ListItem',
            position: i + 1,
            name: crumb.name,
            item: crumb.url?.startsWith('http') ? crumb.url : `${base}${crumb.url}`,
        })),
    };
});

const jsonLdString = computed(() => {
    const blocks = [];
    if (props.jsonLd) {
        Array.isArray(props.jsonLd) ? blocks.push(...props.jsonLd) : blocks.push(props.jsonLd);
    }
    if (breadcrumbJsonLd.value) blocks.push(breadcrumbJsonLd.value);
    if (!blocks.length) return null;
    // If we have multiple blocks, emit them as a single @graph rather
    // than multiple <script> tags — Google parses both, but @graph keeps
    // related entities linked via @id references.
    return JSON.stringify(blocks.length === 1 ? blocks[0] : blocks);
});

// Inject per-page JSON-LD via a teleport to <head>. Inertia's <Head> component
// strips child content of <script> tags (its virtual head only tracks attrs),
// so we bypass it for structured data.
</script>

<template>
    <Head :title="title">
        <meta head-key="description" name="description" :content="description" />
        <meta v-if="noindex" name="robots" content="noindex, nofollow" />

        <link head-key="canonical" rel="canonical" :href="canonical" />

        <meta head-key="og:title" property="og:title" :content="fullTitleForMeta" />
        <meta head-key="og:description" property="og:description" :content="description" />
        <meta head-key="og:url" property="og:url" :content="canonical" />
        <meta head-key="og:image" property="og:image" :content="ogImage" />
        <meta head-key="og:image:alt" property="og:image:alt" :content="title" />
        <meta head-key="og:image:width" property="og:image:width" content="1200" />
        <meta head-key="og:image:height" property="og:image:height" content="630" />

        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:title" name="twitter:title" :content="fullTitleForMeta" />
        <meta head-key="twitter:description" name="twitter:description" :content="description" />
        <meta head-key="twitter:image" name="twitter:image" :content="ogImage" />
        <meta head-key="twitter:image:alt" name="twitter:image:alt" :content="title" />
    </Head>

    <Teleport v-if="jsonLdString" to="head">
        <component :is="'script'" type="application/ld+json" v-html="jsonLdString" />
    </Teleport>
</template>
