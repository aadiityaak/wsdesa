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
import { Plus, Pencil, Trash2 } from '@lucide/vue';

interface Potential {
    id: number;
    nama: string;
    category_id: number;
    lokasi: string;
    deskripsi: string | null;
    category: { id: number; nama: string } | null;
}

interface PotentialCategory {
    id: number;
    nama: string;
}

interface Paginated {
    data: Potential[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    potentials: Paginated;
    categories: PotentialCategory[];
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingPotential = ref<Potential | null>(null);

const form = useForm({
    nama: '',
    category_id: '',
    lokasi: '',
    deskripsi: '',
});

const dialogTitle = computed(() => (editingPotential.value ? 'Edit Potensi' : 'Tambah Potensi'));

const openAddDialog = () => {
    editingPotential.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (item: Potential) => {
    editingPotential.value = item;
    form.nama = item.nama;
    form.category_id = String(item.category_id);
    form.lokasi = item.lokasi;
    form.deskripsi = item.deskripsi || '';
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingPotential.value) {
        form.put(`/admin/potensi/${editingPotential.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Potensi berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/potensi', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Potensi berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (item: Potential) => {
    deleteConfirmId.value = item.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/potensi/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Potensi berhasil dihapus.');
            },
        });
    }
};
</script>

<template>
    <Head title="Potensi Desa" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Potensi Desa</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Potensi
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Potensi</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Lokasi</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in potentials.data"
                                :key="item.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (potentials.current_page - 1) * potentials.data.length + index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ item.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.category?.nama || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.lokasi }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openEditDialog(item)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(item)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="potentials.data.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada potensi desa.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="potentials.last_page > 1" class="flex items-center justify-between border-t px-4 py-3">
                    <span class="text-sm text-zinc-500">
                        Halaman {{ potentials.current_page }} dari {{ potentials.last_page }}
                    </span>
                    <div class="flex gap-1">
                        <a
                            v-for="link in potentials.links"
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

        <!-- Add/Edit Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingPotential ? 'Perbarui informasi potensi.' : 'Tambahkan potensi desa baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="nama">Nama</Label>
                        <Input id="nama" v-model="form.nama" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="category_id">Kategori</Label>
                        <Select v-model="form.category_id">
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

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingPotential ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus potensi ini? Tindakan ini tidak dapat dibatalkan.
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
