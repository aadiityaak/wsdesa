<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { ArrowLeft, Building2, Camera, Save } from '@lucide/vue';

interface Member {
    id: number;
    nama: string;
    jabatan: string;
}

interface Institution {
    id: number;
    nama: string;
    singkatan: string;
    deskripsi: string;
    logo: string | null;
    members?: Member[];
}

const props = defineProps<{
    institution?: Institution;
}>();

const isEdit = computed(() => !!props.institution);

const form = useForm({
    nama: props.institution?.nama || '',
    singkatan: props.institution?.singkatan || '',
    deskripsi: props.institution?.deskripsi || '',
    logo: null as File | null,
});

const logoPreview = ref<string | null>(
    props.institution?.logo ? `/storage/${props.institution.logo}` : null,
);

const onLogoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.logo = file;
    if (file) {
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    if (isEdit.value) {
        const url = `/admin/lembaga/${props.institution!.id}`;
        if (form.logo instanceof File) {
            form.transform((data) => ({ ...data, _method: 'put' })).post(url, {
                onSuccess: () => router.visit('/admin/lembaga'),
            });
        } else {
            form.put(url, {
                onSuccess: () => router.visit('/admin/lembaga'),
            });
        }
    } else {
        form.post('/admin/lembaga', {
            onSuccess: () => router.visit('/admin/lembaga'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Lembaga' : 'Tambah Lembaga'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/lembaga" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <Building2 class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Lembaga' : 'Tambah Lembaga' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui data lembaga desa' : 'Tambah data lembaga desa baru' }}
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Main content -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Nama & Singkatan -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="nama" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama Lembaga</Label>
                                <Input
                                    id="nama"
                                    v-model="form.nama"
                                    required
                                    placeholder="Masukkan nama lembaga..."
                                    class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.nama" class="text-sm text-red-500">{{ form.errors.nama }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="singkatan" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Singkatan</Label>
                                <Input
                                    id="singkatan"
                                    v-model="form.singkatan"
                                    placeholder="Masukkan singkatan..."
                                    class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.singkatan" class="text-sm text-red-500">{{ form.errors.singkatan }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="deskripsi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</Label>
                            <Textarea
                                id="deskripsi"
                                v-model="form.deskripsi"
                                placeholder="Tulis deskripsi lembaga..."
                                class="min-h-36 rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.deskripsi" class="text-sm text-red-500">{{ form.errors.deskripsi }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Logo -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Logo</h3>
                        <div class="grid gap-4">
                            <div class="flex items-center justify-center overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                <img
                                    v-if="logoPreview"
                                    :src="logoPreview"
                                    alt="Preview"
                                    class="w-full object-cover"
                                    style="max-height: 180px;"
                                />
                                <div v-else class="flex h-36 w-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                                    <Camera class="h-10 w-10" />
                                </div>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="logo" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-xs transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                    <Camera class="h-4 w-4" />
                                    Pilih Logo
                                </Label>
                                <Input
                                    id="logo"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @input="onLogoChange"
                                />
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">
                                    JPG, PNG, WEBP. Maks 2MB.
                                </p>
                                <p v-if="form.errors.logo" class="text-sm text-red-500">{{ form.errors.logo }}</p>
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
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Lembaga' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/lembaga')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
