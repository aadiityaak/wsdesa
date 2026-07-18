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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Plus, Pencil, Trash2, ChevronLeft, BookOpen } from '@lucide/vue';

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
        <!-- Hero banner with back button -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative">
                <Button variant="outline" size="sm" as="a" href="/admin/kependudukan/kk" class="mb-4 rounded-full">
                    <ChevronLeft class="h-4 w-4" />
                    Kembali ke Daftar KK
                </Button>

                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                            <BookOpen class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Detail Kartu Keluarga</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">{{ family.no_kk }}</p>
                        </div>
                    </div>
                    <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddDialog">
                        <Plus class="h-4 w-4" />
                        Tambah Anggota
                    </Button>
                </div>
            </div>
        </div>

        <!-- KK Header Info -->
        <div class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="px-5 py-4">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Nomor KK</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ family.no_kk }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Alamat</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ family.alamat }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">RT / RW</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ family.rt }} / {{ family.rw }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Dusun</p>
                        <p class="font-medium text-zinc-900 dark:text-white">{{ family.dusun || '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <div class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-100 px-5 py-3 dark:border-zinc-800">
                <h2 class="font-semibold text-zinc-900 dark:text-white">Anggota Keluarga ({{ family.residents.length }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-100 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/50">
                            <th class="w-12 px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
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
                            class="border-b border-zinc-50 transition-colors hover:bg-zinc-50/50 dark:border-zinc-800 dark:hover:bg-zinc-800/50"
                        >
                            <td class="px-4 py-3 text-zinc-400 dark:text-zinc-500">{{ index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ resident.nik }}</td>
                            <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ resident.nama }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ resident.jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ resident.hubungan_keluarga }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                {{ resident.tempat_lahir }}, {{ formatDate(resident.tanggal_lahir) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEditDialog(resident)">
                                        <Pencil class="h-4 w-4" />
                                    </Button>
                                    <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(resident)">
                                        <Trash2 class="h-4 w-4 text-red-500" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="family.residents.length === 0">
                            <td colspan="7" class="px-4 py-12 text-center text-zinc-500 dark:text-zinc-400">
                                Belum ada anggota keluarga.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

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
                            <Input id="nik" v-model="form.nik" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="nama">Nama</Label>
                            <Input id="nama" v-model="form.nama" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="tempat_lahir">Tempat Lahir</Label>
                            <Input id="tempat_lahir" v-model="form.tempat_lahir" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="tanggal_lahir">Tanggal Lahir</Label>
                            <Input id="tanggal_lahir" v-model="form.tanggal_lahir" type="date" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
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
                            <Input id="agama" v-model="form.agama" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="status_perkawinan">Status Perkawinan</Label>
                            <Input id="status_perkawinan" v-model="form.status_perkawinan" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="pekerjaan">Pekerjaan</Label>
                            <Input id="pekerjaan" v-model="form.pekerjaan" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="pendidikan">Pendidikan</Label>
                            <Input id="pendidikan" v-model="form.pendidikan" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="hubungan_keluarga">Hubungan Keluarga</Label>
                        <Input id="hubungan_keluarga" v-model="form.hubungan_keluarga" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>

                    <DialogFooter class="mt-6">
                        <Button type="button" variant="outline" class="rounded-full" @click="dialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" class="rounded-full bg-rose-500 text-white hover:bg-rose-600" :disabled="form.processing">
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
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" :disabled="deleteForm.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
