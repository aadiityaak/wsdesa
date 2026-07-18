<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Pencil, Trash2, Construction, ArrowUp } from '@lucide/vue';

interface DevCategory {
    id: number;
    nama: string;
}

interface Development {
    id: number;
    nama: string;
    development_category_id: number;
    lokasi: string;
    deskripsi: string | null;
    anggaran: number;
    sumber_dana: string;
    tahun: string;
    status: string;
    latitude: string | null;
    longitude: string | null;
    category: DevCategory | null;
}

const props = defineProps<{
    developments: Development[];
    categories: DevCategory[];
}>();

const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/pembangunan/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Pembangunan berhasil dihapus.');
            },
        });
    }
};

const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

const statusVariant = (status: string) => {
    switch (status) {
        case 'rencana': return 'secondary' as const;
        case 'berjalan': return 'default' as const;
        case 'selesai': return 'default' as const;
        default: return 'secondary' as const;
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'rencana': return 'Rencana';
        case 'berjalan': return 'Berjalan';
        case 'selesai': return 'Selesai';
        default: return status;
    }
};

const stats = computed(() => ({
    total: props.developments.length,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Pembangunan" />

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
                            <Construction class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Pembangunan</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola data pembangunan desa</p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/pembangunan/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Pembangunan
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-5 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <Construction class="h-5 w-5 text-zinc-400" />
                    <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</span>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Proyek</span>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="developments.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <Construction class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada data pembangunan</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan data pembangunan desa.</p>
            <Button as="a" href="/admin/pembangunan/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                <Plus class="h-4 w-4" />
                Tambah Pembangunan
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
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Anggaran</th>
                            <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(item, index) in developments"
                            :key="item.id"
                            class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                        >
                            <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                <div>{{ item.nama }}</div>
                                <div class="text-xs text-zinc-400">{{ item.lokasi }}</div>
                            </td>
                            <td class="px-4 py-3 text-zinc-500">{{ item.category?.nama || '-' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    :class="{
                                        'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300': item.status === 'rencana',
                                        'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400': item.status === 'berjalan',
                                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': item.status === 'selesai',
                                    }"
                                >
                                    {{ statusLabel(item.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-sm text-zinc-700 dark:text-zinc-300">{{ formatRupiah(item.anggaran) }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/pembangunan/${item.id}/edit`" class="rounded-lg">
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
        </div>

        <!-- Delete Confirm -->
        <Dialog :open="deleteConfirmId !== null" @update:open="() => (deleteConfirmId = null)">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus pembangunan ini? Tindakan ini tidak dapat dibatalkan.
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
