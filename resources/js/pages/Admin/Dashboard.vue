<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { LayoutDashboard, Newspaper, Users, FileText, MessageSquare, ArrowUp } from '@lucide/vue';

interface Stats {
    total_berita: number;
    total_penduduk: number;
    total_permohonan_surat: number;
    total_pengaduan: number;
}

defineProps<{
    stats: Stats;
}>();

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}

const statItems = [
    { key: 'total_berita', label: 'Total Berita', icon: Newspaper, color: 'text-blue-600 dark:text-blue-400', bg: 'bg-blue-100 dark:bg-blue-900/30' },
    { key: 'total_penduduk', label: 'Total Penduduk', icon: Users, color: 'text-green-600 dark:text-green-400', bg: 'bg-green-100 dark:bg-green-900/30' },
    { key: 'total_permohonan_surat', label: 'Pengajuan Surat', icon: FileText, color: 'text-amber-600 dark:text-amber-400', bg: 'bg-amber-100 dark:bg-amber-900/30' },
    { key: 'total_pengaduan', label: 'Pengaduan', icon: MessageSquare, color: 'text-rose-600 dark:text-rose-400', bg: 'bg-rose-100 dark:bg-rose-900/30' },
] as const;
</script>

<template>
    <Head title="Dashboard Admin" />

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
                        <LayoutDashboard class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Dashboard</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Ringkasan data website desa</p>
                    </div>
                </div>
                <p class="relative mt-4 text-zinc-600 dark:text-zinc-400">Selamat datang di panel admin Website Desa.</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div
                v-for="item in statItems"
                :key="item.key"
                class="overflow-hidden rounded-2xl border border-zinc-100 bg-white shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ item.label }}</span>
                        <div :class="['flex h-10 w-10 items-center justify-center rounded-xl', item.bg]">
                            <component :is="item.icon" :class="['h-5 w-5', item.color]" />
                        </div>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-zinc-900 dark:text-white">{{ stats[item.key] }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
