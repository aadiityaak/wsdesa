<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
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
import { ArrowLeft, Save, Wallet } from '@lucide/vue';

interface Category {
    id: number;
    nama: string;
}

interface Budget {
    id: number;
    budget_category_id: number;
    tahun: number;
    anggaran: number;
    realisasi: number;
    keterangan: string;
    category?: Category;
}

const props = defineProps<{
    budget?: Budget;
    categories: Category[];
}>();

const isEdit = computed(() => !!props.budget);

const form = useForm({
    budget_category_id: props.budget?.budget_category_id?.toString() || '',
    tahun: props.budget?.tahun?.toString() || new Date().getFullYear().toString(),
    anggaran: props.budget?.anggaran?.toString() || '',
    realisasi: props.budget?.realisasi?.toString() || '',
    keterangan: props.budget?.keterangan || '',
});

const submitForm = () => {
    if (isEdit.value) {
        form.put(`/admin/apbdes/${props.budget!.id}`, {
            onSuccess: () => router.visit('/admin/apbdes'),
        });
    } else {
        form.post('/admin/apbdes', {
            onSuccess: () => router.visit('/admin/apbdes'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit APBDes' : 'Tambah APBDes'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/apbdes" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                    <Wallet class="h-6 w-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit APBDes' : 'Tambah APBDes' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui data anggaran desa' : 'Tambah data anggaran desa baru' }}
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Main content -->
                <div class="space-y-6 lg:col-span-2">
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="budget_category_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kategori</Label>
                                <Select v-model="form.budget_category_id">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id.toString()">
                                            {{ cat.nama }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.budget_category_id" class="text-sm text-red-500">{{ form.errors.budget_category_id }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="tahun" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tahun</Label>
                                <Input
                                    id="tahun"
                                    v-model="form.tahun"
                                    type="number"
                                    required
                                    placeholder="Masukkan tahun..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.tahun" class="text-sm text-red-500">{{ form.errors.tahun }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="anggaran" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Anggaran</Label>
                                <Input
                                    id="anggaran"
                                    v-model="form.anggaran"
                                    type="number"
                                    required
                                    placeholder="Masukkan anggaran..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.anggaran" class="text-sm text-red-500">{{ form.errors.anggaran }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="realisasi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Realisasi</Label>
                                <Input
                                    id="realisasi"
                                    v-model="form.realisasi"
                                    type="number"
                                    placeholder="Masukkan realisasi..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.realisasi" class="text-sm text-red-500">{{ form.errors.realisasi }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="keterangan" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Keterangan</Label>
                            <Textarea
                                id="keterangan"
                                v-model="form.keterangan"
                                placeholder="Tulis keterangan..."
                                class="min-h-28 rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.keterangan" class="text-sm text-red-500">{{ form.errors.keterangan }}</p>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="flex flex-col gap-3">
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600"
                        >
                            <Save class="h-4 w-4" />
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah APBDes' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/apbdes')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
