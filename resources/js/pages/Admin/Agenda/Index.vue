 <script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Pencil, Trash2, Search, ArrowUp, Calendar, MapPin } from '@lucide/vue';

interface EventCategory {
    id: number;
    nama: string;
    slug: string;
}

interface EventItem {
    id: number;
    judul: string;
    slug: string;
    event_category_id: number | null;
    deskripsi: string;
    lokasi: string;
    tanggal_mulai: string;
    tanggal_selesai: string | null;
    status: string;
    image: string | null;
    category: EventCategory | null;
}

interface PaginatedData<T> {
    data: T[];
    from: number | null;
    to: number | null;
    total: number;
    current_page: number;
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    events: PaginatedData<EventItem>;
}>();

const search = ref('');
const deleteConfirmId = ref<number | null>(null);

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatDateTime = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const confirmDelete = (event: EventItem) => {
    deleteConfirmId.value = event.id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        router.delete(`/admin/agenda/${deleteConfirmId.value}`, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Agenda berhasil dihapus.');
            },
            onError: () => toast.error('Gagal menghapus agenda.'),
        });
    }
};

const statusVariant = (status: string) => {
    switch (status) {
        case 'upcoming': return 'secondary' as const;
        case 'ongoing': return 'default' as const;
        case 'done': return 'outline' as const;
        default: return 'outline' as const;
    }
};

const statusLabel = (status: string) => {
    switch (status) {
        case 'upcoming': return 'Akan Datang';
        case 'ongoing': return 'Berlangsung';
        case 'done': return 'Selesai';
        default: return status;
    }
};

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
    router.get('/admin/agenda', { page, search: search.value }, { preserveState: true });
};

const performSearch = () => {
    router.get('/admin/agenda', { search: search.value }, { preserveState: true, replace: true });
};

const stats = computed(() => {
    const items = props.events.data;
    return {
        total: props.events.total,
        upcoming: items.filter((e) => e.status === 'upcoming').length,
        ongoing: items.filter((e) => e.status === 'ongoing').length,
        done: items.filter((e) => e.status === 'done').length,
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
    <Head title="Agenda" />

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
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                            <Calendar class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Agenda</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola agenda dan kegiatan desa</p>
                        </div>
                    </div>
                </div>
                <Button as="a" href="/admin/agenda/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Agenda
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-4 gap-3">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-3 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Total</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-3 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ stats.upcoming }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Akan Datang</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-3 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ stats.ongoing }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Berlangsung</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-3 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-xl font-bold text-zinc-500 dark:text-zinc-400">{{ stats.done }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Selesai</p>
                </div>
            </div>

            <!-- Search -->
            <div class="relative mt-6 flex gap-2">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                    <Input
                        v-model="search"
                        placeholder="Cari agenda..."
                        class="rounded-xl border-zinc-200 pl-9 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                        @keyup.enter="performSearch"
                    />
                </div>
                <Button variant="outline" class="rounded-xl" @click="performSearch">Cari</Button>
            </div>
        </div>

        <!-- Daftar -->
        <div class="space-y-4">
            <!-- Empty state -->
            <div v-if="events.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <Calendar class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada agenda</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Buat agenda pertama untuk desa.</p>
                <Button as="a" href="/admin/agenda/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Agenda
                </Button>
            </div>

            <!-- Cards -->
            <div v-for="event in events.data" :key="event.id" class="group rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 sm:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <Badge :variant="statusVariant(event.status)" class="rounded-full px-2.5 py-0.5 text-xs font-medium">
                                {{ statusLabel(event.status) }}
                            </Badge>
                            <span v-if="event.category" class="text-xs text-zinc-400 dark:text-zinc-500">{{ event.category.nama }}</span>
                        </div>

                        <h3 class="mt-1.5 text-base font-semibold text-zinc-900 dark:text-white">
                            {{ event.judul }}
                        </h3>

                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-zinc-400 dark:text-zinc-500">
                            <span class="inline-flex items-center gap-1">
                                <Calendar class="h-3.5 w-3.5" />
                                {{ formatDateTime(event.tanggal_mulai) }}
                                <template v-if="event.tanggal_selesai">
                                    – {{ formatDateTime(event.tanggal_selesai) }}
                                </template>
                            </span>
                            <span v-if="event.lokasi" class="inline-flex items-center gap-1">
                                <MapPin class="h-3.5 w-3.5" />
                                {{ event.lokasi }}
                            </span>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-1">
                        <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/agenda/${event.id}/edit`" class="rounded-lg">
                            <Pencil class="h-4 w-4" />
                        </Button>
                        <Button variant="ghost" size="icon-sm" @click="confirmDelete(event)" class="rounded-lg">
                            <Trash2 class="h-4 w-4 text-red-500" />
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="lastPage > 1 && events.data.length > 0" class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-zinc-100 bg-white px-5 py-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ events.from }}–{{ events.to }} dari {{ events.total }}
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
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" @click="executeDelete">
                        Hapus
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
