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

interface Staff {
    id: number;
    nama: string;
    nip: string;
    jabatan: string;
    foto: string | null;
    alamat: string;
    telepon: string;
    pendidikan_terakhir: string;
    tanggal_mulai_jabatan: string;
    tanggal_akhir_jabatan: string | null;
    urutan: number;
}

const props = defineProps<{
    staff: Staff[];
}>();

const dialogOpen = ref(false);
const editingStaff = ref<Staff | null>(null);
const deleteConfirmId = ref<number | null>(null);

const form = useForm({
    nama: '',
    nip: '',
    jabatan: '',
    foto: null as File | null,
    alamat: '',
    telepon: '',
    pendidikan_terakhir: '',
    tanggal_mulai_jabatan: '',
    tanggal_akhir_jabatan: '',
    urutan: 0,
});

const dialogTitle = computed(() => (editingStaff.value ? 'Edit Perangkat' : 'Tambah Perangkat'));

const openAddDialog = () => {
    editingStaff.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (staff: Staff) => {
    editingStaff.value = staff;
    form.nama = staff.nama;
    form.nip = staff.nip;
    form.jabatan = staff.jabatan;
    form.foto = null;
    form.alamat = staff.alamat ?? '';
    form.telepon = staff.telepon ?? '';
    form.pendidikan_terakhir = staff.pendidikan_terakhir ?? '';
    form.tanggal_mulai_jabatan = staff.tanggal_mulai_jabatan ?? '';
    form.tanggal_akhir_jabatan = staff.tanggal_akhir_jabatan ?? '';
    form.urutan = staff.urutan ?? 0;
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingStaff.value) {
        form.put(`/admin/pemerintahan/staff/${editingStaff.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Data perangkat berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/pemerintahan/staff', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Perangkat baru berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (staff: Staff) => {
    deleteConfirmId.value = staff.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/pemerintahan/staff/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Data perangkat berhasil dihapus.');
            },
        });
    }
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID');
};
</script>

<template>
    <Head title="Perangkat Desa" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Perangkat Desa</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Perangkat
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Perangkat Desa</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">NIP</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Jabatan</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Pendidikan</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tgl Mulai</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in staff"
                                :key="item.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ item.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.nip || '-' }}</td>
                                <td class="px-4 py-3">{{ item.jabatan }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.pendidikan_terakhir || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(item.tanggal_mulai_jabatan) }}</td>
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
                            <tr v-if="staff.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada data perangkat desa.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Dialog Tambah/Edit -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingStaff ? 'Perbarui informasi perangkat desa.' : 'Tambahkan perangkat desa baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="nama">Nama</Label>
                        <Input id="nama" v-model="form.nama" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="nip">NIP</Label>
                        <Input id="nip" v-model="form.nip" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="jabatan">Jabatan</Label>
                        <Input id="jabatan" v-model="form.jabatan" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="foto">Foto</Label>
                        <Input id="foto" type="file" accept="image/*" @input="(e: Event) => (form.foto = (e.target as HTMLInputElement).files?.[0] ?? null)" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="alamat">Alamat</Label>
                        <Input id="alamat" v-model="form.alamat" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="telepon">Telepon</Label>
                        <Input id="telepon" v-model="form.telepon" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="pendidikan_terakhir">Pendidikan Terakhir</Label>
                        <Input id="pendidikan_terakhir" v-model="form.pendidikan_terakhir" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="tanggal_mulai_jabatan">Tanggal Mulai</Label>
                            <Input id="tanggal_mulai_jabatan" v-model="form.tanggal_mulai_jabatan" type="date" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="tanggal_akhir_jabatan">Tanggal Akhir</Label>
                            <Input id="tanggal_akhir_jabatan" v-model="form.tanggal_akhir_jabatan" type="date" />
                        </div>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="urutan">Urutan</Label>
                        <Input id="urutan" v-model="form.urutan" type="number" />
                    </div>

                    <DialogFooter class="mt-6">
                        <Button type="button" variant="outline" @click="dialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingStaff ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus data perangkat ini? Tindakan ini tidak dapat dibatalkan.
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
