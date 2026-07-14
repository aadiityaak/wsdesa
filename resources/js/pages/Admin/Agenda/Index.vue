<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
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
import { Plus, Pencil, Trash2 } from '@lucide/vue';

interface EventItem {
    id: number;
    judul: string;
    slug: string;
    deskripsi: string;
    lokasi: string;
    tanggal_mulai: string;
    tanggal_selesai: string | null;
    status: string;
}

const props = defineProps<{
    events: EventItem[];
}>();

const dialogOpen = ref(false);
const editingEvent = ref<EventItem | null>(null);
const deleteConfirmId = ref<number | null>(null);

const form = useForm({
    judul: '',
    deskripsi: '',
    lokasi: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    status: 'upcoming',
});

const dialogTitle = computed(() => (editingEvent.value ? 'Edit Agenda' : 'Tambah Agenda'));

const openAddDialog = () => {
    editingEvent.value = null;
    form.reset();
    form.clearErrors();
    dialogOpen.value = true;
};

const openEditDialog = (event: EventItem) => {
    editingEvent.value = event;
    form.judul = event.judul;
    form.deskripsi = event.deskripsi;
    form.lokasi = event.lokasi;
    form.tanggal_mulai = event.tanggal_mulai?.split('T')[0] || '';
    form.tanggal_selesai = event.tanggal_selesai?.split('T')[0] || '';
    form.status = event.status;
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
        form.delete(`/admin/agenda/${deleteConfirmId.value}`, {
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
    <Head title="Kelola Agenda" />

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
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Judul</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Lokasi</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal Mulai</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal Selesai</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(event, index) in events"
                                :key="event.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium max-w-xs truncate">{{ event.judul }}</td>
                                <td class="px-4 py-3">{{ event.lokasi }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(event.tanggal_mulai) }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(event.tanggal_selesai) }}</td>
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
                            <tr v-if="events.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada agenda.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Dialog Tambah/Edit Agenda -->
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
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            required
                            rows="4"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="lokasi">Lokasi</Label>
                        <Input id="lokasi" v-model="form.lokasi" required />
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

        <!-- Dialog Konfirmasi Hapus -->
        <Dialog v-model:open="deleteConfirmId" :open="deleteConfirmId !== null" @update:open="deleteConfirmId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus agenda ini? Tindakan ini tidak dapat dibatalkan.
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
