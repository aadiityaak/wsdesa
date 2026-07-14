<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
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

interface DevCategory {
    id: number;
    nama: string;
}

interface DevImage {
    id: number;
    pembangunan_id: number;
    image: string;
    caption: string | null;
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
    images: DevImage[];
}

interface Paginated {
    data: Development[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    developments: Paginated;
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingDevelopment = ref<Development | null>(null);
const photoViewOpen = ref(false);
const photoDev = ref<Development | null>(null);

const form = useForm({
    nama: '',
    development_category_id: '',
    lokasi: '',
    deskripsi: '',
    anggaran: '',
    sumber_dana: '',
    tahun: '',
    status: 'rencana',
    latitude: '',
    longitude: '',
});

const imageForm = useForm({
    image: null as File | null,
});

const dialogTitle = computed(() => (editingDevelopment.value ? 'Edit Pembangunan' : 'Tambah Pembangunan'));

const openAddDialog = () => {
    editingDevelopment.value = null;
    form.reset();
    form.tahun = new Date().getFullYear().toString();
    form.status = 'rencana';
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (item: Development) => {
    editingDevelopment.value = item;
    form.nama = item.nama;
    form.development_category_id = String(item.development_category_id);
    form.lokasi = item.lokasi;
    form.deskripsi = item.deskripsi || '';
    form.anggaran = String(item.anggaran);
    form.sumber_dana = item.sumber_dana;
    form.tahun = item.tahun;
    form.status = item.status;
    form.latitude = item.latitude || '';
    form.longitude = item.longitude || '';
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingDevelopment.value) {
        form.put(`/admin/pembangunan/${editingDevelopment.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Pembangunan berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/pembangunan', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Pembangunan berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (item: Development) => {
    deleteConfirmId.value = item.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/pembangunan/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Pembangunan berhasil dihapus.');
            },
        });
    }
};

const openPhotos = (item: Development) => {
    photoDev.value = item;
    imageForm.reset();
    imageForm.clearErrors();
    photoViewOpen.value = true;
};

const uploadImage = () => {
    if (!photoDev.value || !imageForm.image) return;
    imageForm.transform((data) => ({ image: data.image })).post(`/admin/pembangunan/${photoDev.value.id}/foto`, {
        onSuccess: () => {
            imageForm.reset();
            toast.success('Foto berhasil diupload.');
        },
    });
};

const deleteImage = (image: DevImage) => {
    form.delete(`/admin/pembangunan/${image.pembangunan_id}/foto/${image.id}`, {
        onSuccess: () => {
            toast.success('Foto berhasil dihapus.');
        },
    });
};

const onImageChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        imageForm.image = input.files[0];
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

const statusClass = (status: string) => {
    switch (status) {
        case 'rencana':
            return 'border-transparent bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
        case 'berjalan':
            return 'border-transparent bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'selesai':
            return 'border-transparent bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'tertunda':
            return 'border-transparent bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        default:
            return 'border-transparent bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'rencana':
            return 'Rencana';
        case 'berjalan':
            return 'Berjalan';
        case 'selesai':
            return 'Selesai';
        case 'tertunda':
            return 'Tertunda';
        default:
            return status;
    }
};
</script>

<template>
    <Head title="Pembangunan" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Pembangunan</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Pembangunan
            </Button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="(item, index) in developments.data" :key="item.id" class="overflow-hidden">
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800">
                    <img
                        v-if="item.images?.length"
                        :src="`/storage/${item.images[0].image}`"
                        :alt="item.nama"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full items-center justify-center text-zinc-400">
                        <Images class="size-10" />
                    </div>
                </div>
                <CardContent class="pt-4">
                    <div class="mb-2 flex items-center justify-between">
                        <Badge :class="statusClass(item.status)">{{ statusLabel(item.status) }}</Badge>
                        <span class="text-xs text-zinc-400">{{ item.tahun }}</span>
                    </div>
                    <h3 class="mb-1 font-semibold">{{ item.nama }}</h3>
                    <p class="mb-1 text-sm text-zinc-500">{{ item.category?.nama || '-' }} &middot; {{ item.lokasi }}</p>
                    <p class="mb-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">{{ formatRupiah(item.anggaran) }}</p>
                    <div class="flex flex-wrap gap-1">
                        <Button variant="outline" size="sm" @click="openPhotos(item)">
                            <Images class="size-3" />
                            Foto
                        </Button>
                        <Button variant="ghost" size="sm" @click="openEditDialog(item)">
                            <Pencil class="size-3" />
                        </Button>
                        <Button variant="ghost" size="sm" @click="confirmDelete(item)">
                            <Trash2 class="size-3 text-red-500" />
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="developments.data.length === 0" class="py-12 text-center text-zinc-500">
            Belum ada data pembangunan.
        </div>

        <div v-if="developments.last_page > 1" class="flex items-center justify-between">
            <span class="text-sm text-zinc-500">
                Halaman {{ developments.current_page }} dari {{ developments.last_page }}
            </span>
            <div class="flex gap-1">
                <a
                    v-for="link in developments.links"
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

        <!-- Add/Edit Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingDevelopment ? 'Perbarui data pembangunan.' : 'Tambahkan data pembangunan baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="nama">Nama</Label>
                        <Input id="nama" v-model="form.nama" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="development_category_id">Kategori</Label>
                        <Select v-model="form.development_category_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih kategori" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="1">Infrastruktur</SelectItem>
                                <SelectItem value="2">Pendidikan</SelectItem>
                                <SelectItem value="3">Kesehatan</SelectItem>
                                <SelectItem value="4">Ekonomi</SelectItem>
                                <SelectItem value="5">Sosial</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="lokasi">Lokasi</Label>
                        <Input id="lokasi" v-model="form.lokasi" required />
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
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="anggaran">Anggaran (Rp)</Label>
                            <Input id="anggaran" v-model="form.anggaran" required type="number" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="sumber_dana">Sumber Dana</Label>
                            <Input id="sumber_dana" v-model="form.sumber_dana" required />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="tahun">Tahun</Label>
                            <Input id="tahun" v-model="form.tahun" required type="number" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="status">Status</Label>
                            <Select v-model="form.status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="rencana">Rencana</SelectItem>
                                    <SelectItem value="berjalan">Berjalan</SelectItem>
                                    <SelectItem value="selesai">Selesai</SelectItem>
                                    <SelectItem value="tertunda">Tertunda</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="latitude">Latitude</Label>
                            <Input id="latitude" v-model="form.latitude" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="longitude">Longitude</Label>
                            <Input id="longitude" v-model="form.longitude" />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingDevelopment ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Photo Management Dialog -->
        <Dialog v-model:open="photoViewOpen">
            <DialogContent class="sm:max-w-2xl">
                <DialogHeader>
                    <DialogTitle v-if="photoDev">Kelola Foto - {{ photoDev.nama }}</DialogTitle>
                    <DialogDescription>Upload dan kelola foto pembangunan.</DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <form @submit.prevent="uploadImage" class="flex items-end gap-3">
                        <div class="grid flex-1 gap-1.5">
                            <Label for="devImage">Upload Foto</Label>
                            <Input id="devImage" type="file" accept="image/*" @change="onImageChange" />
                        </div>
                        <Button type="submit" :disabled="imageForm.processing || !imageForm.image">
                            Upload
                        </Button>
                    </form>

                    <div v-if="photoDev?.images?.length" class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                        <div
                            v-for="image in photoDev.images"
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

        <!-- Delete Confirm -->
        <Dialog :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus pembangunan ini? Tindakan ini tidak dapat dibatalkan.
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
