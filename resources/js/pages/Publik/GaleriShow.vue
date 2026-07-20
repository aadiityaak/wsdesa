<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ImageIcon } from '@lucide/vue';

interface GalleryImage {
    id: number;
    file: string;
    judul?: string;
}

interface Gallery {
    id: number;
    nama: string;
    slug: string;
    deskripsi?: string;
    images: GalleryImage[];
}

defineProps<{
    gallery: Gallery;
}>();

const imageUrl = (path: string) => `/storage/${path}`;
</script>

<template>
    <Head :title="gallery.nama" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <Link href="/galeri" class="mb-6 inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
            <ArrowLeft class="size-4" />
            Kembali ke Galeri
        </Link>

        <h1 class="mb-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ gallery.nama }}</h1>
        <p v-if="gallery.deskripsi" class="mb-8 text-zinc-500 dark:text-zinc-400" v-html="gallery.deskripsi" />

        <div v-if="gallery.images.length === 0" class="flex min-h-[30vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <ImageIcon class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada foto</h3>
            </div>
        </div>

        <div v-else class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            <div
                v-for="image in gallery.images"
                :key="image.id"
                class="group relative aspect-square overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800"
            >
                <img
                    :src="imageUrl(image.file)"
                    :alt="image.judul || gallery.nama"
                    class="h-full w-full object-cover transition-transform group-hover:scale-105"
                />
                <div v-if="image.judul" class="absolute inset-x-0 bottom-0 bg-black/50 p-2 opacity-0 transition-opacity group-hover:opacity-100">
                    <p class="text-xs text-white">{{ image.judul }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
