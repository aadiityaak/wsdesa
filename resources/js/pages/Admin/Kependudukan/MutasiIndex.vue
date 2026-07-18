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
import { Plus, ArrowRightLeft, ArrowUp } from '@lucide/vue';

interface Mutation {
    id: number;
    resident_id: number;
    jenis_mutasi: string;
    tanggal: string;
    keterangan: string;
    resident: {
        id: number;
        nama: string;
        nik: string;
    } | null;
}

interface MutationsPaginated {
    data: Mutation[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    mutations: MutationsPaginated;
}>();

const dialogOpen = ref(false);

const form = useForm({
    resident_id: '',
    jenis_mutasi: 'masuk',
    tanggal: '',
    keterangan: '',
});

const currentPage = computed(() => props.mutations.current_page);
const lastPage = computed(() => props.mutations.last_page);

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
    form.get(`/admin/kependudukan/mutasi?page=${page}`);
};

const openAddDialog = () => {
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    form.post('/admin/kependudukan/mutasi', {
        onSuccess: () => {
            dialogOpen.value = false;
            toast.success('Data mutasi berhasil ditambahkan.');
        },
    });
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const jenisMutasiLabel = (jenis: string) => {
    switch (jenis) {
        case 'masuk':
            return 'Masuk';
        case 'keluar':
            return 'Keluar';
        case 'meninggal':
            return 'Meninggal';
        default:
            return jenis;
    }
};

const stats = computed(() => ({
    total: props.mutations.total,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Mutasi Penduduk" />

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
                            <ArrowRightLeft class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Mutasi Penduduk</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola data mutasi penduduk</p>
                        </div>
                    </div>
                </div>
                <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah Mutasi
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <ArrowRightLeft class="h-5 w-5 text-rose-500 dark:text-rose-400" />
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total Mutasi</p>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="space-y-6">
            <!-- Empty state -->
            <div v-if="mutations.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <ArrowRightLeft class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada data mutasi</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Catat mutasi penduduk pertama.</p>
                <Button class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAddDialog">
                    <Plus class="h-4 w-4" />
                    Tambah Mutasi
                </Button>
            </div>

            <div v-else class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 bg-zinc-50/50 dark:border-zinc-800 dark:bg-zinc-800/50">
                                <th class="w-12 px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama Penduduk</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Jenis Mutasi</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(mutation, index) in mutations.data"
                                :key="mutation.id"
                                class="border-b border-zinc-50 transition-colors hover:bg-zinc-50/50 dark:border-zinc-800 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-400 dark:text-zinc-500">{{ (mutations.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">
                                    {{ mutation.resident?.nama || '-' }}
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">
                                        ({{ mutation.resident?.nik || '-' }})
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ jenisMutasiLabel(mutation.jenis_mutasi) }}</td>
                                <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ formatDate(mutation.tanggal) }}</td>
                                <td class="max-w-xs truncate px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ mutation.keterangan || '-' }}</td>
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
                        Menampilkan {{ mutations.from }}–{{ mutations.to }} dari {{ mutations.total }}
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

        <!-- Dialog Tambah Mutasi -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Tambah Mutasi</DialogTitle>
                    <DialogDescription>
                        Catat mutasi penduduk (masuk, keluar, atau meninggal).
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="resident_id">Penduduk</Label>
                        <Input id="resident_id" v-model="form.resident_id" placeholder="Cari NIK atau Nama..." class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="jenis_mutasi">Jenis Mutasi</Label>
                        <Select v-model="form.jenis_mutasi">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih jenis mutasi" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="masuk">Masuk</SelectItem>
                                <SelectItem value="keluar">Keluar</SelectItem>
                                <SelectItem value="meninggal">Meninggal</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="tanggal">Tanggal</Label>
                        <Input id="tanggal" v-model="form.tanggal" type="date" required class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="keterangan">Keterangan</Label>
                        <Input id="keterangan" v-model="form.keterangan" class="rounded-xl border-zinc-200 focus:border-rose-300 dark:border-zinc-700" />
                    </div>

                    <DialogFooter class="mt-6">
                        <Button type="button" variant="outline" class="rounded-full" @click="dialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" class="rounded-full bg-rose-500 text-white hover:bg-rose-600" :disabled="form.processing">
                            Simpan
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
