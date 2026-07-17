 <script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
 
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { id } from 'date-fns/locale/id';
import { Plus, Pencil, Trash2, ArrowUp, Users, GraduationCap, Phone, MapPin, Calendar } from '@lucide/vue';

interface StaffItem {
    id: number;
    nama: string;
    nip: string | null;
    jabatan: string;
    foto: string | null;
    alamat: string | null;
    telepon: string | null;
    pendidikan_terakhir: string | null;
    tanggal_mulai_jabatan: string | null;
    tanggal_akhir_jabatan: string | null;
    urutan: number | null;
}

const props = defineProps<{
    staff: StaffItem[];
}>();

// ── Dialog state ──
const dialogOpen = ref(false);
const editingId = ref<number | null>(null);
const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});
const isEdit = computed(() => editingId.value !== null);

// ── Helper ──
const toDate = (str: string | null): Date | null =>
    str ? new Date(str.replace(' ', 'T')) : null;
const toStr = (d: Date): string => {
    const pad = (n: number) => n.toString().padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
};

// ── Form ──
const form = useForm({
    nama: '',
    nip: '',
    jabatan: '',
    foto: null as File | null,
    alamat: '',
    telepon: '',
    pendidikan_terakhir: '',
    tanggal_mulai_jabatan: '',
    tanggal_akhir_jabatan: '',
    urutan: '',
});

const fotoPreview = ref<string | null>(null);

const dateMulai = ref<Date | null>(null);
const dateAkhir = ref<Date | null>(null);

watch(dateMulai, (v) => { form.tanggal_mulai_jabatan = v ? toStr(v) : ''; });
watch(dateAkhir, (v) => { form.tanggal_akhir_jabatan = v ? toStr(v) : ''; });

const onFotoChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.foto = file;
    if (file) {
        fotoPreview.value = URL.createObjectURL(file);
    }
};

const openAdd = () => {
    editingId.value = null;
    form.reset();
    form.clearErrors();
    fotoPreview.value = null;
    dateMulai.value = null;
    dateAkhir.value = null;
    dialogOpen.value = true;
};

const openEdit = (item: StaffItem) => {
    editingId.value = item.id;
    form.nama = item.nama;
    form.nip = item.nip ?? '';
    form.jabatan = item.jabatan;
    form.alamat = item.alamat ?? '';
    form.telepon = item.telepon ?? '';
    form.pendidikan_terakhir = item.pendidikan_terakhir ?? '';
    form.urutan = item.urutan?.toString() ?? '';
    form.foto = null;
    fotoPreview.value = item.foto ? `/storage/${item.foto}` : null;
    dateMulai.value = toDate(item.tanggal_mulai_jabatan);
    dateAkhir.value = toDate(item.tanggal_akhir_jabatan);
    dialogOpen.value = true;
};

const submitForm = () => {
    if (isEdit.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(`/admin/pemerintahan/${editingId.value}`, {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Perangkat desa berhasil diperbarui.');
            },
            onError: (e) => {
                if (Object.keys(e).length) toast.error('Gagal menyimpan. Cek kembali isian.');
            },
        });
    } else {
        form.post('/admin/pemerintahan', {
            onSuccess: () => {
                dialogOpen.value = false;
                toast.success('Perangkat desa berhasil ditambahkan.');
            },
            onError: (e) => {
                if (Object.keys(e).length) toast.error('Gagal menyimpan. Cek kembali isian.');
            },
        });
    }
};

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        router.delete(`/admin/pemerintahan/${deleteConfirmId.value}`, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Perangkat desa berhasil dihapus.');
            },
            onError: () => toast.error('Gagal menghapus.'),
        });
    }
};

// ── Stats ──
const stats = computed(() => ({
    total: props.staff.length,
}));

// ── Scroll to top ──
const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}

// ── Toast flash ──
const flash = computed(() => (router.page?.props as Record<string, unknown>)?.flash as Record<string, string> | undefined);
watch(() => flash.value?.success, (msg) => {
    if (msg) toast.success(msg);
});
</script>

