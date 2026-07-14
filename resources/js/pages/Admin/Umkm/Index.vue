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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Plus, Pencil, Trash2, Images } from '@lucide/vue';

interface UmkmCategory {
    id: number;
    nama: string;
}

interface UmkmImage {
    id: number;
    umkm_id: number;
    image: string;
    caption: string | null;
}

interface Umkm {
    id: number;
    nama_usaha: string;
    umkm_category_id: number;
    pemilik: string;
    alamat: string | null;
    telepon: string | null;
    deskripsi: string | null;
    thumbnail: string | null;
    latitude: string | null;
    longitude: string | null;
    category: UmkmCategory | null;
    images: UmkmImage[];
}

interface Paginated {
    data: Umkm[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    umkms: Paginated;
    categories: UmkmCategory[];
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingUmkm = ref<Umkm | null>(null);
const photoViewOpen = ref(false);
const photoUmkm = ref<Umkm | null>(null);
const categoryDialogOpen = ref(false);
const editingCategory = ref<UmkmCategory | null>(null);
const categoryDeleteId = ref<number | null>(null);

const form = useForm({
    nama_usaha: '',
    umkm_category_id: '',
    pemilik: '',
    alamat: '',
    telepon: '',
    deskripsi: '',
    thumbnail: null as File | null,
    latitude: '',
    longitude: '',
});

const categoryForm = useForm({
    nama: '',
});

const imageForm = useForm({
    image: null as File | null,
});

const dialogTitle = computed(() => (editingUmkm.value ? 'Edit UMKM' : 'Tambah UMKM'));
const categoryDialogTitle = computed(() => (editingCategory.value ? 'Edit Kategori' : 'Tambah Kategori'));

const openAddDialog = () => {
    editingUmkm.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (item: Umkm) => {
    editingUmkm.value = item;
    form.nama_usaha = item.nama_usaha;
    form.umkm_category_id = String(item.umkm_category_id);
    form.pemilik = item.pemilik;
    form.alamat = item.alamat || '';
    form.telepon = item.telepon || '';
    form.deskripsi = item.deskripsi || '';
    form.thumbnail = null;
    form.latitude = item.latitude || '';
    form.longitude = item.longitude || '';
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    const data = {
        nama_usaha: form.nama_usaha,
        umkm_category_id: form.umkm_category_id,
        pemilik: form.pemilik,
        alamat: form.alamat,
        telepon: form.telepon,
        deskripsi: form.deskripsi,
        latitude: form.latitude,
        longitude: form.longitude,
    };

    if (editingUmkm.value) {
        form.transform(() => form.thumbnail ? { ...data, thumbnail: form.thumbnail } : data).put(`/admin/umkm/${editingUmkm.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('UMKM berhasil diperbarui.');
            },
        });
    } else {
        form.transform(() => form.thumbnail ? { ...data, thumbnail: form.thumbnail } : data).post('/admin/umkm', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('UMKM berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (item: Umkm) => {
    deleteConfirmId.value = item.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/umkm/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('UMKM berhasil dihapus.');
            },
        });
    }
};

const openPhotos = (item: Umkm) => {
    photoUmkm.value = item;
    imageForm.reset();
    imageForm.clearErrors();
    photoViewOpen.value = true;
};

const uploadImage = () => {
    if (!photoUmkm.value || !imageForm.image) return;
    imageForm.transform((data) => ({ image: data.image })).post(`/admin/umkm/${photoUmkm.value.id}/foto`, {
        onSuccess: () => {
            imageForm.reset();
            toast.success('Foto berhasil diupload.');
        },
    });
};

const deleteImage = (image: UmkmImage) => {
    form.delete(`/admin/umkm/${image.umkm_id}/foto/${image.id}`, {
        onSuccess: () => {
            toast.success('Foto berhasil dihapus.');
        },
    });
};

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        form.thumbnail = input.files[0];
    }
};

const onImageChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        imageForm.image = input.files[0];
    }
};

// Category CRUD
const openAddCategory = () => {
    editingCategory.value = null;
    categoryForm.reset();
    categoryForm.clearErrors();
    categoryDialogOpen.value = true;
};

const openEditCategory = (cat: UmkmCategory) => {
    editingCategory.value = cat;
    categoryForm.nama = cat.nama;
    categoryForm.clearErrors();
    categoryDialogOpen.value = true;
};

const submitCategory = () => {
    if (editingCategory.value) {
        categoryForm.put(`/admin/umkm/kategori/${editingCategory.value.id}`, {
            onSuccess: () => {
                categoryDialogOpen.value = false;
                toast.success('Kategori berhasil diperbarui.');
            },
        });
    } else {
        categoryForm.post('/admin/umkm/kategori', {
            onSuccess: () => {
                categoryDialogOpen.value = false;
                toast.success('Kategori berhasil ditambahkan.');
            },
        });
    }
};

const confirmDeleteCategory = (cat: UmkmCategory) => {
    categoryDeleteId.value = cat.id;
};

const executeDeleteCategory = () => {
    if (categoryDeleteId.value) {
        categoryForm.delete(`/admin/umkm/kategori/${categoryDeleteId.value}`, {
            onSuccess: () => {
                categoryDeleteId.value = null;
                toast.success('Kategori berhasil dihapus.');
            },
        });
    }
};
</script>

<template>
    <Head title="UMKM" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">UMKM</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah UMKM
            </Button>
        </div>

        <!-- Category Management -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between pb-3">
                <CardTitle class="text-base">Kategori UMKM</CardTitle>
                <Button size="sm" variant="outline" @click="openAddCategory">
                    <Plus class="size-3" />
                    Tambah Kategori
                </Button>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-2 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-2 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-2 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(cat, index) in categories"
                                :key="cat.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-2 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-2 font-medium">{{ cat.nama }}</td>
                                <td class="px-4 py-2 text-right">
                                    <Button variant="ghost" size="icon-sm" @click="openEditCategory(cat)">
                                        <Pencil class="size-3" />
                                    </Button>
                                    <Button variant="ghost" size="icon-sm" @click="confirmDeleteCategory(cat)">
                                        <Trash2 class="size-3 text-red-500" />
                                    </Button>
                                </td>
                            </tr>
                            <tr v-if="categories.length === 0">
                                <td colspan="3" class="px-4 py-4 text-center text-zinc-500">Belum ada kategori.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- UMKM Table -->
        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar UMKM</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama Usaha</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Pemilik</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kontak</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in umkms.data"
                                :key="item.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (umkms.current_page - 1) * umkms.data.length + index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ item.nama_usaha }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.category?.nama || '-' }}</td>
                                <td class="px-4 py-3">{{ item.pemilik }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.telepon || item.alamat || '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openPhotos(item)">
                                            <Images class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="openEditDialog(item)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(item)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="umkms.data.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada UMKM.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="umkms.last_page > 1" class="flex items-center justify-between border-t px-4 py-3">
                    <span class="text-sm text-zinc-500">
                        Halaman {{ umkms.current_page }} dari {{ umkms.last_page }}
                    </span>
                    <div class="flex gap-1">
                        <a
                            v-for="link in umkms.links"
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
            </CardContent>
        </Card>

        <!-- Add/Edit UMKM Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingUmkm ? 'Perbarui informasi UMKM.' : 'Tambahkan UMKM baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="nama_usaha">Nama Usaha</Label>
                        <Input id="nama_usaha" v-model="form.nama_usaha" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="umkm_category_id">Kategori</Label>
                        <Select v-model="form.umkm_category_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih kategori" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                    {{ cat.nama }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="pemilik">Pemilik</Label>
                        <Input id="pemilik" v-model="form.pemilik" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="alamat">Alamat</Label>
                            <Input id="alamat" v-model="form.alamat" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="telepon">Telepon</Label>
                            <Input id="telepon" v-model="form.telepon" />
                        </div>
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
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="latitude">Latitude</Label>
                            <Input id="latitude" v-model="form.latitude" placeholder="cth: -6.1754" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="longitude">Longitude</Label>
                            <Input id="longitude" v-model="form.longitude" placeholder="cth: 106.8272" />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingUmkm ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Photo Management Dialog -->
        <Dialog v-model:open="photoViewOpen">
            <DialogContent class="sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle v-if="photoUmkm">Kelola Foto - {{ photoUmkm.nama_usaha }}</DialogTitle>
                    <DialogDescription>Upload dan kelola foto UMKM.</DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <form @submit.prevent="uploadImage" class="flex items-end gap-3">
                        <div class="grid flex-1 gap-1.5">
                            <Label for="umkmImage">Upload Foto</Label>
                            <Input id="umkmImage" type="file" accept="image/*" @change="onImageChange" />
                        </div>
                        <Button type="submit" :disabled="imageForm.processing || !imageForm.image">
                            Upload
                        </Button>
                    </form>

                    <div v-if="photoUmkm?.images?.length" class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        <div
                            v-for="image in photoUmkm.images"
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

        <!-- Category Dialog -->
        <Dialog v-model:open="categoryDialogOpen">
            <DialogContent class="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>{{ categoryDialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingCategory ? 'Perbarui nama kategori.' : 'Tambahkan kategori UMKM baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitCategory" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="categoryNama">Nama Kategori</Label>
                        <Input id="categoryNama" v-model="categoryForm.nama" required />
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="categoryDialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="categoryForm.processing">
                            {{ editingCategory ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus UMKM ini? Tindakan ini tidak dapat dibatalkan.
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

        <!-- Category Delete Confirm -->
        <Dialog :open="categoryDeleteId !== null" @update:open="categoryDeleteId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus kategori ini?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="categoryDeleteId = null">Batal</Button>
                    <Button variant="destructive" @click="executeDeleteCategory">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
