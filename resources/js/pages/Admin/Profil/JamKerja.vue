<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import ProfilLayout from './Layout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from '@lucide/vue';
import { ref } from 'vue';

interface JamKerjaItem { id?: number; hari: string; jam_buka: string | null; jam_tutup: string | null; is_libur: boolean }
interface Profile { id: number; jam_kerja: JamKerjaItem[] }

const props = defineProps<{ profile: Profile }>();

const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

const jamKerja = ref<JamKerjaItem[]>(
    HARI.map((hari) => {
        const existing = props.profile.jam_kerja?.find((j) => j.hari === hari);
        return existing ?? { hari, jam_buka: '08:00', jam_tutup: '16:00', is_libur: false };
    }),
);

const onLiburToggle = (index: number) => {
    const item = jamKerja.value[index];
    item.is_libur = !item.is_libur;
    if (item.is_libur) { item.jam_buka = null; item.jam_tutup = null; }
    else { item.jam_buka = '08:00'; item.jam_tutup = '16:00'; }
};

const form = useForm({ jam_kerja: [] as JamKerjaItem[] });

const submit = () => {
    form.jam_kerja = jamKerja.value;
    form.post('/admin/profil/jam-kerja', {
        _method: 'put',
        forceFormData: true,
        onSuccess: () => toast.success('Jam kerja berhasil disimpan.'),
    });
};
</script>

<template>
    <ProfilLayout>
        <Head title="Jam Kerja" />
        <form @submit.prevent="submit" class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="px-6 pt-6 text-lg font-semibold text-zinc-800 dark:text-zinc-200 sm:px-8 sm:pt-8">Jam Kerja</h2>
            <div v-for="(jk, index) in jamKerja" :key="jk.hari" class="flex flex-wrap items-center gap-4 border-b border-zinc-100 px-6 py-4 last:border-b-0 dark:border-zinc-800 sm:flex-nowrap sm:px-8">
                <div class="flex w-20 shrink-0 items-center gap-2">
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ jk.hari }}</span>
                </div>
                <label class="inline-flex shrink-0 cursor-pointer items-center gap-2">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Libur</span>
                    <button type="button" role="switch" :aria-checked="jk.is_libur" class="relative inline-flex h-6 w-10 shrink-0 cursor-pointer items-center rounded-full transition-colors" :class="jk.is_libur ? 'bg-rose-500' : 'bg-zinc-300 dark:bg-zinc-600'" @click="onLiburToggle(index)">
                        <span class="inline-block h-4 w-4 rounded-full bg-white transition-transform" :class="jk.is_libur ? 'translate-x-5' : 'translate-x-1'" />
                    </button>
                </label>
                <template v-if="!jk.is_libur">
                    <div class="flex items-center gap-2"><Label :for="`buka_${index}`" class="text-xs text-zinc-500 dark:text-zinc-400">Buka</Label><Input :id="`buka_${index}`" v-model="jk.jam_buka" type="time" class="h-9 w-32 rounded-lg border-zinc-200 text-sm dark:border-zinc-700" /></div>
                    <div class="flex items-center gap-2"><Label :for="`tutup_${index}`" class="text-xs text-zinc-500 dark:text-zinc-400">Tutup</Label><Input :id="`tutup_${index}`" v-model="jk.jam_tutup" type="time" class="h-9 w-32 rounded-lg border-zinc-200 text-sm dark:border-zinc-700" /></div>
                </template>
                <div v-else class="text-sm italic text-zinc-400 dark:text-zinc-500">Libur</div>
            </div>
            <div class="flex justify-end px-6 py-5 sm:px-8">
                <Button type="submit" :disabled="form.processing" class="gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Save class="h-4 w-4" /> Simpan
                </Button>
            </div>
        </form>
    </ProfilLayout>
</template>
