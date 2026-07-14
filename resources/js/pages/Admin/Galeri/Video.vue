<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pencil, Trash2 } from '@lucide/vue';
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
        form.delete('/admin/video/' + deleteConfirmId.value, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Video berhasil dihapus.');
            },
        });
    }
};
</script>

<template>
    <Head title="Video" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Video</h1>
            <Button @click="openAddDialog">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Video
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Video</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
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
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ video.judul }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-zinc-500 max-w-[200px] truncate">{{ video.url }}</td>
                                <td class="px-4 py-3 text-zinc-500 max-w-[200px] truncate">{{ video.deskripsi || '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openEditDialog(video)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(video)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="videos.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada video.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

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
                        <Input id="judul" v-model="form.judul" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="url">URL</Label>
                        <Input id="url" v-model="form.url" required placeholder="https://www.youtube.com/watch?v=..." />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="2"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
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
                    <Button variant="outline" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" :disabled="form.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
