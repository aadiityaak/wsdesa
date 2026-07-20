<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { ImageIcon } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';

interface Potential {
    id: number;
    nama: string;
    kategori: string;
    deskripsi: string;
    thumbnail: string | null;
}

defineProps<{
    potentials: Potential[];
}>();

const imageUrl = (path: string | null) => (path ? `/storage/${path}` : null);
</script>

<template>
    <Head title="Potensi Desa" />

    <PageHero title="Potensi Desa" description="Kenali berbagai potensi dan kekayaan desa kami">
        <template #icon>
            <ImageIcon class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div v-if="potentials.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <ImageIcon class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada data potensi</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data potensi desa akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="pot in potentials" :key="pot.id" class="overflow-hidden">
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                    <img
                        v-if="pot.thumbnail"
                        :src="imageUrl(pot.thumbnail)"
                        :alt="pot.nama"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center text-zinc-400">
                        <ImageIcon class="size-12" />
                    </div>
                </div>
                <CardContent class="space-y-2 p-4">
                    <h3 class="font-semibold text-zinc-900 dark:text-white">{{ pot.nama }}</h3>
                    <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400">{{ pot.kategori }}</p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-3" v-html="pot.deskripsi" />
                </CardContent>
            </Card>
        </div>
    </div>
</template>
