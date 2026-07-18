<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { ArrowLeft, MessageSquare } from '@lucide/vue';

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
        case 'baru': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        case 'diproses': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'selesai': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'ditolak': return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
        default: return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
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
        <!-- Hero banner -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative">
                <Button as="a" href="/admin/pengaduan" variant="outline" size="sm" class="mb-4 rounded-full">
                    <ArrowLeft class="mr-1.5 h-4 w-4" />
                    Kembali
                </Button>
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                        <MessageSquare class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Detail Pengaduan</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ complaint.kode }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Informasi Pengaduan</h2>
                    </div>
                    <div class="px-5 py-5">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Kode</span>
                                <p class="mt-0.5 font-mono text-sm text-zinc-900 dark:text-white">{{ complaint.kode }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Status</span>
                                <p class="mt-0.5">
                                    <Badge :class="statusClass(complaint.status)" class="rounded-full px-2.5 py-0.5 text-xs">{{ statusLabel(complaint.status) }}</Badge>
                                </p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Nama</span>
                                <p class="mt-0.5 text-zinc-900 dark:text-white">{{ complaint.nama }}</p>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-zinc-500">Kategori</span>
                                <p class="mt-0.5 text-zinc-900 dark:text-white">{{ complaint.category?.nama || complaint.kategori || '-' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-sm font-medium text-zinc-500">Tanggal</span>
                                <p class="mt-0.5 text-zinc-900 dark:text-white">{{ formatDate(complaint.created_at) }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-sm font-medium text-zinc-500">Isi Pengaduan</span>
                                <p class="mt-1 whitespace-pre-wrap rounded-xl border bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ complaint.isi }}</p>
                            </div>
                        </div>

                        <div v-for="(value, key) in complaint" :key="key" class="mt-2">
                            <div v-if="!['id', 'kode', 'nama', 'kategori', 'status', 'isi', 'created_at', 'updated_at', 'category', 'replies'].includes(key)">
                                <span class="text-sm font-medium text-zinc-500 capitalize">{{ key.replace(/_/g, ' ') }}</span>
                                <p class="text-zinc-900 dark:text-white">{{ value }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Balasan -->
                <div class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Balasan</h2>
                    </div>
                    <div class="px-5 py-5">
                        <div v-if="complaint.replies && complaint.replies.length > 0" class="space-y-3 mb-5">
                            <div
                                v-for="reply in complaint.replies"
                                :key="reply.id"
                                class="rounded-xl border border-zinc-100 p-4 dark:border-zinc-800"
                            >
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ reply.user?.name || 'Admin' }}</span>
                                    <span class="text-xs text-zinc-400">{{ formatDate(reply.created_at) }}</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400">{{ reply.isi }}</p>
                            </div>
                        </div>
                        <p v-else class="py-4 text-center text-sm text-zinc-400">Belum ada balasan.</p>

                        <form @submit.prevent="submitReply" class="space-y-3 border-t border-zinc-100 pt-5 dark:border-zinc-800">
                            <div class="grid gap-1.5">
                                <Label for="isi">Tulis Balasan</Label>
                                <textarea
                                    id="isi"
                                    v-model="replyForm.isi"
                                    required
                                    rows="3"
                                    class="flex w-full rounded-xl border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    placeholder="Tulis balasan..."
                                ></textarea>
                            </div>
                            <Button type="submit" class="rounded-full" :disabled="replyForm.processing">Kirim Balasan</Button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar Actions -->
            <div>
                <div class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                        <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Update Status</h2>
                    </div>
                    <div class="space-y-2 px-5 py-4">
                        <Button
                            variant="outline"
                            class="w-full justify-start rounded-xl"
                            :class="{ 'border-blue-500 bg-blue-50 dark:bg-blue-900/20': complaint.status === 'diproses' }"
                            :disabled="statusForm.processing"
                            @click="setStatus('diproses')"
                        >
                            Diproses
                        </Button>
                        <Button
                            variant="outline"
                            class="w-full justify-start rounded-xl"
                            :class="{ 'border-green-500 bg-green-50 dark:bg-green-900/20': complaint.status === 'selesai' }"
                            :disabled="statusForm.processing"
                            @click="setStatus('selesai')"
                        >
                            Selesai
                        </Button>
                        <Button
                            variant="outline"
                            class="w-full justify-start rounded-xl"
                            :class="{ 'border-zinc-500 bg-zinc-50 dark:bg-zinc-800': complaint.status === 'ditolak' }"
                            :disabled="statusForm.processing"
                            @click="setStatus('ditolak')"
                        >
                            Ditolak
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
