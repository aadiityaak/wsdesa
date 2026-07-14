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
import { Plus } from '@lucide/vue';

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
</script>

<template>
    <Head title="Mutasi Penduduk" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Mutasi Penduduk</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Mutasi
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Mutasi Penduduk</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400 w-12">#</th>
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
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (mutations.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium">
                                    {{ mutation.resident?.nama || '-' }}
                                    <span class="text-zinc-400 text-xs">
                                        ({{ mutation.resident?.nik || '-' }})
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ jenisMutasiLabel(mutation.jenis_mutasi) }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(mutation.tanggal) }}</td>
                                <td class="px-4 py-3 text-zinc-500 max-w-xs truncate">{{ mutation.keterangan || '-' }}</td>
                            </tr>
                            <tr v-if="mutations.data.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada data mutasi.
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
                        Menampilkan {{ mutations.from }}–{{ mutations.to }} dari {{ mutations.total }}
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
                        <Input id="resident_id" v-model="form.resident_id" placeholder="Cari NIK atau Nama..." />
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
                        <Input id="tanggal" v-model="form.tanggal" type="date" required />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="keterangan">Keterangan</Label>
                        <Input id="keterangan" v-model="form.keterangan" />
                    </div>

                    <DialogFooter class="mt-6">
                        <Button type="button" variant="outline" @click="dialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            Simpan
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
