<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import ProfilLayout from './Layout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Camera, Save } from '@lucide/vue';
import { computed, ref, watch } from 'vue';

interface WilayahItem { kode: string; nama: string }
interface Profile {
    id: number; nama_desa: string; kode_desa: string; kecamatan: string; kabupaten: string; provinsi: string;
    alamat: string; kode_pos: string; telepon: string; email: string; website: string; logo: string | null;
}

const props = defineProps<{ profile: Profile; provinsiList: WilayahItem[]; kabupatenList: WilayahItem[]; kecamatanList: WilayahItem[] }>();

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
});

const logoPreview = ref<string | null>(props.profile.logo ? `/storage/${props.profile.logo}` : null);

const onLogoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) { form.logo = file; logoPreview.value = URL.createObjectURL(file); }
};

const submit = () => {
    form.post('/admin/profil/data-desa', {
        _method: 'put',
        forceFormData: true,
        onSuccess: () => toast.success('Data desa berhasil disimpan.'),
    });
};

const findKode = (list: WilayahItem[], nama: string) => list.find((i) => i.nama === nama)?.kode ?? '';
const findNama = (list: WilayahItem[], kode: string) => list.find((i) => i.kode === kode)?.nama ?? '';

const selectedProvinsiKode = ref(findKode(props.provinsiList, form.provinsi));
const selectedKabupatenKode = ref(findKode(props.kabupatenList, form.kabupaten));
const selectedKecamatanKode = ref(findKode(props.kecamatanList, form.kecamatan));

const filteredKabupaten = computed(() => selectedProvinsiKode.value ? props.kabupatenList.filter((k) => k.kode.startsWith(selectedProvinsiKode.value)) : []);
const filteredKecamatan = computed(() => selectedKabupatenKode.value ? props.kecamatanList.filter((k) => k.kode.startsWith(selectedKabupatenKode.value)) : []);

watch(selectedProvinsiKode, (n) => { form.provinsi = findNama(props.provinsiList, n); if (!n.startsWith(selectedKabupatenKode.value.slice(0, 2))) { selectedKabupatenKode.value = ''; selectedKecamatanKode.value = ''; form.kabupaten = ''; form.kecamatan = ''; } });
watch(selectedKabupatenKode, (n) => { form.kabupaten = findNama(props.kabupatenList, n); if (!n.startsWith(selectedKecamatanKode.value.slice(0, 5))) { selectedKecamatanKode.value = ''; form.kecamatan = ''; } });
watch(selectedKecamatanKode, (n) => { form.kecamatan = findNama(props.kecamatanList, n); });

const textFields = [
    { key: 'alamat' as const, label: 'Alamat' }, { key: 'kode_pos' as const, label: 'Kode Pos' },
    { key: 'telepon' as const, label: 'Telepon' }, { key: 'email' as const, label: 'Email', type: 'email' },
    { key: 'website' as const, label: 'Website', type: 'url' },
];
</script>

<template>
    <ProfilLayout>
        <Head title="Data Desa" />
        <form @submit.prevent="submit" class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <h2 class="mb-6 text-lg font-semibold text-zinc-800 dark:text-zinc-200">Data Desa</h2>

            <!-- Logo -->
            <div class="mb-6 flex flex-col items-center gap-3 sm:flex-row">
                <div class="group relative shrink-0">
                    <div class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-2xl border-2 border-white bg-white shadow-md ring-1 ring-zinc-100 dark:border-zinc-800 dark:bg-zinc-900 dark:ring-zinc-800">
                        <img v-if="logoPreview" :src="logoPreview" alt="Logo" class="h-full w-full object-cover" />
                        <Camera v-else class="h-8 w-8 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <label for="logo-upload" class="absolute -bottom-1.5 -right-1.5 flex h-7 w-7 cursor-pointer items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 shadow-sm hover:text-rose-500 dark:border-zinc-700 dark:bg-zinc-800">
                        <Camera class="h-3.5 w-3.5" />
                    </label>
                    <input id="logo-upload" type="file" accept="image/*" class="hidden" @change="onLogoChange" />
                </div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Klik ikon kamera untuk mengubah logo desa</div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="grid gap-1.5"><Label for="nama_desa" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Desa</Label><Input id="nama_desa" v-model="form.nama_desa" class="rounded-xl border-zinc-200 dark:border-zinc-700" /></div>
                <div class="grid gap-1.5"><Label for="kode_desa" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kode Desa</Label><Input id="kode_desa" v-model="form.kode_desa" class="rounded-xl border-zinc-200 dark:border-zinc-700" /></div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div class="grid gap-1.5">
                    <Label for="provinsi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Provinsi</Label>
                    <select id="provinsi" v-model="selectedProvinsiKode" class="h-10 w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-xs dark:border-zinc-700">
                        <option value="" disabled>Pilih Provinsi</option>
                        <option v-for="item in provinsiList" :key="item.kode" :value="item.kode">{{ item.nama }}</option>
                    </select>
                </div>
                <div class="grid gap-1.5">
                    <Label for="kabupaten" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kabupaten</Label>
                    <select id="kabupaten" v-model="selectedKabupatenKode" :disabled="!selectedProvinsiKode" class="h-10 w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-xs disabled:opacity-50 dark:border-zinc-700">
                        <option value="" disabled>{{ selectedProvinsiKode ? 'Pilih Kabupaten' : 'Pilih provinsi dulu' }}</option>
                        <option v-for="item in filteredKabupaten" :key="item.kode" :value="item.kode">{{ item.nama }}</option>
                    </select>
                </div>
                <div class="grid gap-1.5">
                    <Label for="kecamatan" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kecamatan</Label>
                    <select id="kecamatan" v-model="selectedKecamatanKode" :disabled="!selectedKabupatenKode" class="h-10 w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-xs disabled:opacity-50 dark:border-zinc-700">
                        <option value="" disabled>{{ selectedKabupatenKode ? 'Pilih Kecamatan' : 'Pilih kabupaten dulu' }}</option>
                        <option v-for="item in filteredKecamatan" :key="item.kode" :value="item.kode">{{ item.nama }}</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div v-for="field in textFields" :key="field.key" class="grid gap-1.5">
                    <Label :for="field.key" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ field.label }}</Label>
                    <Input :id="field.key" v-model="form[field.key]" :type="field.type ?? 'text'" class="rounded-xl border-zinc-200 dark:border-zinc-700" />
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <Button type="submit" :disabled="form.processing" class="gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Save class="h-4 w-4" /> Simpan
                </Button>
            </div>
        </form>
    </ProfilLayout>
</template>
