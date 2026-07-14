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

interface Category {
    id: number;
    nama: string;
    slug: string;
}

const props = defineProps<{
    categories: Category[];
}>();

const dialogOpen = ref(false);
const editingCategory = ref<Category | null>(null);
const deleteConfirmId = ref<number | null>(null);

const form = useForm({
    nama: '',
});

const dialogTitle = computed(() => (editingCategory.value ? 'Edit Kategori' : 'Tambah Kategori'));

const openAddDialog = () => {
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (category: Category) => {
    editingCategory.value = category;
    form.nama = category.nama;
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingCategory.value) {
        form.put(`/admin/kategori-berita/${editingCategory.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Kategori berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/kategori-berita', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Kategori berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (category: Category) => {
    deleteConfirmId.value = category.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/kategori-berita/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Kategori berhasil dihapus.');
            },
        });
    }
};
</script>

<template>
    <Head title="Kategori Berita" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Kategori Berita</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Kategori
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Kategori</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Slug</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(category, index) in categories"
                                :key="category.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ category.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ category.slug }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openEditDialog(category)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(category)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="categories.length === 0">
                                <td colspan="4" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada kategori.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Dialog Tambah/Edit Kategori -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingCategory ? 'Perbarui nama kategori.' : 'Tambahkan kategori berita baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="nama">Nama Kategori</Label>
                        <Input id="nama" v-model="form.nama" required />
                    </div>

                    <DialogFooter class="mt-6">
                        <Button type="button" variant="outline" @click="dialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingCategory ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Dialog Konfirmasi Hapus -->
        <Dialog v-model:open="deleteConfirmId" :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.
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
