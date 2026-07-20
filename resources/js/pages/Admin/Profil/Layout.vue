<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { MapPin, Globe, Heart } from '@lucide/vue';
import { computed } from 'vue';

interface Profile {
    id: number;
    nama_desa: string;
    provinsi: string;
    kabupaten: string;
    kecamatan: string;
    logo: string | null;
}

const page = usePage();
const profile = computed(() => page.props.profile as Profile);
const logoUrl = computed(() => (profile.value.logo ? `/storage/${profile.value.logo}` : null));

const tabs = [
    { key: 'data-desa', label: 'Data Desa', href: '/admin/profil/data-desa' },
    { key: 'jam-kerja', label: 'Jam Kerja', href: '/admin/profil/jam-kerja' },
    { key: 'media', label: 'Media', href: '/admin/profil/media' },
    { key: 'geografi', label: 'Geografi', href: '/admin/profil/geografi' },
    { key: 'visi', label: 'Visi & Sejarah', href: '/admin/profil/visi' },
];

const activeTab = computed(() => {
    const path = window.location.pathname;
    for (const tab of tabs) {
        if (path.includes(`/profil/${tab.key}`)) return tab.key;
    }
    return 'data-desa';
});
</script>

<template>
    <div>
        <Head title="Profil Desa" />

        <!-- Hero header -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" />
            <div class="relative flex flex-col items-center gap-5 sm:flex-row sm:gap-7">
                <div class="flex h-22 w-22 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-white bg-white shadow-md ring-1 ring-zinc-100 dark:border-zinc-800 dark:bg-zinc-900 dark:ring-zinc-800 sm:h-24 sm:w-24">
                    <img v-if="logoUrl" :src="logoUrl" alt="Logo" class="h-full w-full object-cover" />
                    <div v-else class="text-3xl font-bold text-zinc-300 dark:text-zinc-600">{{ profile?.nama_desa?.charAt(0) }}</div>
                </div>
                <div class="text-center sm:text-left">
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ profile?.nama_desa || 'Profil Desa' }}
                    </h1>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Kelola informasi dan identitas desa
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-zinc-400 dark:text-zinc-500">
                        <span v-if="profile?.provinsi" class="inline-flex items-center gap-1"><MapPin class="size-3" />{{ profile.provinsi }}</span>
                        <span v-if="profile?.kabupaten" class="inline-flex items-center gap-1"><Globe class="size-3" />{{ profile.kabupaten }}</span>
                        <span v-if="profile?.kecamatan" class="inline-flex items-center gap-1"><Heart class="size-3" />{{ profile.kecamatan }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab nav -->
        <div class="mb-8 flex flex-wrap items-center gap-1.5 border-b border-zinc-200 pb-3 dark:border-zinc-700">
            <a
                v-for="tab in tabs"
                :key="tab.key"
                :href="tab.href"
                class="rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="activeTab === tab.key
                    ? 'bg-rose-500 text-white shadow-sm'
                    : 'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-zinc-200'"
            >
                {{ tab.label }}
            </a>
        </div>

        <!-- Content slot -->
        <slot />
    </div>
</template>