<template>
    <Head title="Perangkat Desa" />

    <div class="relative">
        <!-- Scroll to top -->
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="translate-y-2 opacity-0"
        >
            <button
                v-if="showScrollTop"
                type="button"
                class="fixed bottom-8 right-8 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition hover:scale-105 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                @click="scrollToTop"
            >
                <ArrowUp class="h-5 w-5" />
            </button>
        </Transition>

        <!-- Hero banner -->
        <div
            class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12"
        >
            <div
                class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10"
                aria-hidden="true"
            />

            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400"
                        >
                            <Users class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                                Perangkat Desa
                            </h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                                Kelola data perangkat dan staf desa
                            </p>
                        </div>
                    </div>
                </div>
                <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAdd">
                    <Plus class="h-4 w-4" />
                    Tambah Perangkat
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-5 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <Users class="h-5 w-5 text-zinc-400" />
                    <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</span>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Perangkat</span>
                </div>
            </div>
        </div>

        <!-- Staff grid -->
        <div v-if="staff.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <Users class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada perangkat desa</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan perangkat desa pertama.</p>
            <Button class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAdd">
                <Plus class="h-4 w-4" />
                Tambah Perangkat
            </Button>
        </div>

        <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="item in staff"
                :key="item.id"
                class="group rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
            >
                <div class="flex items-start gap-4">
                    <!-- Foto -->
                    <div class="shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                        <img
                            v-if="item.foto"
                            :src="`/storage/${item.foto}`"
                            :alt="item.nama"
                            class="h-16 w-16 object-cover transition duration-300 group-hover:scale-105 sm:h-20 sm:w-20"
                        />
                        <div v-else class="flex h-16 w-16 items-center justify-center text-zinc-300 dark:text-zinc-600 sm:h-20 sm:w-20">
                            <Users class="h-8 w-8" />
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-zinc-900 dark:text-white">{{ item.nama }}</h3>
                        <p class="text-sm text-rose-500">{{ item.jabatan }}</p>

                        <div class="mt-2 space-y-1 text-xs text-zinc-400 dark:text-zinc-500">
                            <p v-if="item.nip" class="truncate">NIP: {{ item.nip }}</p>
                            <p v-if="item.pendidikan_terakhir" class="inline-flex items-center gap-1">
                                <GraduationCap class="h-3 w-3" /> {{ item.pendidikan_terakhir }}
                            </p>
                        </div>

                        <div class="mt-3 flex gap-1">
                            <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEdit(item)">
                                <Pencil class="h-3.5 w-3.5" />
                            </Button>
                            <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(item.id)">
                                <Trash2 class="h-3.5 w-3.5 text-red-500" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ──── Form Dialog ──── -->
        <Dialog :open="dialogOpen" @update:open="(v) => { dialogOpen = v; if (!v) form.clearErrors(); }">
            <DialogContent class="sm:max-w-lg max-h-[90vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>{{ isEdit ? 'Edit Perangkat Desa' : 'Tambah Perangkat Desa' }}</DialogTitle>
                    <DialogDescription>
                        {{ isEdit ? 'Perbarui data perangkat desa.' : 'Masukkan data perangkat desa baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="space-y-5">
                    <!-- Nama & NIP -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label for="nama" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Nama <span class="text-red-500">*</span>
                            </Label>
                            <Input id="nama" v-model="form.nama" required class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                            <p v-if="form.errors.nama" class="text-sm text-red-500">{{ form.errors.nama }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="nip" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">NIP</Label>
                            <Input id="nip" v-model="form.nip" class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                            <p v-if="form.errors.nip" class="text-sm text-red-500">{{ form.errors.nip }}</p>
                        </div>
                    </div>

                    <!-- Jabatan & Urutan -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label for="jabatan" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Jabatan <span class="text-red-500">*</span>
                            </Label>
                            <Input id="jabatan" v-model="form.jabatan" required class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                            <p v-if="form.errors.jabatan" class="text-sm text-red-500">{{ form.errors.jabatan }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="urutan" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Urutan</Label>
                            <Input id="urutan" v-model="form.urutan" type="number" class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                            <p v-if="form.errors.urutan" class="text-sm text-red-500">{{ form.errors.urutan }}</p>
                        </div>
                    </div>

                    <!-- Alamat & Telepon -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label for="alamat" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Alamat</Label>
                            <Input id="alamat" v-model="form.alamat" class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                            <p v-if="form.errors.alamat" class="text-sm text-red-500">{{ form.errors.alamat }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="telepon" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Telepon</Label>
                            <Input id="telepon" v-model="form.telepon" class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                            <p v-if="form.errors.telepon" class="text-sm text-red-500">{{ form.errors.telepon }}</p>
                        </div>
                    </div>

                    <!-- Pendidikan -->
                    <div class="grid gap-1.5">
                        <Label for="pendidikan_terakhir" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Pendidikan Terakhir</Label>
                        <Input id="pendidikan_terakhir" v-model="form.pendidikan_terakhir" class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                        <p v-if="form.errors.pendidikan_terakhir" class="text-sm text-red-500">{{ form.errors.pendidikan_terakhir }}</p>
                    </div>

                    <!-- Tanggal Jabatan -->
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="grid gap-1.5">
                            <Label for="tanggal_mulai_jabatan" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Mulai Jabatan</Label>
                            <VueDatePicker
                                v-model="dateMulai"
                                :locale="id"
                                auto-apply
                                placeholder="Pilih tanggal"
                                class="dp-wrap"
                            />
                            <p v-if="form.errors.tanggal_mulai_jabatan" class="text-sm text-red-500">{{ form.errors.tanggal_mulai_jabatan }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="tanggal_akhir_jabatan" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Akhir Jabatan</Label>
                            <VueDatePicker
                                v-model="dateAkhir"
                                :locale="id"
                                auto-apply
                                placeholder="Pilih tanggal"
                                class="dp-wrap"
                            />
                            <p v-if="form.errors.tanggal_akhir_jabatan" class="text-sm text-red-500">{{ form.errors.tanggal_akhir_jabatan }}</p>
                        </div>
                    </div>

                    <!-- Foto -->
                    <div class="grid gap-3">
                        <Label for="foto" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Foto</Label>
                        <div class="flex items-center gap-4">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                <img v-if="fotoPreview" :src="fotoPreview" alt="Preview" class="h-full w-full object-cover" />
                                <Users v-else class="h-8 w-8 text-zinc-300 dark:text-zinc-600" />
                            </div>
                            <Label for="foto" class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-xs transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                Pilih Foto
                                <Input id="foto" type="file" accept="image/*" class="hidden" @input="onFotoChange" />
                            </Label>
                        </div>
                        <p v-if="form.errors.foto" class="text-sm text-red-500">{{ form.errors.foto }}</p>
                    </div>

                    <DialogFooter>
                        <Button type="button" variant="outline" class="rounded-full" @click="dialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="form.processing" class="rounded-full bg-rose-500 text-white hover:bg-rose-600">
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Perangkat' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- ──── Delete Confirmation ──── -->
        <Dialog :open="deleteConfirmId !== null" @update:open="(v) => { if (!v) deleteConfirmId = null; }">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>
                        Apakah Anda yakin ingin menghapus perangkat desa ini? Tindakan ini tidak dapat dibatalkan.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" @click="executeDelete">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style scoped>
.dp-wrap :deep(.dp__input) {
    display: flex;
    height: 2.5rem;
    width: 100%;
    border-radius: 0.75rem;
    border: 1px solid #e4e4e7;
    background: #fff;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: border-color, box-shadow 0.15s ease;
}
.dp-wrap :deep(.dp__input)::placeholder {
    color: #a1a1aa;
}
.dp-wrap :deep(.dp__input:focus),
.dp-wrap :deep(.dp__input_focus) {
    border-color: #f43f5e;
    box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.2);
    outline: none;
}
:is(.dark) .dp-wrap :deep(.dp__input) {
    border-color: #3f3f46;
    background: #18181b;
    color: #d4d4d8;
}
:is(.dark) .dp-wrap :deep(.dp__input)::placeholder {
    color: #71717a;
}
:is(.dark) .dp-wrap :deep(.dp__input_focus) {
    border-color: #fb7185;
    box-shadow: 0 0 0 3px rgba(251, 113, 133, 0.3);
}
.dp-wrap :deep(.dp__menu) {
    border-radius: 0.75rem;
    border: 1px solid #e4e4e7;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
:is(.dark) .dp-wrap :deep(.dp__menu) {
    border-color: #3f3f46;
}
.dp-wrap :deep(.dp__cell_inner) {
    border-radius: 0.5rem;
}
.dp-wrap :deep(.dp__active_date) {
    background: #f43f5e;
    color: #fff;
}
.dp-wrap :deep(.dp__today) {
    border-color: #fda4af;
}
.dp-wrap :deep(.dp__action_button) {
    border-radius: 9999px;
    padding: 0.25rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
}
.dp-wrap :deep(.dp__action_select) {
    background: #f43f5e;
    color: #fff;
}
.dp-wrap :deep(.dp__action_select:hover) {
    background: #e11d48;
}
.dp-wrap :deep(.dp__action_cancel) {
    color: #52525b;
}
:is(.dark) .dp-wrap :deep(.dp__action_cancel) {
    color: #a1a1aa;
}
.dp-wrap :deep(.dp__inc_dec_button) {
    color: #71717a;
}
.dp-wrap :deep(.dp__inc_dec_button:hover) {
    color: #f43f5e;
}
</style>
