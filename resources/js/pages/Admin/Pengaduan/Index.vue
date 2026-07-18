<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
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
import { Eye, Pencil, Search, ArrowUp, MessageSquare } from '@lucide/vue';

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
    from: number | null;
    to: number | null;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    complaints: Paginated;
}>();

const search = ref('');
const detailOpen = ref(false);
const statusOpen = ref(false);
const selectedComplaint = ref<Complaint | null>(null);

const statusForm = useForm({
    status: '',
});

const replyForm = useForm({
    pesan: '',
});

const performSearch = () => {
    router.get('/admin/pengaduan', { search: search.value }, { preserveState: true, replace: true });
};

const currentPage = computed(() => props.complaints.current_page);
const lastPage = computed(() => props.complaints.last_page);

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
    router.get('/admin/pengaduan', { page, search: search.value }, { preserveState: true });
};

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
    replyForm.post(`/admin/pengaduan/${selectedComplaint.value.id}/balasan`, {
        onSuccess: () => {
            replyForm.reset();
            toast.success('Balasan berhasil dikirim.');
        },
    });
};

const statusClass = (status: string) => {
    switch (status) {
        case 'baru':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        case 'diproses':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'selesai':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'ditolak':
            return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
        default:
            return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'baru': return 'Baru';
        case 'diproses': return 'Diproses';
        case 'selesai': return 'Selesai';
        case 'ditolak': return 'Ditolak';
        default: return status;
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

const stats = computed(() => {
    const data = props.complaints.data;
    return {
        total: props.complaints.total,
        baru: data.filter((c) => c.status === 'baru').length,
        diproses: data.filter((c) => c.status === 'diproses').length,
        selesai: data.filter((c) => c.status === 'selesai').length,
    };
});

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Pengaduan" />

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

            <div class="relative">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                        <MessageSquare class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Pengaduan</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola pengaduan dari warga</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.baru }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Baru</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.diproses }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Diproses</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.selesai }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Selesai</p>
                </div>
            </div>

            <!-- Search bar -->
            <div class="relative mt-6 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <Input
                        v-model="search"
                        placeholder="Cari pengaduan..."
                        class="rounded-xl border-zinc-200 pl-9 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                        @keyup.enter="performSearch"
                    />
                </div>
                <Button variant="outline" class="rounded-xl" @click="performSearch">Cari</Button>
            </div>
        </div>

        <!-- Table -->
        <div class="space-y-4">
            <!-- Empty state -->
            <div v-if="complaints.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <MessageSquare class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada pengaduan</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Pengaduan dari warga akan muncul di sini.</p>
            </div>

            <div v-else class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
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
                                <td class="px-4 py-3 text-zinc-500">{{ (complaints.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-zinc-900 dark:text-white">{{ item.kode }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ item.nama }}</td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ item.kategori }}</td>
                                <td class="px-4 py-3">
                                    <Badge :class="statusClass(item.status)" class="rounded-full px-2.5 py-0.5 text-xs font-medium">
                                        {{ statusLabel(item.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(item.created_at) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openDetail(item)">
                                            <Eye class="size-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openStatus(item)">
                                            <Pencil class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="lastPage > 1" class="flex flex-col items-center justify-between gap-3 border-t px-5 py-4 sm:flex-row">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        Menampilkan {{ complaints.from }}–{{ complaints.to }} dari {{ complaints.total }}
                    </p>
                    <div class="flex items-center gap-1">
                        <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">
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
                        <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)">
                            Selanjutnya
                        </Button>
                    </div>
                </div>
            </div>
        </div>

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
                            <Badge :class="statusClass(selectedComplaint.status)" class="rounded-full px-2.5 py-0.5 text-xs">
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
                        <h4 class="mb-3 font-semibold text-zinc-900 dark:text-white">Balasan</h4>
                        <div v-if="selectedComplaint.replies && selectedComplaint.replies.length > 0" class="space-y-3">
                            <div
                                v-for="reply in selectedComplaint.replies"
                                :key="reply.id"
                                class="rounded-lg border p-3 dark:border-zinc-700"
                            >
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="font-medium text-zinc-900 dark:text-white">{{ reply.nama }}</span>
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
                                    class="flex w-full rounded-xl border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                    placeholder="Tulis balasan..."
                                ></textarea>
                            </div>
                            <Button type="submit" class="rounded-full" :disabled="replyForm.processing">Kirim Balasan</Button>
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
                            <SelectTrigger class="rounded-xl">
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
                        <Button type="button" variant="outline" class="rounded-full" @click="statusOpen = false">Batal</Button>
                        <Button type="submit" class="rounded-full" :disabled="statusForm.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
