<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ArrowLeft, Megaphone, Save } from '@lucide/vue';

interface Announcement {
    id: number;
    judul: string;
    konten: string;
    tipe: string;
    tanggal_mulai: string;
    tanggal_selesai: string;
    is_active: boolean;
}

const props = defineProps<{
    announcement?: Announcement;
}>();

const isEdit = computed(() => !!props.announcement);

const form = useForm({
    judul: props.announcement?.judul || '',
    konten: props.announcement?.konten || '',
    tipe: props.announcement?.tipe || 'info',
    tanggal_mulai: props.announcement?.tanggal_mulai || '',
    tanggal_selesai: props.announcement?.tanggal_selesai || '',
    is_active: props.announcement?.is_active ?? true,
});

const submitForm = () => {
    if (isEdit.value) {
        form.put(`/admin/pengumuman/${props.announcement!.id}`, {
            onSuccess: () => router.visit('/admin/pengumuman'),
        });
    } else {
        form.post('/admin/pengumuman', {
            onSuccess: () => router.visit('/admin/pengumuman'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Pengumuman' : 'Tambah Pengumuman'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/pengumuman" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <Megaphone class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Pengumuman' : 'Tambah Pengumuman' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui pengumuman desa' : 'Buat pengumuman desa baru' }}
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
                            <Label for="judul" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Judul Pengumuman</Label>
                            <Input
                                id="judul"
                                v-model="form.judul"
                                required
                                placeholder="Masukkan judul pengumuman..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.judul" class="text-sm text-red-500">{{ form.errors.judul }}</p>
                        </div>
                    </div>

                    <!-- Konten -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="konten" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Konten</Label>
                            <TiptapEditor
                                id="konten"
                                v-model="form.konten"
                                placeholder="Tulis konten pengumuman..."
                                :min-height="'300px'"
                            />
                            <p v-if="form.errors.konten" class="text-sm text-red-500">{{ form.errors.konten }}</p>
                        </div>
                    </div>

                    <!-- Tanggal -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="tanggal_mulai" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tanggal Mulai</Label>
                                <Input
                                    id="tanggal_mulai"
                                    v-model="form.tanggal_mulai"
                                    type="date"
                                    required
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.tanggal_mulai" class="text-sm text-red-500">{{ form.errors.tanggal_mulai }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="tanggal_selesai" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tanggal Selesai</Label>
                                <Input
                                    id="tanggal_selesai"
                                    v-model="form.tanggal_selesai"
                                    type="date"
                                    required
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.tanggal_selesai" class="text-sm text-red-500">{{ form.errors.tanggal_selesai }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Pengaturan -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Pengaturan</h3>
                        <div class="space-y-5">
                            <div class="grid gap-1.5">
                                <Label for="tipe" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipe</Label>
                                <Select v-model="form.tipe">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih tipe" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="info">Info</SelectItem>
                                        <SelectItem value="warning">Warning</SelectItem>
                                        <SelectItem value="urgent">Urgent</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.tipe" class="text-sm text-red-500">{{ form.errors.tipe }}</p>
                            </div>

                            <div class="flex items-center gap-2">
                                <Checkbox id="is_active" v-model:checked="form.is_active" />
                                <Label for="is_active" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Aktif</Label>
                            </div>
                            <p v-if="form.errors.is_active" class="text-sm text-red-500">{{ form.errors.is_active }}</p>
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
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Pengumuman' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/pengumuman')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
