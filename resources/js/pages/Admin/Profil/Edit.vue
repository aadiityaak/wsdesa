 <script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Camera, Globe, MapPin, Heart, Save, ArrowUp } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import TiptapEditor from '@/components/TiptapEditor.vue';

interface WilayahItem {
    kode: string;
    nama: string;
}

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
    provinsiList: WilayahItem[];
    kabupatenList: WilayahItem[];
    kecamatanList: WilayahItem[];
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

const logoPreview = ref<string | null>(props.profile.logo ? `/storage/${props.profile.logo}` : null);

const onLogoChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        form.logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post('/admin/profil', {
        _method: 'put',
        forceFormData: true,
        onSuccess: () => {
            toast.success('Profil desa berhasil diperbarui.');
        },
    });
};

const findKode = (list: WilayahItem[], nama: string): string =>
    list.find((i) => i.nama === nama)?.kode ?? '';

const findNama = (list: WilayahItem[], kode: string): string =>
    list.find((i) => i.kode === kode)?.nama ?? '';

const selectedProvinsiKode = ref(findKode(props.provinsiList, form.provinsi));
const selectedKabupatenKode = ref(findKode(props.kabupatenList, form.kabupaten));
const selectedKecamatanKode = ref(findKode(props.kecamatanList, form.kecamatan));

const filteredKabupaten = computed(() =>
    selectedProvinsiKode.value
        ? props.kabupatenList.filter((k) => k.kode.startsWith(selectedProvinsiKode.value))
        : [],
);

const filteredKecamatan = computed(() =>
    selectedKabupatenKode.value
        ? props.kecamatanList.filter((k) => k.kode.startsWith(selectedKabupatenKode.value))
        : [],
);

watch(selectedProvinsiKode, (newKode) => {
    form.provinsi = findNama(props.provinsiList, newKode);
    if (!newKode.startsWith(selectedKabupatenKode.value.slice(0, 2))) {
        selectedKabupatenKode.value = '';
        selectedKecamatanKode.value = '';
        form.kabupaten = '';
        form.kecamatan = '';
    }
});

watch(selectedKabupatenKode, (newKode) => {
    form.kabupaten = findNama(props.kabupatenList, newKode);
    if (!newKode.startsWith(selectedKecamatanKode.value.slice(0, 5))) {
        selectedKecamatanKode.value = '';
        form.kecamatan = '';
    }
});

watch(selectedKecamatanKode, (newKode) => {
    form.kecamatan = findNama(props.kecamatanList, newKode);
});

