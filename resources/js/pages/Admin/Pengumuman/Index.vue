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

interface Announcement {
    id: number;
    judul: string;
    konten: string;
    tipe: string;
    tanggal_mulai: string;
    tanggal_selesai: string | null;
}

const props = defineProps<{
    announcements: Announcement[];
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingAnnouncement = ref<Announcement | null>(null);

const form = useForm({
    judul: '',
    konten: '',
    tipe: 'info',
    tanggal_mulai: '',
    tanggal_selesai: '',
});

const dialogTitle = computed(() => (editingAnnouncement.value ? 'Edit Pengumuman' : 'Tambah Pengumuman'));

const openAddDialog = () => {
    editingAnnouncement.value = null;
    form.reset();
    form.tipe = 'info';
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (item: Announcement) => {
    editingAnnouncement.value = item;
    form.judul = item.judul;
    form.konten = item.konten;
    form.tipe = item.tipe;
    form.tanggal_mulai = item.tanggal_mulai?.split('T')[0] || '';
    form.tanggal_selesai = item.tanggal_selesai?.split('T')[0] || '';
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingAnnouncement.value) {
        form.put(`/admin/pengumuman/${editingAnnouncement.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Pengumuman berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/pengumuman', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Pengumuman berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (item: Announcement) => {
    deleteConfirmId.value = item.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/pengumuman/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Pengumuman berhasil dihapus.');
            },
        });
    }
};

const tipeClass = (tipe: string) => {
    switch (tipe) {
        case 'info':
            return 'border-transparent bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'warning':
            return 'border-transparent bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'urgent':
            return 'border-transparent bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'border-transparent bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const tipeLabel = (tipe: string) => {
    switch (tipe) {
        case 'info':
            return 'Info';
        case 'warning':
            return 'Peringatan';
        case 'urgent':
            return 'Penting';
        default:
            return tipe;
    }
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const isActive = (item: Announcement) => {
    if (!item.tanggal_selesai) return true;
    const now = new Date();
    const selese = new Date(item.tanggal_selesai);
    return now <= selese;
};
</script>

<template>
    <Head title="Pengumuman" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Pengumuman</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Pengumuman
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Pengumuman</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tipe</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Masa Berlaku</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in announcements"
                                :key="item.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium max-w-xs truncate">{{ item.judul }}</td>
                                <td class="px-4 py-3">
                                    <Badge :class="tipeClass(item.tipe)">
                                        {{ tipeLabel(item.tipe) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">
                                    {{ formatDate(item.tanggal_mulai) }} - {{ formatDate(item.tanggal_selesai) }}
                                </td>
                                <td class="px-4 py-3">
                                    <Badge :variant="isActive(item) ? 'default' : 'secondary'">
                                        {{ isActive(item) ? 'Aktif' : 'Nonaktif' }}
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
                            <tr v-if="announcements.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada pengumuman.
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
                        {{ editingAnnouncement ? 'Perbarui pengumuman.' : 'Tambahkan pengumuman baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="konten">Konten</Label>
                        <textarea
                            id="konten"
                            v-model="form.konten"
                            rows="5"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="tipe">Tipe</Label>
                        <Select v-model="form.tipe">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih tipe" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="info">Info</SelectItem>
                                <SelectItem value="warning">Peringatan</SelectItem>
                                <SelectItem value="urgent">Penting</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="tanggal_mulai">Tanggal Mulai</Label>
                            <Input id="tanggal_mulai" v-model="form.tanggal_mulai" type="date" required />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="tanggal_selesai">Tanggal Selesai</Label>
                            <Input id="tanggal_selesai" v-model="form.tanggal_selesai" type="date" />
                        </div>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingAnnouncement ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus pengumuman ini? Tindakan ini tidak dapat dibatalkan.
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
