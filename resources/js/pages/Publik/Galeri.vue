<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { ImageIcon } from '@lucide/vue';

interface Gallery {
    id: number;
    nama: string;
    slug: string;
    thumbnail: string | null;
    images_count?: number;
}

defineProps<{
    galleries: {
        data: Gallery[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        links?: { url: string | null; label: string; active: boolean }[];
    };
}>();

const imageUrl = (path: string | null) => (path ? `/storage/${path}` : null);
</script>

<template>
    <Head title="Galeri" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-zinc-900 dark:text-white">Galeri</h1>

        <div v-if="galleries.data.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <ImageIcon class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada galeri</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Galeri akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Link v-for="gallery in galleries.data" :key="gallery.id" :href="`/galeri/${gallery.slug}`">
                <Card class="overflow-hidden transition-shadow hover:shadow-md">
                    <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                        <img
                            v-if="gallery.thumbnail"
                            :src="imageUrl(gallery.thumbnail)"
                            :alt="gallery.nama"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-zinc-400">
                            <ImageIcon class="size-12" />
                        </div>
                    </div>
                    <CardContent class="p-4">
                        <h3 class="font-semibold text-zinc-900 dark:text-white">{{ gallery.nama }}</h3>
                        <p v-if="gallery.images_count != null" class="mt-1 text-xs text-zinc-500">
                            {{ gallery.images_count }} foto
                        </p>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="galleries.last_page > 1" class="mt-8 flex justify-center">
            <div class="flex items-center gap-1">
                <a
                    v-for="link in galleries.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="inline-flex items-center justify-center rounded-md px-3 py-1.5 text-sm"
                    :class="{
                        'bg-blue-600 text-white': link.active,
                        'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800': !link.active && link.url,
                        'cursor-default text-zinc-300': !link.url,
                    }"
                ></a>
            </div>
        </div>
    </div>
</template>
