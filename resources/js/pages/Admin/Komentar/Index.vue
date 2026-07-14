<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
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

interface CommentsPaginated {
    data: CommentItem[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    comments: CommentsPaginated;
}>();

const deleteConfirmId = ref<number | null>(null);
const form = useForm({});

const currentPage = computed(() => props.comments.current_page);
const lastPage = computed(() => props.comments.last_page);

const pageNumbers = computed(() => {
    const pages: (number | string)[] = [];
    const current = currentPage.value;
    const last = lastPage.value;

    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current > 3) pages.push('...');
        const start = Math.max(2, current - 1);
        const end = Math.min(last - 1, current + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        if (current < last - 2) pages.push('...');
        pages.push(last);
    }
    return pages;
});

const goToPage = (page: number) => {
    form.get(`/admin/komentar?page=${page}`);
};

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

const truncateText = (text: string, max: number) => {
    return text.length > max ? text.slice(0, max) + '...' : text;
};
</script>

<template>
    <Head title="Komentar" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Komentar</h1>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Komentar</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400 w-12">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Email</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Berita</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Konten</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(comment, index) in comments.data"
                                :key="comment.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (comments.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium">{{ comment.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ comment.email || '-' }}</td>
                                <td class="px-4 py-3">
                                    <a
                                        v-if="comment.post"
                                        :href="`/admin/berita/${comment.post.id}/edit`"
                                        class="text-blue-600 hover:underline dark:text-blue-400"
                                    >
                                        {{ comment.post.judul }}
                                    </a>
                                    <span v-else class="text-zinc-400">-</span>
                                </td>
                                <td class="px-4 py-3 max-w-xs">
                                    <p class="truncate text-zinc-500">{{ truncateText(comment.konten, 80) }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge :variant="comment.approved ? 'default' : 'outline'">
                                        {{ comment.approved ? 'Disetujui' : 'Pending' }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            v-if="!comment.approved"
                                            variant="ghost"
                                            size="icon-sm"
                                            @click="approveComment(comment)"
                                        >
                                            <Check class="size-4 text-green-500" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(comment)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="comments.data.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-zinc-500">
                                    Tidak ada komentar.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="lastPage > 1"
                    class="flex items-center justify-between border-t px-4 py-3"
                >
                    <p class="text-sm text-zinc-500">
                        Menampilkan {{ comments.from }}–{{ comments.to }} dari {{ comments.total }}
                    </p>
                    <div class="flex items-center gap-1">
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="currentPage === 1"
                            @click="goToPage(currentPage - 1)"
                        >
                            Sebelumnya
                        </Button>
                        <template v-for="page in pageNumbers" :key="page">
                            <span v-if="page === '...'" class="px-2 text-zinc-400">...</span>
                            <Button
                                v-else
                                :variant="currentPage === page ? 'default' : 'outline'"
                                size="sm"
                                @click="goToPage(page as number)"
                            >
                                {{ page }}
                            </Button>
                        </template>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="currentPage === lastPage"
                            @click="goToPage(currentPage + 1)"
                        >
                            Selanjutnya
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Dialog Hapus -->
        <Dialog :open="deleteConfirmId !== null" @update:open="() => (deleteConfirmId = null)">
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
