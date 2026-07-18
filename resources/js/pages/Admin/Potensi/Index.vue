<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';
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
import { Plus, Pencil, Trash2, TrendingUp, Search, ArrowUp } from '@lucide/vue';

interface Potential {
    id: number;
    nama: string;
    category_id: number;
    lokasi: string;
    deskripsi: string | null;
    category: { id: number; nama: string } | null;
}

interface PotentialCategory {
    id: number;
    nama: string;
}

interface Paginated {
    data: Potential[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    potentials: Paginated;
    categories: PotentialCategory[];
}>();

const search = ref('');
const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

const currentPage = computed(() => props.potentials.current_page);
const lastPage = computed(() => props.potentials.last_page);

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
    router.get('/admin/potensi', { search: search.value }, { preserveState: true, replace: true });
};

const goToPage = (page: number) => {
    router.get('/admin/potensi', { page, search: search.value }, { preserveState: true });
};

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/potensi/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Potensi berhasil dihapus.');
            },
        });
    }
};

const stats = computed(() => ({
    total: props.potentials.total,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Potensi Desa" />

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
                            <TrendingUp class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Potensi Desa</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola data potensi dan sumber daya desa</p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/potensi/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Potensi
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Potensi</p>
                </div>
            </div>

            <!-- Search -->
            <div class="relative mt-6 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <Input
                        v-model="search"
                        placeholder="Cari potensi..."
                        class="rounded-xl border-zinc-200 pl-9 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                        @keyup.enter="performSearch"
                    />
                </div>
                <Button variant="outline" class="rounded-xl" @click="performSearch">Cari</Button>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="potentials.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <TrendingUp class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada potensi desa</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan data potensi desa baru.</p>
            <Button as="a" href="/admin/potensi/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                <Plus class="h-4 w-4" />
                Tambah Potensi
            </Button>
        </div>

        <!-- Data table -->
        <div v-else class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Lokasi</th>
                            <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in potentials.data"
                            :key="item.id"
                            class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                        >
                            <td class="px-4 py-3 text-zinc-500">{{ (potentials.from ?? 1) + index }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ item.nama }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ item.category?.nama || '-' }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ item.lokasi }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/potensi/${item.id}/edit`" class="rounded-lg">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(item.id)">
                                        <Trash2 class="h-4 w-4 text-red-500" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1 && potentials.data.length > 0" class="flex flex-col items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-zinc-800 sm:flex-row">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ potentials.from }}–{{ potentials.to }} dari {{ potentials.total }}
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

        <!-- Delete Confirm -->
        <Dialog :open="deleteConfirmId !== null" @update:open="() => (deleteConfirmId = null)">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus potensi ini? Tindakan ini tidak dapat dibatalkan.
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
