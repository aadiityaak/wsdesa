<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Search } from '@lucide/vue';

const form = useForm({
    kode_tracking: '',
});

const result = ref<Record<string, any> | null>(null);

const checkStatus = () => {
    form.post('/layanan-surat/cek-status', {
        preserveScroll: true,
        onSuccess: (page: any) => {
            result.value = page.props?.result || null;
        },
    });
};
</script>

<template>
    <Head title="Cek Status Surat" />

    <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="mb-2 text-3xl font-bold text-zinc-900 dark:text-white">Cek Status Surat</h1>
        <p class="mb-8 text-zinc-500 dark:text-zinc-400">Masukkan kode tracking untuk melihat status pengajuan surat Anda.</p>

        <Card>
            <CardHeader>
                <CardTitle>Masukkan Kode Tracking</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="checkStatus" class="flex gap-3">
                    <div class="flex-1">
                        <Input v-model="form.kode_tracking" placeholder="Masukkan kode tracking..." required />
                    </div>
                    <Button type="submit" :disabled="form.processing">
                        <Search class="size-4" />
                        Cek Status
                    </Button>
                </form>
            </CardContent>
        </Card>

        <!-- Result -->
        <Card v-if="result" class="mt-6">
            <CardHeader>
                <CardTitle>Detail Pengajuan</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Kode Tracking</span>
                    <span class="font-mono font-semibold text-zinc-900 dark:text-white">{{ result.kode_tracking }}</span>
                </div>
                <div class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Nama Pemohon</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ result.nama_pemohon }}</span>
                </div>
                <div class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Jenis Surat</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ result.jenis_surat }}</span>
                </div>
                <div class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Status</span>
                    <Badge :variant="result.status === 'selesai' ? 'default' : 'secondary'">
                        {{ result.status }}
                    </Badge>
                </div>
                <div v-if="result.catatan_admin" class="rounded-md bg-amber-50 p-3 dark:bg-amber-950">
                    <span class="text-xs font-medium text-amber-700 dark:text-amber-300">Catatan Admin</span>
                    <p class="mt-1 text-sm text-amber-800 dark:text-amber-200">{{ result.catatan_admin }}</p>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
