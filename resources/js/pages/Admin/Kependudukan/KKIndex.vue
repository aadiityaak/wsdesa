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
import { Plus, Pencil, Trash2, Eye, BookOpen, ArrowUp } from '@lucide/vue';

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

const stats = computed(() => ({
    total: props.families.total,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Kartu Keluarga" />

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
                            <BookOpen class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Data KK</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola data kartu keluarga</p>
                        </div>
                    </div>
                </div>
                <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah KK
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <BookOpen class="h-5 w-5 text-rose-500 dark:text-rose-400" />
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total KK</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="space-y-6">
            <!-- Empty state -->
            <div v-if="families.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <BookOpen class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada data KK</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan data kartu keluarga pertama.</p>
                <Button class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah KK
                </Button>
            </div>

            <div v-else class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                <th class="w-12 px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
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
                                class="border-b border-zinc-50 transition-colors hover:bg-zinc-50/50 dark:border-zinc-800 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-400 dark:text-zinc-500">{{ (families.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ family.no_kk }}</td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ family.kepala_keluarga }}</td>
                                <td class="max-w-xs truncate px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ family.alamat }}</td>
                                <td class="px-4 py-3 text-center text-zinc-500 dark:text-zinc-400">{{ family.jumlah_anggota }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            class="rounded-lg"
                                            as="a"
                                            :href="`/admin/kependudukan/kk/${family.id}`"
                                        >
                                            <Eye class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEditDialog(family)">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(family)">
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
                        Menampilkan {{ families.from }}–{{ families.to }} dari {{ families.total }}
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
                        {{ editingFamily ? 'Perbarui data kartu keluarga.' : 'Tambahkan kartu keluarga baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="no_kk">Nomor KK</Label>
                        <Input id="no_kk" v-model="form.no_kk" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="alamat">Alamat</Label>
                        <Input id="alamat" v-model="form.alamat" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="rt">RT</Label>
                            <Input id="rt" v-model="form.rt" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="rw">RW</Label>
                            <Input id="rw" v-model="form.rw" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
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
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" :disabled="deleteForm.processing" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
