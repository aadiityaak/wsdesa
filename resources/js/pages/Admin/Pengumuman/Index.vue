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
import { Plus, Pencil, Trash2, Megaphone, ArrowUp } from '@lucide/vue';

interface Announcement {
    id: number;
    judul: string;
    konten: string;
    tipe: string;
    tanggal_mulai: string;
    tanggal_selesai: string | null;
    is_active: boolean;
}

const props = defineProps<{
    announcements: Announcement[];
}>();

const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/pengumuman/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Pengumuman berhasil dihapus.');
            },
        });
    }
};

const tipeLabel = (tipe: string) => {
    switch (tipe) {
        case 'info': return 'Info';
        case 'warning': return 'Peringatan';
        case 'urgent': return 'Penting';
        default: return tipe;
    }
};

const tipeClass = (tipe: string) => {
    switch (tipe) {
        case 'info': return 'border-transparent bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'warning': return 'border-transparent bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400';
        case 'urgent': return 'border-transparent bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default: return 'border-transparent bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
};

const isActive = (item: Announcement) => {
    if (item.is_active === false) return false;
    if (!item.tanggal_selesai) return true;
    const now = new Date();
    const selesai = new Date(item.tanggal_selesai);
    return now <= selesai;
};

const stats = computed(() => ({
    total: props.announcements.length,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Pengumuman" />

    <div class="relative">
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

        <div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                            <Megaphone class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Pengumuman</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola pengumuman desa</p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/pengumuman/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Pengumuman
                </Button>
            </div>

            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-5 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <Megaphone class="h-5 w-5 text-zinc-400" />
                    <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</span>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Pengumuman</span>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="announcements.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <Megaphone class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada pengumuman</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan pengumuman untuk warga desa.</p>
            <Button as="a" href="/admin/pengumuman/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                <Plus class="h-4 w-4" />
                Tambah Pengumuman
            </Button>
        </div>

        <!-- Data table -->
        <div v-else class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tipe</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Masa Berlaku</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in announcements" :key="item.id" class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white max-w-xs truncate">{{ item.judul }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="tipeClass(item.tipe)">
                                    {{ tipeLabel(item.tipe) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-500 text-xs">{{ formatDate(item.tanggal_mulai) }} - {{ formatDate(item.tanggal_selesai) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="isActive(item) ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300'">
                                    {{ isActive(item) ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/pengumuman/${item.id}/edit`" class="rounded-lg">
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
                    <DialogDescription>Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" :disabled="deleteForm.processing" @click="executeDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
