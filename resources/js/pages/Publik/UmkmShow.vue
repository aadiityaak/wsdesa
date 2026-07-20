<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, MapPin, Phone, Store } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';

interface UmkmImage {
    id: number;
    file: string;
    judul?: string;
}

interface UmkmCategory {
    id: number;
    nama: string;
    slug: string;
}

interface Umkm {
    id: number;
    slug: string;
    nama_usaha: string;
    pemilik: string;
    category: UmkmCategory | null;
    alamat?: string;
    telepon?: string;
    deskripsi: string;
    thumbnail: string | null;
    images: UmkmImage[];
}

defineProps<{
    umkm: Umkm;
}>();

const imageUrl = (path: string) => `/storage/${path}`;
</script>

<template>
    <Head :title="umkm.nama_usaha" />

    <PageHero :title="umkm.nama_usaha" :description="umkm.category?.nama || 'UMKM Desa'">
        <template #icon>
            <Store class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
        <Breadcrumb :items="[
            { label: 'UMKM', href: '/umkm' },
            { label: umkm.nama_usaha },
        ]" />
    </div>

    <div class="mx-auto max-w-4xl px-4 pb-8 sm:px-6 lg:px-8">

        <div class="mb-6 flex flex-wrap gap-3">
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700 dark:bg-blue-950 dark:text-blue-300">
                {{ umkm.category?.nama || 'Umum' }}
            </span>
            <span class="inline-flex items-center gap-1 text-sm text-zinc-500">
                <Store class="size-4" />
                {{ umkm.pemilik }}
            </span>
            <span v-if="umkm.alamat" class="inline-flex items-center gap-1 text-sm text-zinc-500">
                <MapPin class="size-4" />
                {{ umkm.alamat }}
            </span>
            <span v-if="umkm.telepon" class="inline-flex items-center gap-1 text-sm text-zinc-500">
                <Phone class="size-4" />
                {{ umkm.telepon }}
            </span>
        </div>

        <div class="mb-8 text-zinc-600 dark:text-zinc-300 leading-relaxed whitespace-pre-line" v-html="umkm.deskripsi" />

        <!-- Image Gallery -->
        <div v-if="umkm.images?.length">
            <h2 class="mb-4 text-xl font-semibold text-zinc-900 dark:text-white">Galeri Foto</h2>
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <div
                    v-for="image in umkm.images"
                    :key="image.id"
                    class="group relative aspect-square overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800"
                >
                    <img
                        :src="imageUrl(image.file)"
                        :alt="image.judul || umkm.nama_usaha"
                        class="h-full w-full object-cover transition-transform group-hover:scale-105"
                    />
                    <div v-if="image.judul" class="absolute inset-x-0 bottom-0 bg-black/50 p-2 opacity-0 transition-opacity group-hover:opacity-100">
                        <p class="text-xs text-white">{{ image.judul }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
