<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
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
import { Plus, Pencil, Trash2, Search, Download, Upload, Users, ArrowUp } from '@lucide/vue';

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

const stats = computed(() => {
    const total = props.residents.total;
    const laki = props.residents.data.filter((r) => r.jenis_kelamin === 'L').length;
    const perempuan = props.residents.data.filter((r) => r.jenis_kelamin === 'P').length;
    return { total, laki, perempuan };
});

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Data Penduduk" />

    <div class="relative">
        <!-- Scroll to top -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="translate-y-2 opacity-0"
        >
            <button
                v-if="showScrollTop"
                type="button"
                class="fixed bottom-8 right-8 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition hover:scale-105 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                @click="scrollToTop"
            >
                <ArrowUp class="h-5 w-5" />
            </button>
        </Transition>

        <!-- Hero banner -->
        <div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                            <Users class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Data Penduduk</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola data kependudukan desa</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="outline" class="rounded-full" as="a" href="/admin/kependudukan/penduduk/export">
                        <Download class="h-4 w-4" />
                        Export Excel
                    </Button>
                    <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddDialog">
                        <Plus class="h-4 w-4" />
                        Tambah Penduduk
                    </Button>
                </div>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-3 gap-3 sm:gap-4">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.laki }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Laki-laki</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-pink-600 dark:text-pink-400">{{ stats.perempuan }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Perempuan</p>
                </div>
            </div>

            <!-- Search & Import -->
            <div class="relative mt-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-2">
                    <div class="relative max-w-sm">
                        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                        <Input
                            v-model="search"
                            placeholder="Cari nama atau NIK..."
                            class="rounded-xl border-zinc-200 pl-9 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            @keyup.enter="performSearch"
                        />
                    </div>
                    <Button variant="outline" class="rounded-xl" @click="performSearch">Cari</Button>
                </div>

                <div class="flex items-center gap-2">
                    <Input
                        id="import-file"
                        type="file"
                        accept=".xlsx,.xls,.csv"
                        class="rounded-xl border-zinc-200 dark:border-zinc-700"
                        @input="(e: Event) => (importForm.file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                    />
                    <Button
                        variant="outline"
                        class="rounded-xl"
                        :disabled="!importForm.file || importForm.processing"
                        @click="importExcel"
                    >
                        <Upload class="h-4 w-4" />
                        Import
                    </Button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="space-y-6">
            <!-- Empty state -->
            <div v-if="residents.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <Users class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada data penduduk</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan data penduduk pertama.</p>
                <Button class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah Penduduk
                </Button>
            </div>

            <div v-else class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                <th class="w-12 px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
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
                                class="border-b border-zinc-50 transition-colors hover:bg-zinc-50/50 dark:border-zinc-800 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-400 dark:text-zinc-500">{{ (residents.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ resident.nik }}</td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ resident.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">
                                    {{ resident.jenis_kelamin === 'L' ? 'L' : 'P' }}
                                </td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ resident.dusun || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ resident.pekerjaan || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ resident.status_perkawinan || '-' }}</td>
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
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div
                    v-if="lastPage > 1"
                    class="flex flex-col items-center justify-between gap-3 border-t border-zinc-100 px-5 py-4 dark:border-zinc-800 sm:flex-row"
                >
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Menampilkan {{ residents.from }}–{{ residents.to }} dari {{ residents.total }}
                    </p>
                    <div class="flex items-center gap-1">
                        <Button
                            variant="outline"
                            size="sm"
                            class="rounded-lg"
                            :disabled="currentPage === 1"
                            @click="goToPage(currentPage - 1)"
                        >
                            Sebelumnya
                        </Button>
                        <template v-for="page in pageNumbers" :key="page">
                            <span v-if="page === '...'" class="px-2 text-zinc-400">...</span>
                            <Button
                                v-else
                                size="sm"
                                class="rounded-lg"
                                :variant="currentPage === page ? 'default' : 'outline'"
                                @click="goToPage(page as number)"
                            >
                                {{ page }}
                            </Button>
                        </template>
                        <Button
                            variant="outline"
                            size="sm"
                            class="rounded-lg"
                            :disabled="currentPage === lastPage"
                            @click="goToPage(currentPage + 1)"
                        >
                            Selanjutnya
                        </Button>
                    </div>
                </div>
            </div>
        </div>

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
                        <Label for="dusun">Dusun</Label>
                        <Input id="dusun" v-model="form.dusun" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
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
                        Apakah Anda yakin ingin menghapus data penduduk ini? Tindakan ini tidak dapat dibatalkan.
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
