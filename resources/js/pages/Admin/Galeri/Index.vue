<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Pencil, Trash2, Images, Film } from '@lucide/vue';

interface GalleryImage {
    id: number;
    galeri_id: number;
    image: string;
    caption: string | null;
}

interface Video {
    id: number;
    galeri_id: number;
    judul: string;
    url: string;
    deskripsi: string | null;
}

interface Gallery {
    id: number;
    nama: string;
    deskripsi: string | null;
    thumbnail: string | null;
    images: GalleryImage[];
    videos: Video[];
}

interface Paginated {
    data: Gallery[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    galleries: Paginated;
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingGallery = ref<Gallery | null>(null);
const photoViewOpen = ref(false);
const photoGallery = ref<Gallery | null>(null);
const videoViewOpen = ref(false);
const videoGallery = ref<Gallery | null>(null);
const videoDialogOpen = ref(false);
const editingVideo = ref<Video | null>(null);

const form = useForm({
    nama: '',
    deskripsi: '',
    thumbnail: null as File | null,
});

const imageForm = useForm({
    images: [] as File[],
});

const videoForm = useForm({
    judul: '',
    url: '',
    deskripsi: '',
});

const uploadImageForm = useForm({
    gambar: null as File | null,
});

const dialogTitle = computed(() => (editingGallery.value ? 'Edit Galeri' : 'Tambah Galeri'));
const videoDialogTitle = computed(() => (editingVideo.value ? 'Edit Video' : 'Tambah Video'));

const openAddDialog = () => {
    editingGallery.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (gallery: Gallery) => {
    editingGallery.value = gallery;
    form.nama = gallery.nama;
    form.deskripsi = gallery.deskripsi || '';
    form.thumbnail = null;
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    const data: Record<string, unknown> = {
        nama: form.nama,
        deskripsi: form.deskripsi,
    };
    if (form.thumbnail) {
        data.thumbnail = form.thumbnail;
    }

    if (editingGallery.value) {
        form.transform(() => data).put(`/admin/galeri/${editingGallery.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Galeri berhasil diperbarui.');
            },
        });
    } else {
        form.transform(() => data).post('/admin/galeri', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Galeri berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (gallery: Gallery) => {
    deleteConfirmId.value = gallery.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/galeri/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Galeri berhasil dihapus.');
            },
        });
    }
};

const openPhotos = (gallery: Gallery) => {
    photoGallery.value = gallery;
    imageForm.reset();
    imageForm.clearErrors();
    photoViewOpen.value = true;
};

const submitImages = () => {
    if (!photoGallery.value) return;
    imageForm.transform((data) => ({ images: data.images })).post(`/admin/galeri/${photoGallery.value.id}/foto`, {
        onSuccess: () => {
            imageForm.reset();
            toast.success('Foto berhasil diupload.');
        },
    });
};

const deleteImage = (image: GalleryImage) => {
    form.delete(`/admin/galeri/${image.galeri_id}/foto/${image.id}`, {
        onSuccess: () => {
            toast.success('Foto berhasil dihapus.');
        },
    });
};

const openVideoTab = (gallery: Gallery) => {
    videoGallery.value = gallery;
    videoViewOpen.value = true;
};

const openAddVideo = () => {
    editingVideo.value = null;
    videoForm.reset();
    videoForm.clearErrors();
    videoDialogOpen.value = true;
};

const openEditVideo = (video: Video) => {
    editingVideo.value = video;
    videoForm.judul = video.judul;
    videoForm.url = video.url;
    videoForm.deskripsi = video.deskripsi || '';
    videoForm.clearErrors();
    videoDialogOpen.value = true;
};

const submitVideo = () => {
    if (!videoGallery.value) return;
    if (editingVideo.value) {
        videoForm.put(`/admin/galeri/${videoGallery.value.id}/video/${editingVideo.value.id}`, {
            onSuccess: () => {
                videoDialogOpen.value = false;
                toast.success('Video berhasil diperbarui.');
            },
        });
    } else {
        videoForm.post(`/admin/galeri/${videoGallery.value.id}/video`, {
            onSuccess: () => {
                videoDialogOpen.value = false;
                toast.success('Video berhasil ditambahkan.');
            },
        });
    }
};

const deleteVideo = (video: Video) => {
    form.delete(`/admin/galeri/${video.galeri_id}/video/${video.id}`, {
        onSuccess: () => {
            toast.success('Video berhasil dihapus.');
        },
    });
};

const truncate = (text: string | null, max: number) => {
    if (!text) return '-';
    return text.length > max ? text.substring(0, max) + '...' : text;
};

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        form.thumbnail = input.files[0];
    }
};

const onImagesChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files) {
        imageForm.images = Array.from(input.files);
    }
};
</script>

<template>
    <Head title="Galeri" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Galeri</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Galeri
            </Button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <Card v-for="(gallery, index) in galleries.data" :key="gallery.id" class="overflow-hidden">
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800">
                    <img
                        v-if="gallery.thumbnail"
                        :src="`/storage/${gallery.thumbnail}`"
                        :alt="gallery.nama"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full items-center justify-center text-zinc-400">
                        <Images class="size-10" />
                    </div>
                </div>
                <CardContent class="pt-4">
                    <div class="mb-1 flex items-center justify-between">
                        <span class="text-xs text-zinc-400">#{{ (galleries.current_page - 1) * galleries.data.length + index + 1 }}</span>
                        <span class="text-xs text-zinc-400">{{ gallery.images?.length || 0 }} foto</span>
                    </div>
                    <h3 class="mb-1 font-semibold">{{ gallery.nama }}</h3>
                    <p class="mb-3 text-sm text-zinc-500">{{ truncate(gallery.deskripsi, 80) }}</p>
                    <div class="flex flex-wrap gap-1">
                        <Button variant="outline" size="sm" @click="openPhotos(gallery)">
                            <Images class="size-3" />
                            Kelola Foto
                        </Button>
                        <Button variant="outline" size="sm" @click="openVideoTab(gallery)">
                            <Film class="size-3" />
                            Video
                        </Button>
                        <Button variant="ghost" size="sm" @click="openEditDialog(gallery)">
                            <Pencil class="size-3" />
                        </Button>
                        <Button variant="ghost" size="sm" @click="confirmDelete(gallery)">
                            <Trash2 class="size-3 text-red-500" />
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="galleries.data.length === 0" class="py-12 text-center text-zinc-500">
            Belum ada galeri.
        </div>

        <div v-if="galleries.last_page > 1" class="flex items-center justify-between">
            <span class="text-sm text-zinc-500">
                Halaman {{ galleries.current_page }} dari {{ galleries.last_page }}
            </span>
            <div class="flex gap-1">
                <a
                    v-for="link in galleries.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    class="rounded-md px-3 py-1 text-sm transition-colors"
                    :class="link.active
                        ? 'bg-primary text-primary-foreground'
                        : !link.url
                            ? 'text-zinc-300 pointer-events-none'
                            : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                    v-html="link.label"
                />
            </div>
        </div>

        <!-- Add/Edit Gallery Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingGallery ? 'Perbarui informasi galeri.' : 'Tambahkan galeri baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4" enctype="multipart/form-data">
                    <div class="grid gap-1.5">
                        <Label for="nama">Nama</Label>
                        <Input id="nama" v-model="form.nama" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="3"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="thumbnail">Thumbnail</Label>
                        <Input id="thumbnail" type="file" accept="image/*" @change="onFileChange" />
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingGallery ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Photo Management Dialog -->
        <Dialog v-model:open="photoViewOpen">
            <DialogContent class="sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle v-if="photoGallery">Kelola Foto - {{ photoGallery.nama }}</DialogTitle>
                    <DialogDescription>Upload dan kelola foto galeri.</DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <!-- Upload Form -->
                    <form @submit.prevent="submitImages" class="flex items-end gap-3" enctype="multipart/form-data">
                        <div class="grid flex-1 gap-1.5">
                            <Label for="images">Upload Foto</Label>
                            <Input id="images" type="file" accept="image/*" multiple @change="onImagesChange" />
                        </div>
                        <Button type="submit" :disabled="imageForm.processing || imageForm.images.length === 0">
                            Upload
                        </Button>
                    </form>

                    <!-- Image Grid -->
                    <div v-if="photoGallery?.images?.length" class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        <div
                            v-for="image in photoGallery.images"
                            :key="image.id"
                            class="group relative aspect-square overflow-hidden rounded-lg border"
                        >
                            <img
                                :src="`/storage/${image.image}`"
                                :alt="image.caption || ''"
                                class="h-full w-full object-cover"
                            />
                            <Button
                                variant="destructive"
                                size="sm"
                                class="absolute right-1 top-1 opacity-0 transition-opacity group-hover:opacity-100"
                                @click="deleteImage(image)"
                            >
                                <Trash2 class="size-3" />
                            </Button>
                        </div>
                    </div>
                    <p v-else class="py-8 text-center text-sm text-zinc-400">Belum ada foto.</p>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Video Tab Dialog -->
        <Dialog v-model:open="videoViewOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <DialogTitle v-if="videoGallery">Video - {{ videoGallery.nama }}</DialogTitle>
                            <DialogDescription>Kelola video galeri.</DialogDescription>
                        </div>
                        <Button size="sm" @click="openAddVideo">
                            <Plus class="size-4" />
                            Tambah
                        </Button>
                    </div>
                </DialogHeader>

                <div class="space-y-3">
                    <div v-if="videoGallery?.videos?.length">
                        <div
                            v-for="video in videoGallery.videos"
                            :key="video.id"
                            class="flex items-center justify-between rounded-lg border p-3"
                        >
                            <div class="flex-1">
                                <h4 class="font-medium">{{ video.judul }}</h4>
                                <p class="text-xs text-zinc-500">{{ video.url }}</p>
                                <p v-if="video.deskripsi" class="text-xs text-zinc-400">{{ video.deskripsi }}</p>
                            </div>
                            <div class="flex gap-1">
                                <Button variant="ghost" size="icon-sm" @click="openEditVideo(video)">
                                    <Pencil class="size-4" />
                                </Button>
                                <Button variant="ghost" size="icon-sm" @click="deleteVideo(video)">
                                    <Trash2 class="size-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="py-8 text-center text-sm text-zinc-400">Belum ada video.</p>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Add/Edit Video Dialog -->
        <Dialog v-model:open="videoDialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ videoDialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingVideo ? 'Perbarui informasi video.' : 'Tambahkan video baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitVideo" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="videoJudul">Judul</Label>
                        <Input id="videoJudul" v-model="videoForm.judul" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="videoUrl">URL</Label>
                        <Input id="videoUrl" v-model="videoForm.url" required placeholder="https://www.youtube.com/watch?v=..." />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="videoDeskripsi">Deskripsi</Label>
                        <textarea
                            id="videoDeskripsi"
                            v-model="videoForm.deskripsi"
                            rows="2"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="videoDialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="videoForm.processing">
                            {{ editingVideo ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirm Dialog -->
        <Dialog :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus galeri ini? Tindakan ini tidak dapat dibatalkan.
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
