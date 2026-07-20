<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Palette, Save, Check, Type } from '@lucide/vue';

interface Profile {
    id: number;
    header_style: string;
    footer_style: string;
    font_style: string;
    heading_font: string;
}

const props = defineProps<{ profile: Profile }>();

const form = useForm({
    header_style: props.profile.header_style ?? 'default',
    footer_style: props.profile.footer_style ?? 'default',
    font_style: props.profile.font_style ?? 'inter',
    heading_font: props.profile.heading_font ?? 'inter',
});

const headerStyles = [
    {
        key: 'default',
        label: 'Standar',
        description: 'Header putih dengan border bawah, navigasi sederhana',
        preview: 'bg-white dark:bg-zinc-950',
    },
    {
        key: 'modern',
        label: 'Modern',
        description: 'Gradient background dengan efek glass, tampilan kontemporer',
        preview: 'bg-gradient-to-r from-blue-600 to-indigo-600',
    },
    {
        key: 'classic',
        label: 'Klasik',
        description: 'Background gelap elegan, cocok untuk desa tradisional',
        preview: 'bg-zinc-800 dark:bg-zinc-900',
    },
    {
        key: 'minimal',
        label: 'Minimal',
        description: 'Header transparan ringan, fokus pada konten',
        preview: 'bg-zinc-50 dark:bg-zinc-900',
    },
];

const footerStyles = [
    {
        key: 'default',
        label: 'Standar',
        description: 'Footer 3 kolom dengan background abu-abu terang',
        preview: 'bg-zinc-50 dark:bg-zinc-950',
    },
    {
        key: 'modern',
        label: 'Modern',
        description: 'Footer gelap penuh dengan aksen warna, lebih banyak link',
        preview: 'bg-zinc-900',
    },
    {
        key: 'classic',
        label: 'Klasik',
        description: 'Footer dengan border atas tebal, layout tradisional',
        preview: 'bg-zinc-100 dark:bg-zinc-900',
    },
    {
        key: 'minimal',
        label: 'Minimal',
        description: 'Hanya copyright bar, sangat sederhana',
        preview: 'bg-white dark:bg-zinc-950',
    },
];

