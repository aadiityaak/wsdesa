<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectItemText, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import PageHero from '@/components/PageHero.vue';
import {
    FileText, ClipboardList, Send, Search, CheckCircle2, Clock, ArrowRight,
    CheckSquare, User, MapPin, Calendar, Heart, X,
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
    tanggal_lahir: null as string | null,
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
    'surat kuasa': FileText,
    'surat keterangan catatan kepolisian': FileText,
    'surat keterangan pindah': MapPin,
    'surat keterangan ahli waris': Heart,
    'surat izin keramaian': Calendar,
};

const getCatIcon = (nama: string) => {
    const key = Object.keys(categoryIcons).find(k => nama.toLowerCase().includes(k));
    return key ? categoryIcons[key] : FileText;
};

const formatDate = (date: Date | string | null): string | null => {
    if (!date) return null;
    const d = typeof date === 'string' ? new Date(date) : date;
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
};
</script>

<template>
    <Head title="Layanan Surat" />

    <PageHero title="Layanan Surat Online" description="Ajukan surat secara online tanpa perlu datang ke kantor desa">
        <template #icon>
            <FileText class="size-6 text-white" />
        </template>
    </PageHero>

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
            <DialogContent class="max-h-[90vh] max-w-lg overflow-y-auto p-0">

                <!-- Success State -->
                <template v-if="successTrackingCode">
                    <div class="flex flex-col items-center px-8 py-12 text-center">
                        <div class="mx-auto flex size-16 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                            <CheckCircle2 class="size-8 text-green-600 dark:text-green-400" />
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-zinc-900 dark:text-white">Pengajuan Berhasil!</h3>
                        <p class="mt-1 text-sm text-zinc-500">Surat {{ selectedCategory?.nama }} berhasil diajukan.</p>

                        <div class="mt-6 w-full rounded-xl border bg-green-50 p-5 dark:border-green-800 dark:bg-green-950">
                            <p class="text-xs text-green-600 dark:text-green-400">Kode Tracking</p>
                            <p class="mt-1 text-2xl font-bold tracking-widest text-green-700 dark:text-green-300">{{ successTrackingCode }}</p>
                        </div>

                        <p class="mt-4 text-xs text-zinc-400">Simpan kode ini untuk memantau status pengajuan melalui form Cek Status di halaman ini.</p>

                        <div class="mt-6 flex w-full gap-3">
                            <Button variant="outline" class="flex-1" @click="dialogOpen = false">
                                <X class="mr-1 size-4" /> Tutup
                            </Button>
                            <Button class="flex-1" @click="trackingCode = successTrackingCode!; dialogOpen = false">
                                <Search class="mr-1 size-4" /> Cek Status
                            </Button>
                        </div>
                    </div>
                </template>

                <!-- Form State -->
                <template v-else>
                    <!-- Header dengan kategori -->
                    <div class="sticky top-0 z-10 border-b bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="flex items-center gap-3">
                            <div class="flex size-9 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950">
                                <component :is="selectedCategory ? getCatIcon(selectedCategory.nama) : FileText" class="size-4.5 text-blue-600 dark:text-blue-400" />
                            </div>
                            <div>
                                <DialogTitle class="text-sm font-semibold">{{ selectedCategory?.nama }}</DialogTitle>
                                <DialogDescription class="text-xs">Lengkapi data diri Anda di bawah ini.</DialogDescription>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-6 px-6 py-5">
                        <!-- Data Diri -->
                        <div>
                            <h4 class="mb-3 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-zinc-400">
                                <User class="size-3.5" /> Data Diri
                            </h4>
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <Label for="nama_pemohon" class="text-sm">Nama Lengkap <span class="text-red-500">*</span></Label>
                                    <Input id="nama_pemohon" v-model="form.nama_pemohon" placeholder="Sesuai KTP" required />
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="nik" class="text-sm">NIK <span class="text-red-500">*</span></Label>
                                    <Input id="nik" v-model="form.nik" placeholder="16 digit NIK" maxlength="16" required />
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <Label for="tempat_lahir" class="text-sm">Tempat Lahir <span class="text-red-500">*</span></Label>
                                        <Input id="tempat_lahir" v-model="form.tempat_lahir" placeholder="Tempat lahir" required />
                                    </div>
                                    <div class="space-y-1.5">
                                        <Label for="tanggal_lahir" class="text-sm">Tanggal Lahir <span class="text-red-500">*</span></Label>
                                        <VueDatePicker
                                            v-model="form.tanggal_lahir"
                                            :enable-time-picker="false"
                                            format="dd/MM/yyyy"
                                            placeholder="Pilih tanggal"
                                            text-input
                                            auto-apply
                                            :max-date="new Date()"
                                            class="datepicker-custom"
                                        />
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1.5">
                                        <Label for="jenis_kelamin" class="text-sm">Jenis Kelamin <span class="text-red-500">*</span></Label>
                                        <Select v-model="form.jenis_kelamin">
                                            <SelectTrigger class="w-full">
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
                                    <div class="space-y-1.5">
                                        <Label for="agama" class="text-sm">Agama <span class="text-red-500">*</span></Label>
                                        <Select v-model="form.agama">
                                            <SelectTrigger class="w-full">
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
                                <div class="space-y-1.5">
                                    <Label for="pekerjaan" class="text-sm">Pekerjaan <span class="text-red-500">*</span></Label>
                                    <Input id="pekerjaan" v-model="form.pekerjaan" placeholder="Pekerjaan saat ini" required />
                                </div>
                            </div>
                        </div>

                        <hr class="border-zinc-200 dark:border-zinc-700" />

                        <!-- Detail Tambahan -->
                        <div>
                            <h4 class="mb-3 flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-zinc-400">
                                <MapPin class="size-3.5" /> Detail Tambahan
                            </h4>
                            <div class="space-y-4">
                                <div class="space-y-1.5">
                                    <Label for="alamat" class="text-sm">Alamat <span class="text-red-500">*</span></Label>
                                    <textarea id="alamat" v-model="form.alamat" rows="3" placeholder="Alamat lengkap sesuai KTP"
                                        class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                                        required></textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="keperluan" class="text-sm">Keperluan <span class="text-red-500">*</span></Label>
                                    <textarea id="keperluan" v-model="form.keperluan" rows="3" placeholder="Jelaskan keperluan pengajuan surat ini"
                                        class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                                        required></textarea>
                                </div>
                                <div class="space-y-1.5">
                                    <Label for="no_hp" class="text-sm">No. HP <span class="text-red-500">*</span></Label>
                                    <Input id="no_hp" v-model="form.no_hp" placeholder="Nomor handphone aktif" required />
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <DialogFooter class="sticky bottom-0 -mx-6 -mb-5 border-t bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="flex w-full gap-3">
                                <Button type="button" variant="outline" class="flex-1" @click="dialogOpen = false">Batal</Button>
                                <Button type="submit" class="flex-1" :disabled="form.processing">
                                    {{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan' }}
                                </Button>
                            </div>
                        </DialogFooter>
                    </form>
                </template>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
.datepicker-custom :deep(.dp__input) {
    height: 36px;
    font-size: 0.875rem;
    padding: 0 12px;
    border-radius: calc(var(--radius) * 1px);
    border-color: oklch(0.87 0 0);
}
.dark .datepicker-custom :deep(.dp__input) {
    background-color: oklch(0.268 0.007 34.298);
    border-color: oklch(0.371 0 0);
    color: oklch(0.985 0 0);
}
.datepicker-custom :deep(.dp__input::placeholder) {
    color: oklch(0.556 0 0);
}
.dark .datepicker-custom :deep(.dp__input::placeholder) {
    color: oklch(0.637 0 0);
}
.datepicker-custom :deep(.dp__theme_dark) {
    --dp-background-color: oklch(0.268 0.007 34.298);
    --dp-text-color: oklch(0.985 0 0);
    --dp-border-color: oklch(0.371 0 0);
}
</style>
