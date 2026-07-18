<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Download, Save, Upload } from '@lucide/vue';

interface Category {
    id: number;
    nama: string;
}

interface DownloadItem {
    id: number;
    nama: string;
    download_category_id: number;
    file: string;
    counter: number;
    category?: Category;
}

const props = defineProps<{
    download?: DownloadItem;
    categories: Category[];
}>();

const isEdit = computed(() => !!props.download);

const form = useForm({
    nama: props.download?.nama || '',
    download_category_id: props.download?.download_category_id?.toString() || '',
    file: null as File | null,
});

const submitForm = () => {
    if (isEdit.value) {
        const url = `/admin/download/${props.download!.id}`;
        if (form.file instanceof File) {
            form.transform((data) => ({ ...data, _method: 'put' })).post(url, {
                onSuccess: () => router.visit('/admin/download'),
            });
        } else {
            form.put(url, {
                onSuccess: () => router.visit('/admin/download'),
            });
        }
    } else {
        form.post('/admin/download', {
            onSuccess: () => router.visit('/admin/download'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Download' : 'Tambah Download'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/download" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <Download class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Download' : 'Tambah Download' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui file download' : 'Tambah file download baru' }}
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
                            <Label for="nama" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nama File</Label>
                            <Input
                                id="nama"
                                v-model="form.nama"
                                required
                                placeholder="Masukkan nama file..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.nama" class="text-sm text-red-500">{{ form.errors.nama }}</p>
                        </div>
                    </div>

                    <!-- File -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="file" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">File</Label>
                            <Label for="file" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm font-medium text-zinc-700 shadow-xs transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                <Upload class="h-4 w-4" />
                                Pilih File
                            </Label>
                            <Input
                                id="file"
                                type="file"
                                class="hidden"
                                @input="(e) => (form.file = (e.target as HTMLInputElement).files?.[0] ?? null)"
                            />
                            <p v-if="isEdit" class="text-xs text-zinc-400 dark:text-zinc-500">
                                Kosongkan jika tidak ingin mengganti file.
                            </p>
                            <p v-if="form.errors.file" class="text-sm text-red-500">{{ form.errors.file }}</p>
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
                                <Label for="download_category_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kategori</Label>
                                <Select v-model="form.download_category_id">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id.toString()">
                                            {{ cat.nama }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.download_category_id" class="text-sm text-red-500">{{ form.errors.download_category_id }}</p>
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
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Download' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/download')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
