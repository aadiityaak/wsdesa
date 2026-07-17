 <script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Search, ArrowUp, MessageSquareText, Trash2, CheckCheck, User, Mail, Clock, Newspaper } from '@lucide/vue';

interface Post {
    id: number;
    judul: string;
}

interface CommentItem {
    id: number;
    post_id: number;
    nama: string;
    email: string;
    konten: string;
    status: string;
    created_at: string;
    post: Post | null;
}

interface PaginatedData<T> {
    data: T[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    comments: PaginatedData<CommentItem>;
}>();

const search = ref('');
const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});
const approving = ref<number | null>(null);

const statusVariant = (status: string) => {
    switch (status) {
        case 'approved': return 'default' as const;
        case 'pending': return 'secondary' as const;
        case 'rejected': return 'destructive' as const;
        default: return 'secondary' as const;
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'approved': return 'Disetujui';
        case 'pending': return 'Menunggu';
        case 'rejected': return 'Ditolak';
        default: return status;
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const truncate = (text: string, max: number) =>
    text.length > max ? text.slice(0, max) + '…' : text;

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        router.delete(`/admin/komentar/${deleteConfirmId.value}`, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Komentar berhasil dihapus.');
            },
            onError: () => toast.error('Gagal menghapus komentar.'),
        });
    }
};

const approveComment = (id: number) => {
    approving.value = id;
    router.put(`/admin/komentar/${id}/approve`, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            approving.value = null;
            toast.success('Komentar berhasil disetujui.');
        },
        onError: () => {
            approving.value = null;
            toast.error('Gagal menyetujui komentar.');
        },
    });
};

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
    router.get('/admin/komentar', { page, search: search.value }, { preserveState: true });
};

const performSearch = () => {
    router.get('/admin/komentar', { search: search.value }, { preserveState: true, replace: true });
};

const stats = computed(() => {
    const items = props.comments.data;
    return {
        total: props.comments.total,
        approved: items.filter((c) => c.status === 'approved').length,
        pending: items.filter((c) => c.status === 'pending').length,
    };
});

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Komentar" />

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
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                        <MessageSquareText class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Komentar</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola komentar dari pengunjung</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-3 gap-3">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-3 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-3 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ stats.approved }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Disetujui</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-3 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ stats.pending }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Menunggu</p>
                </div>
            </div>

            <!-- Search -->
            <div class="relative mt-6 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <Input
                        v-model="search"
                        placeholder="Cari komentar..."
                        class="rounded-xl border-zinc-200 pl-9 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                        @keyup.enter="performSearch"
                    />
                </div>
                <Button variant="outline" class="rounded-xl" @click="performSearch">Cari</Button>
            </div>
        </div>

        <!-- Daftar -->
        <div class="space-y-4">
            <!-- Empty state -->
            <div v-if="comments.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <MessageSquareText class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada komentar</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Komentar dari pengunjung akan muncul di sini.</p>
            </div>

            <!-- Comment cards -->
            <div v-for="comment in comments.data" :key="comment.id" class="group rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <Badge :variant="statusVariant(comment.status)" class="rounded-full px-2.5 py-0.5 text-xs font-medium">
                                {{ statusLabel(comment.status) }}
                            </Badge>
                        </div>

                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            {{ truncate(comment.konten, 200) }}
                        </p>

                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs text-zinc-400 dark:text-zinc-500">
                            <span class="inline-flex items-center gap-1">
                                <User class="h-3.5 w-3.5" />
                                {{ comment.nama }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Mail class="h-3.5 w-3.5" />
                                {{ comment.email }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <Clock class="h-3.5 w-3.5" />
                                {{ formatDate(comment.created_at) }}
                            </span>
                            <span v-if="comment.post" class="inline-flex items-center gap-1">
                                <Newspaper class="h-3.5 w-3.5" />
                                {{ comment.post.judul }}
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Button
                            v-if="comment.status !== 'approved'"
                            variant="ghost"
                            size="icon-sm"
                            class="rounded-lg"
                            :disabled="approving === comment.id"
                            @click="approveComment(comment.id)"
                        >
                            <CheckCheck class="h-4 w-4 text-green-500" />
                        </Button>
                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(comment.id)">
                            <Trash2 class="h-4 w-4 text-red-500" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1 && comments.data.length > 0" class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-zinc-100 bg-white px-5 py-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ comments.from }}–{{ comments.to }} dari {{ comments.total }}
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
                        Apakah Anda yakin ingin menghapus komentar ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" @click="executeDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
