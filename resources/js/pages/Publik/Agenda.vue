<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { Calendar, MapPin } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';

interface Event {
    id: number;
    judul: string;
    tanggal: string;
    lokasi: string;
    deskripsi: string;
    status?: string;
}

defineProps<{
    events: {
        data: Event[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        links?: { url: string | null; label: string; active: boolean }[];
    };
}>();

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
</script>

<template>
    <Head title="Agenda" />

    <PageHero title="Agenda Desa" description="Jadwal kegiatan, acara, dan aktivitas yang akan diselenggarakan">
        <template #icon>
            <Calendar class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div v-if="events.data.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada agenda</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Agenda akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="event in events.data" :key="event.id">
                <CardContent class="space-y-3 p-5">
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ event.judul }}</h3>
                        <Badge :variant="event.status === 'selesai' ? 'secondary' : 'default'">
                            {{ event.status || 'Akan Datang' }}
                        </Badge>
                    </div>
                    <div class="space-y-1.5 text-sm text-zinc-500">
                        <span class="flex items-center gap-1.5">
                            <Calendar class="size-4" />
                            {{ formatDate(event.tanggal) }}
                        </span>
                        <span v-if="event.lokasi" class="flex items-center gap-1.5">
                            <MapPin class="size-4" />
                            {{ event.lokasi }}
                        </span>
                    </div>
                    <p v-if="event.deskripsi" class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-3" v-html="event.deskripsi" />
                </CardContent>
            </Card>
        </div>

        <!-- Pagination -->
        <div v-if="events.last_page > 1" class="mt-8 flex justify-center">
            <div class="flex items-center gap-1">
                <a
                    v-for="link in events.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="inline-flex items-center justify-center rounded-md px-3 py-1.5 text-sm"
                    :class="{
                        'bg-blue-600 text-white': link.active,
                        'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800': !link.active && link.url,
                        'cursor-default text-zinc-300': !link.url,
                    }"
                ></a>
            </div>
        </div>
    </div>
</template>
