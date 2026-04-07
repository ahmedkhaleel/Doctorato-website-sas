<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import { useScrollAnimation } from '@/composables/useScrollAnimation';
import { useLocale } from '@/composables/useLocale';
import { useI18n } from 'vue-i18n';
import { Head, Link, router } from '@inertiajs/vue3';
import SeoHead from '@/Components/SeoHead.vue';
import { ref, computed, watch } from 'vue';

const { t } = useI18n();
const { localizedField, isRtl } = useLocale();
useScrollAnimation();

const props = defineProps({
    posts: Object, // paginated { data, links, meta }
    categories: { type: Array, default: () => [] },
    currentCategory: { type: String, default: null },
});

const selectedCategory = ref(props.currentCategory);

watch(selectedCategory, (val) => {
    router.get(route('blog.index'), { category: val || undefined }, {
        preserveState: true,
        preserveScroll: true,
    });
});

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString(isRtl.value ? 'ar-SA' : 'en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
}

function getExcerpt(post) {
    const excerpt = localizedField(post, 'excerpt');
    if (excerpt) return excerpt;
    const body = localizedField(post, 'body') || '';
    return body.substring(0, 160) + '...';
}
</script>

<template>
    <SeoHead
        :title="t('blog.page_title')"
        :description="t('blog.subtitle') || 'مقالات ونصائح لإدارة العيادات الطبية بذكاء'"
    />
    <MainLayout>
        <!-- Hero Section -->
        <section class="relative py-24 bg-gradient-to-br from-primary via-primary-dark to-primary overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-20 start-20 w-72 h-72 bg-secondary rounded-full blur-3xl"></div>
                <div class="absolute bottom-10 end-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
            </div>

            <div class="relative container mx-auto px-4 text-center">
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white mb-6 animate-fade-up">
                    {{ t('blog.hero_title') }}
                </h1>
                <p class="text-xl text-white/80 max-w-3xl mx-auto animate-fade-up">
                    {{ t('blog.hero_subtitle') }}
                </p>
            </div>
        </section>

        <!-- Blog Content -->
        <section class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-10">
                    <!-- Sidebar - Categories -->
                    <aside class="lg:col-span-1 order-2 lg:order-1">
                        <div class="sticky top-24 bg-light-blue rounded-2xl p-6 shadow-sm animate-fade-up">
                            <h3 class="text-lg font-bold text-dark mb-4">{{ t('blog.categories') }}</h3>
                            <div class="space-y-2">
                                <button
                                    @click="selectedCategory = null"
                                    class="w-full text-start px-4 py-2.5 rounded-xl text-sm font-medium transition-all"
                                    :class="!selectedCategory
                                        ? 'bg-primary text-white'
                                        : 'text-gray hover:bg-white hover:text-dark'"
                                >
                                    {{ t('blog.all_categories') }}
                                </button>
                                <button
                                    v-for="cat in categories"
                                    :key="cat.slug"
                                    @click="selectedCategory = cat.slug"
                                    class="w-full text-start px-4 py-2.5 rounded-xl text-sm font-medium transition-all"
                                    :class="selectedCategory === cat.slug
                                        ? 'bg-primary text-white'
                                        : 'text-gray hover:bg-white hover:text-dark'"
                                >
                                    {{ localizedField(cat, 'name') }}
                                    <span class="text-xs opacity-60 ms-1">({{ cat.posts_count }})</span>
                                </button>
                            </div>
                        </div>
                    </aside>

                    <!-- Posts Grid -->
                    <div class="lg:col-span-3 order-1 lg:order-2">
                        <!-- Empty state -->
                        <div v-if="!posts?.data?.length" class="text-center py-20 animate-fade-up">
                            <svg class="w-20 h-20 text-gray-light mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <h3 class="text-xl font-bold text-dark mb-2">{{ t('blog.no_posts') }}</h3>
                            <p class="text-gray">{{ t('blog.no_posts_desc') }}</p>
                        </div>

                        <!-- Posts -->
                        <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <article
                                v-for="post in posts.data"
                                :key="post.id"
                                class="group bg-light-blue rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 animate-fade-up"
                            >
                                <!-- Image -->
                                <Link :href="'/blog/' + post.slug" class="block overflow-hidden aspect-video bg-gradient-to-br from-primary/10 to-secondary/10">
                                    <img
                                        v-if="post.featured_image"
                                        :src="post.featured_image"
                                        :alt="localizedField(post, 'title')"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    />
                                    <div v-else class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-primary/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </Link>

                                <!-- Content -->
                                <div class="p-6">
                                    <!-- Category & Date -->
                                    <div class="flex items-center gap-3 mb-3">
                                        <span
                                            v-if="post.category"
                                            class="px-3 py-1 bg-primary/10 text-primary text-xs font-medium rounded-full"
                                        >
                                            {{ localizedField(post.category, 'name') }}
                                        </span>
                                        <span class="text-gray text-xs">{{ formatDate(post.published_at || post.created_at) }}</span>
                                    </div>

                                    <!-- Title -->
                                    <h2 class="text-lg font-bold text-dark mb-2 group-hover:text-primary transition-colors line-clamp-2">
                                        <Link :href="'/blog/' + post.slug">
                                            {{ localizedField(post, 'title') }}
                                        </Link>
                                    </h2>

                                    <!-- Excerpt -->
                                    <p class="text-gray text-sm leading-relaxed line-clamp-3 mb-4">
                                        {{ getExcerpt(post) }}
                                    </p>

                                    <!-- Read More -->
                                    <Link
                                        :href="'/blog/' + post.slug"
                                        class="inline-flex items-center gap-1 text-secondary font-medium text-sm hover:gap-2 transition-all"
                                    >
                                        {{ t('blog.read_more') }}
                                        <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                        </svg>
                                    </Link>
                                </div>
                            </article>
                        </div>

                        <!-- Pagination -->
                        <nav v-if="posts?.links && posts.links.length > 3" class="mt-12 flex justify-center animate-fade-up">
                            <div class="flex items-center gap-1">
                                <template v-for="link in posts.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        class="px-4 py-2 rounded-xl text-sm font-medium transition-all"
                                        :class="link.active
                                            ? 'bg-primary text-white'
                                            : 'bg-light-blue text-gray hover:bg-primary/10 hover:text-primary'"
                                        v-html="link.label"
                                        preserve-scroll
                                    />
                                    <span
                                        v-else
                                        class="px-4 py-2 text-sm text-gray-light cursor-not-allowed"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </section>
    </MainLayout>
</template>
