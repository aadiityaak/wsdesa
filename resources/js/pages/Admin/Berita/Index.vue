<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, watch, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Pencil, Trash2, Search } from '@lucide/vue';

interface Post {
    id: number;
    judul: string;
    slug: string;
    post_category_id: number;
    thumbnail_url: string | null;
    status: string;
    views: number;
    published_at: string | null;
    category: { id: number; nama: string; slug: string } | null;
}

interface PostsPaginated {
    data: Post[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    posts: PostsPaginated;
}>();

const search = ref('');
const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

const currentPage = computed(() => props.posts.current_page);
const lastPage = computed(() => props.posts.last_page);

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

const performSearch = () => {
    router.get('/admin/berita', { search: search.value }, { preserveState: true, replace: true });
};

const goToPage = (page: number) => {
    router.get('/admin/berita', { page, search: search.value }, { preserveState: true });
};

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/berita/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Berita berhasil dihapus.');
            },
        });
    }
};

const statusVariant = (status: string) => {
    switch (status) {
        case 'publish':
            return 'default';
        case 'draft':
            return 'secondary';
        case 'archive':
            return 'outline';
        default:
            return 'secondary';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'publish':
            return 'Dipublikasikan';
        case 'draft':
            return 'Draft';
        case 'archive':
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
    <Head title="Berita" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Berita</h1>
            <Button as="a" href="/admin/berita/tambah">
                <Plus class="size-4" />
                Tambah Berita
            </Button>
        </div>

        <div class="flex items-center gap-2">
            <div class="relative flex-1 max-w-sm">
                <Search class="absolute left-2.5 top-2.5 size-4 text-zinc-500" />
                <Input
                    v-model="search"
                    placeholder="Cari judul berita..."
                    class="pl-8"
                    @keyup.enter="performSearch"
                />
            </div>
            <Button variant="outline" @click="performSearch">Cari</Button>
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
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400 w-12">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400 w-16">Thumbnail</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Views</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(post, index) in posts.data"
                                :key="post.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (posts.from ?? 1) + index }}</td>
                                <td class="px-4 py-3">
                                    <img
                                        v-if="post.thumbnail_url"
                                        :src="post.thumbnail_url"
                                        alt="Thumbnail"
                                        class="size-10 rounded object-cover"
                                    />
                                    <span v-else class="text-zinc-400">-</span>
                                </td>
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
                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            as="a"
                                            :href="`/admin/berita/${post.id}/edit`"
                                        >
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(post.id)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="posts.data.length === 0">
                                <td colspan="8" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada berita.
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
                        Menampilkan {{ posts.from }}–{{ posts.to }} dari {{ posts.total }}
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
                        Apakah Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" :disabled="deleteForm.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
