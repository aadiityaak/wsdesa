<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Building2 } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';

defineProps<{
    staff: Array<{
        nama: string;
        nip?: string;
        jabatan: string;
        photo_url?: string;
    }>;
    institutions: Array<{
        nama: string;
        members: Array<{
            nama: string;
            jabatan: string;
        }>;
    }>;
}>();
</script>

<template>
    <Head title="Pemerintahan" />

    <PageHero title="Pemerintahan Desa" description="Struktur organisasi dan perangkat pemerintahan desa">
        <template #icon>
            <Building2 class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <!-- Perangkat Desa Section -->
        <section>
            <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">Perangkat Desa</h2>

            <div v-if="staff.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                <div class="text-center p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada data perangkat desa</h3>
                    <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data perangkat desa akan ditampilkan di sini setelah tersedia.</p>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <div
                    v-for="(person, index) in staff"
                    :key="index"
                    class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800"
                >
                    <div v-if="person.photo_url" class="mx-auto h-24 w-24 overflow-hidden rounded-full">
                        <img :src="person.photo_url" :alt="person.nama" class="h-full w-full object-cover" />
                    </div>
                    <div v-else class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900">
                        <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-300">
                            {{ person.nama.charAt(0).toUpperCase() }}
                        </span>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-zinc-900 dark:text-white">{{ person.nama }}</h3>
                    <p v-if="person.nip" class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ person.nip }}</p>
                    <p class="mt-1 text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ person.jabatan }}</p>
                </div>
            </div>
        </section>

        <!-- Lembaga Desa Section -->
        <section class="mt-12">
            <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">Lembaga Desa</h2>

            <div v-if="institutions.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
                <div class="text-center p-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada data lembaga desa</h3>
                    <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data lembaga desa akan ditampilkan di sini setelah tersedia.</p>
                </div>
            </div>

            <div v-else class="space-y-8">
                <div
                    v-for="(institution, index) in institutions"
                    :key="index"
                    class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800"
                >
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ institution.nama }}</h3>

                    <div v-if="institution.members.length === 0" class="mt-4 text-center text-sm text-zinc-400 dark:text-zinc-500">
                        Belum ada anggota.
                    </div>

                    <div v-else class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="(member, mIndex) in institution.members"
                            :key="mIndex"
                            class="flex items-center gap-3 rounded-md border border-zinc-100 p-3 dark:border-zinc-700"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-600 dark:bg-blue-900 dark:text-blue-300">
                                {{ member.nama.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ member.nama }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ member.jabatan }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
