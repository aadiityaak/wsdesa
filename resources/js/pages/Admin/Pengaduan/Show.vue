<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

interface Reply {
    id: number;
    user: { id: number; name: string } | null;
    isi: string;
    created_at: string;
}

interface Complaint {
    id: number;
    kode: string;
    nama: string;
    kategori: string;
    status: string;
    isi: string;
    created_at: string;
    category: { id: number; nama: string } | null;
    replies: Reply[];
    [key: string]: unknown;
}

const props = defineProps<{
    complaint: Complaint;
}>();

const replyForm = useForm({
    isi: '',
});

const statusForm = useForm({
    status: '',
});

const submitReply = () => {
    replyForm.post('/admin/pengaduan/' + props.complaint.id + '/balasan', {
        onSuccess: () => {
            replyForm.reset();
            toast.success('Balasan berhasil dikirim.');
        },
    });
};

const setStatus = (status: string) => {
    statusForm.status = status;
    statusForm.put('/admin/pengaduan/' + props.complaint.id + '/status', {
        onSuccess: () => {
            toast.success('Status berhasil diperbarui.');
        },
    });
};

const statusClass = (status: string) => {
    switch (status) {
        case 'baru': return 'border-transparent bg-red-100 text-red-800';
        case 'diproses': return 'border-transparent bg-blue-100 text-blue-800';
        case 'selesai': return 'border-transparent bg-green-100 text-green-800';
        case 'ditolak': return 'border-transparent bg-zinc-100 text-zinc-800';
        default: return 'border-transparent bg-zinc-100 text-zinc-800';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'baru': return 'Baru';
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
    <Head title="Detail Pengaduan" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Detail Pengaduan</h1>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Info -->
            <div class="lg:col-span-2 space-y-6">
                <Card>
                    <CardHeader class="pb-3">
                        <CardTitle>Informasi Pengaduan</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Kode</span>
                                <p class="font-mono text-sm">{{ complaint.kode }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Status</span>
                                <p><Badge :class="statusClass(complaint.status)">{{ statusLabel(complaint.status) }}</Badge></p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Nama</span>
                                <p>{{ complaint.nama }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Kategori</span>
                                <p>{{ complaint.category?.nama || complaint.kategori || '-' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-sm font-medium text-zinc-500">Tanggal</span>
                                <p>{{ formatDate(complaint.created_at) }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-sm font-medium text-zinc-500">Isi Pengaduan</span>
                                <p class="mt-1 whitespace-pre-wrap rounded-lg border bg-zinc-50 p-4 dark:bg-zinc-800">{{ complaint.isi }}</p>
                            </div>
                        </div>

                        <div v-for="(value, key) in complaint" :key="key" class="mt-2">
                            <div v-if="!['id', 'kode', 'nama', 'kategori', 'status', 'isi', 'created_at', 'updated_at', 'category', 'replies'].includes(key)">
                                <span class="text-sm font-medium text-zinc-500 capitalize">{{ key.replace(/_/g, ' ') }}</span>
                                <p>{{ value }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Balasan -->
                <Card>
                    <CardHeader class="pb-3">
                        <CardTitle>Balasan</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="complaint.replies && complaint.replies.length > 0" class="space-y-3 mb-4">
                            <div
                                v-for="reply in complaint.replies"
                                :key="reply.id"
                                class="rounded-lg border p-3"
                            >
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="font-medium">{{ reply.user?.name || 'Admin' }}</span>
                                    <span class="text-xs text-zinc-400">{{ formatDate(reply.created_at) }}</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400">{{ reply.isi }}</p>
                            </div>
                        </div>
                        <p v-else class="py-4 text-center text-sm text-zinc-400">Belum ada balasan.</p>

                        <form @submit.prevent="submitReply" class="space-y-3 border-t pt-4">
                            <div class="grid gap-1.5">
                                <Label for="isi">Tulis Balasan</Label>
                                <textarea
                                    id="isi"
                                    v-model="replyForm.isi"
                                    required
                                    rows="3"
                                    class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    placeholder="Tulis balasan..."
                                ></textarea>
                            </div>
                            <Button type="submit" :disabled="replyForm.processing">Kirim Balasan</Button>
                        </form>
                    </CardContent>
                </Card>
            </div>

            <!-- Sidebar Actions -->
            <div>
                <Card>
                    <CardHeader class="pb-3">
                        <CardTitle class="text-base">Update Status</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <Button
                            variant="outline"
                            class="w-full justify-start"
                            :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/20': complaint.status === 'diproses' }"
                            :disabled="statusForm.processing"
                            @click="setStatus('diproses')"
                        >
                            Diproses
                        </Button>
                        <Button
                            variant="outline"
                            class="w-full justify-start"
                            :class="{ 'border-green-500 bg-green-50 dark:bg-green-900/20': complaint.status === 'selesai' }"
                            :disabled="statusForm.processing"
                            @click="setStatus('selesai')"
                        >
                            Selesai
                        </Button>
                        <Button
                            variant="outline"
                            class="w-full justify-start"
                            :class="{ 'border-zinc-500 bg-zinc-50 dark:bg-zinc-800': complaint.status === 'ditolak' }"
                            :disabled="statusForm.processing"
                            @click="setStatus('ditolak')"
                        >
                            Ditolak
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
