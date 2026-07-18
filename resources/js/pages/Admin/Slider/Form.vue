<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Checkbox } from '@/components/ui/checkbox';
import { ArrowLeft, Camera, Save, SlidersHorizontal } from '@lucide/vue';

interface Slider {
    id: number;
    judul: string;
    deskripsi: string;
    image: string;
    link: string;
    is_active: boolean;
}

const props = defineProps<{
    slider?: Slider;
}>();

const isEdit = computed(() => !!props.slider);

const form = useForm({
    judul: props.slider?.judul || '',
    deskripsi: props.slider?.deskripsi || '',
    image: null as File | null,
    link: props.slider?.link || '',
    is_active: props.slider?.is_active ?? true,
});

const imagePreview = ref<string | null>(
    props.slider?.image ? `/storage/${props.slider.image}` : null,
);

const onImageChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.image = file;
    if (file) {
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    if (isEdit.value) {
        const url = `/admin/slider/${props.slider!.id}`;
        if (form.image instanceof File) {
            form.transform((data) => ({ ...data, _method: 'put' })).post(url, {
                onSuccess: () => router.visit('/admin/slider'),
            });
        } else {
            form.put(url, {
                onSuccess: () => router.visit('/admin/slider'),
            });
        }
    } else {
        form.post('/admin/slider', {
            onSuccess: () => router.visit('/admin/slider'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Slider' : 'Tambah Slider'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/slider" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <SlidersHorizontal class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Slider' : 'Tambah Slider' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui slider tampilan' : 'Tambah slider tampilan baru' }}
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Main content -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Judul -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="judul" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Judul Slider</Label>
                            <Input
                                id="judul"
                                v-model="form.judul"
                                required
                                placeholder="Masukkan judul slider..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.judul" class="text-sm text-red-500">{{ form.errors.judul }}</p>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="deskripsi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</Label>
                            <Textarea
                                id="deskripsi"
                                v-model="form.deskripsi"
                                placeholder="Tulis deskripsi slider..."
                                class="min-h-28 rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.deskripsi" class="text-sm text-red-500">{{ form.errors.deskripsi }}</p>
                        </div>
                    </div>

                    <!-- Link -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="link" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Link</Label>
                            <Input
                                id="link"
                                v-model="form.link"
                                placeholder="Masukkan link (opsional)..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.link" class="text-sm text-red-500">{{ form.errors.link }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Pengaturan -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Pengaturan</h3>
                        <div class="flex items-center gap-2">
                            <Checkbox id="is_active" v-model:checked="form.is_active" />
                            <Label for="is_active" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Aktif</Label>
                        </div>
                        <p v-if="form.errors.is_active" class="text-sm text-red-500">{{ form.errors.is_active }}</p>
                    </div>

                    <!-- Gambar -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Gambar</h3>
                        <div class="grid gap-4">
                            <div class="flex items-center justify-center overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                <img
                                    v-if="imagePreview"
                                    :src="imagePreview"
                                    alt="Preview"
                                    class="w-full object-cover"
                                    style="max-height: 180px;"
                                />
                                <div v-else class="flex h-36 w-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                                    <Camera class="h-10 w-10" />
                                </div>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="image" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-xs transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                    <Camera class="h-4 w-4" />
                                    Pilih Gambar
                                </Label>
                                <Input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @input="onImageChange"
                                />
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                                    JPG, PNG, WEBP. Maks 2MB.
                                </p>
                                <p v-if="form.errors.image" class="text-sm text-red-500">{{ form.errors.image }}</p>
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
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Slider' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/slider')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
