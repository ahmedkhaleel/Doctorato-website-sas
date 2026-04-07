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

const jsonLdString = computed(() =>
    props.jsonLd ? JSON.stringify(props.jsonLd) : null
);

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

        <meta head-key="twitter:title" name="twitter:title" :content="fullTitleForMeta" />
        <meta head-key="twitter:description" name="twitter:description" :content="description" />
        <meta head-key="twitter:image" name="twitter:image" :content="ogImage" />
    </Head>

    <Teleport v-if="jsonLdString" to="head">
        <component :is="'script'" type="application/ld+json" v-html="jsonLdString" />
    </Teleport>
</template>
