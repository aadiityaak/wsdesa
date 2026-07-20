<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import PageHero from '@/components/PageHero.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';
import { Calendar, MapPin, ArrowLeft } from '@lucide/vue';

interface Event {
    id: number;
    judul: string;
    tanggal_mulai: string;
    tanggal_selesai?: string;
    lokasi: string;
    deskripsi: string;
    status?: string;
}

defineProps<{
    event: Event;
}>();

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
</script>

<template>
    <Head :title="event.judul" />

    <PageHero :title="event.judul" description="Detail agenda kegiatan desa">
        <template #icon>
            <Calendar class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-3xl px-4 py-4 sm:px-6 lg:px-8">
        <Breadcrumb :items="[
            { label: 'Agenda', href: '/agenda' },
            { label: event.judul },
        ]" />

        <div class="mt-6 flex flex-wrap items-center gap-2">
            <Badge>{{ event.status || 'Akan Datang' }}</Badge>
        </div>
        <h1 class="mb-4 text-3xl font-bold text-zinc-900 dark:text-white">{{ event.judul }}</h1>

        <div class="mb-6 space-y-2 text-sm text-zinc-500">
            <div class="flex items-center gap-2">
                <Calendar class="size-4" />
                {{ formatDate(event.tanggal_mulai) }}
                <span v-if="event.tanggal_selesai"> - {{ formatDate(event.tanggal_selesai) }}</span>
            </div>
            <div v-if="event.lokasi" class="flex items-center gap-2">
                <MapPin class="size-4" />
                {{ event.lokasi }}
            </div>
        </div>

        <div v-if="event.deskripsi" class="prose prose-zinc max-w-none dark:prose-invert" v-html="event.deskripsi"></div>
    </div>
</template>
