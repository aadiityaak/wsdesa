<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Pencil, Trash2, Store, ArrowUp, Phone, MapPin, ImageIcon } from '@lucide/vue';

interface UmkmCategory {
    id: number;
    nama: string;
}

interface Umkm {
    id: number;
    nama_usaha: string;
    pemilik: string;
    umkm_category_id: number;
    alamat: string | null;
    telepon: string | null;
    deskripsi: string | null;
    thumbnail: string | null;
    category: UmkmCategory | null;
}

interface Paginated {
    data: Umkm[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    umkms: Paginated;
    categories: UmkmCategory[];
}>();

const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

const currentPage = computed(() => props.umkms.current_page);
const lastPage = computed(() => props.umkms.last_page);

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
    router.get('/admin/umkm', { page }, { preserveState: true });
};

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/umkm/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('UMKM berhasil dihapus.');
            },
        });
    }
};

const stats = computed(() => ({
    total: props.umkms.total,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="UMKM" />

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
                            <Store class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">UMKM</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola data usaha mikro kecil menengah</p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/umkm/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah UMKM
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-5 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <Store class="h-5 w-5 text-zinc-400" />
                    <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</span>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">UMKM</span>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="umkms.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <Store class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada UMKM</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan data UMKM desa.</p>
            <Button as="a" href="/admin/umkm/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                <Plus class="h-4 w-4" />
                Tambah UMKM
            </Button>
        </div>

        <!-- Data table -->
        <div v-else class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Usaha</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Pemilik</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Alamat</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Telepon</th>
                            <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in umkms.data"
                            :key="item.id"
                            class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                        >
                            <td class="px-4 py-3 text-zinc-500">{{ (umkms.from ?? 1) + index }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="shrink-0 overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800">
                                        <img
                                            v-if="item.thumbnail"
                                            :src="`/storage/${item.thumbnail}`"
                                            :alt="item.nama_usaha"
                                            class="h-8 w-8 object-cover"
                                        />
                                        <div v-else class="flex h-8 w-8 items-center justify-center text-zinc-300 dark:text-zinc-600">
                                            <Store class="h-4 w-4" />
                                        </div>
                                    </div>
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ item.nama_usaha }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-zinc-500">{{ item.pemilik }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                    {{ item.category?.nama || '-' }}
                                </span>
                            </td>
                            <td class="max-w-[200px] truncate px-4 py-3 text-zinc-500">{{ item.alamat || '-' }}</td>
                            <td class="px-4 py-3">
                                <span v-if="item.telepon" class="inline-flex items-center gap-1 text-zinc-500">
                                    <Phone class="h-3.5 w-3.5" /> {{ item.telepon }}
                                </span>
                                <span v-else class="text-zinc-400">-</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/umkm/${item.id}/edit`" class="rounded-lg">
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
            <div v-if="lastPage > 1 && umkms.data.length > 0" class="flex flex-col items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-zinc-800 sm:flex-row">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ umkms.from }}–{{ umkms.to }} dari {{ umkms.total }}
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
                        Apakah Anda yakin ingin menghapus UMKM ini? Tindakan ini tidak dapat dibatalkan.
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
