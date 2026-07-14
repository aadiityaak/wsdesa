<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Store } from '@lucide/vue';

interface Umkm {
    id: number;
    slug: string;
    nama_usaha: string;
    pemilik: string;
    kategori: string;
    deskripsi: string;
    thumbnail: string | null;
}

defineProps<{
    umkms: {
        data: Umkm[];
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
    <Head title="UMKM" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-zinc-900 dark:text-white">UMKM Desa</h1>

        <div v-if="umkms.data.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <Store class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada data UMKM</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data UMKM akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Link v-for="umkm in umkms.data" :key="umkm.id" :href="`/umkm/${umkm.slug}`">
                <Card class="overflow-hidden transition-shadow hover:shadow-md">
                    <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                        <img
                            v-if="umkm.thumbnail"
                            :src="imageUrl(umkm.thumbnail)"
                            :alt="umkm.nama_usaha"
                            class="h-full w-full object-cover"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center text-zinc-400">
                            <Store class="size-12" />
                        </div>
                    </div>
                    <CardContent class="space-y-2 p-4">
                        <h3 class="font-semibold text-zinc-900 dark:text-white">{{ umkm.nama_usaha }}</h3>
                        <p class="text-sm text-zinc-500">Oleh: {{ umkm.pemilik }}</p>
                        <p class="text-xs font-medium text-blue-600 dark:text-blue-400">{{ umkm.kategori }}</p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">{{ umkm.deskripsi }}</p>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="umkms.last_page > 1" class="mt-8 flex justify-center">
            <div class="flex items-center gap-1">
                <a
                    v-for="link in umkms.links"
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
