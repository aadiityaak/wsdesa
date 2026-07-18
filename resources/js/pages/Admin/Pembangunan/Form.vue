<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Construction, Save } from '@lucide/vue';

interface Category {
    id: number;
    nama: string;
}

interface Development {
    id: number;
    nama: string;
    lokasi: string;
    deskripsi: string;
    anggaran: number;
    sumber_dana: string;
    tahun: number;
    status: string;
    latitude: number | null;
    longitude: number | null;
    category?: Category;
}

const props = defineProps<{
    development?: Development;
    categories: Category[];
}>();

const isEdit = computed(() => !!props.development);

const form = useForm({
    nama: props.development?.nama || '',
    development_category_id: props.development?.category?.id?.toString() || '',
    lokasi: props.development?.lokasi || '',
    deskripsi: props.development?.deskripsi || '',
    anggaran: props.development?.anggaran?.toString() || '',
    sumber_dana: props.development?.sumber_dana || '',
    tahun: props.development?.tahun?.toString() || new Date().getFullYear().toString(),
    status: props.development?.status || 'rencana',
    latitude: props.development?.latitude?.toString() || '',
    longitude: props.development?.longitude?.toString() || '',
});

const submitForm = () => {
    if (isEdit.value) {
        form.put(`/admin/pembangunan/${props.development!.id}`, {
            onSuccess: () => router.visit('/admin/pembangunan'),
        });
    } else {
        form.post('/admin/pembangunan', {
            onSuccess: () => router.visit('/admin/pembangunan'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Pembangunan' : 'Tambah Pembangunan'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/pembangunan" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <Construction class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Pembangunan' : 'Tambah Pembangunan' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui data pembangunan desa' : 'Tambah data pembangunan desa baru' }}
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Main content -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Nama -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="nama" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Pembangunan</Label>
                            <Input
                                id="nama"
                                v-model="form.nama"
                                required
                                placeholder="Masukkan nama pembangunan..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.nama" class="text-sm text-red-500">{{ form.errors.nama }}</p>
                        </div>
                    </div>

                    <!-- Lokasi -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="lokasi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Lokasi</Label>
                            <Input
                                id="lokasi"
                                v-model="form.lokasi"
                                required
                                placeholder="Masukkan lokasi..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.lokasi" class="text-sm text-red-500">{{ form.errors.lokasi }}</p>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="deskripsi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</Label>
                            <TiptapEditor
                                id="deskripsi"
                                v-model="form.deskripsi"
                                placeholder="Tulis deskripsi pembangunan..."
                                :min-height="'250px'"
                            />
                            <p v-if="form.errors.deskripsi" class="text-sm text-red-500">{{ form.errors.deskripsi }}</p>
                        </div>
                    </div>

                    <!-- Anggaran & Sumber Dana -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="anggaran" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Anggaran</Label>
                                <Input
                                    id="anggaran"
                                    v-model="form.anggaran"
                                    type="number"
                                    placeholder="Masukkan anggaran..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.anggaran" class="text-sm text-red-500">{{ form.errors.anggaran }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="sumber_dana" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Sumber Dana</Label>
                                <Input
                                    id="sumber_dana"
                                    v-model="form.sumber_dana"
                                    placeholder="Masukkan sumber dana..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.sumber_dana" class="text-sm text-red-500">{{ form.errors.sumber_dana }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tahun & Status -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="tahun" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tahun</Label>
                                <Input
                                    id="tahun"
                                    v-model="form.tahun"
                                    type="number"
                                    placeholder="Masukkan tahun..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.tahun" class="text-sm text-red-500">{{ form.errors.tahun }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="status" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="rencana">Rencana</SelectItem>
                                        <SelectItem value="berjalan">Berjalan</SelectItem>
                                        <SelectItem value="selesai">Selesai</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.status" class="text-sm text-red-500">{{ form.errors.status }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Koordinat -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="latitude" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Latitude</Label>
                                <Input
                                    id="latitude"
                                    v-model="form.latitude"
                                    placeholder="Masukkan latitude..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.latitude" class="text-sm text-red-500">{{ form.errors.latitude }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="longitude" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Longitude</Label>
                                <Input
                                    id="longitude"
                                    v-model="form.longitude"
                                    placeholder="Masukkan longitude..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.longitude" class="text-sm text-red-500">{{ form.errors.longitude }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Kategori -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Pengaturan</h3>
                        <div class="space-y-5">
                            <div class="grid gap-1.5">
                                <Label for="development_category_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kategori</Label>
                                <Select v-model="form.development_category_id">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id.toString()">
                                            {{ cat.nama }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.development_category_id" class="text-sm text-red-500">{{ form.errors.development_category_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex flex-col gap-3">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                        >
                            <Save class="h-4 w-4" />
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Pembangunan' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/pembangunan')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
