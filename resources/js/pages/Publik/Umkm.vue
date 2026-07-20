<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import {
    Store, MapPin, Phone, Search, Star,
} from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';

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

const props = defineProps<{
    umkms: {
        data: Umkm[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
    };
}>();

const search = defineModel<string>('search', { default: '' });

const filtered = computed(() => {
    if (!search.value) return props.umkms.data;
    const q = search.value.toLowerCase();
    return props.umkms.data.filter(
        (u) =>
            u.nama_usaha.toLowerCase().includes(q) ||
            u.pemilik.toLowerCase().includes(q) ||
            u.category?.nama.toLowerCase().includes(q) ||
            u.alamat?.toLowerCase().includes(q),
    );
});

const imageUrl = (path: string | null) => path ? `/storage/${path}` : null;
</script>

<template>
    <Head title="UMKM" />

    <PageHero title="UMKM Desa" description="Jelajahi produk dan usaha mikro, kecil, dan menengah di desa kami">
        <template #icon>
            <Store class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Search -->
        <div class="relative mb-8">
            <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
            <Input
                v-model="search"
                placeholder="Cari UMKM..."
                class="h-11 pl-10"
            />
        </div>

        <!-- Data Summary -->
        <p class="mb-6 text-sm text-zinc-500">
            Menampilkan {{ umkms.from }}–{{ umkms.to }} dari {{ umkms.total }} UMKM
        </p>

        <!-- Empty -->
        <div v-if="filtered.length === 0" class="flex min-h-[30vh] items-center justify-center rounded-xl border-2 border-dashed bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="p-8 text-center">
                <Store class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Tidak ditemukan</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Coba ubah kata kunci pencarian.</p>
            </div>
        </div>

        <!-- Cards -->
        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="umkm in filtered"
                :key="umkm.id"
                :href="`/umkm/${umkm.slug}`"
                class="group block"
            >
                <Card class="overflow-hidden py-0 transition-shadow hover:shadow-md">
                    <!-- Thumbnail -->
                    <div class="relative aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            v-if="imageUrl(umkm.thumbnail)"
                            :src="imageUrl(umkm.thumbnail)"
                            :alt="umkm.nama_usaha"
                            class="h-full w-full object-cover transition-transform group-hover:scale-105"
                        />
                        <div v-else class="flex h-full w-full items-center justify-center">
                            <Store class="size-12 text-zinc-300 dark:text-zinc-600" />
                        </div>
                        <div class="absolute right-2 top-2">
                            <Badge class="bg-white/90 text-xs text-zinc-700 backdrop-blur-sm dark:bg-zinc-900/90 dark:text-zinc-300">
                                {{ umkm.category?.nama || 'Umum' }}
                            </Badge>
                        </div>
                    </div>
                    <CardContent class="p-4">
                        <h3 class="mb-1 font-semibold text-zinc-900 dark:text-white">{{ umkm.nama_usaha }}</h3>
                        <p class="mb-3 flex items-center gap-1 text-xs text-zinc-500">
                            <Star class="size-3" />
                            {{ umkm.pemilik }}
                        </p>
                        <p class="mb-2 line-clamp-2 text-sm text-zinc-600 dark:text-zinc-400" v-html="umkm.deskripsi" />
                        <div class="flex flex-wrap gap-2 text-xs text-zinc-400">
                            <span v-if="umkm.alamat" class="inline-flex items-center gap-1">
                                <MapPin class="size-3" />
                                {{ umkm.alamat.slice(0, 30) }}...
                            </span>
                            <span v-if="umkm.telepon" class="inline-flex items-center gap-1">
                                <Phone class="size-3" />
                                {{ umkm.telepon }}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </Link>
        </div>

        <!-- Pagination -->
        <div v-if="umkms.last_page > 1" class="mt-8 flex items-center justify-center gap-2">
            <Link
                v-if="umkms.current_page > 1"
                :href="`/umkm?page=${umkms.current_page - 1}`"
                class="rounded-md border px-4 py-2 text-sm text-zinc-600 transition-colors hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
            >
                Sebelumnya
            </Link>
            <span class="text-sm text-zinc-500">
                Halaman {{ umkms.current_page }} dari {{ umkms.last_page }}
            </span>
            <Link
                v-if="umkms.current_page < umkms.last_page"
                :href="`/umkm?page=${umkms.current_page + 1}`"
                class="rounded-md border px-4 py-2 text-sm text-zinc-600 transition-colors hover:bg-zinc-100 dark:border-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800"
            >
                Selanjutnya
            </Link>
        </div>
    </div>
</template>
