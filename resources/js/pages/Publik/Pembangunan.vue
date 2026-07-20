<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Construction } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';
import { MapPin, Calendar, ImageIcon } from '@lucide/vue';

interface DevelopmentImage {
    id: number;
    gambar: string;
    caption?: string;
}

interface Development {
    id: number;
    nama_proyek: string;
    lokasi: string;
    anggaran: number;
    status: string;
    tahun: number;
    deskripsi: string;
    images?: DevelopmentImage[];
}

defineProps<{
    developments: Development[];
}>();

const imageUrl = (path: string) => `/storage/${path}`;

const formatRupiah = (value: number) =>
    'Rp ' + Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

const statusVariant = (status: string) => {
    const map: Record<string, string> = {
        selesai: 'default',
        dalam_pengerjaan: 'secondary',
        direncanakan: 'outline',
    };
    return map[status] || 'secondary';
};
</script>

<template>
    <Head title="Pembangunan" />

    <PageHero title="Pembangunan Desa" description="Informasi perkembangan pembangunan dan infrastruktur desa">
        <template #icon>
            <Construction class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        <div v-if="developments.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada data pembangunan</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data pembangunan akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="space-y-6">
            <Card v-for="dev in developments" :key="dev.id">
                <CardContent class="p-5">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex-1 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ dev.nama_proyek }}</h3>
                                <Badge :variant="statusVariant(dev.status)">{{ dev.status }}</Badge>
                            </div>
                            <div class="flex flex-wrap gap-3 text-sm text-zinc-500">
                                <span class="flex items-center gap-1">
                                    <MapPin class="size-4" />
                                    {{ dev.lokasi }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <Calendar class="size-4" />
                                    {{ dev.tahun }}
                                </span>
                                <span class="font-semibold text-zinc-900 dark:text-white">
                                    {{ formatRupiah(dev.anggaran) }}
                                </span>
                            </div>
                            <p v-if="dev.deskripsi" class="text-sm text-zinc-600 dark:text-zinc-400" v-html="dev.deskripsi" />
                        </div>
                    </div>

                    <!-- Images -->
                    <div v-if="dev.images?.length" class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                        <div
                            v-for="image in dev.images"
                            :key="image.id"
                            class="aspect-video overflow-hidden rounded-md bg-zinc-100 dark:bg-zinc-800"
                        >
                            <img
                                :src="imageUrl(image.gambar)"
                                :alt="image.caption || dev.nama_proyek"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
