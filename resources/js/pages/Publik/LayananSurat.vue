<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectGroup, SelectItem, SelectItemText, SelectTrigger, SelectValue } from '@/components/ui/select';
import { FileText } from '@lucide/vue';

interface Requirement {
    id: number;
    nama: string;
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

const selectedCategory = ref<Category | null>(null);
const dialogOpen = ref(false);
const successTrackingCode = ref<string | null>(null);

const form = useForm({
    kategori_id: null as number | null,
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

const openDialog = (cat: Category) => {
    selectedCategory.value = cat;
    form.reset();
    form.kategori_id = cat.id;
    successTrackingCode.value = null;
    dialogOpen.value = true;
};

const submitForm = () => {
    form.post('/layanan-surat/ajukan', {
        onSuccess: (page: any) => {
            successTrackingCode.value = page.props?.tracking_code || null;
        },
    });
};
</script>

<template>
    <Head title="Layanan Surat" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-2 text-3xl font-bold text-zinc-900 dark:text-white">Layanan Surat Online</h1>
        <p class="mb-8 text-zinc-500 dark:text-zinc-400">Ajukan surat secara online tanpa perlu datang ke kantor desa.</p>

        <div v-if="categories.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <FileText class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada jenis surat</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Jenis surat akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="cat in categories" :key="cat.id">
                <CardHeader>
                    <CardTitle>{{ cat.nama }}</CardTitle>
                    <CardDescription v-if="cat.requirements?.length">
                        {{ cat.requirements.length }} persyaratan
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <ul v-if="cat.requirements?.length" class="space-y-1">
                        <li v-for="req in cat.requirements" :key="req.id" class="flex items-start gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                            <span class="mt-1.5 block size-1.5 shrink-0 rounded-full bg-blue-500"></span>
                            {{ req.nama }}
                        </li>
                    </ul>
                    <Button class="w-full" @click="openDialog(cat)">Ajukan</Button>
                </CardContent>
            </Card>
        </div>

        <!-- Dialog Form -->
        <Dialog :open="dialogOpen" @update:open="dialogOpen = $event">
            <DialogContent class="max-h-[90vh] max-w-lg overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Ajukan {{ selectedCategory?.nama }}</DialogTitle>
                    <DialogDescription>Isi formulir di bawah ini untuk mengajukan surat.</DialogDescription>
                </DialogHeader>

                <div v-if="successTrackingCode" class="rounded-lg bg-green-50 p-4 text-center dark:bg-green-950">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">Pengajuan berhasil dikirim!</p>
                    <p class="mt-2 text-lg font-bold text-green-700 dark:text-green-300">Kode Tracking: {{ successTrackingCode }}</p>
                    <p class="mt-1 text-xs text-green-600 dark:text-green-400">Simpan kode ini untuk mengecek status pengajuan Anda.</p>
                    <Button variant="outline" class="mt-3" @click="dialogOpen = false">Tutup</Button>
                </div>

                <form v-else @submit.prevent="submitForm" class="space-y-4">
                    <div class="space-y-2">
                        <Label for="nama_pemohon">Nama Pemohon</Label>
                        <Input id="nama_pemohon" v-model="form.nama_pemohon" placeholder="Nama lengkap" required />
                    </div>
                    <div class="space-y-2">
                        <Label for="nik">NIK</Label>
                        <Input id="nik" v-model="form.nik" placeholder="Nomor Induk Kependudukan" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="tempat_lahir">Tempat Lahir</Label>
                            <Input id="tempat_lahir" v-model="form.tempat_lahir" placeholder="Tempat lahir" />
                        </div>
                        <div class="space-y-2">
                            <Label for="tanggal_lahir">Tanggal Lahir</Label>
                            <Input id="tanggal_lahir" v-model="form.tanggal_lahir" type="date" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="jenis_kelamin">Jenis Kelamin</Label>
                            <Select v-model="form.jenis_kelamin">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="Laki-laki">
                                            <SelectItemText>Laki-laki</SelectItemText>
                                        </SelectItem>
                                        <SelectItem value="Perempuan">
                                            <SelectItemText>Perempuan</SelectItemText>
                                        </SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="space-y-2">
                            <Label for="agama">Agama</Label>
                            <Select v-model="form.agama">
                                <SelectTrigger>
                                    <SelectValue placeholder="Pilih..." />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectGroup>
                                        <SelectItem value="Islam"><SelectItemText>Islam</SelectItemText></SelectItem>
                                        <SelectItem value="Kristen"><SelectItemText>Kristen</SelectItemText></SelectItem>
                                        <SelectItem value="Katolik"><SelectItemText>Katolik</SelectItemText></SelectItem>
                                        <SelectItem value="Hindu"><SelectItemText>Hindu</SelectItemText></SelectItem>
                                        <SelectItem value="Buddha"><SelectItemText>Buddha</SelectItemText></SelectItem>
                                        <SelectItem value="Konghucu"><SelectItemText>Konghucu</SelectItemText></SelectItem>
                                    </SelectGroup>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <Label for="pekerjaan">Pekerjaan</Label>
                        <Input id="pekerjaan" v-model="form.pekerjaan" placeholder="Pekerjaan" />
                    </div>
                    <div class="space-y-2">
                        <Label for="alamat">Alamat</Label>
                        <textarea
                            id="alamat"
                            v-model="form.alamat"
                            rows="3"
                            placeholder="Alamat lengkap"
                            class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                        ></textarea>
                    </div>
                    <div class="space-y-2">
                        <Label for="keperluan">Keperluan</Label>
                        <textarea
                            id="keperluan"
                            v-model="form.keperluan"
                            rows="3"
                            placeholder="Jelaskan keperluan Anda"
                            class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px]"
                        ></textarea>
                    </div>
                    <div class="space-y-2">
                        <Label for="no_hp">No. HP</Label>
                        <Input id="no_hp" v-model="form.no_hp" placeholder="Nomor handphone" />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing">Kirim Pengajuan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