const textFields: { key: keyof typeof form; label: string; type?: string }[] = [
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

const activeTab = ref<'data' | 'media' | 'geo' | 'visi'>('data');

const dataSection = ref<HTMLElement | null>(null);
const mediaSection = ref<HTMLElement | null>(null);
const geoSection = ref<HTMLElement | null>(null);
const visiSection = ref<HTMLElement | null>(null);

const sectionMap: Record<string, typeof dataSection> = {
    data: dataSection,
    media: mediaSection,
    geo: geoSection,
    visi: visiSection,
};

const scrollTo = (key: string) => {
    activeTab.value = key as 'data' | 'media' | 'geo' | 'visi';
    sectionMap[key]?.value?.scrollIntoView({ behavior: 'smooth', block: 'start' });
};

const showScrollTop = ref(false);

const onScroll = () => {
    showScrollTop.value = window.scrollY > 400;
};

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Profil Desa" />

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
        <div
            class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-14"
        >
            <div
                class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10"
                aria-hidden="true"
            />
            <div class="relative flex flex-col items-center gap-6 sm:flex-row sm:gap-8">
                <!-- Logo -->
                <div class="group relative shrink-0">
                    <div
                        class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl border-2 border-white bg-white shadow-md ring-1 ring-zinc-100 transition duration-300 group-hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:ring-zinc-800 sm:h-32 sm:w-32"
                    >
                        <img
                            v-if="logoPreview"
                            :src="logoPreview"
                            alt="Logo desa"
                            class="h-full w-full object-cover"
                        />
                        <Camera v-else class="h-10 w-10 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <label
                        for="logo-hero"
                        class="absolute -bottom-1.5 -right-1.5 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full border border-zinc-200 bg-white text-zinc-500 shadow-sm transition hover:bg-zinc-100 hover:text-rose-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-rose-400"
                    >
                        <Camera class="h-4 w-4" />
                    </label>
                    <input
                        id="logo-hero"
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="onLogoChange"
                    />
                </div>

                <!-- Title & desc -->
                <div class="text-center sm:text-left">
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-4xl">
                        {{ form.nama_desa || 'Profil Desa' }}
                    </h1>
                    <p class="mt-2 text-base text-zinc-500 dark:text-zinc-400">
                        Kelola informasi dan identitas desa. Semua perubahan akan langsung tampil di
                        halaman publik.
                    </p>
                    <div
                        class="mt-4 flex flex-wrap items-center gap-3 text-sm text-zinc-400 dark:text-zinc-500"
                    >
                        <span v-if="form.provinsi" class="inline-flex items-center gap-1.5">
                            <MapPin class="h-3.5 w-3.5" />
                            {{ form.provinsi }}
                        </span>
                        <span v-if="form.kabupaten" class="inline-flex items-center gap-1.5">
                            <Globe class="h-3.5 w-3.5" />
                            {{ form.kabupaten }}
                        </span>
                        <span v-if="form.kecamatan" class="inline-flex items-center gap-1.5">
                            <Heart class="h-3.5 w-3.5" />
                            {{ form.kecamatan }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section nav + save -->
            <div
                class="mt-8 flex items-center justify-between border-t border-zinc-100 pt-5 dark:border-zinc-800"
            >
                <div class="flex flex-wrap gap-1.5">
                    <button
                        v-for="tab in [
                            { key: 'data', label: 'Data Desa' },
                            { key: 'media', label: 'Media' },
                            { key: 'geo', label: 'Geografi' },
                            { key: 'visi', label: 'Visi & Sejarah' },
                        ]"
                        :key="tab.key"
                        type="button"
                        class="rounded-full px-4 py-1.5 text-sm font-medium transition"
                        :class="
                            activeTab === tab.key
                                ? 'bg-rose-500 text-white shadow-sm'
                                : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700'
                        "
                        @click="scrollTo(tab.key)"
                    >
                        {{ tab.label }}
                    </button>
                </div>
                <Button
                    type="submit"
                    :disabled="form.processing"
                    size="lg"
                    class="hidden gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600 sm:inline-flex"
                    @click="submit"
                >
                    <Save class="h-4 w-4" />
                    Simpan Perubahan
                </Button>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <!-- ────────────── DATA DESA ────────────── -->
            <section ref="dataSection" class="scroll-mt-24">
                <div class="mb-4 flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400"
                    >
                        <span class="text-sm font-bold">1</span>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">
                        Data Desa
                    </h2>
                </div>
                <div
                    class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8"
                >
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label
                                for="nama_desa"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Nama Desa
                            </Label>
                            <Input
                                id="nama_desa"
                                v-model="form.nama_desa"
                                type="text"
                                class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label
                                for="kode_desa"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Kode Desa
                            </Label>
                            <Input
                                id="kode_desa"
                                v-model="form.kode_desa"
                                type="text"
                                class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <div class="grid gap-1.5">
                            <Label
                                for="provinsi"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Provinsi
                            </Label>
                            <select
                                id="provinsi"
                                v-model="selectedProvinsiKode"
                                class="flex h-10 w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] focus:border-rose-300 focus:ring-[3px] focus:ring-rose-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-700 dark:focus:border-rose-400 dark:focus:ring-rose-900"
                            >
                                <option value="" disabled>Pilih Provinsi</option>
                                <option
                                    v-for="item in provinsiList"
                                    :key="item.kode"
                                    :value="item.kode"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>
                        </div>
                        <div class="grid gap-1.5">
                            <Label
                                for="kabupaten"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Kabupaten
                            </Label>
                            <select
                                id="kabupaten"
                                v-model="selectedKabupatenKode"
                                :disabled="!selectedProvinsiKode"
                                class="flex h-10 w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] focus:border-rose-300 focus:ring-[3px] focus:ring-rose-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-700 dark:focus:border-rose-400 dark:focus:ring-rose-900"
                            >
                                <option value="" disabled>
                                    {{ selectedProvinsiKode ? 'Pilih Kabupaten' : 'Pilih provinsi dulu' }}
                                </option>
                                <option
                                    v-for="item in filteredKabupaten"
                                    :key="item.kode"
                                    :value="item.kode"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>
                        </div>
                        <div class="grid gap-1.5">
                            <Label
                                for="kecamatan"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Kecamatan
                            </Label>
                            <select
                                id="kecamatan"
                                v-model="selectedKecamatanKode"
                                :disabled="!selectedKabupatenKode"
                                class="flex h-10 w-full rounded-xl border border-zinc-200 bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] focus:border-rose-300 focus:ring-[3px] focus:ring-rose-200 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-700 dark:focus:border-rose-400 dark:focus:ring-rose-900"
                            >
                                <option value="" disabled>
                                    {{ selectedKabupatenKode ? 'Pilih Kecamatan' : 'Pilih kabupaten dulu' }}
                                </option>
                                <option
                                    v-for="item in filteredKecamatan"
                                    :key="item.kode"
                                    :value="item.kode"
                                >
                                    {{ item.nama }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div
                            v-for="field in textFields"
                            :key="field.key"
                            class="grid gap-1.5"
                        >
                            <Label
                                :for="field.key"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                {{ field.label }}
                            </Label>
                            <Input
                                :id="field.key"
                                v-model="form[field.key]"
                                :type="field.type ?? 'text'"
                                class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- ────────────── MEDIA ────────────── -->
            <section ref="mediaSection" class="scroll-mt-24">
                <div class="mb-4 flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400"
                    >
                        <span class="text-sm font-bold">2</span>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">
                        Media & Sosial
                    </h2>
                </div>
                <div
                    class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8"
                >
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div
                            v-for="field in mediaFields"
                            :key="field.key"
                            class="grid gap-1.5"
                        >
                            <Label
                                :for="field.key"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                {{ field.label }}
                            </Label>
                            <Input
                                :id="field.key"
                                v-model="form[field.key]"
                                type="url"
                                :placeholder="field.placeholder"
                                class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- ────────────── GEOGRAFI ────────────── -->
            <section ref="geoSection" class="scroll-mt-24">
                <div class="mb-4 flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400"
                    >
                        <span class="text-sm font-bold">3</span>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">
                        Geografi & Batas Wilayah
                    </h2>
                </div>
                <div
                    class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8"
                >
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <div
                            v-for="field in geoFields"
                            :key="field.key"
                            class="grid gap-1.5"
                        >
                            <Label
                                :for="field.key"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                {{ field.label }}
                            </Label>
                            <Input
                                :id="field.key"
                                v-model="form[field.key]"
                                type="text"
                                class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- ────────────── VISI & SEJARAH ────────────── -->
            <section ref="visiSection" class="scroll-mt-24">
                <div class="mb-4 flex items-center gap-3">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400"
                    >
                        <span class="text-sm font-bold">4</span>
                    </div>
                    <h2 class="text-lg font-semibold text-zinc-800 dark:text-zinc-200">
                        Visi, Misi & Sejarah
                    </h2>
                </div>
                <div class="space-y-6">
                    <div
                        class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8"
                    >
                        <div class="grid gap-1.5">
                            <Label
                                for="visi"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Visi
                            </Label>
                            <TiptapEditor
                                id="visi"
                                v-model="form.visi"
                                placeholder="Tulis visi desa..."
                                :min-height="'100px'"
                            />
                        </div>
                    </div>
                    <div
                        class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8"
                    >
                        <div class="grid gap-1.5">
                            <Label
                                for="misi"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Misi
                            </Label>
                            <TiptapEditor
                                id="misi"
                                v-model="form.misi"
                                placeholder="Tulis misi desa..."
                                :min-height="'160px'"
                            />
                        </div>
                    </div>
                    <div
                        class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8"
                    >
                        <div class="grid gap-1.5">
                            <Label
                                for="sejarah"
                                class="text-sm font-medium text-zinc-700 dark:text-zinc-300"
                            >
                                Sejarah Desa
                            </Label>
                            <TiptapEditor
                                id="sejarah"
                                v-model="form.sejarah"
                                placeholder="Tulis sejarah desa..."
                                :min-height="'250px'"
                            />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sticky bottom save (mobile) -->
            <div class="sticky bottom-4 z-40 flex justify-center sm:hidden">
                <Button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full max-w-xs gap-2 rounded-full bg-rose-500 text-white shadow-lg hover:bg-rose-600"
                >
                    <Save class="h-4 w-4" />
                    Simpan Perubahan
                </Button>
            </div>
        </form>
    </div>
</template>
