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
import { ArrowLeft, FileText, Save } from '@lucide/vue';

interface Page {
    id: number;
    judul: string;
    slug: string;
    konten: string;
    status: string;
}

const props = defineProps<{
    page?: Page;
}>();

const isEdit = computed(() => !!props.page);

const form = useForm({
    judul: props.page?.judul || '',
    slug: props.page?.slug || '',
    konten: props.page?.konten || '',
    status: props.page?.status || 'draft',
});

const submitForm = () => {
    if (isEdit.value) {
        form.put(`/admin/halaman/${props.page!.id}`, {
            onSuccess: () => router.visit('/admin/halaman'),
        });
    } else {
        form.post('/admin/halaman', {
            onSuccess: () => router.visit('/admin/halaman'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Halaman' : 'Tambah Halaman'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/halaman" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <FileText class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Halaman' : 'Tambah Halaman' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui konten halaman statis' : 'Buat halaman statis baru' }}
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
                            <Label for="judul" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Judul Halaman</Label>
                            <Input
                                id="judul"
                                v-model="form.judul"
                                required
                                placeholder="Masukkan judul halaman..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.judul" class="text-sm text-red-500">{{ form.errors.judul }}</p>
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="slug" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Slug</Label>
                            <Input
                                id="slug"
                                v-model="form.slug"
                                required
                                placeholder="Masukkan slug halaman..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.slug" class="text-sm text-red-500">{{ form.errors.slug }}</p>
                        </div>
                    </div>

                    <!-- Konten -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="konten" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Konten</Label>
                            <TiptapEditor
                                id="konten"
                                v-model="form.konten"
                                placeholder="Tulis konten halaman..."
                                :min-height="'400px'"
                            />
                            <p v-if="form.errors.konten" class="text-sm text-red-500">{{ form.errors.konten }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Pengaturan</h3>
                        <div class="space-y-5">
                            <div class="grid gap-1.5">
                                <Label for="status" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="draft">Draft</SelectItem>
                                        <SelectItem value="publish">Publikasi</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.status" class="text-sm text-red-500">{{ form.errors.status }}</p>
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
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Halaman' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/halaman')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
