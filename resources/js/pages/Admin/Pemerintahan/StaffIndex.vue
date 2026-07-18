 <script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';
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
import { Plus, Pencil, Trash2, ArrowUp, Users, GraduationCap } from '@lucide/vue';

interface StaffItem {
    id: number;
    nama: string;
    nip: string | null;
    jabatan: string;
    foto: string | null;
    alamat: string | null;
    telepon: string | null;
    pendidikan_terakhir: string | null;
    tanggal_mulai_jabatan: string | null;
    tanggal_akhir_jabatan: string | null;
    urutan: number | null;
}

const props = defineProps<{
    staff: StaffItem[];
}>();

const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/pemerintahan/${deleteConfirmId.value}`, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Perangkat desa berhasil dihapus.');
            },
            onError: () => toast.error('Gagal menghapus.'),
        });
    }
};

const stats = computed(() => ({
    total: props.staff.length,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Perangkat Desa" />

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
        <div
            class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12"
        >
            <div
                class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10"
                aria-hidden="true"
            />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400"
                        >
                            <Users class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                                Perangkat Desa
                            </h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                                Kelola data perangkat dan staf desa
                            </p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/pemerintahan/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Perangkat
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-5 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <Users class="h-5 w-5 text-zinc-400" />
                    <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</span>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Perangkat</span>
                </div>
            </div>
        </div>

        <!-- Staff grid -->
        <div v-if="staff.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <Users class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada perangkat desa</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan perangkat desa pertama.</p>
            <Button as="a" href="/admin/pemerintahan/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                <Plus class="h-4 w-4" />
                Tambah Perangkat
            </Button>
        </div>

        <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="item in staff"
                :key="item.id"
                class="group rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div class="flex items-start gap-4">
                    <!-- Foto -->
                    <div class="shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                        <img
                            v-if="item.foto"
                            :src="`/storage/${item.foto}`"
                            :alt="item.nama"
                            class="h-16 w-16 object-cover transition duration-300 group-hover:scale-105 sm:h-20 sm:w-20"
                        />
                        <div v-else class="flex h-16 w-16 items-center justify-center text-zinc-300 dark:text-zinc-600 sm:h-20 sm:w-20">
                            <Users class="h-8 w-8" />
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-zinc-900 dark:text-white">{{ item.nama }}</h3>
                        <p class="text-sm text-rose-500">{{ item.jabatan }}</p>

                        <div class="mt-2 space-y-1 text-xs text-zinc-400 dark:text-zinc-500">
                            <p v-if="item.nip" class="truncate">NIP: {{ item.nip }}</p>
                            <p v-if="item.pendidikan_terakhir" class="inline-flex items-center gap-1">
                                <GraduationCap class="h-3 w-3" /> {{ item.pendidikan_terakhir }}
                            </p>
                        </div>

                        <div class="mt-3 flex gap-1">
                            <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/pemerintahan/${item.id}/edit`" class="rounded-lg">
                                <Pencil class="h-3.5 w-3.5" />
                            </Button>
                            <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(item.id)">
                                <Trash2 class="h-3.5 w-3.5 text-red-500" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation -->
        <Dialog :open="deleteConfirmId !== null" @update:open="(v) => { if (!v) deleteConfirmId = null; }">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus perangkat desa ini? Tindakan ini tidak dapat dibatalkan.
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
