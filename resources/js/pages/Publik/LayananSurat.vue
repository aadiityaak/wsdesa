<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectItemText, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import {
    FileText, ClipboardList, Send, Search, CheckCircle2, Clock, ArrowRight,
    CheckSquare,
} from '@lucide/vue';

interface Requirement {
    id: number;
    nama: string;
    wajib: boolean;
}

interface Category {
    id: number;
    nama: string;
    slug: string;
    requirements: Requirement[];
}

defineProps<{
    categories: Category[];
}>();

// Cek status
const trackingCode = ref('');
const cekResult = ref<Record<string, any> | null>(null);
const cekLoading = ref(false);
const cekError = ref('');

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const checkStatus = async () => {
    if (!trackingCode.value.trim()) return;
    cekLoading.value = true;
    cekError.value = '';
    cekResult.value = null;
    try {
        const res = await fetch('/layanan-surat/cek-status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ kode_tracking: trackingCode.value.trim() }),
        });
        const data = await res.json();
        if (data.letterRequest) {
            cekResult.value = data.letterRequest;
        } else {
            cekError.value = data.error || 'Data tidak ditemukan.';
        }
    } catch {
        cekError.value = 'Gagal memeriksa status. Coba lagi.';
    }
    cekLoading.value = false;
};

// Form
const selectedCategory = ref<Category | null>(null);
const dialogOpen = ref(false);
const successTrackingCode = ref<string | null>(null);

const form = useForm({
    letter_category_id: null as number | null,
    nama_pemohon: '',
    nik: '',
    tempat_lahir: '',
    tanggal_lahir: '',
    jenis_kelamin: '',
    agama: '',
    pekerjaan: '',
    alamat: '',
    keperluan: '',
    no_hp: '',
});

const genderOpts = [
    { value: 'L', label: 'Laki-laki' },
    { value: 'P', label: 'Perempuan' },
];

const agamaOpts = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];

const openDialog = (cat: Category) => {
    selectedCategory.value = cat;
    form.reset();
    form.letter_category_id = cat.id;
    successTrackingCode.value = null;
    dialogOpen.value = true;
};

const submitForm = () => {
    form.post('/layanan-surat/ajukan', {
        onSuccess: (page: any) => {
            successTrackingCode.value = page.props?.flash?.tracking_code || null;
        },
    });
};

const formatStatus = (s: string) => {
    const map: Record<string, string> = {
        pending: 'Menunggu', diproses: 'Diproses', selesai: 'Selesai', ditolak: 'Ditolak',
    };
    return map[s] || s;
};

const categoryIcons: Record<string, any> = {
    'surat keterangan': FileText,
    'surat izin': ClipboardList,
    'surat pengantar': Send,
};

const getCatIcon = (nama: string) => {
    const key = Object.keys(categoryIcons).find(k => nama.toLowerCase().includes(k));
    return key ? categoryIcons[key] : FileText;
};
</script>

