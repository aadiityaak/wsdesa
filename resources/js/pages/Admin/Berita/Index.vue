<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Plus, Pencil, Trash2, Eye } from '@lucide/vue';

interface PostCategory {
    id: number;
    nama: string;
    slug: string;
}

interface Post {
    id: number;
    judul: string;
    slug: string;
    post_category_id: number;
    status: string;
    views: number;
    published_at: string | null;
    category: PostCategory | null;
    ringkasan: string | null;
    konten: string;
    gambar: string | null;
}

const props = defineProps<{
    posts: Post[];
    categories: PostCategory[];
}>();

const deleteConfirmId = ref<number | null>(null);
const form = useForm({});

const openCreate = () => {
    window.location.href = '/admin/berita/tambah';
};

const openEdit = (post: Post) => {
    window.location.href = `/admin/berita/${post.id}/edit`;
};

const confirmDelete = (post: Post) => {
    deleteConfirmId.value = post.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/berita/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Berita berhasil dihapus.');
            },
        });
    }
};

const statusVariant = (status: string) => {
    switch (status) {
        case 'published':
            return 'default';
        case 'draft':
            return 'secondary';
        case 'archived':
            return 'outline';
        default:
            return 'secondary';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'published':
            return 'Dipublikasikan';
        case 'draft':
            return 'Draft';
        case 'archived':
            return 'Diarsipkan';
        default:
            return status;
    }
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};
</script>

<template>
    <Head title="Kelola Berita" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Berita</h1>
            <Button @click="openCreate">
                <Plus class="size-4" />
                Tambah Berita
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Berita</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Views</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Dipublikasikan</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(post, index) in posts"
                                :key="post.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium max-w-xs truncate">{{ post.judul }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ post.category?.nama || '-' }}</td>
                                <td class="px-4 py-3">
                                    <Badge :variant="statusVariant(post.status)">
                                        {{ statusLabel(post.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ post.views }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(post.published_at) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openEdit(post)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(post)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="posts.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada berita.
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
                        Apakah Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan.
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
