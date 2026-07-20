 <script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Globe, Mail, MapPin, Target, BookOpen, Compass, ExternalLink, Quote, Camera, Play, Music } from '@lucide/vue';
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps<{
    profile: Record<string, any> | null;
}>();

// SVG marker icon (sama seperti di admin)
const markerIcon = L.divIcon({
    html: `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="currentColor" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#e11d48;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3))"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><path d="m9 10 2 2 4-4"/></svg>`,
    className: 'custom-marker',
    iconSize: [36, 36],
    iconAnchor: [18, 36],
    popupAnchor: [0, -36],
});

// Leaflet map
const mapContainer = ref<HTMLDivElement | null>(null);
let map: L.Map | null = null;

const hasCoords = computed(() => props.profile?.latitude && props.profile?.longitude);

const initMap = () => {
    if (!mapContainer.value || map || !hasCoords.value) return;
    const lat = parseFloat(props.profile!.latitude);
    const lng = parseFloat(props.profile!.longitude);

    map = L.map(mapContainer.value, {
        center: [lat, lng],
        zoom: 14,
        zoomControl: true,
        scrollWheelZoom: false,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    L.marker([lat, lng], { icon: markerIcon })
        .addTo(map)
        .bindPopup(props.profile!.nama_desa || 'Lokasi Desa');

    setTimeout(() => map?.invalidateSize(), 100);
};

onMounted(() => nextTick(() => initMap()));
onUnmounted(() => { if (map) { map.remove(); map = null; } });
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
                        <img
                            v-if="profile.logo"
                            :src="'/storage/' + profile.logo"
                            alt="Logo Desa"
                            class="h-full w-full rounded-full object-cover"
                        />
                        <AppLogoIcon v-else class="h-full w-full text-white" />
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

        <!-- Map lokasi -->
        <div v-if="hasCoords" class="mx-auto -mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div ref="mapContainer" class="h-[300px] w-full overflow-hidden rounded-xl border border-zinc-200 shadow-md sm:h-[380px] dark:border-zinc-700" />
        </div>

        <div class="mx-auto max-w-7xl space-y-10 px-4 py-12 sm:px-6 lg:px-8 md:py-16">
            <!-- Data Dasar -->
            <section>
                <div class="mb-8 flex items-center gap-4">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                        <MapPin class="size-5" />
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-zinc-900 sm:text-2xl dark:text-white">Data Dasar</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Informasi identitas dan kontak Desa {{ profile.nama_desa }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Sub: Identitas -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Identitas</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <Card v-if="profile.nama_desa" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                        <MapPin class="size-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Nama Desa</p>
                                        <p class="mt-0.5 truncate font-semibold text-zinc-900 dark:text-white">{{ profile.nama_desa }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.kode_desa" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Kode Desa</p>
                                        <p class="mt-0.5 truncate font-semibold text-zinc-900 dark:text-white">{{ profile.kode_desa }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.kecamatan" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Kecamatan</p>
                                        <p class="mt-0.5 truncate font-semibold text-zinc-900 dark:text-white">{{ profile.kecamatan }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.kabupaten" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                                        <Globe class="size-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Kabupaten</p>
                                        <p class="mt-0.5 truncate font-semibold text-zinc-900 dark:text-white">{{ profile.kabupaten }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.provinsi" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-teal-50 text-teal-600 dark:bg-teal-900/30 dark:text-teal-400">
                                        <MapPin class="size-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Provinsi</p>
                                        <p class="mt-0.5 truncate font-semibold text-zinc-900 dark:text-white">{{ profile.provinsi }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    <!-- Sub: Alamat & Kontak -->
                    <div>
                        <h3 class="mb-3 text-sm font-semibold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Alamat &amp; Kontak</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Card v-if="profile.alamat" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400">
                                        <MapPin class="size-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Alamat</p>
                                        <p class="mt-0.5 font-semibold text-zinc-900 dark:text-white">{{ profile.alamat }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.kode_pos" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600 dark:bg-cyan-900/30 dark:text-cyan-400">
                                        <span class="text-sm font-bold">#</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Kode Pos</p>
                                        <p class="mt-0.5 font-semibold text-zinc-900 dark:text-white">{{ profile.kode_pos }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.telepon" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400">
                                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Telepon</p>
                                        <p class="mt-0.5 font-semibold text-zinc-900 dark:text-white">{{ profile.telepon }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.email" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-pink-50 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400">
                                        <Mail class="size-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Email</p>
                                        <p class="mt-0.5 truncate font-semibold text-zinc-900 dark:text-white">{{ profile.email }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                            <Card v-if="profile.website" class="group border-zinc-200/60 transition-shadow hover:shadow-md dark:border-zinc-700/60">
                                <CardContent class="flex items-start gap-3 p-5">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                        <Globe class="size-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Website</p>
                                        <p class="mt-0.5 truncate font-semibold text-zinc-900 dark:text-white">{{ profile.website }}</p>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
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
