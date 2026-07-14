<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Stats {
    total_berita: number;
    total_penduduk: number;
    total_permohonan_surat: number;
    total_pengaduan: number;
}

defineProps<{
    stats: Stats;
}>();
</script>

<template>
    <Head title="Dashboard Admin" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Dashboard</h1>
        <p class="text-zinc-600 dark:text-zinc-400">Selamat datang di panel admin Website Desa.</p>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Berita</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-2xl font-bold">{{ stats.total_berita }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Penduduk</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-2xl font-bold">{{ stats.total_penduduk }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pengajuan Surat</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-2xl font-bold">{{ stats.total_permohonan_surat }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="pb-2">
                    <CardTitle class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Pengaduan</CardTitle>
                </CardHeader>
                <CardContent>
                    <p class="text-2xl font-bold">{{ stats.total_pengaduan }}</p>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
