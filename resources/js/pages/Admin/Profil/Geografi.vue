<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import ProfilLayout from './Layout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { MapPin, Save } from '@lucide/vue';
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

// Custom SVG marker icon
const markerIcon = L.divIcon({
    html: `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="currentColor" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#e11d48;filter:drop-shadow(0 2px 4px rgba(0,0,0,.3))"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><path d="m9 10 2 2 4-4"/></svg>`,
    className: 'custom-marker',
    iconSize: [36, 36],
    iconAnchor: [18, 36],
    popupAnchor: [0, -36],
});

interface Profile {
    id: number; luas_wilayah: string; batas_utara: string; batas_selatan: string; batas_timur: string;
    batas_barat: string; orbitasi_ke_kecamatan: string; orbitasi_ke_kabupaten: string; latitude: string; longitude: string;
}

const props = defineProps<{ profile: Profile }>();

const form = useForm({
    luas_wilayah: props.profile.luas_wilayah ?? '',
    batas_utara: props.profile.batas_utara ?? '',
    batas_selatan: props.profile.batas_selatan ?? '',
    batas_timur: props.profile.batas_timur ?? '',
    batas_barat: props.profile.batas_barat ?? '',
    orbitasi_ke_kecamatan: props.profile.orbitasi_ke_kecamatan ?? '',
    orbitasi_ke_kabupaten: props.profile.orbitasi_ke_kabupaten ?? '',
    latitude: props.profile.latitude ?? '',
    longitude: props.profile.longitude ?? '',
});

const fields = [
    { key: 'luas_wilayah' as const, label: 'Luas Wilayah (km²)' },
    { key: 'batas_utara' as const, label: 'Batas Utara' },
    { key: 'batas_selatan' as const, label: 'Batas Selatan' },
    { key: 'batas_timur' as const, label: 'Batas Timur' },
    { key: 'batas_barat' as const, label: 'Batas Barat' },
    { key: 'orbitasi_ke_kecamatan' as const, label: 'Orbitasi ke Kecamatan (km)' },
    { key: 'orbitasi_ke_kabupaten' as const, label: 'Orbitasi ke Kabupaten (km)' },
];

// Leaflet map
const mapContainer = ref<HTMLDivElement | null>(null);
let map: L.Map | null = null;
let marker: L.Marker | null = null;

const defaultLat = props.profile.latitude ? parseFloat(props.profile.latitude) : -6.2;
const defaultLng = props.profile.longitude ? parseFloat(props.profile.longitude) : 106.8;

const initMap = () => {
    if (!mapContainer.value || map) return;

    map = L.map(mapContainer.value, {
        center: [defaultLat, defaultLng],
        zoom: 13,
        zoomControl: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19,
    }).addTo(map);

    // Place marker at current lat/lng
    marker = L.marker([defaultLat, defaultLng], { draggable: true, icon: markerIcon }).addTo(map);

    // Update form on marker drag
    marker.on('dragend', () => {
        if (!marker) return;
        const pos = marker.getLatLng();
        form.latitude = pos.lat.toFixed(7);
        form.longitude = pos.lng.toFixed(7);
    });

    // Click on map to move marker
    map.on('click', (e: L.LeafletMouseEvent) => {
        if (!marker) {
            marker = L.marker(e.latlng, { draggable: true, icon: markerIcon }).addTo(map!);
            marker.on('dragend', () => {
                if (!marker) return;
                const pos = marker.getLatLng();
                form.latitude = pos.lat.toFixed(7);
                form.longitude = pos.lng.toFixed(7);
            });
        } else {
            marker.setLatLng(e.latlng);
        }
        form.latitude = e.latlng.lat.toFixed(7);
        form.longitude = e.latlng.lng.toFixed(7);
    });

    // Invalidate size after mount
    setTimeout(() => map?.invalidateSize(), 100);
};

// Watch for manual lat/lng input changes → move marker
watch([() => form.latitude, () => form.longitude], ([lat, lng]) => {
    if (lat && lng && marker && map) {
        const newLat = parseFloat(lat);
        const newLng = parseFloat(lng);
        if (!isNaN(newLat) && !isNaN(newLng)) {
            marker.setLatLng([newLat, newLng]);
            map.setView([newLat, newLng], map.getZoom());
        }
    }
});

onMounted(() => {
    nextTick(() => initMap());
});

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
    }
});

const submit = () => {
    form.post('/admin/profil/geografi', {
        _method: 'put',
        forceFormData: true,
        onSuccess: () => toast.success('Data geografi berhasil disimpan.'),
    });
};
</script>

<template>
    <ProfilLayout>
        <Head title="Geografi" />
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Data geografi fields -->
            <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <h2 class="mb-6 text-lg font-semibold text-zinc-800 dark:text-zinc-200">Geografi & Batas Wilayah</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="field in fields" :key="field.key" class="grid gap-1.5">
                        <Label :for="field.key" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ field.label }}</Label>
                        <Input :id="field.key" v-model="form[field.key]" type="text" class="rounded-xl border-zinc-200 dark:border-zinc-700" />
                    </div>
                </div>
            </div>

            <!-- Koordinat + Map -->
            <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <div class="mb-4 flex items-center gap-2">
                    <MapPin class="size-5 text-rose-500" />
                    <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">Koordinat Desa</h2>
                </div>
                <p class="mb-5 text-sm text-zinc-500 dark:text-zinc-400">
                    Klik pada peta untuk menentukan lokasi desa, atau seret pin ke posisi yang tepat.
                </p>

                <!-- Lat/Lng inputs -->
                <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="grid gap-1.5">
                        <Label for="latitude" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Latitude</Label>
                        <Input id="latitude" v-model="form.latitude" type="text" placeholder="-6.2000000" class="rounded-xl border-zinc-200 font-mono text-sm dark:border-zinc-700" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="longitude" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Longitude</Label>
                        <Input id="longitude" v-model="form.longitude" type="text" placeholder="106.8000000" class="rounded-xl border-zinc-200 font-mono text-sm dark:border-zinc-700" />
                    </div>
                </div>

                <!-- Map -->
                <div
                    ref="mapContainer"
                    class="h-[420px] w-full overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700"
                />
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing" class="gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Save class="h-4 w-4" /> Simpan
                </Button>
            </div>
        </form>
    </ProfilLayout>
</template>
