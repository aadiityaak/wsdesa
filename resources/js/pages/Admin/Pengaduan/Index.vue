<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref } from 'vue';
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

interface Reply {
    id: number;
    pengaduan_id: number;
    nama: string;
    pesan: string;
    created_at: string;
}

interface Complaint {
    id: number;
    kode: string;
    nama: string;
    kategori: string;
    status: string;
    isi: string;
    created_at: string;
    replies: Reply[];
    [key: string]: unknown;
}

interface Paginated {
    data: Complaint[];
    current_page: number;
    last_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    complaints: Paginated;
}>();

const detailOpen = ref(false);
const statusOpen = ref(false);
const selectedComplaint = ref<Complaint | null>(null);

const statusForm = useForm({
    status: '',
});

const replyForm = useForm({
    pesan: '',
});

const openDetail = (item: Complaint) => {
    selectedComplaint.value = item;
    replyForm.reset();
    replyForm.clearErrors();
    detailOpen.value = true;
};

const openStatus = (item: Complaint) => {
    selectedComplaint.value = item;
    statusForm.status = item.status;
    statusForm.clearErrors();
    statusOpen.value = true;
};

const submitStatus = () => {
    if (!selectedComplaint.value) return;
    statusForm.put(`/admin/pengaduan/${selectedComplaint.value.id}/status`, {
        onSuccess: () => {
            statusOpen.value = false;
            toast.success('Status berhasil diperbarui.');
        },
    });
};

const submitReply = () => {
    if (!selectedComplaint.value) return;
    replyForm.post(`/admin/pengaduan/${selectedComplaint.value.id}/balas`, {
        onSuccess: () => {
            replyForm.reset();
            toast.success('Balasan berhasil dikirim.');
        },
    });
};

const statusClass = (status: string) => {
    switch (status) {
        case 'baru':
            return 'border-transparent bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        case 'diproses':
            return 'border-transparent bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'selesai':
            return 'border-transparent bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'ditolak':
            return 'border-transparent bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
        default:
            return 'border-transparent bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'baru':
            return 'Baru';
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
    <Head title="Pengaduan" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Pengaduan</h1>
        </div>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Pengaduan</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kode</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Kategori</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(item, index) in complaints.data"
                                :key="item.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (complaints.current_page - 1) * 10 + index + 1 }}</td>
                                <td class="px-4 py-3 font-mono text-sm">{{ item.kode }}</td>
                                <td class="px-4 py-3 font-medium">{{ item.nama }}</td>
                                <td class="px-4 py-3">{{ item.kategori }}</td>
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
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="complaints.data.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada pengaduan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="complaints.last_page > 1" class="flex items-center justify-between border-t px-4 py-3">
                    <span class="text-sm text-zinc-500">
                        Halaman {{ complaints.current_page }} dari {{ complaints.last_page }}
                    </span>
                    <div class="flex gap-1">
                        <a
                            v-for="link in complaints.links"
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
                    <DialogTitle>Detail Pengaduan</DialogTitle>
                    <DialogDescription>Informasi lengkap pengaduan dan balasan.</DialogDescription>
                </DialogHeader>
                <div v-if="selectedComplaint" class="space-y-4 text-sm">
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Kode</span>
                        <span class="col-span-2">{{ selectedComplaint.kode }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Nama</span>
                        <span class="col-span-2">{{ selectedComplaint.nama }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Kategori</span>
                        <span class="col-span-2">{{ selectedComplaint.kategori }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Status</span>
                        <span class="col-span-2">
                            <Badge :class="statusClass(selectedComplaint.status)">
                                {{ statusLabel(selectedComplaint.status) }}
                            </Badge>
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Tanggal</span>
                        <span class="col-span-2">{{ formatDate(selectedComplaint.created_at) }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <span class="font-medium text-zinc-500">Isi</span>
                        <span class="col-span-2">{{ selectedComplaint.isi }}</span>
                    </div>

                    <div v-for="(value, key) in selectedComplaint" :key="key">
                        <div v-if="!['id', 'kode', 'nama', 'kategori', 'status', 'isi', 'created_at', 'updated_at', 'replies'].includes(key)" class="grid grid-cols-3 gap-2">
                            <span class="font-medium text-zinc-500 capitalize">{{ key.replace(/_/g, ' ') }}</span>
                            <span class="col-span-2">{{ value }}</span>
                        </div>
                    </div>

                    <!-- Balasan -->
                    <div class="border-t pt-4">
                        <h4 class="mb-3 font-semibold">Balasan</h4>
                        <div v-if="selectedComplaint.replies && selectedComplaint.replies.length > 0" class="space-y-3">
                            <div
                                v-for="reply in selectedComplaint.replies"
                                :key="reply.id"
                                class="rounded-lg border p-3"
                            >
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="font-medium">{{ reply.nama }}</span>
                                    <span class="text-xs text-zinc-400">{{ formatDate(reply.created_at) }}</span>
                                </div>
                                <p class="text-zinc-600 dark:text-zinc-400">{{ reply.pesan }}</p>
                            </div>
                        </div>
                        <p v-else class="py-2 text-center text-sm text-zinc-400">Belum ada balasan.</p>

                        <!-- Reply Form -->
                        <form @submit.prevent="submitReply" class="mt-4 space-y-3 border-t pt-4">
                            <div class="grid gap-1.5">
                                <Label for="pesan">Tulis Balasan</Label>
                                <textarea
                                    id="pesan"
                                    v-model="replyForm.pesan"
                                    required
                                    rows="3"
                                    class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    placeholder="Tulis balasan..."
                                ></textarea>
                            </div>
                            <Button type="submit" :disabled="replyForm.processing">Kirim Balasan</Button>
                        </form>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Update Status Dialog -->
        <Dialog v-model:open="statusOpen">
            <DialogContent class="sm:max-w-sm">
                <DialogHeader>
                    <DialogTitle>Update Status</DialogTitle>
                    <DialogDescription>
                        Perbarui status pengaduan ini.
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
                                <SelectItem value="baru">Baru</SelectItem>
                                <SelectItem value="diproses">Diproses</SelectItem>
                                <SelectItem value="selesai">Selesai</SelectItem>
                                <SelectItem value="ditolak">Ditolak</SelectItem>
                            </SelectContent>
                        </Select>
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
