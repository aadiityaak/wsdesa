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
import { Plus, Pencil, Trash2, Eye } from '@lucide/vue';

interface Family {
    id: number;
    no_kk: string;
    kepala_keluarga: string;
    alamat: string;
    rt: string;
    rw: string;
    dusun: string;
    jumlah_anggota: number;
}

interface FamiliesPaginated {
    data: Family[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    families: FamiliesPaginated;
}>();

const dialogOpen = ref(false);
const editingFamily = ref<Family | null>(null);
const deleteConfirmId = ref<number | null>(null);

const form = useForm({
    no_kk: '',
    alamat: '',
    rt: '',
    rw: '',
    dusun: '',
});

const deleteForm = useForm({});

const dialogTitle = computed(() => (editingFamily.value ? 'Edit KK' : 'Tambah KK'));

const currentPage = computed(() => props.families.current_page);
const lastPage = computed(() => props.families.last_page);

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

const goToPage = (page: number) => {
    form.get(`/admin/kependudukan/kk?page=${page}`);
};

const openAddDialog = () => {
    editingFamily.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (family: Family) => {
    editingFamily.value = family;
    form.no_kk = family.no_kk;
    form.alamat = family.alamat;
    form.rt = family.rt;
    form.rw = family.rw;
    form.dusun = family.dusun;
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingFamily.value) {
        form.put(`/admin/kependudukan/kk/${editingFamily.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Data KK berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/kependudukan/kk', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('KK baru berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (family: Family) => {
    deleteConfirmId.value = family.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/kependudukan/kk/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Data KK berhasil dihapus.');
            },
        });
    }
};
</script>

<template>
    <Head title="Kartu Keluarga" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Data Kartu Keluarga</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah KK
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Kartu Keluarga</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400 w-12">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">No KK</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kepala Keluarga</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Alamat</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Jumlah Anggota</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(family, index) in families.data"
                                :key="family.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (families.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium">{{ family.no_kk }}</td>
                                <td class="px-4 py-3">{{ family.kepala_keluarga }}</td>
                                <td class="px-4 py-3 max-w-xs truncate text-zinc-500">{{ family.alamat }}</td>
                                <td class="px-4 py-3 text-center">{{ family.jumlah_anggota }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            as="a"
                                            :href="`/admin/kependudukan/kk/${family.id}`"
                                        >
                                            <Eye class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="openEditDialog(family)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(family)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="families.data.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada data KK.
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
                        Menampilkan {{ families.from }}–{{ families.to }} dari {{ families.total }}
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
                        {{ editingFamily ? 'Perbarui data kartu keluarga.' : 'Tambahkan kartu keluarga baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="no_kk">Nomor KK</Label>
                        <Input id="no_kk" v-model="form.no_kk" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="alamat">Alamat</Label>
                        <Input id="alamat" v-model="form.alamat" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="rt">RT</Label>
                            <Input id="rt" v-model="form.rt" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="rw">RW</Label>
                            <Input id="rw" v-model="form.rw" />
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
                            {{ editingFamily ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus data KK ini? Tindakan ini tidak dapat dibatalkan.
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
