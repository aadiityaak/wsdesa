<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { ArrowLeft, FileText } from '@lucide/vue';

interface LetterRequest {
    id: number;
    kode_tracking: string;
    pemohon: string;
    nik: string;
    jenis_surat: string;
    status: string;
    created_at: string;
    catatan_admin: string | null;
    [key: string]: unknown;
}

defineProps<{
    letterRequest: LetterRequest;
}>();

const statusClass = (status: string) => {
    switch (status) {
        case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'diproses': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'selesai': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'ditolak': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default: return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'pending': return 'Pending';
        case 'diproses': return 'Diproses';
        case 'selesai': return 'Selesai';
        case 'ditolak': return 'Ditolak';
        default: return status;
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Detail Permohonan Surat" />

    <div class="space-y-6">
        <!-- Hero banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative">
                <Button as="a" href="/admin/layanan-surat" variant="outline" size="sm" class="mb-4 rounded-full">
                    <ArrowLeft class="mr-1.5 h-4 w-4" />
                    Kembali
                </Button>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                        <FileText class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Detail Permohonan Surat</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ letterRequest.kode_tracking }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail content -->
        <div class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Informasi Permohonan</h2>
            </div>
            <div class="px-5 py-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Kode Tracking</span>
                        <p class="mt-0.5 font-mono text-sm text-zinc-900 dark:text-white">{{ letterRequest.kode_tracking }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Status</span>
                        <p class="mt-0.5">
                            <Badge :class="statusClass(letterRequest.status)" class="rounded-full px-2.5 py-0.5 text-xs">{{ statusLabel(letterRequest.status) }}</Badge>
                        </p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Pemohon</span>
                        <p class="mt-0.5 text-zinc-900 dark:text-white">{{ letterRequest.pemohon }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">NIK</span>
                        <p class="mt-0.5 text-zinc-900 dark:text-white">{{ letterRequest.nik }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Jenis Surat</span>
                        <p class="mt-0.5 text-zinc-900 dark:text-white">{{ letterRequest.jenis_surat }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Tanggal Pengajuan</span>
                        <p class="mt-0.5 text-zinc-900 dark:text-white">{{ formatDate(letterRequest.created_at) }}</p>
                    </div>
                    <div v-if="letterRequest.catatan_admin" class="sm:col-span-2">
                        <span class="text-sm font-medium text-zinc-500">Catatan Admin</span>
                        <p class="mt-0.5 rounded-lg border bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ letterRequest.catatan_admin }}</p>
                    </div>
                </div>

                <div v-for="(value, key) in letterRequest" :key="key" class="mt-4">
                    <div v-if="!['id', 'kode_tracking', 'pemohon', 'nik', 'jenis_surat', 'status', 'created_at', 'catatan_admin', 'updated_at', 'category'].includes(key)">
                        <span class="text-sm font-medium text-zinc-500 capitalize">{{ key.replace(/_/g, ' ') }}</span>
                        <p class="mt-0.5 text-zinc-900 dark:text-white">{{ value }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
