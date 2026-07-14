<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Check, Trash2 } from '@lucide/vue';

interface CommentItem {
    id: number;
    nama: string;
    email: string | null;
    konten: string;
    approved: boolean;
    created_at: string;
    post: {
        id: number;
        judul: string;
    } | null;
}

const props = defineProps<{
    comments: CommentItem[];
}>();

const deleteConfirmId = ref<number | null>(null);
const form = useForm({});

const approveComment = (comment: CommentItem) => {
    form.put(`/admin/komentar/${comment.id}/approve`, {
        onSuccess: () => {
            toast.success('Komentar berhasil disetujui.');
        },
    });
};

const confirmDelete = (comment: CommentItem) => {
    deleteConfirmId.value = comment.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/komentar/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Komentar berhasil dihapus.');
            },
        });
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Moderasi Komentar" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Moderasi Komentar</h1>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Komentar Belum Disetujui</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Email</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Komentar</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Berita</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="comment in comments"
                                :key="comment.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 font-medium">{{ comment.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ comment.email || '-' }}</td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="truncate text-zinc-500">{{ comment.konten }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge variant="secondary">{{ comment.post?.judul || '-' }}</Badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(comment.created_at) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="approveComment(comment)">
                                            <Check class="size-4 text-green-500" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(comment)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="comments.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-zinc-500">
                                    Tidak ada komentar yang perlu dimoderasi.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Dialog Konfirmasi Hapus -->
        <Dialog v-model:open="deleteConfirmId" :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus komentar ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" :disabled="form.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
