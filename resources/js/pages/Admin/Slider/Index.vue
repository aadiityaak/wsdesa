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
import { Plus, Pencil, Trash2, SlidersHorizontal, ArrowUp, ImageIcon } from '@lucide/vue';

interface Slider {
    id: number;
    judul: string;
    deskripsi: string | null;
    image: string;
    link: string | null;
    is_active: boolean;
    urutan: number;
}

const props = defineProps<{
    sliders: Slider[];
}>();

const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/slider/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Slider berhasil dihapus.');
            },
        });
    }
};

const stats = computed(() => ({
    total: props.sliders.length,
    active: props.sliders.filter((s) => s.is_active !== false).length,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Slider" />

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
                            <SlidersHorizontal class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Slider</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola slider halaman depan</p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/slider/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Slider
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.active }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Aktif</p>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="sliders.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <SlidersHorizontal class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada slider</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan slider untuk halaman depan.</p>
            <Button as="a" href="/admin/slider/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                <Plus class="h-4 w-4" />
                Tambah Slider
            </Button>
        </div>

        <!-- Card Grid -->
        <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <div
                v-for="item in sliders"
                :key="item.id"
                class="group overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
            >
                <!-- Image -->
                <div class="aspect-[16/9] bg-zinc-100 dark:bg-zinc-800">
                    <img
                        v-if="item.image"
                        :src="`/storage/${item.image}`"
                        :alt="item.judul"
                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                    />
                    <div v-else class="flex h-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                        <ImageIcon class="h-10 w-10" />
                    </div>
                </div>

                <!-- Content -->
                <div class="p-4">
                    <div class="mb-2 flex items-center gap-2">
                        <span class="text-xs text-zinc-400">Urutan {{ item.urutan }}</span>
                        <span
                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                            :class="item.is_active !== false ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300'"
                        >
                            {{ item.is_active !== false ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white truncate">{{ item.judul }}</h3>
                    <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500 line-clamp-2">{{ item.deskripsi || '-' }}</p>

                    <div class="mt-3 flex items-center gap-1">
                        <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/slider/${item.id}/edit`" class="rounded-lg">
                            <Pencil class="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(item.id)">
                            <Trash2 class="h-4 w-4 text-red-500" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirm -->
        <Dialog :open="deleteConfirmId !== null" @update:open="() => (deleteConfirmId = null)">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus slider ini? Tindakan ini tidak dapat dibatalkan.
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
