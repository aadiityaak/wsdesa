<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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

const submitForm = () => {
    if (isEdit.value) {
        const url = `/admin/agenda/${props.event!.id}`;
        if (form.image instanceof File) {
            form.transform((data) => ({ ...data, _method: 'put' })).post(url);
        } else {
            form.put(url);
        }
    } else {
        form.post('/admin/agenda');
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Agenda' : 'Tambah Agenda'" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ isEdit ? 'Edit Agenda' : 'Tambah Agenda' }}
        </h1>

        <Card>
            <CardHeader>
                <CardTitle>{{ isEdit ? 'Form Edit Agenda' : 'Form Tambah Agenda' }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submitForm" class="space-y-6 max-w-2xl">
                    <div class="grid gap-1.5">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required />
                        <p v-if="form.errors.judul" class="text-sm text-red-500">{{ form.errors.judul }}</p>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="event_category_id">Kategori</Label>
                        <Select v-model="form.event_category_id">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih kategori" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="cat.id.toString()"
                                >
                                    {{ cat.nama }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.event_category_id" class="text-sm text-red-500">{{ form.errors.event_category_id }}</p>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="deskripsi">Deskripsi</Label>
                        <textarea
                            id="deskripsi"
                            v-model="form.deskripsi"
                            rows="4"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            required
                        ></textarea>
                        <p v-if="form.errors.deskripsi" class="text-sm text-red-500">{{ form.errors.deskripsi }}</p>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="lokasi">Lokasi</Label>
                        <Input id="lokasi" v-model="form.lokasi" />
                        <p v-if="form.errors.lokasi" class="text-sm text-red-500">{{ form.errors.lokasi }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-1.5">
                            <Label for="tanggal_mulai">Tanggal Mulai</Label>
                            <Input id="tanggal_mulai" v-model="form.tanggal_mulai" type="datetime-local" required />
                            <p v-if="form.errors.tanggal_mulai" class="text-sm text-red-500">{{ form.errors.tanggal_mulai }}</p>
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="tanggal_selesai">Tanggal Selesai</Label>
                            <Input id="tanggal_selesai" v-model="form.tanggal_selesai" type="datetime-local" />
                            <p v-if="form.errors.tanggal_selesai" class="text-sm text-red-500">{{ form.errors.tanggal_selesai }}</p>
                        </div>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger>
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
                        <Label for="image">Gambar</Label>
                        <Input
                            id="image"
                            type="file"
                            accept="image/*"
                            @input="(e: Event) => (form.image = (e.target as HTMLInputElement).files?.[0] ?? null)"
                        />
                        <p v-if="form.errors.image" class="text-sm text-red-500">{{ form.errors.image }}</p>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <Button type="submit" :disabled="form.processing">
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Agenda' }}
                        </Button>
                        <Button type="button" variant="outline" @click="router.visit('/admin/agenda')">
                            Batal
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
