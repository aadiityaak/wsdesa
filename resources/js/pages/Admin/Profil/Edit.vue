<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Camera } from '@lucide/vue';
import { ref } from 'vue';

interface Profile {
    id: number;
    nama_desa: string;
    kode_desa: string;
    kecamatan: string;
    kabupaten: string;
    provinsi: string;
    alamat: string;
    kode_pos: string;
    telepon: string;
    email: string;
    website: string;
    logo: string | null;
    facebook: string;
    instagram: string;
    youtube: string;
    tiktok: string;
    luas_wilayah: string;
    batas_utara: string;
    batas_selatan: string;
    batas_timur: string;
    batas_barat: string;
    orbitasi_ke_kecamatan: string;
    orbitasi_ke_kabupaten: string;
    latitude: string;
    longitude: string;
    visi: string;
    misi: string;
    sejarah: string;
}

const props = defineProps<{
    profile: Profile;
}>();

const form = useForm({
    nama_desa: props.profile.nama_desa ?? '',
    kode_desa: props.profile.kode_desa ?? '',
    kecamatan: props.profile.kecamatan ?? '',
    kabupaten: props.profile.kabupaten ?? '',
    provinsi: props.profile.provinsi ?? '',
    alamat: props.profile.alamat ?? '',
    kode_pos: props.profile.kode_pos ?? '',
    telepon: props.profile.telepon ?? '',
    email: props.profile.email ?? '',
    website: props.profile.website ?? '',
    logo: null as File | null,
    facebook: props.profile.facebook ?? '',
    instagram: props.profile.instagram ?? '',
    youtube: props.profile.youtube ?? '',
    tiktok: props.profile.tiktok ?? '',
    luas_wilayah: props.profile.luas_wilayah ?? '',
    batas_utara: props.profile.batas_utara ?? '',
    batas_selatan: props.profile.batas_selatan ?? '',
    batas_timur: props.profile.batas_timur ?? '',
    batas_barat: props.profile.batas_barat ?? '',
    orbitasi_ke_kecamatan: props.profile.orbitasi_ke_kecamatan ?? '',
    orbitasi_ke_kabupaten: props.profile.orbitasi_ke_kabupaten ?? '',
    latitude: props.profile.latitude ?? '',
    longitude: props.profile.longitude ?? '',
    visi: props.profile.visi ?? '',
    misi: props.profile.misi ?? '',
    sejarah: props.profile.sejarah ?? '',
});

const logoPreview = ref<string | null>(props.profile.logo ?? null);

const onLogoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.put('/admin/profil', {
        onSuccess: () => {
            toast.success('Profil desa berhasil diperbarui.');
        },
    });
};

const textFields: { key: keyof typeof form; label: string; type?: string }[] = [
    { key: 'nama_desa', label: 'Nama Desa' },
    { key: 'kode_desa', label: 'Kode Desa' },
    { key: 'kecamatan', label: 'Kecamatan' },
    { key: 'kabupaten', label: 'Kabupaten' },
    { key: 'provinsi', label: 'Provinsi' },
    { key: 'alamat', label: 'Alamat' },
    { key: 'kode_pos', label: 'Kode Pos' },
    { key: 'telepon', label: 'Telepon' },
    { key: 'email', label: 'Email', type: 'email' },
    { key: 'website', label: 'Website', type: 'url' },
];

const mediaFields: { key: keyof typeof form; label: string; placeholder: string }[] = [
    { key: 'facebook', label: 'Facebook', placeholder: 'https://facebook.com/...' },
    { key: 'instagram', label: 'Instagram', placeholder: 'https://instagram.com/...' },
    { key: 'youtube', label: 'YouTube', placeholder: 'https://youtube.com/@...' },
    { key: 'tiktok', label: 'TikTok', placeholder: 'https://tiktok.com/@...' },
];

const geoFields: { key: keyof typeof form; label: string }[] = [
    { key: 'luas_wilayah', label: 'Luas Wilayah (km²)' },
    { key: 'batas_utara', label: 'Batas Utara' },
    { key: 'batas_selatan', label: 'Batas Selatan' },
    { key: 'batas_timur', label: 'Batas Timur' },
    { key: 'batas_barat', label: 'Batas Barat' },
    { key: 'orbitasi_ke_kecamatan', label: 'Orbitasi ke Kecamatan (km)' },
    { key: 'orbitasi_ke_kabupaten', label: 'Orbitasi ke Kabupaten (km)' },
    { key: 'latitude', label: 'Latitude' },
    { key: 'longitude', label: 'Longitude' },
];
</script>

<template>
    <Head title="Profil Desa" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Profil Desa</h1>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Data Dasar -->
            <Card>
                <CardHeader>
                    <CardTitle>Data Dasar</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div v-for="field in textFields" :key="field.key" class="grid gap-1.5">
                            <Label :for="field.key">{{ field.label }}</Label>
                            <Input
                                :id="field.key"
                                v-model="form[field.key]"
                                :type="field.type ?? 'text'"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Media -->
            <Card>
                <CardHeader>
                    <CardTitle>Media</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label>Logo Desa</Label>
                        <div class="flex items-start gap-4">
                            <div
                                class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-zinc-100 dark:bg-zinc-800"
                            >
                                <img
                                    v-if="logoPreview"
                                    :src="logoPreview"
                                    alt="Logo preview"
                                    class="h-full w-full object-cover"
                                />
                                <Camera v-else class="h-8 w-8 text-zinc-400" />
                            </div>
                            <div class="grid gap-1.5">
                                <Label
                                    for="logo"
                                    class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-1.5 text-sm font-medium hover:bg-zinc-50 dark:hover:bg-zinc-800"
                                >
                                    Pilih File
                                </Label>
                                <input
                                    id="logo"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="onLogoChange"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div v-for="field in mediaFields" :key="field.key" class="grid gap-1.5">
                            <Label :for="field.key">{{ field.label }}</Label>
                            <Input
                                :id="field.key"
                                v-model="form[field.key]"
                                type="url"
                                :placeholder="field.placeholder"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Geografi -->
            <Card>
                <CardHeader>
                    <CardTitle>Geografi</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="field in geoFields" :key="field.key" class="grid gap-1.5">
                            <Label :for="field.key">{{ field.label }}</Label>
                            <Input
                                :id="field.key"
                                v-model="form[field.key]"
                                type="text"
                            />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Visi & Misi -->
            <Card>
                <CardHeader>
                    <CardTitle>Visi &amp; Misi</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="visi">Visi</Label>
                        <textarea
                            id="visi"
                            v-model="form.visi"
                            rows="3"
                            class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="misi">Misi</Label>
                        <textarea
                            id="misi"
                            v-model="form.misi"
                            rows="5"
                            class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30"
                        ></textarea>
                    </div>
                </CardContent>
            </Card>

            <!-- Sejarah -->
            <Card>
                <CardHeader>
                    <CardTitle>Sejarah</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-1.5">
                        <Label for="sejarah">Sejarah Desa</Label>
                        <textarea
                            id="sejarah"
                            v-model="form.sejarah"
                            rows="8"
                            class="w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 dark:bg-input/30"
                        ></textarea>
                    </div>
                </CardContent>
            </Card>

            <!-- Submit -->
            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing" size="lg">
                    Simpan Perubahan
                </Button>
            </div>
        </form>
    </div>
</template>
