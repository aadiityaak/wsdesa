 <script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Globe, Mail, MapPin, Target, BookOpen, Compass, ExternalLink, Quote, Camera, Play, Music } from '@lucide/vue';

defineProps<{
    profile: Record<string, any> | null;
}>();
</script>

<template>
    <Head title="Profil Desa" />

    <!-- Empty State -->
    <div v-if="!profile" class="flex min-h-[60vh] items-center justify-center px-4">
        <div class="text-center">
            <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800">
                <MapPin class="size-8 text-zinc-300 dark:text-zinc-600" />
            </div>
            <h2 class="mt-4 text-xl font-semibold text-zinc-700 dark:text-zinc-300">Data Profil Belum Tersedia</h2>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">Profil desa sedang dalam proses penyusunan. Silakan kunjungi kembali nanti.</p>
        </div>
    </div>

    <div v-else>
        <!-- Hero -->
        <section class="relative bg-gradient-to-br from-blue-600 to-indigo-900 py-16 md:py-20">
            <div class="absolute inset-0 bg-black/20" />
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center gap-5 text-center md:flex-row md:gap-8 md:text-left">
                    <div class="flex size-24 shrink-0 items-center justify-center rounded-full bg-white/20 p-4 backdrop-blur-sm md:size-28">
                        <AppLogoIcon class="h-full w-full text-white" />
                    </div>
                    <div>
                        <h1 class="text-balance text-2xl font-bold text-white sm:text-3xl md:text-4xl">
                            {{ profile.nama_desa || 'Desa Digital' }}
                        </h1>
                        <p class="mt-1 text-emerald-100 sm:text-lg">
                            {{ profile.kecamatan }} — {{ profile.kabupaten }}
                        </p>
                        <div class="mt-3 flex flex-wrap items-center justify-center gap-2 md:justify-start">
                            <Badge v-if="profile.provinsi" variant="secondary" class="bg-white/15 text-white hover:bg-white/20">
                                <MapPin class="mr-1 size-3" />{{ profile.provinsi }}
                            </Badge>
                            <a v-if="profile.website" :href="profile.website" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 text-xs text-emerald-200 underline underline-offset-2 hover:text-white">
                                <Globe class="size-3" /> {{ profile.website.replace('https://', '') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="mx-auto max-w-7xl space-y-10 px-4 py-12 sm:px-6 lg:px-8 md:py-16">
            <!-- Data Dasar -->
            <section>
                <div class="mb-6 flex items-center gap-3">
                    <MapPin class="size-5 text-emerald-600 dark:text-emerald-400" />
                    <h2 class="text-xl font-bold text-zinc-900 sm:text-2xl dark:text-white">Data Dasar</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-if="profile.nama_desa" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Nama Desa</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.nama_desa }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.kode_desa" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kode Desa</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kode_desa }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.kecamatan" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kecamatan</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kecamatan }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.kabupaten" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kabupaten</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kabupaten }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.provinsi" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Provinsi</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.provinsi }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.alamat" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Alamat</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.alamat }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.kode_pos" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Kode Pos</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.kode_pos }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.telepon" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Telepon</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.telepon }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.email" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Email</p>
                            <p class="mt-1 flex items-center gap-1 font-medium text-emerald-600 dark:text-emerald-400">
                                <Mail class="size-3.5" />
                                {{ profile.email }}
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <!-- Geografi -->
            <section>
                <div class="mb-6 flex items-center gap-3">
                    <Compass class="size-5 text-emerald-600 dark:text-emerald-400" />
                    <h2 class="text-xl font-bold text-zinc-900 sm:text-2xl dark:text-white">Geografi</h2>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-if="profile.luas_wilayah" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Luas Wilayah</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.luas_wilayah }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.orbitasi_ke_kecamatan" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Jarak ke Kecamatan</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.orbitasi_ke_kecamatan }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.orbitasi_ke_kabupaten" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Jarak ke Kabupaten</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.orbitasi_ke_kabupaten }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.batas_utara" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Utara</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_utara }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.batas_selatan" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Selatan</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_selatan }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.batas_timur" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Timur</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_timur }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.batas_barat" class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="p-4">
                            <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Batas Barat</p>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">{{ profile.batas_barat }}</p>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <!-- Visi & Misi -->
            <section v-if="profile.visi || profile.misi">
                <div class="mb-6 flex items-center gap-3">
                    <Target class="size-5 text-emerald-600 dark:text-emerald-400" />
                    <h2 class="text-xl font-bold text-zinc-900 sm:text-2xl dark:text-white">Visi &amp; Misi</h2>
                </div>
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <Card v-if="profile.visi" class="border-l-4 border-l-emerald-500 border-zinc-200/60 dark:border-zinc-700/60">
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <Quote class="size-4 text-emerald-600 dark:text-emerald-400" />
                                <CardTitle class="text-lg">Visi</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="leading-relaxed whitespace-pre-line text-zinc-600 dark:text-zinc-300">{{ profile.visi }}</p>
                        </CardContent>
                    </Card>
                    <Card v-if="profile.misi" class="border-l-4 border-l-emerald-500 border-zinc-200/60 dark:border-zinc-700/60">
                        <CardHeader>
                            <div class="flex items-center gap-2">
                                <BookOpen class="size-4 text-emerald-600 dark:text-emerald-400" />
                                <CardTitle class="text-lg">Misi</CardTitle>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="leading-relaxed whitespace-pre-line text-zinc-600 dark:text-zinc-300">{{ profile.misi }}</p>
                        </CardContent>
                    </Card>
                </div>
            </section>

            <!-- Sejarah -->
            <section v-if="profile.sejarah">
                <div class="mb-6 flex items-center gap-3">
                    <BookOpen class="size-5 text-emerald-600 dark:text-emerald-400" />
                    <h2 class="text-xl font-bold text-zinc-900 sm:text-2xl dark:text-white">Sejarah</h2>
                </div>
                <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                    <CardContent class="p-6">
                        <p class="leading-relaxed whitespace-pre-line text-zinc-600 dark:text-zinc-300">{{ profile.sejarah }}</p>
                    </CardContent>
                </Card>
            </section>

            <!-- Media Sosial -->
            <section v-if="profile.facebook || profile.instagram || profile.youtube || profile.tiktok">
                <div class="mb-6 flex items-center gap-3">
                    <Globe class="size-5 text-emerald-600 dark:text-emerald-400" />
                    <h2 class="text-xl font-bold text-zinc-900 sm:text-2xl dark:text-white">Media Sosial</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a v-if="profile.facebook" :href="profile.facebook" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-lg border border-zinc-200/60 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-800 dark:text-zinc-300">
                        <Globe class="size-4 text-blue-600" /> Facebook
                        <ExternalLink class="size-3 text-zinc-400" />
                    </a>
                    <a v-if="profile.instagram" :href="profile.instagram" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-lg border border-zinc-200/60 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-800 dark:text-zinc-300">
                        <Camera class="size-4 text-pink-600" /> Instagram
                        <ExternalLink class="size-3 text-zinc-400" />
                    </a>
                    <a v-if="profile.youtube" :href="profile.youtube" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-lg border border-zinc-200/60 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-800 dark:text-zinc-300">
                        <Play class="size-4 text-red-600" /> YouTube
                        <ExternalLink class="size-3 text-zinc-400" />
                    </a>
                    <a v-if="profile.tiktok" :href="profile.tiktok" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 rounded-lg border border-zinc-200/60 bg-white px-4 py-2.5 text-sm font-medium text-zinc-700 shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md dark:border-zinc-700/60 dark:bg-zinc-800 dark:text-zinc-300">
                        <Music class="size-4 text-zinc-900 dark:text-white" /> TikTok
                        <ExternalLink class="size-3 text-zinc-400" />
                    </a>
                </div>
            </section>
        </div>
    </div>
</template>
