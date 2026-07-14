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
import { Eye, Pencil } from '@lucide/vue';

interface LetterRequest {
    id: number;
    kode_tracking: string;
    pemohon: string;
    nik: string;
    jenis_surat: string;
    status: string;
    created_at: string;
    catatan_admin: string | null;
    [key: string]: unknown;
}

interface Paginated {
    data: LetterRequest[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    letterRequests: Paginated;
}>();

const detailOpen = ref(false);
const statusOpen = ref(false);
const selectedRequest = ref<LetterRequest | null>(null);

const statusForm = useForm({
    status: '',
    catatan_admin: '',
});

const openDetail = (item: LetterRequest) => {
    selectedRequest.value = item;
    detailOpen.value = true;
};

const openStatus = (item: LetterRequest) => {
    selectedRequest.value = item;
    statusForm.status = item.status;
    statusForm.catatan_admin = item.catatan_admin || '';
    statusForm.clearErrors();
    statusOpen.value = true;
};

const submitStatus = () => {
    if (!selectedRequest.value) return;
    statusForm.put(`/admin/layanan-surat/${selectedRequest.value.id}/status`, {
        onSuccess: () => {
            statusOpen.value = false;
            toast.success('Status berhasil diperbarui.');
        },
    });
};

const statusClass = (status: string) => {
    switch (status) {
        case 'pending':
            return 'border-transparent bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'diproses':
            return 'border-transparent bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'selesai':
            return 'border-transparent bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'ditolak':
            return 'border-transparent bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'border-transparent bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'pending':
            return 'Pending';
        case 'diproses':
            return 'Diproses';
        case 'selesai':
            return 'Selesai';
        case 'ditolak':
            return 'Ditolak';
        default:
            return status;
    }
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Layanan Surat" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Layanan Surat</h1>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Permohonan Surat</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kode Tracking</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Pemohon</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">NIK</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Jenis Surat</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in letterRequests.data"
                                :key="item.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (letterRequests.current_page - 1) * letterRequests.data.length + index + 1 }}</td>
                                <td class="px-4 py-3 font-mono text-sm">{{ item.kode_tracking }}</td>
                                <td class="px-4 py-3 font-medium">{{ item.pemohon }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.nik }}</td>
                                <td class="px-4 py-3">{{ item.jenis_surat }}</td>
                                <td class="px-4 py-3">
                                    <Badge :variant="statusVariant(item.status)">
                                        {{ statusLabel(item.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(item.created_at) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" @click="openDetail(item)">
                                            <Eye class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" @click="openStatus(item)">
                                            <Pencil class="size-4" />
                                        </Button>
                                        <Button
                                            v-if="item.status === 'selesai'"
                                            variant="ghost"
                                            size="icon-sm"
                                            as-child
                                        >
                                            <a :href="`/admin/layanan-surat/${item.id}/cetak`" target="_blank">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                            </a>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="letterRequests.data.length === 0">
                                <td colspan="8" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada permohonan surat.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="letterRequests.last_page > 1" class="flex items-center justify-between border-t px-4 py-3">
                    <span class="text-sm text-zinc-500">
                        Halaman {{ letterRequests.current_page }} dari {{ letterRequests.last_page }}
                    </span>
                    <div class="flex gap-1">
                        <a
                            v-for="link in letterRequests.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            class="rounded-md px-3 py-1 text-sm transition-colors"
                            :class="link.active
                                ? 'bg-primary text-primary-foreground'
                                : !link.url
                                    ? 'text-zinc-300 pointer-events-none'
                                    : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Detail Dialog -->
        <Dialog v-model:open="detailOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>Detail Permohonan</DialogTitle>
                    <DialogDescription>Informasi lengkap permohonan surat.</DialogDescription>
                </DialogHeader>
                <div v-if="selectedRequest" class="space-y-3 text-sm">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Kode Tracking</span>
                        <span class="col-span-2">{{ selectedRequest.kode_tracking }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Pemohon</span>
                        <span class="col-span-2">{{ selectedRequest.pemohon }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">NIK</span>
                        <span class="col-span-2">{{ selectedRequest.nik }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Jenis Surat</span>
                        <span class="col-span-2">{{ selectedRequest.jenis_surat }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Status</span>
                        <span class="col-span-2">
                            <Badge :class="statusClass(selectedRequest.status)">
                                {{ statusLabel(selectedRequest.status) }}
                            </Badge>
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Tanggal</span>
                        <span class="col-span-2">{{ formatDate(selectedRequest.created_at) }}</span>
                    </div>
                    <div v-if="selectedRequest.catatan_admin" class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Catatan Admin</span>
                        <span class="col-span-2">{{ selectedRequest.catatan_admin }}</span>
                    </div>
                    <div v-for="(value, key) in selectedRequest" :key="key">
                        <div v-if="!['id', 'kode_tracking', 'pemohon', 'nik', 'jenis_surat', 'status', 'created_at', 'catatan_admin', 'updated_at'].includes(key)" class="grid grid-cols-3 gap-2">
                            <span class="font-medium text-zinc-500 capitalize">{{ key.replace(/_/g, ' ') }}</span>
                            <span class="col-span-2">{{ value }}</span>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Update Status Dialog -->
        <Dialog v-model:open="statusOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Update Status</DialogTitle>
                    <DialogDescription>
                        Perbarui status dan catatan untuk permohonan ini.
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitStatus" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="status">Status</Label>
                        <Select v-model="statusForm.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="pending">Pending</SelectItem>
                                <SelectItem value="diproses">Diproses</SelectItem>
                                <SelectItem value="selesai">Selesai</SelectItem>
                                <SelectItem value="ditolak">Ditolak</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="catatan_admin">Catatan Admin</Label>
                        <textarea
                            id="catatan_admin"
                            v-model="statusForm.catatan_admin"
                            rows="3"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" @click="statusOpen = false">Batal</Button>
                        <Button type="submit" :disabled="statusForm.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
