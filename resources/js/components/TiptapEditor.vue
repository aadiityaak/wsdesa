<script setup lang="ts">
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import { Bold, Italic, Heading2, Heading3, List, ListOrdered, Link as LinkIcon, Undo2, Redo2 } from '@lucide/vue';
import { computed, watch, ref } from 'vue';

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
            HTMLAttributes: { class: 'text-[#d4a056] underline underline-offset-2 hover:text-[#c08f40]' },
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
</script>

<template>
    <div
        class="overflow-hidden rounded-lg border border-[#e3ddd2] bg-white shadow-sm transition-all duration-200 focus-within:border-[#d4a056] focus-within:ring-[#d4a056]/30 focus-within:ring-[3px]"
    >
        <!-- Toolbar -->
        <div
            class="flex flex-wrap items-center gap-0.5 border-b border-[#e3ddd2] bg-[#faf8f5] px-2 py-1.5"
        >
            <button
                type="button"
                title="Bold"
                @click="editor?.chain().focus().toggleBold().run()"
                :class="[
                    'rounded-md p-1.5 transition-colors',
                    isActive.bold ? 'bg-[#d4a056]/15 text-[#1a1410]' : 'text-[#5a5142] hover:bg-[#e3ddd2] hover:text-[#1a1410]',
                ]"
            >
                <Bold class="size-4" />
            </button>
            <button
                type="button"
                title="Italic"
                @click="editor?.chain().focus().toggleItalic().run()"
                :class="[
                    'rounded-md p-1.5 transition-colors',
                    isActive.italic ? 'bg-[#d4a056]/15 text-[#1a1410]' : 'text-[#5a5142] hover:bg-[#e3ddd2] hover:text-[#1a1410]',
                ]"
            >
                <Italic class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-[#e3ddd2]" />

            <button
                type="button"
                title="Heading 2"
                @click="editor?.chain().focus().toggleHeading({ level: 2 }).run()"
                :class="[
                    'rounded-md p-1.5 transition-colors',
                    isActive.heading2 ? 'bg-[#d4a056]/15 text-[#1a1410]' : 'text-[#5a5142] hover:bg-[#e3ddd2] hover:text-[#1a1410]',
                ]"
            >
                <Heading2 class="size-4" />
            </button>
            <button
                type="button"
                title="Heading 3"
                @click="editor?.chain().focus().toggleHeading({ level: 3 }).run()"
                :class="[
                    'rounded-md p-1.5 transition-colors',
                    isActive.heading3 ? 'bg-[#d4a056]/15 text-[#1a1410]' : 'text-[#5a5142] hover:bg-[#e3ddd2] hover:text-[#1a1410]',
                ]"
            >
                <Heading3 class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-[#e3ddd2]" />

            <button
                type="button"
                title="Bullet List"
                @click="editor?.chain().focus().toggleBulletList().run()"
                :class="[
                    'rounded-md p-1.5 transition-colors',
                    isActive.bulletList ? 'bg-[#d4a056]/15 text-[#1a1410]' : 'text-[#5a5142] hover:bg-[#e3ddd2] hover:text-[#1a1410]',
                ]"
            >
                <List class="size-4" />
            </button>
            <button
                type="button"
                title="Ordered List"
                @click="editor?.chain().focus().toggleOrderedList().run()"
                :class="[
                    'rounded-md p-1.5 transition-colors',
                    isActive.orderedList ? 'bg-[#d4a056]/15 text-[#1a1410]' : 'text-[#5a5142] hover:bg-[#e3ddd2] hover:text-[#1a1410]',
                ]"
            >
                <ListOrdered class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-[#e3ddd2]" />

            <button
                type="button"
                title="Link"
                @click="setLink"
                :class="[
                    'rounded-md p-1.5 transition-colors',
                    isActive.link ? 'bg-[#d4a056]/15 text-[#1a1410]' : 'text-[#5a5142] hover:bg-[#e3ddd2] hover:text-[#1a1410]',
                ]"
            >
                <LinkIcon class="size-4" />
            </button>

            <span class="mx-1 h-5 w-px bg-[#e3ddd2]" />

            <button
                type="button"
                title="Undo"
                @click="editor?.chain().focus().undo().run()"
                class="rounded-md p-1.5 text-[#5a5142] transition-colors hover:bg-[#e3ddd2] hover:text-[#1a1410]"
            >
                <Undo2 class="size-4" />
            </button>
            <button
                type="button"
                title="Redo"
                @click="editor?.chain().focus().redo().run()"
                class="rounded-md p-1.5 text-[#5a5142] transition-colors hover:bg-[#e3ddd2] hover:text-[#1a1410]"
            >
                <Redo2 class="size-4" />
            </button>
        </div>

        <!-- Editor Content -->
        <div
            class="prose prose-sm max-w-none cursor-text px-3.5 py-3"
            :style="{ minHeight: minHeight ?? '120px' }"
            @click="editor?.chain().focus().run()"
        >
            <EditorContent :editor="editor" />
        </div>
    </div>
</template>

<style scoped>
.tiptap:focus {
    outline: none;
}

.tiptap p {
    margin: 0.25em 0;
}

.tiptap h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin: 0.75em 0 0.25em;
    color: #1a1410;
}

.tiptap h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0.5em 0 0.25em;
    color: #1a1410;
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
    color: #d4a056;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.tiptap a:hover {
    color: #c08f40;
}

.tiptap p.is-editor-empty:first-child::before {
    color: #a89c8b;
    content: attr(data-placeholder);
    float: left;
    height: 0;
    pointer-events: none;
}
</style>
