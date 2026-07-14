<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
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

interface EventCategory {
    id: number;
    nama: string;
    slug: string;
}

interface EventItem {
    id: number;
    judul: string;
    slug: string;
    deskripsi: string;
    lokasi: string;
    tanggal_mulai: string;
    tanggal_selesai: string | null;
    status: string;
    event_category_id: number | null;
    event_category: EventCategory | null;
    gambar: string | null;
}

interface EventsPaginated {
    data: EventItem[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
}

const props = defineProps<{
    events: EventsPaginated;
    categories: EventCategory[];
}>();

const dialogOpen = ref(false);
const editingEvent = ref<EventItem | null>(null);
const deleteConfirmId = ref<number | null>(null);

const deleteForm = useForm({});
const form = useForm({
    judul: '',
    event_category_id: '',
    deskripsi: '',
    lokasi: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    status: 'upcoming',
    gambar: null as File | null,
});

const dialogTitle = computed(() => (editingEvent.value ? 'Edit Agenda' : 'Tambah Agenda'));

const currentPage = computed(() => props.events.current_page);
const lastPage = computed(() => props.events.last_page);

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
    form.get(`/admin/agenda?page=${page}`);
};

const openAddDialog = () => {
    editingEvent.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (event: EventItem) => {
    editingEvent.value = event;
    form.judul = event.judul;
    form.event_category_id = event.event_category_id?.toString() || '';
    form.deskripsi = event.deskripsi;
    form.lokasi = event.lokasi;
    form.tanggal_mulai = event.tanggal_mulai?.slice(0, 16) || '';
    form.tanggal_selesai = event.tanggal_selesai?.slice(0, 16) || '';
    form.status = event.status;
    form.gambar = null;
    form.clearErrors();
    dialogOpen.value = true;
};

const submitForm = () => {
    if (editingEvent.value) {
        form.put(`/admin/agenda/${editingEvent.value.id}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Agenda berhasil diperbarui.');
            },
        });
    } else {
        form.post('/admin/agenda', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Agenda berhasil ditambahkan.');
            },
        });
    }
};

const confirmDelete = (event: EventItem) => {
    deleteConfirmId.value = event.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/agenda/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Agenda berhasil dihapus.');
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
        hour: '2-digit',
        minute: '2-digit',
    });
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'upcoming':
            return 'Akan Datang';
        case 'ongoing':
            return 'Berlangsung';
        case 'done':
            return 'Selesai';
        default:
            return status;
    }
};

const statusVariant = (status: string) => {
    switch (status) {
        case 'upcoming':
            return 'secondary';
        case 'ongoing':
            return 'default';
        case 'done':
            return 'outline';
        default:
            return 'secondary';
    }
};
</script>

<template>
    <Head title="Agenda" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Agenda</h1>
            <Button @click="openAddDialog">
                <Plus class="size-4" />
                Tambah Agenda
            </Button>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Agenda</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400 w-12">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(event, index) in events.data"
                                :key="event.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (events.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-medium max-w-xs truncate">{{ event.judul }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ event.event_category?.nama || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(event.tanggal_mulai) }}</td>
                                <td class="px-4 py-3">
                                    <Badge :variant="statusVariant(event.status)">
                                        {{ statusLabel(event.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openEditDialog(event)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(event)">
                                            <Trash2 class="size-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="events.data.length === 0">
                                <td colspan="6" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada agenda.
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
                        Menampilkan {{ events.from }}–{{ events.to }} dari {{ events.total }}
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
                        {{ editingEvent ? 'Perbarui informasi agenda.' : 'Tambahkan agenda baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="event_category_id">Kategori</Label>
                        <Select v-model="form.event_category_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih kategori" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="cat.id.toString()"
                                >
                                    {{ cat.nama }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="4"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="lokasi">Lokasi</Label>
                        <Input id="lokasi" v-model="form.lokasi" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="tanggal_mulai">Tanggal Mulai</Label>
                            <Input id="tanggal_mulai" v-model="form.tanggal_mulai" type="datetime-local" required />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="tanggal_selesai">Tanggal Selesai</Label>
                            <Input id="tanggal_selesai" v-model="form.tanggal_selesai" type="datetime-local" />
                        </div>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="upcoming">Akan Datang</SelectItem>
                                <SelectItem value="ongoing">Berlangsung</SelectItem>
                                <SelectItem value="done">Selesai</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="gambar">Gambar</Label>
                        <Input
                            id="gambar"
                            type="file"
                            accept="image/*"
                            @input="(e: Event) => (form.gambar = (e.target as HTMLInputElement).files?.[0] ?? null)"
                        />
                    </div>

                    <DialogFooter class="mt-6">
                        <Button type="button" variant="outline" @click="dialogOpen = false">
                            Batal
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            {{ editingEvent ? 'Simpan' : 'Tambah' }}
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
                        Apakah Anda yakin ingin menghapus agenda ini? Tindakan ini tidak dapat dibatalkan.
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
