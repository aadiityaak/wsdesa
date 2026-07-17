 <script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';
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
import { Plus, Pencil, Trash2, Search, ArrowUp, Newspaper, Eye, Clock } from '@lucide/vue';

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
        case 'publish': return 'default' as const;
        case 'draft': return 'secondary' as const;
        case 'archive': return 'outline' as const;
        default: return 'secondary' as const;
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'publish': return 'Publikasi';
        case 'draft': return 'Draft';
        case 'archive': return 'Arsip';
        default: return status;
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

const stats = computed(() => {
    const total = props.posts.total;
    const published = props.posts.data.filter((p) => p.status === 'publish').length;
    const draft = props.posts.data.filter((p) => p.status === 'draft').length;
    return { total, published, draft };
});

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Berita" />

    <div class="relative">
        <!-- Scroll to top -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="translate-y-2 opacity-0"
        >
            <button
                v-if="showScrollTop"
                type="button"
                class="fixed bottom-8 right-8 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition hover:scale-105 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                @click="scrollToTop"
            >
                <ArrowUp class="h-5 w-5" />
            </button>
        </Transition>

        <!-- Hero banner -->
        <div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                            <Newspaper class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Berita</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola berita dan artikel desa</p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/berita/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Berita
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-3 gap-3 sm:gap-4">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.published }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Publikasi</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ stats.draft }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Draft</p>
                </div>
            </div>

            <!-- Search bar -->
            <div class="relative mt-6 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <Input
                        v-model="search"
                        placeholder="Cari judul berita..."
                        class="rounded-xl border-zinc-200 pl-9 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                        @keyup.enter="performSearch"
                    />
                </div>
                <Button variant="outline" class="rounded-xl" @click="performSearch">Cari</Button>
            </div>
        </div>

        <!-- Daftar berita -->
        <div class="space-y-4">
            <!-- Empty state -->
            <div v-if="posts.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <Newspaper class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada berita</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Buat berita pertama untuk desa.</p>
                <Button as="a" href="/admin/berita/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Berita
                </Button>
            </div>

            <!-- Post cards -->
            <div v-for="post in posts.data" :key="post.id" class="group rounded-2xl border border-zinc-100 bg-white p-4 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 sm:p-5">
                <div class="flex items-start gap-4">
                    <!-- Thumbnail -->
                    <div class="hidden shrink-0 overflow-hidden rounded-xl sm:block">
                        <img
                            v-if="post.thumbnail_url"
                            :src="post.thumbnail_url"
                            :alt="post.judul"
                            class="h-20 w-28 object-cover transition duration-300 group-hover:scale-105"
                        />
                        <div v-else class="flex h-20 w-28 items-center justify-center bg-zinc-100 text-zinc-300 dark:bg-zinc-800 dark:text-zinc-600">
                            <Newspaper class="h-8 w-8" />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <Badge :variant="statusVariant(post.status)" class="rounded-full px-2.5 py-0.5 text-xs font-medium">
                                        {{ statusLabel(post.status) }}
                                    </Badge>
                                    <span v-if="post.category" class="text-xs text-zinc-400 dark:text-zinc-500">{{ post.category.nama }}</span>
                                </div>
                                <h3 class="mt-1.5 truncate text-base font-semibold text-zinc-900 dark:text-white">
                                    {{ post.judul }}
                                </h3>
                            </div>
                            <!-- Actions -->
                            <div class="flex shrink-0 items-center gap-1">
                                <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/berita/${post.id}/edit`" class="rounded-lg">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon-sm" @click="confirmDelete(post.id)" class="rounded-lg">
                                    <Trash2 class="h-4 w-4 text-red-500" />
                                </Button>
                            </div>
                        </div>

                        <!-- Meta -->
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-zinc-400 dark:text-zinc-500">
                            <span class="inline-flex items-center gap-1">
                                <Clock class="h-3.5 w-3.5" />
                                {{ formatDate(post.published_at) }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Eye class="h-3.5 w-3.5" />
                                {{ post.views }} dilihat
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1 && posts.data.length > 0" class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-zinc-100 bg-white px-5 py-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ posts.from }}–{{ posts.to }} dari {{ posts.total }}
                </p>
                <div class="flex items-center gap-1">
                    <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
                        Sebelumnya
                    </Button>
                    <template v-for="page in pageNumbers" :key="page">
                        <span v-if="page === '...'" class="px-2 text-zinc-400">...</span>
                        <Button
                            v-else
                            size="sm"
                            class="rounded-lg"
                            :variant="currentPage === page ? 'default' : 'outline'"
                            @click="goToPage(page as number)"
                        >
                            {{ page }}
                        </Button>
                    </template>
                    <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)">
                        Selanjutnya
                    </Button>
                </div>
            </div>
        </div>

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
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" :disabled="deleteForm.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