const fontStyles = [
    {
        key: 'inter',
        label: 'Inter',
        description: 'Font sans-serif modern, bersih dan mudah dibaca',
        previewClass: 'font-sans',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
    {
        key: 'serif',
        label: 'Serif',
        description: 'Font dengan kait, memberikan kesan klasik dan formal',
        previewClass: 'font-serif',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
    {
        key: 'mono',
        label: 'Monospace',
        description: 'Font dengan lebar karakter seragam, tampilan teknis',
        previewClass: 'font-mono',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
    {
        key: 'rounded',
        label: 'Rounded',
        description: 'Font membulat, ramah dan modern untuk desa digital',
        previewClass: 'font-rounded',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
    {
        key: 'poppins',
        label: 'Poppins',
        description: 'Font geometris modern, bersih dan profesional',
        previewClass: 'font-poppins',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
    {
        key: 'playfair',
        label: 'Playfair Display',
        description: 'Font serif elegan, cocok untuk judul dan berita',
        previewClass: 'font-playfair',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
    {
        key: 'bricolage',
        label: 'Bricolage Grotesque',
        description: 'Font sans-serif unik, berani dan ekspresif',
        previewClass: 'font-bricolage',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
    {
        key: 'jakarta',
        label: 'Plus Jakarta Sans',
        description: 'Font modern dan netral, cocok untuk body teks',
        previewClass: 'font-jakarta',
        previewText: 'ABCDEFGHIJKL abcdefghijkl 12345',
    },
];

const submit = () => {
    form.post('/admin/tampilan', {
        _method: 'put',
        forceFormData: true,
        onSuccess: () => toast.success('Pengaturan tampilan berhasil disimpan.'),
    });
};
</script>

<template>
    <Head title="Tampilan" />

    <div class="relative">
        <!-- Hero -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-purple-100/40 blur-3xl dark:bg-purple-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-rose-100/30 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />

            <div class="relative">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                        <Palette class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Tampilan</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Atur desain header, footer, dan typography website desa</p>
                    </div>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit">
            <!-- Header Style -->
            <div class="mb-8 rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <h2 class="mb-1 text-lg font-semibold text-zinc-800 dark:text-zinc-200">Header Style</h2>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">Pilih desain header untuk tampilan atas website</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <button
                        v-for="style in headerStyles"
                        :key="style.key"
                        type="button"
                        class="relative flex flex-col overflow-hidden rounded-xl border-2 transition-all"
                        :class="form.header_style === style.key
                            ? 'border-purple-500 ring-2 ring-purple-200 dark:border-purple-400 dark:ring-purple-800'
                            : 'border-zinc-200 hover:border-zinc-300 dark:border-zinc-700 dark:hover:border-zinc-600'"
                        @click="form.header_style = style.key"
                    >
                        <!-- Preview bar -->
                        <div class="flex h-14 items-center gap-2 px-3" :class="style.preview">
                            <div class="h-3 w-3 rounded-sm bg-zinc-400/50" />
                            <div class="h-2 w-12 rounded bg-zinc-400/50" />
                            <div class="ml-auto flex gap-1.5">
                                <div class="h-1.5 w-6 rounded bg-zinc-400/50" />
                                <div class="h-1.5 w-6 rounded bg-zinc-400/50" />
                                <div class="h-1.5 w-6 rounded bg-zinc-400/50" />
                            </div>
                        </div>
                        <!-- Content preview -->
                        <div class="flex-1 bg-white p-2 dark:bg-zinc-950">
                            <div class="h-1 w-3/4 rounded bg-zinc-200 dark:bg-zinc-800" />
                            <div class="mt-1 h-1 w-1/2 rounded bg-zinc-100 dark:bg-zinc-800" />
                        </div>
                        <!-- Check indicator -->
                        <div
                            v-if="form.header_style === style.key"
                            class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-purple-500 text-white"
                        >
                            <Check class="h-3.5 w-3.5" />
                        </div>
                        <!-- Label -->
                        <div class="border-t border-zinc-100 px-3 py-2.5 dark:border-zinc-800">
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ style.label }}</div>
                            <div class="mt-0.5 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ style.description }}</div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Footer Style -->
            <div class="mb-8 rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <h2 class="mb-1 text-lg font-semibold text-zinc-800 dark:text-zinc-200">Footer Style</h2>
                <p class="mb-6 text-sm text-zinc-500 dark:text-zinc-400">Pilih desain footer untuk tampilan bawah website</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <button
                        v-for="style in footerStyles"
                        :key="style.key"
                        type="button"
                        class="relative flex flex-col overflow-hidden rounded-xl border-2 transition-all"
                        :class="form.footer_style === style.key
                            ? 'border-purple-500 ring-2 ring-purple-200 dark:border-purple-400 dark:ring-purple-800'
                            : 'border-zinc-200 hover:border-zinc-300 dark:border-zinc-700 dark:hover:border-zinc-600'"
                        @click="form.footer_style = style.key"
                    >
                        <!-- Preview -->
                        <div class="flex flex-col gap-2 px-3 py-4" :class="style.preview">
                            <div class="flex gap-3">
                                <div class="flex-1 space-y-1.5">
                                    <div class="h-2 w-16 rounded bg-zinc-400/50" />
                                    <div class="h-1.5 w-full rounded bg-zinc-400/30" />
                                    <div class="h-1.5 w-3/4 rounded bg-zinc-400/30" />
                                </div>
                                <div class="flex-1 space-y-1.5">
                                    <div class="h-2 w-16 rounded bg-zinc-400/50" />
                                    <div class="h-1.5 w-full rounded bg-zinc-400/30" />
                                    <div class="h-1.5 w-3/4 rounded bg-zinc-400/30" />
                                </div>
                            </div>
                            <div class="mt-1 h-px bg-zinc-300/50 dark:bg-zinc-700/50" />
                            <div class="h-1.5 w-1/2 rounded bg-zinc-400/30" />
                        </div>
                        <!-- Check indicator -->
                        <div
                            v-if="form.footer_style === style.key"
                            class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-purple-500 text-white"
                        >
                            <Check class="h-3.5 w-3.5" />
                        </div>
                        <!-- Label -->
                        <div class="border-t border-zinc-100 px-3 py-2.5 dark:border-zinc-800">
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ style.label }}</div>
                            <div class="mt-0.5 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ style.description }}</div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Heading Font -->
            <div class="mb-8 rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <Type class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">Heading Font</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Pilih font untuk judul dan heading (h1-h6)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <button
                        v-for="style in fontStyles"
                        :key="style.key"
                        type="button"
                        class="relative flex flex-col overflow-hidden rounded-xl border-2 transition-all"
                        :class="form.heading_font === style.key
                            ? 'border-amber-500 ring-2 ring-amber-200 dark:border-amber-400 dark:ring-amber-800'
                            : 'border-zinc-200 hover:border-zinc-300 dark:border-zinc-700 dark:hover:border-zinc-600'"
                        @click="form.heading_font = style.key"
                    >
                        <div class="flex flex-col gap-1.5 px-4 py-6" :class="style.previewClass">
                            <div class="text-xl font-extrabold leading-tight text-zinc-800 dark:text-zinc-200">H1 {{ style.label }}</div>
                            <div class="text-sm font-semibold text-zinc-600 dark:text-zinc-300">H2 Sub-heading</div>
                            <div class="text-xs font-medium text-zinc-400 dark:text-zinc-500">H3 SECTION TITLE</div>
                        </div>
                        <div
                            v-if="form.heading_font === style.key"
                            class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 text-white"
                        >
                            <Check class="h-3.5 w-3.5" />
                        </div>
                        <div class="border-t border-zinc-100 px-3 py-2.5 dark:border-zinc-800">
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ style.label }}</div>
                            <div class="mt-0.5 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ style.description }}</div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Content Font -->
            <div class="mb-8 rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                        <Type class="h-5 w-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">Content Font</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Pilih font untuk isi, paragraf, dan body teks</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <button
                        v-for="style in fontStyles"
                        :key="style.key"
                        type="button"
                        class="relative flex flex-col overflow-hidden rounded-xl border-2 transition-all"
                        :class="form.font_style === style.key
                            ? 'border-blue-500 ring-2 ring-blue-200 dark:border-blue-400 dark:ring-blue-800'
                            : 'border-zinc-200 hover:border-zinc-300 dark:border-zinc-700 dark:hover:border-zinc-600'"
                        @click="form.font_style = style.key"
                    >
                        <div class="flex flex-col gap-1.5 px-4 py-6" :class="style.previewClass">
                            <div class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore.
                            </div>
                            <div class="flex gap-1">
                                <div class="h-2 w-6 rounded-full bg-zinc-300/60 dark:bg-zinc-600/50" />
                                <div class="h-2 w-10 rounded-full bg-zinc-300/60 dark:bg-zinc-600/50" />
                                <div class="h-2 w-8 rounded-full bg-zinc-300/60 dark:bg-zinc-600/50" />
                            </div>
                        </div>
                        <div
                            v-if="form.font_style === style.key"
                            class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white"
                        >
                            <Check class="h-3.5 w-3.5" />
                        </div>
                        <div class="border-t border-zinc-100 px-3 py-2.5 dark:border-zinc-800">
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ style.label }}</div>
                            <div class="mt-0.5 text-xs leading-relaxed text-zinc-500 dark:text-zinc-400">{{ style.description }}</div>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing" class="gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Save class="h-4 w-4" /> Simpan Pengaturan
                </Button>
            </div>
        </form>
    </div>
</template>