<template>
    <Head title="Layanan Surat" />

    <!-- Hero -->
    <section class="bg-gradient-to-br from-blue-600 to-blue-800 py-12 md:py-16">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <FileText class="mx-auto size-10 text-blue-200" />
            <h1 class="mt-4 text-2xl font-bold text-white sm:text-3xl">Layanan Surat Online</h1>
            <p class="mt-2 text-blue-100">Ajukan surat secara online tanpa perlu datang ke kantor desa.</p>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        <!-- Steps -->
        <div class="mb-10 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="flex items-start gap-3 rounded-xl border bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">1</span>
                <div>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">Pilih Jenis Surat</p>
                    <p class="mt-0.5 text-xs text-zinc-500">Tentukan surat yang Anda perlukan</p>
                </div>
            </div>
            <div class="flex items-start gap-3 rounded-xl border bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">2</span>
                <div>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">Isi Data Diri</p>
                    <p class="mt-0.5 text-xs text-zinc-500">Lengkapi formulir dengan data valid</p>
                </div>
            </div>
            <div class="flex items-start gap-3 rounded-xl border bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700 dark:bg-blue-900 dark:text-blue-300">3</span>
                <div>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">Dapatkan Kode Tracking</p>
                    <p class="mt-0.5 text-xs text-zinc-500">Pantau status pengajuan secara online</p>
                </div>
            </div>
        </div>

        <!-- Cek Status -->
        <Card class="mb-10 border-blue-100 dark:border-blue-900">
            <CardHeader class="flex-row items-center justify-between">
                <div>
                    <CardTitle class="flex items-center gap-2 text-base"><Search class="size-4 text-blue-600" /> Cek Status Surat</CardTitle>
                    <CardDescription>Sudah mengajukan? Masukkan kode tracking untuk cek status.</CardDescription>
                </div>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="checkStatus" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <Label for="tracking" class="sr-only">Kode Tracking</Label>
                        <Input id="tracking" v-model="trackingCode" placeholder="Masukkan kode tracking..." class="h-10" />
                    </div>
                    <Button type="submit" :disabled="cekLoading || !trackingCode.trim()">
                        <Search class="size-4" />
                        <span v-if="cekLoading">Memeriksa...</span>
                        <span v-else>Cek Status</span>
                    </Button>
                </form>

                <p v-if="cekError" class="mt-3 text-sm text-red-600">{{ cekError }}</p>

                <div v-if="cekResult" class="mt-4 space-y-2 rounded-lg border bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-800">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-500">Kode Tracking</span>
                        <span class="font-mono font-semibold text-zinc-900 dark:text-white">{{ cekResult.kode_tracking }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-500">Pemohon</span>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ cekResult.nama_pemohon }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-500">Jenis Surat</span>
                        <span class="font-medium text-zinc-900 dark:text-white">{{ cekResult.jenis_surat || cekResult.category?.nama }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-500">Status</span>
                        <Badge :variant="cekResult.status === 'selesai' ? 'default' : 'secondary'">
                            <Clock v-if="cekResult.status !== 'selesai'" class="mr-1 size-3" />
                            <CheckCircle2 v-else class="mr-1 size-3" />
                            {{ formatStatus(cekResult.status) }}
                        </Badge>
                    </div>
                    <div v-if="cekResult.catatan_admin" class="mt-2 rounded-md bg-amber-50 p-3 text-sm dark:bg-amber-950">
                        <span class="font-medium text-amber-700 dark:text-amber-300">Catatan:</span>
                        <p class="mt-0.5 text-amber-800 dark:text-amber-200">{{ cekResult.catatan_admin }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Empty -->
        <div v-if="categories.length === 0" class="flex min-h-[30vh] items-center justify-center rounded-xl border-2 border-dashed bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="p-8 text-center">
                <FileText class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada jenis surat</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Jenis surat akan ditampilkan setelah tersedia.</p>
            </div>
        </div>

        <!-- Cards -->
        <div v-else>
            <h2 class="mb-5 text-xl font-bold text-zinc-900 dark:text-white">Pilih Jenis Surat</h2>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <Card v-for="cat in categories" :key="cat.id" class="transition-shadow hover:shadow-md">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950">
                                <component :is="getCatIcon(cat.nama)" class="size-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <Badge variant="outline" class="shrink-0 text-xs">{{ cat.requirements?.length || 0 }} syarat</Badge>
                        </div>
                        <CardTitle class="mt-3 text-base">{{ cat.nama }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <ul v-if="cat.requirements?.length" class="space-y-1.5">
                            <li v-for="req in cat.requirements" :key="req.id" class="flex items-start gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                <CheckSquare class="mt-0.5 size-3.5 shrink-0 text-emerald-500" />
                                {{ req.nama }}
                            </li>
                        </ul>
                        <p v-else class="text-sm text-zinc-400">Tidak ada persyaratan khusus.</p>
                        <Button class="mt-2 w-full" @click="openDialog(cat)">
                            Ajukan <ArrowRight class="ml-1 size-4" />
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Dialog -->
        <Dialog :open="dialogOpen" @update:open="dialogOpen = $event">
            <DialogContent class="max-h-[90vh] max-w-lg overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Ajukan {{ selectedCategory?.nama }}</DialogTitle>
                    <DialogDescription>Isi formulir dengan data yang valid.</DialogDescription>
                </DialogHeader>

                <div v-if="successTrackingCode" class="space-y-4 text-center">
                    <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                        <CheckCircle2 class="size-8 text-green-600 dark:text-green-400" />
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Pengajuan Berhasil!</h3>
                    <div class="rounded-lg bg-green-50 p-4 dark:bg-green-950">
                        <p class="text-xs text-green-600 dark:text-green-400">Kode Tracking Anda</p>
                        <p class="mt-1 text-xl font-bold tracking-wider text-green-700 dark:text-green-300">{{ successTrackingCode }}</p>
                    </div>
                    <p class="text-sm text-zinc-500">Simpan kode ini untuk memantau status pengajuan.</p>
                    <div class="flex gap-3">
                        <Button variant="outline" class="flex-1" @click="dialogOpen = false">Tutup</Button>
                        <Button class="flex-1" @click="trackingCode = successTrackingCode!; dialogOpen = false">Cek Status</Button>
                    </div>
                </div>

                <form v-else @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="nama_pemohon">Nama Lengkap <span class="text-red-500">*</span></Label>
                        <Input id="nama_pemohon" v-model="form.nama_pemohon" placeholder="Sesuai KTP" required />
                    </div>
                    <div class="space-y-2">
                        <Label for="nik">NIK <span class="text-red-500">*</span></Label>
                        <Input id="nik" v-model="form.nik" placeholder="16 digit NIK" maxlength="16" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="tempat_lahir">Tempat Lahir <span class="text-red-500">*</span></Label>
                            <Input id="tempat_lahir" v-model="form.tempat_lahir" placeholder="Tempat lahir" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="tanggal_lahir">Tgl. Lahir <span class="text-red-500">*</span></Label>
                            <Input id="tanggal_lahir" v-model="form.tanggal_lahir" type="date" required />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="jenis_kelamin">Jenis Kelamin <span class="text-red-500">*</span></Label>
                            <Select v-model="form.jenis_kelamin">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="g in genderOpts" :key="g.value" :value="g.value">
                                            <SelectItemText>{{ g.label }}</SelectItemText>
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-2">
                            <Label for="agama">Agama <span class="text-red-500">*</span></Label>
                            <Select v-model="form.agama">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem v-for="a in agamaOpts" :key="a" :value="a">
                                            <SelectItemText>{{ a }}</SelectItemText>
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label for="pekerjaan">Pekerjaan <span class="text-red-500">*</span></Label>
                        <Input id="pekerjaan" v-model="form.pekerjaan" placeholder="Pekerjaan saat ini" required />
                    </div>
                    <div class="space-y-2">
                        <Label for="alamat">Alamat <span class="text-red-500">*</span></Label>
                        <textarea id="alamat" v-model="form.alamat" rows="3" placeholder="Alamat lengkap sesuai KTP"
                            class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            required></textarea>
                    </div>
                    <div class="space-y-2">
                        <Label for="keperluan">Keperluan <span class="text-red-500">*</span></Label>
                        <textarea id="keperluan" v-model="form.keperluan" rows="3" placeholder="Jelaskan keperluan pengajuan"
                            class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                            required></textarea>
                    </div>
                    <div class="space-y-2">
                        <Label for="no_hp">No. HP <span class="text-red-500">*</span></Label>
                        <Input id="no_hp" v-model="form.no_hp" placeholder="Nomor handphone aktif" required />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">{{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan' }}</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
