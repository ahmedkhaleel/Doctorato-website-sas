<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RichEditor from '@/Components/RichEditor.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    posts: Object,
    categories: Array,
    filters: Object,
    stats: Object,
});

const showModal = ref(false);
const editing = ref(null);
const activeLang = ref('ar');

const filter = ref({
    q: props.filters?.q ?? '',
    status: props.filters?.status ?? '',
    category: props.filters?.category ?? '',
});

const form = useForm({
    category_id: null,
    title_ar: '',
    title_en: '',
    slug: '',
    excerpt_ar: '',
    excerpt_en: '',
    content_ar: '',
    content_en: '',
    featured_image: '',
    seo_title_ar: '',
    seo_title_en: '',
    seo_desc_ar: '',
    seo_desc_en: '',
    status: 'draft',
    is_featured: false,
    published_at: '',
});

const statusLabels = {
    draft: 'مسودة',
    published: 'منشور',
    scheduled: 'مجدول',
};
const statusColors = {
    draft: 'bg-gray-100 text-gray-700',
    published: 'bg-emerald-100 text-emerald-700',
    scheduled: 'bg-amber-100 text-amber-700',
};

const featuredPreview = computed(() => form.featured_image || null);

function openCreate() {
    editing.value = null;
    form.reset();
    form.status = 'draft';
    activeLang.value = 'ar';
    showModal.value = true;
}

function openEdit(post) {
    // Reset any state from a prior add/edit session before populating.
    form.reset();
    form.clearErrors();
    editing.value = post;
    form.category_id = post.category_id;
    form.title_ar = post.title_ar;
    form.title_en = post.title_en;
    form.slug = post.slug;
    form.excerpt_ar = post.excerpt_ar || '';
    form.excerpt_en = post.excerpt_en || '';
    form.content_ar = post.content_ar || '';
    form.content_en = post.content_en || '';
    form.featured_image = post.featured_image || '';
    form.seo_title_ar = post.seo_title_ar || '';
    form.seo_title_en = post.seo_title_en || '';
    form.seo_desc_ar = post.seo_desc_ar || '';
    form.seo_desc_en = post.seo_desc_en || '';
    form.status = post.status;
    form.is_featured = !!post.is_featured;
    form.published_at = post.published_at ? post.published_at.slice(0, 16) : '';
    activeLang.value = 'ar';
    showModal.value = true;
}

function save() {
    const opts = {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    };
    if (editing.value) {
        form.put(`/admin/blog/posts/${editing.value.id}`, opts);
    } else {
        form.post('/admin/blog/posts', opts);
    }
}

function destroy(post) {
    if (!confirm(`حذف المقال "${post.title_ar}"?`)) return;
    router.delete(`/admin/blog/posts/${post.id}`, { preserveScroll: true });
}

async function uploadFeatured(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('image', file);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await fetch('/admin/blog/upload-image', {
        method: 'POST',
        body: fd,
        headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        credentials: 'same-origin',
    });
    event.target.value = '';
    if (!res.ok) {
        alert('فشل رفع الصورة');
        return;
    }
    const data = await res.json();
    form.featured_image = data.url;
}

function applyFilters() {
    router.get('/admin/blog/posts', {
        q: filter.value.q || undefined,
        status: filter.value.status || undefined,
        category: filter.value.category || undefined,
    }, { preserveState: true, preserveScroll: true });
}

function fmtDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('ar-EG', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <Head title="المدونة — لوحة التحكم" />

    <AdminLayout page-title="إدارة المدونة">
        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-500">إجمالي المقالات</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 border border-emerald-100">
                <p class="text-xs text-emerald-700">منشور</p>
                <p class="text-2xl font-bold text-emerald-700 mt-1">{{ stats.published }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                <p class="text-xs text-gray-700">مسودات</p>
                <p class="text-2xl font-bold text-gray-700 mt-1">{{ stats.draft }}</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                <p class="text-xs text-amber-700">مجدول</p>
                <p class="text-2xl font-bold text-amber-700 mt-1">{{ stats.scheduled }}</p>
            </div>
            <div class="bg-gradient-to-br from-[#1B4F72] to-[#0A1628] text-white rounded-xl p-4">
                <p class="text-xs text-white/70">إجمالي المشاهدات</p>
                <p class="text-2xl font-bold mt-1">{{ new Intl.NumberFormat('ar-EG').format(stats.total_views) }}</p>
            </div>
        </div>

        <!-- Toolbar -->
        <div class="bg-white rounded-xl p-4 border border-gray-100 mb-4 flex flex-wrap items-center gap-3">
            <input v-model="filter.q" @keydown.enter="applyFilters" placeholder="بحث بالعنوان أو الـ slug" class="flex-1 min-w-[220px] px-4 py-2 border border-gray-200 rounded-lg text-sm" />
            <select v-model="filter.status" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل الحالات</option>
                <option value="draft">مسودة</option>
                <option value="published">منشور</option>
                <option value="scheduled">مجدول</option>
            </select>
            <select v-model="filter.category" @change="applyFilters" class="px-4 py-2 border border-gray-200 rounded-lg text-sm">
                <option value="">كل التصنيفات</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name_ar }}</option>
            </select>
            <button @click="openCreate" class="px-5 py-2 bg-[#1B4F72] hover:bg-[#0A1628] text-white rounded-lg text-sm font-semibold">+ مقال جديد</button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-right">العنوان</th>
                            <th class="px-4 py-3 text-right">التصنيف</th>
                            <th class="px-4 py-3 text-right">الحالة</th>
                            <th class="px-4 py-3 text-right">المشاهدات</th>
                            <th class="px-4 py-3 text-right">تاريخ النشر</th>
                            <th class="px-4 py-3 text-right">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="post in posts.data" :key="post.id" class="border-t border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img v-if="post.featured_image" :src="post.featured_image" class="w-12 h-12 rounded-lg object-cover" alt="" />
                                    <div v-else class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300">📄</div>
                                    <div>
                                        <div class="font-medium text-gray-800">{{ post.title_ar }}</div>
                                        <div class="text-xs text-gray-400 font-mono">/blog/{{ post.slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ post.category?.name_ar || '—' }}</td>
                            <td class="px-4 py-3">
                                <span :class="statusColors[post.status]" class="px-2 py-1 rounded-full text-xs font-medium">{{ statusLabels[post.status] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ post.views_count || 0 }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ fmtDate(post.published_at) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <button @click="openEdit(post)" class="text-xs text-[#1B4F72] hover:underline">تعديل</button>
                                    <a :href="`/blog/${post.slug}`" target="_blank" class="text-xs text-emerald-600 hover:underline">عرض</a>
                                    <button @click="destroy(post)" class="text-xs text-red-600 hover:underline">حذف</button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!posts.data.length">
                            <td colspan="6" class="px-4 py-12 text-center text-gray-400">لا توجد مقالات بعد — أنشئ أول مقال!</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="posts.links && posts.links.length > 3" class="flex justify-center gap-1 p-4 border-t border-gray-100">
                <Link
                    v-for="link in posts.links"
                    :key="link.label"
                    :href="link.url || ''"
                    v-html="link.label"
                    preserve-scroll
                    class="px-3 py-1.5 rounded text-sm"
                    :class="[
                        link.active ? 'bg-[#1B4F72] text-white' : 'bg-gray-50 text-gray-600 hover:bg-gray-100',
                        !link.url ? 'opacity-40 pointer-events-none' : '',
                    ]"
                />
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-start justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto" @click.self="showModal = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl my-8">
                <div class="sticky top-0 z-10 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                    <h3 class="text-lg font-bold text-gray-800">{{ editing ? 'تعديل المقال' : 'مقال جديد' }}</h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
                </div>

                <form @submit.prevent="save" class="p-6 space-y-5">
                    <!-- Language tabs -->
                    <div class="flex gap-2 border-b border-gray-100 pb-3">
                        <button type="button" @click="activeLang = 'ar'" :class="activeLang === 'ar' ? 'bg-[#1B4F72] text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-semibold">عربي</button>
                        <button type="button" @click="activeLang = 'en'" :class="activeLang === 'en' ? 'bg-[#1B4F72] text-white' : 'bg-gray-100 text-gray-600'" class="px-4 py-2 rounded-lg text-sm font-semibold">English</button>
                    </div>

                    <!-- AR fields -->
                    <div v-show="activeLang === 'ar'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">العنوان (عربي)</label>
                            <input v-model="form.title_ar" type="text" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            <p v-if="form.errors.title_ar" class="text-xs text-red-600 mt-1">{{ form.errors.title_ar }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">المقتطف (عربي)</label>
                            <textarea v-model="form.excerpt_ar" rows="2" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">المحتوى (عربي)</label>
                            <RichEditor v-model="form.content_ar" upload-url="/admin/blog/upload-image" dir="rtl" placeholder="اكتب محتوى المقال هنا..." />
                            <p v-if="form.errors.content_ar" class="text-xs text-red-600 mt-1">{{ form.errors.content_ar }}</p>
                        </div>
                    </div>

                    <!-- EN fields -->
                    <div v-show="activeLang === 'en'" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Title (English)</label>
                            <input v-model="form.title_en" type="text" dir="ltr" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            <p v-if="form.errors.title_en" class="text-xs text-red-600 mt-1">{{ form.errors.title_en }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Excerpt (English)</label>
                            <textarea v-model="form.excerpt_en" rows="2" dir="ltr" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Content (English)</label>
                            <RichEditor v-model="form.content_en" upload-url="/admin/blog/upload-image" dir="ltr" placeholder="Write your post here..." />
                            <p v-if="form.errors.content_en" class="text-xs text-red-600 mt-1">{{ form.errors.content_en }}</p>
                        </div>
                    </div>

                    <!-- Settings grid -->
                    <div class="grid md:grid-cols-2 gap-4 border-t border-gray-100 pt-5">
                        <div>
                            <label class="block text-sm font-medium mb-1">التصنيف</label>
                            <select v-model="form.category_id" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm">
                                <option :value="null">— بدون تصنيف —</option>
                                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name_ar }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Slug (URL)</label>
                            <input v-model="form.slug" type="text" dir="ltr" placeholder="auto" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm font-mono" />
                            <p v-if="form.errors.slug" class="text-xs text-red-600 mt-1">{{ form.errors.slug }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">الحالة</label>
                            <select v-model="form.status" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm">
                                <option value="draft">مسودة</option>
                                <option value="published">منشور</option>
                                <option value="scheduled">مجدول</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">تاريخ النشر</label>
                            <input v-model="form.published_at" type="datetime-local" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                        </div>
                    </div>

                    <!-- Featured image -->
                    <div class="border-t border-gray-100 pt-5">
                        <label class="block text-sm font-medium mb-2">الصورة الرئيسية</label>
                        <div class="flex items-center gap-4">
                            <div class="w-32 h-20 rounded-lg border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center overflow-hidden">
                                <img v-if="featuredPreview" :src="featuredPreview" class="w-full h-full object-cover" alt="" />
                                <span v-else class="text-gray-300 text-2xl">🖼</span>
                            </div>
                            <div class="flex-1">
                                <input type="file" accept="image/*" @change="uploadFeatured" class="text-sm" />
                                <p class="text-xs text-gray-400 mt-1">JPEG/PNG/WEBP — أقصى 5MB</p>
                                <button v-if="form.featured_image" type="button" @click="form.featured_image = ''" class="text-xs text-red-600 hover:underline mt-1">إزالة الصورة</button>
                            </div>
                        </div>
                    </div>

                    <!-- SEO panel -->
                    <details class="border-t border-gray-100 pt-5">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700">⚙️ إعدادات SEO</summary>
                        <div class="grid md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-xs font-medium mb-1">SEO Title (عربي)</label>
                                <input v-model="form.seo_title_ar" type="text" maxlength="160" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">SEO Title (English)</label>
                                <input v-model="form.seo_title_en" type="text" dir="ltr" maxlength="160" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">SEO Description (عربي)</label>
                                <textarea v-model="form.seo_desc_ar" rows="2" maxlength="300" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium mb-1">SEO Description (English)</label>
                                <textarea v-model="form.seo_desc_en" rows="2" dir="ltr" maxlength="300" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm"></textarea>
                            </div>
                        </div>
                    </details>

                    <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input v-model="form.is_featured" type="checkbox" class="rounded text-[#1B4F72]" />
                            مقال مميز
                        </label>
                    </div>

                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                        <button type="button" @click="showModal = false" class="px-5 py-2 border border-gray-200 rounded-lg text-sm font-semibold hover:bg-gray-50">إلغاء</button>
                        <button type="submit" :disabled="form.processing" class="px-6 py-2 bg-[#1B4F72] hover:bg-[#0A1628] text-white rounded-lg text-sm font-semibold disabled:opacity-60">
                            {{ form.processing ? 'جاري الحفظ...' : (editing ? 'حفظ التعديلات' : 'إنشاء المقال') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
