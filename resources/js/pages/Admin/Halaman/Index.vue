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
import { Plus, Pencil, Trash2 } from '@lucide/vue';

interface Page {
    id: number;
    judul: string;
    slug: string;
    konten: string;
    status: string;
}

const props = defineProps<{
    pages: Page[];
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingPage = ref<Page | null>(null);

const form = useForm({
    judul: '',
    slug: '',
    konten: '',
    status: 'draft',
});

const dialogTitle = computed(() => (editingPage.value ? 'Edit Halaman' : 'Tambah Halaman'));

const openAddDialog = () => {
    editingPage.value = null;
    form.reset();
    form.status = 'draft';
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (item: Page) => {
    editingPage.value = item;
    form.judul = item.judul;
    form.slug = item.slug;
    form.konten = item.konten;
    form.status = item.status;
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingPage.value) {
        form.put(`/admin/halaman/${editingPage.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Halaman berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/halaman', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Halaman berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (item: Page) => {
    deleteConfirmId.value = item.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/halaman/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Halaman berhasil dihapus.');
            },
        });
    }
};

const statusVariant = (status: string) => {
    return status === 'publish' ? 'default' : 'secondary';
};

const statusLabel = (status: string) => {
    return status === 'publish' ? 'Publish' : 'Draft';
};
</script>

<template>
    <Head title="Halaman Statis" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Halaman Statis</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Halaman
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Halaman</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Slug</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in pages"
                                :key="item.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ item.judul }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-zinc-500">{{ item.slug }}</td>
                                <td class="px-4 py-3">
                                    <Badge :variant="statusVariant(item.status)">
                                        {{ statusLabel(item.status) }}
                                    </Badge>
                                </td>
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
                            <tr v-if="pages.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada halaman.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Add/Edit Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingPage ? 'Perbarui halaman statis.' : 'Tambahkan halaman statis baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="slug">Slug</Label>
                        <Input id="slug" v-model="form.slug" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="konten">Konten</Label>
                        <textarea
                            id="konten"
                            v-model="form.konten"
                            rows="8"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="publish">Publish</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingPage ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus halaman ini? Tindakan ini tidak dapat dibatalkan.
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
