<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

defineProps<{
    profile: Record<string, any> | null;
}>();
</script>

<template>
    <Head title="Profil Desa" />

    <!-- Empty State -->
    <div v-if="!profile" class="flex min-h-[60vh] items-center justify-center">
        <div class="text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-16 w-16 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h2 class="mt-4 text-xl font-semibold text-zinc-700 dark:text-zinc-300">Data Profil Belum Tersedia</h2>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Profil desa sedang dalam proses penyusunan. Silakan kunjungi kembali nanti.</p>
        </div>
    </div>

    <div v-else>
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-green-600 to-emerald-800 py-20">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center gap-6 text-center md:flex-row md:text-left">
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-white/20 p-3 backdrop-blur-sm">
                        <AppLogoIcon class="h-full w-full text-white" />
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white sm:text-4xl">
                            {{ profile.nama_desa || 'Desa Digital' }}
                        </h1>
                        <p class="mt-2 text-lg text-emerald-100">
                            {{ profile.kecamatan }} — {{ profile.kabupaten }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <!-- Data Dasar Section -->
            <section>
                <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">Data Dasar</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-if="profile.nama_desa" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Nama Desa</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.nama_desa }}</p>
                    </div>
                    <div v-if="profile.kode_desa" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kode Desa</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kode_desa }}</p>
                    </div>
                    <div v-if="profile.kecamatan" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kecamatan</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kecamatan }}</p>
                    </div>
                    <div v-if="profile.kabupaten" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kabupaten</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kabupaten }}</p>
                    </div>
                    <div v-if="profile.provinsi" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Provinsi</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.provinsi }}</p>
                    </div>
                    <div v-if="profile.alamat" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Alamat</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.alamat }}</p>
                    </div>
                    <div v-if="profile.kode_pos" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kode Pos</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kode_pos }}</p>
                    </div>
                    <div v-if="profile.telepon" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Telepon</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.telepon }}</p>
                    </div>
                    <div v-if="profile.email" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Email</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.email }}</p>
                    </div>
                </div>
            </section>

            <!-- Geography Section -->
            <section class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">Geografi</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-if="profile.luas_wilayah" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Luas Wilayah</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.luas_wilayah }}</p>
                    </div>
                    <div v-if="profile.orbitasi" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Orbitasi</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.orbitasi }}</p>
                    </div>
                    <div v-if="profile.batas_utara" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Utara</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_utara }}</p>
                    </div>
                    <div v-if="profile.batas_selatan" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Selatan</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_selatan }}</p>
                    </div>
                    <div v-if="profile.batas_timur" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Timur</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_timur }}</p>
                    </div>
                    <div v-if="profile.batas_barat" class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                        <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Barat</span>
                        <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_barat }}</p>
                    </div>
                </div>
            </section>

            <!-- Visi & Misi Section -->
            <section v-if="profile.visi || profile.misi" class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">Visi &amp; Misi</h2>
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <div v-if="profile.visi" class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Visi</h3>
                        <p class="mt-3 leading-relaxed text-zinc-600 dark:text-zinc-300">{{ profile.visi }}</p>
                    </div>
                    <div v-if="profile.misi" class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Misi</h3>
                        <p class="mt-3 leading-relaxed whitespace-pre-line text-zinc-600 dark:text-zinc-300">{{ profile.misi }}</p>
                    </div>
                </div>
            </section>

            <!-- Sejarah Section -->
            <section v-if="profile.sejarah" class="mt-12">
                <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">Sejarah</h2>
                <div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
                    <p class="leading-relaxed whitespace-pre-line text-zinc-600 dark:text-zinc-300">{{ profile.sejarah }}</p>
                </div>
            </section>
        </div>
    </div>
</template>
