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
import { Plus, Pencil, Trash2, ChevronLeft } from '@lucide/vue';

interface Resident {
    id: number;
    nik: string;
    nama: string;
    jenis_kelamin: string;
    tempat_lahir: string;
    tanggal_lahir: string;
    agama: string;
    status_perkawinan: string;
    pekerjaan: string;
    pendidikan: string;
    hubungan_keluarga: string;
}

interface Family {
    id: number;
    no_kk: string;
    kepala_keluarga: string;
    alamat: string;
    rt: string;
    rw: string;
    dusun: string;
    residents: Resident[];
}

const props = defineProps<{
    family: Family;
}>();

const dialogOpen = ref(false);
const editingResident = ref<Resident | null>(null);
const deleteConfirmId = ref<number | null>(null);

const form = useForm({
    nik: '',
    nama: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    jenis_kelamin: 'L',
    agama: '',
    status_perkawinan: '',
    pekerjaan: '',
    pendidikan: '',
    hubungan_keluarga: '',
});

const deleteForm = useForm({});

const dialogTitle = computed(() => (editingResident.value ? 'Edit Anggota' : 'Tambah Anggota'));

const openAddDialog = () => {
    editingResident.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (resident: Resident) => {
    editingResident.value = resident;
    form.nik = resident.nik;
    form.nama = resident.nama;
    form.tempat_lahir = resident.tempat_lahir;
    form.tanggal_lahir = resident.tanggal_lahir || '';
    form.jenis_kelamin = resident.jenis_kelamin;
    form.agama = resident.agama || '';
    form.status_perkawinan = resident.status_perkawinan || '';
    form.pekerjaan = resident.pekerjaan || '';
    form.pendidikan = resident.pendidikan || '';
    form.hubungan_keluarga = resident.hubungan_keluarga;
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingResident.value) {
        form.put(`/admin/kependudukan/kk/${props.family.id}/penduduk/${editingResident.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Data anggota berhasil diperbarui.');
            },
        });
    } else {
        form.post(`/admin/kependudukan/kk/${props.family.id}/penduduk`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Anggota baru berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (resident: Resident) => {
    deleteConfirmId.value = resident.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/kependudukan/kk/${props.family.id}/penduduk/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Data anggota berhasil dihapus.');
            },
        });
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
</script>

<template>
    <Head title="Detail KK" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <Button variant="outline" size="sm" as="a" href="/admin/kependudukan/kk" class="mb-3">
                    <ChevronLeft class="size-4" />
                    Kembali ke Daftar KK
                </Button>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                    Detail Kartu Keluarga
                </h1>
            </div>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Anggota
            </Button>
        </div>

        <!-- KK Header Info -->
        <Card>
            <CardContent class="py-4">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <p class="text-sm text-zinc-500">Nomor KK</p>
                        <p class="font-medium">{{ family.no_kk }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">Alamat</p>
                        <p class="font-medium">{{ family.alamat }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">RT / RW</p>
                        <p class="font-medium">{{ family.rt }} / {{ family.rw }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">Dusun</p>
                        <p class="font-medium">{{ family.dusun || '-' }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Members Table -->
        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Anggota Keluarga ({{ family.residents.length }})</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400 w-12">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">NIK</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">JK</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Hubungan</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tempat/Tgl Lahir</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(resident, index) in family.residents"
                                :key="resident.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ resident.nik }}</td>
                                <td class="px-4 py-3">{{ resident.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ resident.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ resident.hubungan_keluarga }}</td>
                                <td class="px-4 py-3 text-zinc-500">
                                    {{ resident.tempat_lahir }}, {{ formatDate(resident.tanggal_lahir) }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openEditDialog(resident)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(resident)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="family.residents.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada anggota keluarga.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Dialog Tambah/Edit Anggota -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingResident ? 'Perbarui data anggota keluarga.' : 'Tambahkan anggota keluarga baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="nik">NIK</Label>
                            <Input id="nik" v-model="form.nik" required />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="nama">Nama</Label>
                            <Input id="nama" v-model="form.nama" required />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="tempat_lahir">Tempat Lahir</Label>
                            <Input id="tempat_lahir" v-model="form.tempat_lahir" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="tanggal_lahir">Tanggal Lahir</Label>
                            <Input id="tanggal_lahir" v-model="form.tanggal_lahir" type="date" />
                        </div>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="jenis_kelamin">Jenis Kelamin</Label>
                        <Select v-model="form.jenis_kelamin">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih jenis kelamin" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="L">Laki-laki</SelectItem>
                                <SelectItem value="P">Perempuan</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="agama">Agama</Label>
                            <Input id="agama" v-model="form.agama" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="status_perkawinan">Status Perkawinan</Label>
                            <Input id="status_perkawinan" v-model="form.status_perkawinan" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="pekerjaan">Pekerjaan</Label>
                            <Input id="pekerjaan" v-model="form.pekerjaan" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="pendidikan">Pendidikan</Label>
                            <Input id="pendidikan" v-model="form.pendidikan" />
                        </div>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="hubungan_keluarga">Hubungan Keluarga</Label>
                        <Input id="hubungan_keluarga" v-model="form.hubungan_keluarga" required />
                    </div>

                    <DialogFooter class="mt-6">
                        <Button type="button" variant="outline" @click="dialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingResident ? 'Simpan' : 'Tambah' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Dialog Hapus -->
        <Dialog :open="deleteConfirmId !== null" @update:open="() => (deleteConfirmId = null)">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus data anggota ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" :disabled="deleteForm.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
