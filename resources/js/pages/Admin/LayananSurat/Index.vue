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
import { Eye, Pencil, Search, ArrowUp, FileText } from '@lucide/vue';

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
    from: number | null;
    to: number | null;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    letterRequests: Paginated;
}>();

const search = ref('');
const detailOpen = ref(false);
const statusOpen = ref(false);
const selectedRequest = ref<LetterRequest | null>(null);

const statusForm = useForm({
    status: '',
    catatan_admin: '',
});

const performSearch = () => {
    router.get('/admin/layanan-surat', { search: search.value }, { preserveState: true, replace: true });
};

const currentPage = computed(() => props.letterRequests.current_page);
const lastPage = computed(() => props.letterRequests.last_page);

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
    router.get('/admin/layanan-surat', { page, search: search.value }, { preserveState: true });
};

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
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
        case 'diproses':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
        case 'selesai':
            return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
        case 'ditolak':
            return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
        default:
            return 'bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'pending': return 'Pending';
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
    const data = props.letterRequests.data;
    return {
        total: props.letterRequests.total,
        pending: data.filter((r) => r.status === 'pending').length,
        diproses: data.filter((r) => r.status === 'diproses').length,
        selesai: data.filter((r) => r.status === 'selesai').length,
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
    <Head title="Layanan Surat" />

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
                        <FileText class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Layanan Surat</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola permohonan surat dari warga</p>
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
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ stats.pending }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Pending</p>
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
                        placeholder="Cari permohonan surat..."
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
            <div v-if="letterRequests.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <FileText class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada permohonan surat</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Permohonan surat dari warga akan muncul di sini.</p>
            </div>

            <div v-else class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
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
                                <td class="px-4 py-3 text-zinc-500">{{ (letterRequests.from ?? 1) + index }}</td>
                                <td class="px-4 py-3 font-mono text-sm text-zinc-900 dark:text-white">{{ item.kode_tracking }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ item.pemohon }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ item.nik }}</td>
                                <td class="px-4 py-3 text-zinc-700 dark:text-zinc-300">{{ item.jenis_surat }}</td>
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
                                        <Button
                                            v-if="item.status === 'selesai'"
                                            variant="ghost"
                                            size="icon-sm"
                                            class="rounded-lg"
                                            as-child
                                        >
                                            <a :href="`/admin/layanan-surat/${item.id}/cetak`" target="_blank">
                                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                            </a>
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
                        Menampilkan {{ letterRequests.from }}–{{ letterRequests.to }} dari {{ letterRequests.total }}
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
                            <Badge :class="statusClass(selectedRequest.status)" class="rounded-full px-2.5 py-0.5 text-xs">
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
                            <SelectTrigger class="rounded-xl">
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
                            class="flex w-full rounded-xl border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
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
