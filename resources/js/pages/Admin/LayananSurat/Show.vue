<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

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
        case 'pending': return 'border-transparent bg-yellow-100 text-yellow-800';
        case 'diproses': return 'border-transparent bg-blue-100 text-blue-800';
        case 'selesai': return 'border-transparent bg-green-100 text-green-800';
        case 'ditolak': return 'border-transparent bg-red-100 text-red-800';
        default: return 'border-transparent bg-zinc-100 text-zinc-800';
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
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Detail Permohonan</h1>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Informasi Permohonan</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Kode Tracking</span>
                        <p class="font-mono text-sm">{{ letterRequest.kode_tracking }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Status</span>
                        <p><Badge :class="statusClass(letterRequest.status)">{{ statusLabel(letterRequest.status) }}</Badge></p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Pemohon</span>
                        <p>{{ letterRequest.pemohon }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">NIK</span>
                        <p>{{ letterRequest.nik }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Jenis Surat</span>
                        <p>{{ letterRequest.jenis_surat }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-zinc-500">Tanggal Pengajuan</span>
                        <p>{{ formatDate(letterRequest.created_at) }}</p>
                    </div>
                    <div v-if="letterRequest.catatan_admin">
                        <span class="text-sm font-medium text-zinc-500">Catatan Admin</span>
                        <p>{{ letterRequest.catatan_admin }}</p>
                    </div>
                </div>

                <div v-for="(value, key) in letterRequest" :key="key" class="mt-4">
                    <div v-if="!['id', 'kode_tracking', 'pemohon', 'nik', 'jenis_surat', 'status', 'created_at', 'catatan_admin', 'updated_at', 'category'].includes(key)">
                        <span class="text-sm font-medium text-zinc-500 capitalize">{{ key.replace(/_/g, ' ') }}</span>
                        <p>{{ value }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
