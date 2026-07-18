<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pencil, Trash2, Film, Plus, ArrowUp } from '@lucide/vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Video {
    id: number;
    judul: string;
    url: string;
    deskripsi: string | null;
}

const props = defineProps<{
    videos: Video[];
}>();

const dialogOpen = ref(false);
const editingVideo = ref<Video | null>(null);
const deleteConfirmId = ref<number | null>(null);

const form = useForm({
    judul: '',
    url: '',
    deskripsi: '',
});

const deleteForm = useForm({});

const dialogTitle = computed(() => (editingVideo.value ? 'Edit Video' : 'Tambah Video'));

const openAddDialog = () => {
    editingVideo.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (video: Video) => {
    editingVideo.value = video;
    form.judul = video.judul;
    form.url = video.url;
    form.deskripsi = video.deskripsi || '';
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingVideo.value) {
        form.put('/admin/video/' + editingVideo.value.id, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Video berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/video', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Video berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (video: Video) => {
    deleteConfirmId.value = video.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete('/admin/video/' + deleteConfirmId.value, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Video berhasil dihapus.');
            },
        });
    }
};

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Video" />

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
                            <Film class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Galeri Video</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola video galeri desa</p>
                        </div>
                    </div>
                </div>
                <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah Video
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <Film class="h-5 w-5 text-rose-500 dark:text-rose-400" />
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ videos.length }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Video</p>
                </div>
            </div>
        </div>

        <!-- Video List -->
        <div class="space-y-6">
            <!-- Empty state -->
            <div v-if="videos.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <Film class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada video</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan video galeri pertama untuk desa.</p>
                <Button class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah Video
                </Button>
            </div>

            <!-- Video table -->
            <div v-else class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                <th class="w-12 px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">URL</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Deskripsi</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(video, index) in videos"
                                :key="video.id"
                                class="border-b border-zinc-50 transition-colors hover:bg-zinc-50/50 dark:border-zinc-800 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-400 dark:text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ video.judul }}</td>
                                <td class="max-w-[200px] truncate px-4 py-3 font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ video.url }}</td>
                                <td class="max-w-[200px] truncate px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ video.deskripsi || '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEditDialog(video)">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(video)">
                                            <Trash2 class="h-4 w-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add/Edit Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingVideo ? 'Perbarui informasi video.' : 'Tambahkan video baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="url">URL</Label>
                        <Input id="url" v-model="form.url" required placeholder="https://www.youtube.com/watch?v=..." class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="2"
                            class="flex w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:border-rose-300 focus:outline-none focus:ring-1 focus:ring-rose-200 dark:border-zinc-700"
                        ></textarea>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" class="rounded-full" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" class="rounded-full bg-rose-500 text-white hover:bg-rose-600" :disabled="form.processing">
                            {{ editingVideo ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirm -->
        <Dialog :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus video ini? Tindakan ini tidak dapat dibatalkan.
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
