<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
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
import { Plus, Pencil, Trash2, Images, Film, ArrowUp } from '@lucide/vue';

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

const deleteForm = useForm({});

const dialogTitle = computed(() => (editingGallery.value ? 'Edit Galeri' : 'Tambah Galeri'));
const videoDialogTitle = computed(() => (editingVideo.value ? 'Edit Video' : 'Tambah Video'));

const stats = computed(() => {
    const total = props.galleries.total;
    const totalPhotos = props.galleries.data.reduce((sum, g) => sum + (g.images?.length || 0), 0);
    return { total, totalPhotos };
});

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
        deleteForm.delete(`/admin/galeri/${deleteConfirmId.value}`, {
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
    deleteForm.delete(`/admin/galeri/${image.galeri_id}/foto/${image.id}`, {
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
    deleteForm.delete(`/admin/galeri/${video.galeri_id}/video/${video.id}`, {
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

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Galeri" />

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
                            <Images class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Galeri Foto</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola album dan foto galeri desa</p>
                        </div>
                    </div>
                </div>
                <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah Album
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-2 gap-3 sm:gap-4">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Album</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.totalPhotos }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total Foto</p>
                </div>
            </div>
        </div>

        <!-- Album Grid -->
        <div class="space-y-6">
            <!-- Empty state -->
            <div v-if="galleries.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <Images class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada album</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Buat album galeri pertama untuk desa.</p>
                <Button class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah Album
                </Button>
            </div>

            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="gallery in galleries.data"
                    :key="gallery.id"
                    class="group overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <div class="aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <img
                            v-if="gallery.thumbnail"
                            :src="`/storage/${gallery.thumbnail}`"
                            :alt="gallery.nama"
                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                        />
                        <div v-else class="flex h-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                            <Images class="size-12" />
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="mb-1 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 text-xs text-zinc-400 dark:text-zinc-500">
                                <Images class="h-3 w-3" /> {{ gallery.images?.length || 0 }} foto
                            </span>
                            <span v-if="(gallery.videos?.length || 0) > 0" class="inline-flex items-center gap-1 text-xs text-zinc-400 dark:text-zinc-500">
                                <Film class="h-3 w-3" /> {{ gallery.videos?.length || 0 }} video
                            </span>
                        </div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">{{ gallery.nama }}</h3>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ truncate(gallery.deskripsi, 80) }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            <Button variant="outline" size="sm" class="rounded-lg" @click="openPhotos(gallery)">
                                <Images class="h-3.5 w-3.5" />
                                Kelola Foto
                            </Button>
                            <Button variant="outline" size="sm" class="rounded-lg" @click="openVideoTab(gallery)">
                                <Film class="h-3.5 w-3.5" />
                                Video
                            </Button>
                            <Button variant="ghost" size="sm" class="rounded-lg" @click="openEditDialog(gallery)">
                                <Pencil class="h-3.5 w-3.5" />
                            </Button>
                            <Button variant="ghost" size="sm" class="rounded-lg" @click="confirmDelete(gallery)">
                                <Trash2 class="h-3.5 w-3.5 text-red-500" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="galleries.last_page > 1 && galleries.data.length > 0" class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-zinc-100 bg-white px-5 py-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Halaman {{ galleries.current_page }} dari {{ galleries.last_page }}
                </p>
                <div class="flex items-center gap-1">
                    <a
                        v-for="link in galleries.links"
                        :key="link.label"
                        :href="link.url || '#'"
                        class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm transition-colors"
                        :class="link.active
                            ? 'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900'
                            : !link.url
                                ? 'pointer-events-none text-zinc-300 dark:text-zinc-600'
                                : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                        v-html="link.label"
                    />
                </div>
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
                        <Input id="nama" v-model="form.nama" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="3"
                            class="flex w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:border-rose-300 focus:outline-none focus:ring-1 focus:ring-rose-200 dark:border-zinc-700"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="thumbnail">Thumbnail</Label>
                        <Input id="thumbnail" type="file" accept="image/*" class="rounded-xl border-zinc-200 dark:border-zinc-700" @change="onFileChange" />
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" class="rounded-full" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" class="rounded-full bg-rose-500 text-white hover:bg-rose-600" :disabled="form.processing">
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
                    <form @submit.prevent="submitImages" class="flex items-end gap-3" enctype="multipart/form-data">
                        <div class="grid flex-1 gap-1.5">
                            <Label for="images">Upload Foto</Label>
                            <Input id="images" type="file" accept="image/*" multiple class="rounded-xl border-zinc-200 dark:border-zinc-700" @change="onImagesChange" />
                        </div>
                        <Button type="submit" class="rounded-full bg-rose-500 text-white hover:bg-rose-600" :disabled="imageForm.processing || imageForm.images.length === 0">
                            Upload
                        </Button>
                    </form>

                    <div v-if="photoGallery?.images?.length" class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        <div
                            v-for="image in photoGallery.images"
                            :key="image.id"
                            class="group relative aspect-square overflow-hidden rounded-xl border border-zinc-100 dark:border-zinc-800"
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
                                <Trash2 class="h-3 w-3" />
                            </Button>
                        </div>
                    </div>
                    <p v-else class="py-8 text-center text-sm text-zinc-400 dark:text-zinc-500">Belum ada foto.</p>
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
                        <Button size="sm" class="rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAddVideo">
                            <Plus class="h-4 w-4" />
                            Tambah
                        </Button>
                    </div>
                </DialogHeader>

                <div class="space-y-3">
                    <div v-if="videoGallery?.videos?.length">
                        <div
                            v-for="video in videoGallery.videos"
                            :key="video.id"
                            class="flex items-center justify-between rounded-xl border border-zinc-100 p-3 dark:border-zinc-800"
                        >
                            <div class="min-w-0 flex-1">
                                <h4 class="font-medium text-zinc-900 dark:text-white">{{ video.judul }}</h4>
                                <p class="truncate text-xs text-zinc-500 dark:text-zinc-400">{{ video.url }}</p>
                                <p v-if="video.deskripsi" class="text-xs text-zinc-400 dark:text-zinc-500">{{ video.deskripsi }}</p>
                            </div>
                            <div class="flex shrink-0 gap-1">
                                <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEditVideo(video)">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="deleteVideo(video)">
                                    <Trash2 class="h-4 w-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="py-8 text-center text-sm text-zinc-400 dark:text-zinc-500">Belum ada video.</p>
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
                        <Input id="videoJudul" v-model="videoForm.judul" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="videoUrl">URL</Label>
                        <Input id="videoUrl" v-model="videoForm.url" required placeholder="https://www.youtube.com/watch?v=..." class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="videoDeskripsi">Deskripsi</Label>
                        <textarea
                            id="videoDeskripsi"
                            v-model="videoForm.deskripsi"
                            rows="2"
                            class="flex w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus:border-rose-300 focus:outline-none focus:ring-1 focus:ring-rose-200 dark:border-zinc-700"
                        ></textarea>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" class="rounded-full" @click="videoDialogOpen = false">Batal</Button>
                        <Button type="submit" class="rounded-full bg-rose-500 text-white hover:bg-rose-600" :disabled="videoForm.processing">
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
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" :disabled="deleteForm.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
