 <script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
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
import { VueDatePicker } from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { id } from 'date-fns/locale/id';
import { ArrowLeft, Camera, Save } from '@lucide/vue';

interface EventCategory {
    id: number;
    nama: string;
    slug: string;
}

interface EventItem {
    id: number;
    judul: string;
    slug: string;
    event_category_id: number | null;
    deskripsi: string;
    lokasi: string;
    tanggal_mulai: string;
    tanggal_selesai: string | null;
    status: string;
    image: string | null;
    category: EventCategory | null;
}

const props = defineProps<{
    event?: EventItem;
    categories: EventCategory[];
}>();

const isEdit = computed(() => !!props.event);

const form = useForm({
    judul: props.event?.judul || '',
    event_category_id: props.event?.event_category_id?.toString() || '',
    deskripsi: props.event?.deskripsi || '',
    lokasi: props.event?.lokasi || '',
    tanggal_mulai: props.event?.tanggal_mulai?.slice(0, 16) || '',
    tanggal_selesai: props.event?.tanggal_selesai?.slice(0, 16) || '',
    status: props.event?.status || 'upcoming',
    image: null as File | null,
});

const gambarPreview = ref<string | null>(
    props.event?.image ? `/storage/${props.event.image}` : null,
);

const toDate = (str: string): Date | null => (str ? new Date(str.replace(' ', 'T')) : null);
const toStr = (d: Date): string => {
    const pad = (n: number) => n.toString().padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
};

const dateMulai = ref<Date | null>(toDate(form.tanggal_mulai));
const dateSelesai = ref<Date | null>(toDate(form.tanggal_selesai));

watch(dateMulai, (v) => { form.tanggal_mulai = v ? toStr(v) : ''; });
watch(dateSelesai, (v) => { form.tanggal_selesai = v ? toStr(v) : ''; });

const onGambarChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.image = file;
    if (file) {
        gambarPreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    // Convert "_" back to empty string for nullable category
    if (form.event_category_id === '_') {
        form.event_category_id = '';
    }

    if (isEdit.value) {
        const url = `/admin/agenda/${props.event!.id}`;
        if (form.image instanceof File) {
            form.transform((data) => ({ ...data, _method: 'put' })).post(url, {
                onSuccess: () => router.visit('/admin/agenda'),
            });
        } else {
            form.put(url, {
                onSuccess: () => router.visit('/admin/agenda'),
            });
        }
    } else {
        form.post('/admin/agenda', {
            onSuccess: () => router.visit('/admin/agenda'),
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Agenda' : 'Tambah Agenda'" />

    <div class="relative">
        <!-- Hero banner -->
        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-8 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-10">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative flex items-center gap-4">
                <Button variant="ghost" size="icon" as="a" href="/admin/agenda" class="rounded-full">
                    <ArrowLeft class="h-5 w-5" />
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">
                        {{ isEdit ? 'Edit Agenda' : 'Tambah Agenda' }}
                    </h1>
                    <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ isEdit ? 'Perbarui agenda dan kegiatan desa' : 'Buat agenda baru untuk desa' }}
                    </p>
                </div>
            </div>
        </div>

        <form @submit.prevent="submitForm">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Main -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Judul -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="judul" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Judul Agenda</Label>
                            <Input
                                id="judul"
                                v-model="form.judul"
                                required
                                placeholder="Masukkan judul agenda..."
                                class="rounded-xl border-zinc-200 text-base focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                            />
                            <p v-if="form.errors.judul" class="text-sm text-red-500">{{ form.errors.judul }}</p>
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <div class="grid gap-1.5">
                            <Label for="deskripsi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Deskripsi</Label>
                            <TiptapEditor
                                id="deskripsi"
                                v-model="form.deskripsi"
                                placeholder="Tulis deskripsi agenda..."
                                :min-height="'250px'"
                            />
                            <p v-if="form.errors.deskripsi" class="text-sm text-red-500">{{ form.errors.deskripsi }}</p>
                        </div>
                    </div>

                    <!-- Waktu -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Waktu</h3>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div class="grid gap-1.5">
                                <Label for="tanggal_mulai" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tanggal Mulai</Label>
                                <VueDatePicker
                                    v-model="dateMulai"
                                    :locale="id"
                                    :time-config="{ enableTimePicker: true, is24: true }"
                                    auto-apply
                                    cancel-text="Batal"
                                    select-text="Pilih"
                                    placeholder="Pilih tanggal & waktu mulai"
                                    required
                                    class="dp-wrap"
                                />
                                <p v-if="form.errors.tanggal_mulai" class="text-sm text-red-500">{{ form.errors.tanggal_mulai }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="tanggal_selesai" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tanggal Selesai</Label>
                                <VueDatePicker
                                    v-model="dateSelesai"
                                    :locale="id"
                                    :time-config="{ enableTimePicker: true, is24: true }"
                                    auto-apply
                                    cancel-text="Batal"
                                    select-text="Pilih"
                                    placeholder="Pilih tanggal & waktu selesai"
                                    class="dp-wrap"
                                />
                                <p v-if="form.errors.tanggal_selesai" class="text-sm text-red-500">{{ form.errors.tanggal_selesai }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Kategori & Status -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Pengaturan</h3>
                        <div class="space-y-5">
                            <div class="grid gap-1.5">
                                <Label for="event_category_id" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Kategori</Label>
                                <Select v-model="form.event_category_id">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="_">Tidak ada</SelectItem>
                                        <SelectItem v-for="cat in categories" :key="cat.id" :value="cat.id.toString()">
                                            {{ cat.nama }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.event_category_id" class="text-sm text-red-500">{{ form.errors.event_category_id }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="status" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</Label>
                                <Select v-model="form.status">
                                    <SelectTrigger class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700">
                                        <SelectValue placeholder="Pilih status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="upcoming">Akan Datang</SelectItem>
                                        <SelectItem value="ongoing">Berlangsung</SelectItem>
                                        <SelectItem value="done">Selesai</SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="form.errors.status" class="text-sm text-red-500">{{ form.errors.status }}</p>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="lokasi" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Lokasi</Label>
                                <Input
                                    id="lokasi"
                                    v-model="form.lokasi"
                                    placeholder="Masukkan lokasi..."
                                    class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700"
                                />
                                <p v-if="form.errors.lokasi" class="text-sm text-red-500">{{ form.errors.lokasi }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Gambar -->
                    <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        <h3 class="mb-4 text-sm font-semibold text-zinc-800 dark:text-zinc-200">Gambar</h3>
                        <div class="grid gap-4">
                            <div class="flex items-center justify-center overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800">
                                <img
                                    v-if="gambarPreview"
                                    :src="gambarPreview"
                                    alt="Preview"
                                    class="w-full object-cover"
                                    style="max-height: 180px;"
                                />
                                <div v-else class="flex h-36 w-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                                    <Camera class="h-10 w-10" />
                                </div>
                            </div>
                            <div class="grid gap-1.5">
                                <Label for="image" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2 text-sm font-medium text-zinc-700 shadow-xs transition hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                                    <Camera class="h-4 w-4" />
                                    Pilih Gambar
                                </Label>
                                <Input
                                    id="image"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @input="onGambarChange"
                                />
                                <p v-if="form.errors.image" class="text-sm text-red-500">{{ form.errors.image }}</p>
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
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Agenda' }}
                        </Button>
                        <Button type="button" variant="outline" class="w-full rounded-full" @click="router.visit('/admin/agenda')">
                            Batal
                        </Button>
                    </div>
                </div>
            </div>
        </form>
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
:is(.dark) .dp-wrap :deep(.dp__input:focus),
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
.dp-wrap :deep(.dp__arrow_top) {
    border-color: #e4e4e7;
}
:is(.dark) .dp-wrap :deep(.dp__arrow_top) {
    border-color: #3f3f46;
}
.dp-wrap :deep(.dp__cell_inner) {
    border-radius: 0.5rem;
}
.dp-wrap :deep(.dp__active_date),
.dp-wrap :deep(.dp__range_start),
.dp-wrap :deep(.dp__range_end) {
    background: #f43f5e;
    color: #fff;
}
.dp-wrap :deep(.dp__today) {
    border-color: #fda4af;
}
.dp-wrap :deep(.dp__button),
.dp-wrap :deep(.dp__action_select) {
    color: #f43f5e;
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
.dp-wrap :deep(.dp__action_cancel:hover) {
    background: #f4f4f5;
}
:is(.dark) .dp-wrap :deep(.dp__action_cancel:hover) {
    background: #27272a;
}
.dp-wrap :deep(.dp__action_button) {
    border-radius: 9999px;
    padding: 0.25rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
}
.dp-wrap :deep(.dp__time_input) {
    border-radius: 0.5rem;
    border-color: #e4e4e7;
}
:is(.dark) .dp-wrap :deep(.dp__time_input) {
    border-color: #3f3f46;
}
.dp-wrap :deep(.dp__inc_dec_button) {
    color: #71717a;
}
.dp-wrap :deep(.dp__inc_dec_button:hover) {
    color: #f43f5e;
}
</style>
