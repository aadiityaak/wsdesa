<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Download as DownloadIcon, FileText } from '@lucide/vue';

interface DownloadItem {
    id: number;
    nama: string;
    kategori: string;
    file_url: string;
    downloads: number;
}

defineProps<{
    downloads: DownloadItem[];
}>();
</script>

<template>
    <Head title="Download" />

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-zinc-900 dark:text-white">Download</h1>

        <div v-if="downloads.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <DownloadIcon class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada file</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">File download akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="space-y-3">
            <Card v-for="file in downloads" :key="file.id">
                <CardContent class="flex items-center gap-4 p-4">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950">
                        <FileText class="size-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-sm font-medium text-zinc-900 dark:text-white">{{ file.nama }}</h3>
                        <div class="mt-1 flex items-center gap-2">
                            <Badge variant="secondary" class="text-xs">{{ file.kategori }}</Badge>
                            <span class="text-xs text-zinc-500">{{ file.downloads }} kali diunduh</span>
                        </div>
                    </div>
                    <a :href="file.file_url" target="_blank" rel="noopener noreferrer">
                        <Button variant="outline" size="sm">
                            <DownloadIcon class="size-4" />
                            Download
                        </Button>
                    </a>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
