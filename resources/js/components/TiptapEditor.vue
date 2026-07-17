 <script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import { Bold, Italic, Heading2, Heading3, List, ListOrdered, Link as LinkIcon, Undo2, Redo2 } from '@lucide/vue';
import { computed, watch } from 'vue';

const props = defineProps<{
    modelValue: string;
    placeholder?: string;
    minHeight?: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
}>();

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit.configure({
            heading: { levels: [2, 3] },
        }),
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-rose-500 underline underline-offset-2 hover:text-rose-600',
            },
        }),
    ],
    onUpdate: ({ editor }) => {
        const html = editor.getHTML();
        if (html !== props.modelValue) {
            emit('update:modelValue', html);
        }
    },
});

watch(
    () => props.modelValue,
    (newVal) => {
        if (editor.value && newVal !== editor.value.getHTML()) {
            editor.value.commands.setContent(newVal, false);
        }
    },
);

const setLink = () => {
    if (!editor.value) return;
    const previousUrl = editor.value.getAttributes('link').href;
    const url = window.prompt('Masukkan URL:', previousUrl ?? 'https://');
    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
    } else {
        editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
    }
};

const isActive = computed(() => ({
    bold: editor.value?.isActive('bold') ?? false,
    italic: editor.value?.isActive('italic') ?? false,
    heading2: editor.value?.isActive('heading', { level: 2 }) ?? false,
    heading3: editor.value?.isActive('heading', { level: 3 }) ?? false,
    bulletList: editor.value?.isActive('bulletList') ?? false,
    orderedList: editor.value?.isActive('orderedList') ?? false,
    link: editor.value?.isActive('link') ?? false,
}));

const btnClass = 'rounded-lg p-1.5 transition-colors';

const btnActive = 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300';
const btnInactive = 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200';
</script>

<template>
    <div
        class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xs transition-all duration-200 focus-within:border-rose-300 focus-within:ring-[3px] focus-within:ring-rose-200 dark:border-zinc-700 dark:bg-zinc-900 dark:focus-within:border-rose-400 dark:focus-within:ring-rose-900"
    >
        <!-- Toolbar -->
        <div
            class="flex flex-wrap items-center gap-0.5 border-b border-zinc-200 bg-zinc-50 px-2 py-1.5 dark:border-zinc-700 dark:bg-zinc-800/50"
        >
            <button type="button" title="Bold" @click="editor?.chain().focus().toggleBold().run()" :class="[btnClass, isActive.bold ? btnActive : btnInactive]">
                <Bold class="size-4" />
            </button>
            <button type="button" title="Italic" @click="editor?.chain().focus().toggleItalic().run()" :class="[btnClass, isActive.italic ? btnActive : btnInactive]">
                <Italic class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-zinc-200 dark:bg-zinc-700" />

            <button type="button" title="Heading 2" @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()" :class="[btnClass, isActive.heading2 ? btnActive : btnInactive]">
                <Heading2 class="size-4" />
            </button>
            <button type="button" title="Heading 3" @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()" :class="[btnClass, isActive.heading3 ? btnActive : btnInactive]">
                <Heading3 class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-zinc-200 dark:bg-zinc-700" />

            <button type="button" title="Bullet List" @click="editor?.chain().focus().toggleBulletList().run()" :class="[btnClass, isActive.bulletList ? btnActive : btnInactive]">
                <List class="size-4" />
            </button>
            <button type="button" title="Ordered List" @click="editor?.chain().focus().toggleOrderedList().run()" :class="[btnClass, isActive.orderedList ? btnActive : btnInactive]">
                <ListOrdered class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-zinc-200 dark:bg-zinc-700" />

            <button type="button" title="Link" @click="setLink" :class="[btnClass, isActive.link ? btnActive : btnInactive]">
                <LinkIcon class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-zinc-200 dark:bg-zinc-700" />

            <button type="button" title="Undo" @click="editor?.chain().focus().undo().run()" :class="btnInactive">
                <Undo2 class="size-4" />
            </button>
            <button type="button" title="Redo" @click="editor?.chain().focus().redo().run()" :class="btnInactive">
                <Redo2 class="size-4" />
            </button>
        </div>

        <!-- Editor Content -->
        <div
            class="prose prose-sm max-w-none cursor-text px-3.5 py-3 dark:prose-invert"
            :style="{ minHeight: minHeight ?? '120px' }"
            @click="editor?.chain().focus().run()"
        >
            <EditorContent :editor="editor" />
        </div>
    </div>
</template>

<style scoped>
:deep(.tiptap:focus),
:deep(.ProseMirror:focus),
:deep(.ProseMirror-focused) {
    outline: none !important;
}

.tiptap p {
    margin: 0.25em 0;
}

.tiptap h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0.75em 0 0.25em;
}

.tiptap h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0.5em 0 0.25em;
}

.tiptap ul,
.tiptap ol {
    padding-left: 1.25rem;
    margin: 0.25em 0;
}

.tiptap li {
    margin: 0.125em 0;
}

.tiptap a {
    color: #f43f5e;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.tiptap a:hover {
    color: #e11d48;
}

.tiptap p.is-editor-empty:first-child::before {
    color: #a1a1aa;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}
</style>
