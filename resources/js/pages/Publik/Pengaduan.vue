<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectItemText, SelectTrigger, SelectValue } from '@/components/ui/select';
import { MessageSquareWarning } from '@lucide/vue';

interface Category {
    id: number;
    nama: string;
}

defineProps<{
    categories?: Category[];
}>();

const successTrackingCode = ref<string | null>(null);

const form = useForm({
    nama: '',
    email: '',
    telepon: '',
    complaint_category_id: null as number | null,
    isi: '',
});

const submitForm = () => {
    form.post('/pengaduan', {
        onSuccess: (page: any) => {
            successTrackingCode.value = page.props?.tracking_code || null;
        },
    });
};
</script>

<template>
    <Head title="Pengaduan" />

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <MessageSquareWarning class="mx-auto size-12 text-blue-600 dark:text-blue-400" />
            <h1 class="mt-4 text-3xl font-bold text-zinc-900 dark:text-white">Pengaduan</h1>
            <p class="mt-2 text-zinc-500 dark:text-zinc-400">Sampaikan pengaduan atau aspirasi Anda kepada pemerintah desa.</p>
        </div>

        <div v-if="successTrackingCode" class="rounded-lg bg-green-50 p-6 text-center dark:bg-green-950">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">Pengaduan berhasil dikirim!</p>
            <p class="mt-2 text-lg font-bold text-green-700 dark:text-green-300">Kode Tracking: {{ successTrackingCode }}</p>
            <p class="mt-1 text-xs text-green-600 dark:text-green-400">Simpan kode ini untuk mengecek status pengaduan Anda.</p>
        </div>

        <Card v-else>
            <CardHeader>
                <CardTitle>Form Pengaduan</CardTitle>
                <CardDescription>Isi formulir berikut untuk menyampaikan pengaduan.</CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <Label for="nama">Nama <span class="text-red-500">*</span></Label>
                            <Input id="nama" v-model="form.nama" placeholder="Nama lengkap" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input id="email" v-model="form.email" type="email" placeholder="email@contoh.com" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label for="telepon">Telepon</Label>
                        <Input id="telepon" v-model="form.telepon" placeholder="Nomor telepon" />
                    </div>
                    <div v-if="categories?.length" class="space-y-2">
                        <Label for="category">Kategori Pengaduan</Label>
                        <Select v-model="form.complaint_category_id">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Pilih kategori..." />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id">
                                        <SelectItemText>{{ cat.nama }}</SelectItemText>
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="space-y-2">
                        <Label for="isi">Isi Pengaduan <span class="text-red-500">*</span></Label>
                        <textarea
                            id="isi"
                            v-model="form.isi"
                            rows="5"
                            required
                            placeholder="Tulis pengaduan atau aspirasi Anda..."
                            class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50"
                        ></textarea>
                    </div>
                    <Button type="submit" class="w-full" :disabled="form.processing">
                        Kirim Pengaduan
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
