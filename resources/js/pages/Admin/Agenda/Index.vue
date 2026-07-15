<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const confirmDelete = (event: EventItem) => {
    if (!confirm(`Hapus agenda "${event.judul}"?`)) return;

    router.delete(`/admin/agenda/${event.id}`, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => toast.success('Agenda berhasil dihapus.'),
        onError: () => toast.error('Gagal menghapus agenda.'),
    });
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
</script>

<template>
    <Head title="Agenda" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                Agenda
            </h1>
            <Button as="a" href="/admin/agenda/tambah">
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
                                <td class="px-4 py-3 font-medium max-w-xs truncate">
                                    {{ event.judul }}
                                    <p v-if="event.lokasi" class="text-xs text-zinc-400 truncate">{{ event.lokasi }}</p>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ event.category?.nama || '-' }}</td>
                                <td class="px-4 py-3 text-zinc-500">
                                    <p>{{ formatDate(event.tanggal_mulai) }}</p>
                                    <p v-if="event.tanggal_selesai" class="text-xs text-zinc-400">
                                        s/d {{ formatDate(event.tanggal_selesai) }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge :variant="statusVariant(event.status)">
                                        {{ statusLabel(event.status) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            as="a"
                                            :href="`/admin/agenda/${event.id}/edit`"
                                        >
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

                <div
                    v-if="events.last_page > 1"
                    class="flex items-center justify-center gap-1 pt-6 pb-4"
                >
                    <Button
                        v-for="link in events.links"
                        :key="link.label"
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        :disabled="!link.url"
                        @click="link.url && router.visit(link.url)"
                        v-html="link.label"
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
