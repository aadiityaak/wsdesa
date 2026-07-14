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
import { Plus, Pencil, Trash2 } from '@lucide/vue';

interface Slider {
    id: number;
    judul: string;
    deskripsi: string | null;
    image: string;
    link: string | null;
    urutan: number;
}

const props = defineProps<{
    sliders: Slider[];
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingSlider = ref<Slider | null>(null);

const form = useForm({
    judul: '',
    deskripsi: '',
    image: null as File | null,
    link: '',
    urutan: '',
});

const dialogTitle = computed(() => (editingSlider.value ? 'Edit Slider' : 'Tambah Slider'));

const openAddDialog = () => {
    editingSlider.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (item: Slider) => {
    editingSlider.value = item;
    form.judul = item.judul;
    form.deskripsi = item.deskripsi || '';
    form.image = null;
    form.link = item.link || '';
    form.urutan = String(item.urutan);
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    const data = {
        judul: form.judul,
        deskripsi: form.deskripsi,
        link: form.link,
        urutan: form.urutan,
    };

    if (editingSlider.value) {
        form.transform(() => form.image ? { ...data, image: form.image } : data).put(`/admin/slider/${editingSlider.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Slider berhasil diperbarui.');
            },
        });
    } else {
        form.transform(() => form.image ? { ...data, image: form.image } : data).post('/admin/slider', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Slider berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (item: Slider) => {
    deleteConfirmId.value = item.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/slider/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Slider berhasil dihapus.');
            },
        });
    }
};

const onFileChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    if (input.files?.[0]) {
        form.image = input.files[0];
    }
};
</script>

<template>
    <Head title="Slider" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Slider</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Slider
            </Button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <Card v-for="(item, index) in sliders" :key="item.id" class="overflow-hidden">
                <div class="aspect-[16/9] bg-zinc-100 dark:bg-zinc-800">
                    <img
                        :src="`/storage/${item.image}`"
                        :alt="item.judul"
                        class="h-full w-full object-cover"
                    />
                </div>
                <CardContent class="pt-4">
                    <span class="text-xs text-zinc-400">Urutan {{ item.urutan }}</span>
                    <h3 class="mb-1 font-semibold">{{ item.judul }}</h3>
                    <p class="mb-3 text-sm text-zinc-500">{{ item.deskripsi || '-' }}</p>
                    <div class="flex items-center gap-1">
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

        <div v-if="sliders.length === 0" class="py-12 text-center text-zinc-500">
            Belum ada slider.
        </div>

        <!-- Add/Edit Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingSlider ? 'Perbarui slider.' : 'Tambahkan slider baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required />
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
                    <div class="grid gap-1.5">
                        <Label for="sliderImage">Gambar</Label>
                        <Input id="sliderImage" type="file" accept="image/*" @change="onFileChange" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="link">Link</Label>
                        <Input id="link" v-model="form.link" placeholder="https://..." />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="urutan">Urutan</Label>
                        <Input id="urutan" v-model="form.urutan" type="number" />
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingSlider ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus slider ini? Tindakan ini tidak dapat dibatalkan.
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
