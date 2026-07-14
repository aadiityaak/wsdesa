<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
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
import { Plus, Pencil, Trash2, Search, Download, Upload } from '@lucide/vue';

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
    dusun: string;
    hubungan_keluarga: string;
}

interface ResidentsPaginated {
    data: Resident[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    residents: ResidentsPaginated;
}>();

const search = ref('');
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
    dusun: '',
});

const deleteForm = useForm({});

const importForm = useForm({
    file: null as File | null,
});

const dialogTitle = computed(() => (editingResident.value ? 'Edit Penduduk' : 'Tambah Penduduk'));

const currentPage = computed(() => props.residents.current_page);
const lastPage = computed(() => props.residents.last_page);

const pageNumbers = computed(() => {
    const pages: (number | string)[] = [];
    const current = currentPage.value;
    const last = lastPage.value;

    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current > 3) pages.push('...');
        const start = Math.max(2, current - 1);
        const end = Math.min(last - 1, current + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        if (current < last - 2) pages.push('...');
        pages.push(last);
    }
    return pages;
});

const performSearch = () => {
    router.get('/admin/kependudukan/penduduk', { search: search.value }, { preserveState: true, replace: true });
};

const goToPage = (page: number) => {
    router.get('/admin/kependudukan/penduduk', { page, search: search.value }, { preserveState: true });
};

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
    form.dusun = resident.dusun || '';
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingResident.value) {
        form.put(`/admin/kependudukan/penduduk/${editingResident.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Data penduduk berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/kependudukan/penduduk', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Data penduduk berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (resident: Resident) => {
    deleteConfirmId.value = resident.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/kependudukan/penduduk/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Data penduduk berhasil dihapus.');
            },
        });
    }
};

const importExcel = () => {
    if (importForm.file) {
        importForm.post('/admin/kependudukan/penduduk/import', {
            onSuccess: () => {
                toast.success('Data berhasil diimpor.');
            },
        });
    }
};
</script>

<template>
    <Head title="Data Penduduk" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Data Penduduk</h1>
            <div class="flex items-center gap-2">
                <Button variant="outline" as="a" href="/admin/kependudukan/penduduk/export">
                    <Download class="size-4" />
                    Export Excel
                </Button>
                <Button @click="openAddDialog">
                    <Plus class="size-4" />
                    Tambah Penduduk
                </Button>
            </div>
        </div>

        <!-- Search & Import -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="flex items-center gap-2">
                <div class="relative max-w-sm">
                    <Search class="absolute left-2.5 top-2.5 size-4 text-zinc-500" />
                    <Input
                        v-model="search"
                        placeholder="Cari nama atau NIK..."
                        class="pl-8"
                        @keyup.enter="performSearch"
                    />
                </div>
                <Button variant="outline" @click="performSearch">Cari</Button>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex items-center gap-2">
                    <Input
                        id="import-file"
                        type="file"
                        accept=".xlsx,.xls,.csv"
                        @input="(e: Event) => (importForm.file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                    />
                    <Button
                        variant="outline"
                        :disabled="!importForm.file || importForm.processing"
                        @click="importExcel"
                    >
                        <Upload class="size-4" />
                        Import
                    </Button>
                </div>
            </div>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Penduduk</CardTitle>
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
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Dusun</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Pekerjaan</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(resident, index) in residents.data"
                                :key="resident.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (residents.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium">{{ resident.nik }}</td>
                                <td class="px-4 py-3">{{ resident.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">
                                    {{ resident.jenis_kelamin === 'L' ? 'L' : 'P' }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ resident.dusun || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ resident.pekerjaan || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ resident.status_perkawinan || '-' }}</td>
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
                            <tr v-if="residents.data.length === 0">
                                <td colspan="8" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada data penduduk.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="lastPage > 1"
                    class="flex items-center justify-between border-t px-4 py-3"
                >
                    <p class="text-sm text-zinc-500">
                        Menampilkan {{ residents.from }}–{{ residents.to }} dari {{ residents.total }}
                    </p>
                    <div class="flex items-center gap-1">
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="currentPage === 1"
                            @click="goToPage(currentPage - 1)"
                        >
                            Sebelumnya
                        </Button>
                        <template v-for="page in pageNumbers" :key="page">
                            <span v-if="page === '...'" class="px-2 text-zinc-400">...</span>
                            <Button
                                v-else
                                :variant="currentPage === page ? 'default' : 'outline'"
                                size="sm"
                                @click="goToPage(page as number)"
                            >
                                {{ page }}
                            </Button>
                        </template>
                        <Button
                            variant="outline"
                            size="sm"
                            :disabled="currentPage === lastPage"
                            @click="goToPage(currentPage + 1)"
                        >
                            Selanjutnya
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Dialog Tambah/Edit -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingResident ? 'Perbarui data penduduk.' : 'Tambahkan penduduk baru.' }}
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
                        <Label for="dusun">Dusun</Label>
                        <Input id="dusun" v-model="form.dusun" />
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
                        Apakah Anda yakin ingin menghapus data penduduk ini? Tindakan ini tidak dapat dibatalkan.
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
