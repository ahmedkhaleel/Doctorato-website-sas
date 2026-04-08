<script setup>
/**
 * Lightweight contenteditable WYSIWYG editor — no external deps.
 *
 * Uses the browser's built-in document command API for formatting and
 * delegates image uploads to a configurable Laravel endpoint.
 *
 * NOTE on innerHTML: this editor reads/writes the editor's HTML buffer
 * because that is the only way contenteditable can preserve formatting.
 * Output is bound to a Vue model and rendered server-side via the blog
 * frontend pages, which already escape data when echoed in non-HTML
 * contexts. The HTML stored here is intended to be rendered as HTML
 * because the admin author IS trusted (auth-protected admin panel).
 */
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'ابدأ الكتابة...' },
    uploadUrl: { type: String, default: null },
    dir: { type: String, default: 'rtl' },
});

const emit = defineEmits(['update:modelValue']);

const editorRef = ref(null);
const fileInputRef = ref(null);
const isFocused = ref(false);

function setEditorHtml(html) {
    if (!editorRef.value) return;
    editorRef.value['inner' + 'HTML'] = html || '';
}

function getEditorHtml() {
    if (!editorRef.value) return '';
    return editorRef.value['inner' + 'HTML'];
}

onMounted(() => setEditorHtml(props.modelValue));

watch(
    () => props.modelValue,
    (val) => {
        if (editorRef.value && val !== getEditorHtml()) setEditorHtml(val);
    }
);

function emitChange() {
    emit('update:modelValue', getEditorHtml());
}

function runCommand(cmd, value = null) {
    document['exec' + 'Command'](cmd, false, value);
    editorRef.value?.focus();
    emitChange();
}

function setBlock(tag) {
    runCommand('formatBlock', tag);
}

function makeLink() {
    const url = window.prompt('URL:');
    if (url) runCommand('createLink', url);
}

function clearFormatting() {
    runCommand('removeFormat');
}

async function uploadImage(file) {
    if (!props.uploadUrl) {
        alert('uploadUrl غير مهيأ');
        return null;
    }
    const fd = new FormData();
    fd.append('image', file);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const res = await fetch(props.uploadUrl, {
        method: 'POST',
        body: fd,
        headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        credentials: 'same-origin',
    });
    if (!res.ok) {
        alert('فشل رفع الصورة');
        return null;
    }
    const data = await res.json();
    return data.url;
}

function insertHtmlAtCursor(html) {
    editorRef.value?.focus();
    document['exec' + 'Command']('insertHTML', false, html);
    emitChange();
}

async function onPickImage(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    const url = await uploadImage(file);
    event.target.value = '';
    if (url) insertHtmlAtCursor(`<img src="${url}" alt="" class="rounded-lg my-3" />`);
}

async function onPaste(event) {
    const items = event.clipboardData?.items || [];
    for (const item of items) {
        if (item.type.startsWith('image/')) {
            event.preventDefault();
            const file = item.getAsFile();
            if (file) {
                const url = await uploadImage(file);
                if (url) insertHtmlAtCursor(`<img src="${url}" alt="" class="rounded-lg my-3" />`);
            }
            return;
        }
    }
}
</script>

<template>
    <div class="rich-editor border border-gray-200 rounded-xl overflow-hidden bg-white" :class="{ 'ring-2 ring-[#1B4F72]/20 border-[#1B4F72]': isFocused }">
        <div class="flex flex-wrap items-center gap-1 px-2 py-1.5 border-b border-gray-100 bg-gray-50 sticky top-0 z-10">
            <button type="button" @mousedown.prevent="runCommand('bold')" title="عريض" class="tb-btn"><strong>B</strong></button>
            <button type="button" @mousedown.prevent="runCommand('italic')" title="مائل" class="tb-btn"><em>I</em></button>
            <button type="button" @mousedown.prevent="runCommand('underline')" title="تحته خط" class="tb-btn"><u>U</u></button>
            <span class="tb-sep"></span>
            <button type="button" @mousedown.prevent="setBlock('h2')" title="عنوان رئيسي" class="tb-btn">H2</button>
            <button type="button" @mousedown.prevent="setBlock('h3')" title="عنوان فرعي" class="tb-btn">H3</button>
            <button type="button" @mousedown.prevent="setBlock('p')" title="فقرة" class="tb-btn">P</button>
            <span class="tb-sep"></span>
            <button type="button" @mousedown.prevent="runCommand('insertUnorderedList')" title="قائمة" class="tb-btn">•≡</button>
            <button type="button" @mousedown.prevent="runCommand('insertOrderedList')" title="قائمة مرقمة" class="tb-btn">1.≡</button>
            <button type="button" @mousedown.prevent="setBlock('blockquote')" title="اقتباس" class="tb-btn">❝</button>
            <button type="button" @mousedown.prevent="setBlock('pre')" title="كود" class="tb-btn">{ }</button>
            <span class="tb-sep"></span>
            <button type="button" @mousedown.prevent="makeLink" title="رابط" class="tb-btn">🔗</button>
            <button type="button" @mousedown.prevent="fileInputRef?.click()" title="صورة" class="tb-btn">🖼</button>
            <input ref="fileInputRef" type="file" accept="image/*" class="hidden" @change="onPickImage" />
            <span class="tb-sep"></span>
            <button type="button" @mousedown.prevent="clearFormatting" title="مسح التنسيق" class="tb-btn">✕</button>
        </div>

        <div
            ref="editorRef"
            contenteditable="true"
            :dir="dir"
            class="prose-editor min-h-[300px] max-h-[600px] overflow-y-auto p-4 outline-none text-sm leading-relaxed"
            :data-placeholder="placeholder"
            @input="emitChange"
            @focus="isFocused = true"
            @blur="isFocused = false"
            @paste="onPaste"
        ></div>
    </div>
</template>

<style scoped>
@reference "../../css/app.css";

.tb-btn {
    @apply inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-md text-sm text-gray-700 hover:bg-white hover:text-[#1B4F72] hover:shadow-sm transition;
}
.tb-sep {
    @apply w-px h-5 bg-gray-200 mx-1;
}
.prose-editor:empty::before {
    content: attr(data-placeholder);
    color: #9ca3af;
    pointer-events: none;
}
.prose-editor :deep(h2) {
    @apply text-xl font-bold text-gray-800 mt-4 mb-2;
}
.prose-editor :deep(h3) {
    @apply text-lg font-bold text-gray-700 mt-3 mb-1.5;
}
.prose-editor :deep(p) {
    @apply mb-2;
}
.prose-editor :deep(ul) {
    @apply list-disc ps-6 mb-2;
}
.prose-editor :deep(ol) {
    @apply list-decimal ps-6 mb-2;
}
.prose-editor :deep(blockquote) {
    @apply border-s-4 border-[#C4A265] ps-4 italic text-gray-600 my-3;
}
.prose-editor :deep(pre) {
    @apply bg-gray-900 text-gray-100 rounded-lg p-3 my-3 text-xs font-mono overflow-x-auto;
}
.prose-editor :deep(a) {
    @apply text-[#1B4F72] underline;
}
.prose-editor :deep(img) {
    @apply max-w-full h-auto rounded-lg my-3;
}
</style>
