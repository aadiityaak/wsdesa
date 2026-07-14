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
import { Plus, Pencil, Trash2 } from '@lucide/vue';

interface BudgetCategory {
    id: number;
    nama: string;
    tipe: string;
}

interface Budget {
    id: number;
    tahun: string;
    budget_category_id: number;
    anggaran: number;
    realisasi: number;
    keterangan: string | null;
    category: BudgetCategory | null;
}

const props = defineProps<{
    budgets: Budget[];
    categories: BudgetCategory[];
}>();

const dialogOpen = ref(false);
const deleteConfirmId = ref<number | null>(null);
const editingBudget = ref<Budget | null>(null);

const form = useForm({
    budget_category_id: '',
    tahun: '',
    anggaran: '',
    realisasi: '',
    keterangan: '',
});

const dialogTitle = computed(() => (editingBudget.value ? 'Edit APBDes' : 'Tambah APBDes'));

const openAddDialog = (year?: string) => {
    editingBudget.value = null;
    form.reset();
    form.tahun = year || new Date().getFullYear().toString();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (item: Budget) => {
    editingBudget.value = item;
    form.budget_category_id = String(item.budget_category_id);
    form.tahun = item.tahun;
    form.anggaran = String(item.anggaran);
    form.realisasi = String(item.realisasi);
    form.keterangan = item.keterangan || '';
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingBudget.value) {
        form.put(`/admin/apbdes/${editingBudget.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('APBDes berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/apbdes', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('APBDes berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (item: Budget) => {
    deleteConfirmId.value = item.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        form.delete(`/admin/apbdes/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('APBDes berhasil dihapus.');
            },
        });
    }
};

const formatRupiah = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

const persenRealisasi = (anggaran: number, realisasi: number) => {
    if (anggaran === 0) return '0%';
    return ((realisasi / anggaran) * 100).toFixed(1) + '%';
};

// Group budgets by year
const groupedByYear = computed(() => {
    const groups: Record<string, Budget[]> = {};
    for (const b of props.budgets) {
        if (!groups[b.tahun]) {
            groups[b.tahun] = [];
        }
        groups[b.tahun].push(b);
    }
    // Sort years descending
    return Object.entries(groups).sort(([a], [b]) => b.localeCompare(a));
});
</script>

<template>
    <Head title="APBDes" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">APBDes</h1>
            <Button @click="openAddDialog()">
                <Plus class="size-4" />
                Tambah APBDes
            </Button>
        </div>

        <div v-for="[tahun, items] in groupedByYear" :key="tahun" class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">Tahun {{ tahun }}</h2>
                <Button size="sm" variant="outline" @click="openAddDialog(tahun)">
                    <Plus class="size-3" />
                    Tambah
                </Button>
            </div>

            <Card>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                    <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                    <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Anggaran</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Realisasi</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">%</th>
                                    <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(item, index) in items"
                                    :key="item.id"
                                    class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                                >
                                    <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs text-zinc-400 uppercase">{{ item.category?.tipe || '-' }}</span>
                                        <span class="ml-1.5 font-medium">{{ item.category?.nama || '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono">{{ formatRupiah(item.anggaran) }}</td>
                                    <td class="px-4 py-3 text-right font-mono">{{ formatRupiah(item.realisasi) }}</td>
                                    <td class="px-4 py-3 text-right">{{ persenRealisasi(item.anggaran, item.realisasi) }}</td>
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
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="budgets.length === 0" class="py-12 text-center text-zinc-500">
            Belum ada data APBDes.
        </div>

        <!-- Add/Edit Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ dialogTitle }}</DialogTitle>
                    <DialogDescription>
                        {{ editingBudget ? 'Perbarui data APBDes.' : 'Tambahkan data APBDes baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="budget_category_id">Kategori</Label>
                        <Select v-model="form.budget_category_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih kategori" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="cat in categories" :key="cat.id" :value="String(cat.id)">
                                    {{ cat.tipe.toUpperCase() }} - {{ cat.nama }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="tahun">Tahun</Label>
                        <Input id="tahun" v-model="form.tahun" required type="number" min="2000" max="2100" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="anggaran">Anggaran (Rp)</Label>
                        <Input id="anggaran" v-model="form.anggaran" required type="number" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="realisasi">Realisasi (Rp)</Label>
                        <Input id="realisasi" v-model="form.realisasi" required type="number" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="keterangan">Keterangan</Label>
                        <textarea
                            id="keterangan"
                            v-model="form.keterangan"
                            rows="2"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingBudget ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus data APBDes ini? Tindakan ini tidak dapat dibatalkan.
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
