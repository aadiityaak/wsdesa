<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Camera, Save, TrendingUp } from '@lucide/vue';

interface Category {
    id: number;
    nama: string;
}

interface Potential {
    id: number;
    nama: string;
    category_id: number;
    lokasi: string;
    deskripsi: string;
    category?: Category;
}

const props = defineProps<{
    potential?: Potential;
    categories: Category[];
}>();

const isEdit = computed(() => !!props.potential);

const form = useForm({
    nama: props.potential?.nama || '',
    category_id: props.potential?.category_id?.toString() || '',
    lokasi: props.potential?.lokasi || '',
    deskripsi: props.potential?.deskripsi || '',
    thumbnail: null as File | null,
});

const gambarPreview = ref<string | null>(
    props.potential?.thumbnail ? `/storage/${props.potential.thumbnail}` : null,
);

const onGambarChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.thumbnail = file;
    if (file) {
        gambarPreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    if (isEdit.value) {
        const url = `/admin/potensi/${props.potential!.id}`;
        if (form.thumbnail instanceof File) {
            form.transform((data) => ({ ...data, _method: 'put' })).post(url, {
                onSuccess: () => router.visit('/admin/potensi'),
            });
        } else {
            form.put(url, {
                onSuccess: () => router.visit('/admin/potensi'),
            });
        }
    } else {
        form.post('/admin/potensi', {
            onSuccess: () => router.visit('/admin/potensi'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Potensi' : 'Tambah Potensi'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/potensi" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <TrendingUp class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Potensi' : 'Tambah Potensi' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui data potensi desa' : 'Tambah data potensi desa baru' }}
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
                            <Label for="nama" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Potensi</Label>
                            <Input
                                id="nama"
                                v-model="form.nama"
                                required
                                placeholder="Masukkan nama potensi..."
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
                            <Textarea
                                id="deskripsi"
                                v-model="form.deskripsi"
                                placeholder="Tulis deskripsi potensi..."
                                class="min-h-36 rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.deskripsi" class="text-sm text-red-500">{{ form.errors.deskripsi }}</p>
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
                                <Label for="category_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kategori</Label>
                                <Select v-model="form.category_id">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id.toString()">
                                            {{ cat.nama }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.category_id" class="text-sm text-red-500">{{ form.errors.category_id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Thumbnail</h3>
                        <div class="grid gap-4">
                            <div class="flex items-center justify-center overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                <img
                                    v-if="gambarPreview"
                                    :src="gambarPreview"
                                    alt="Preview"
                                    class="w-full object-cover"
                                    style="max-height: 180px;"
                                />
                                <div v-else class="flex h-36 w-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                                    <Camera class="h-10 w-10" />
                                </div>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="thumbnail" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-xs transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                    <Camera class="h-4 w-4" />
                                    Pilih Gambar
                                </Label>
                                <Input
                                    id="thumbnail"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @input="onGambarChange"
                                />
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                                    JPG, PNG, WEBP. Maks 2MB.
                                </p>
                                <p v-if="form.errors.thumbnail" class="text-sm text-red-500">{{ form.errors.thumbnail }}</p>
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
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Potensi' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/potensi')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
