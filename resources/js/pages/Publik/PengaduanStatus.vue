<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Search } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';

const form = useForm({
    kode_tracking: '',
});

const result = ref<Record<string, any> | null>(null);

const checkStatus = () => {
    form.post('/pengaduan/cek-status', {
        preserveScroll: true,
        onSuccess: (page: any) => {
            result.value = page.props?.result || null;
        },
    });
};
</script>

<template>
    <Head title="Cek Status Pengaduan" />

    <PageHero title="Cek Status Pengaduan" description="Pantau status dan tindak lanjut pengaduan Anda">
        <template #icon>
            <Search class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <p class="mb-8 text-zinc-500 dark:text-zinc-400">Masukkan kode tracking untuk melihat status pengaduan Anda.</p>

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
                <CardTitle>Detail Pengaduan</CardTitle>
            </CardHeader>
            <CardContent class="space-y-3">
                <div class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Kode Tracking</span>
                    <span class="font-mono font-semibold text-zinc-900 dark:text-white">{{ result.kode_tracking }}</span>
                </div>
                <div class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Nama</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ result.nama }}</span>
                </div>
                <div v-if="result.kategori" class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Kategori</span>
                    <span class="font-medium text-zinc-900 dark:text-white">{{ result.kategori }}</span>
                </div>
                <div class="flex items-center justify-between rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Status</span>
                    <Badge :variant="result.status === 'selesai' ? 'default' : 'secondary'">
                        {{ result.status }}
                    </Badge>
                </div>
                <div class="rounded-md bg-zinc-50 p-3 dark:bg-zinc-800">
                    <span class="text-sm text-zinc-500">Isi Pengaduan</span>
                    <p class="mt-1 text-sm text-zinc-900 dark:text-white">{{ result.isi }}</p>
                </div>

                <!-- Replies -->
                <div v-if="result.replies?.length" class="mt-4 space-y-3 border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Tanggapan</h3>
                    <div v-for="reply in result.replies" :key="reply.id" class="rounded-md border border-blue-200 bg-blue-50 p-3 dark:border-blue-900 dark:bg-blue-950">
                        <p class="text-sm text-zinc-800 dark:text-zinc-200">{{ reply.isi }}</p>
                        <p class="mt-1 text-xs text-zinc-500">{{ new Date(reply.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
