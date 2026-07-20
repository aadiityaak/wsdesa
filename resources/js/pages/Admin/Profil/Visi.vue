<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import ProfilLayout from './Layout.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Save } from '@lucide/vue';
import TiptapEditor from '@/components/TiptapEditor.vue';

interface Profile { id: number; visi: string; misi: string; sejarah: string }

const props = defineProps<{ profile: Profile }>();

const form = useForm({
    visi: props.profile.visi ?? '',
    misi: props.profile.misi ?? '',
    sejarah: props.profile.sejarah ?? '',
});

const submit = () => {
    form.post('/admin/profil/visi', {
        _method: 'put',
        forceFormData: true,
        onSuccess: () => toast.success('Visi, misi & sejarah berhasil disimpan.'),
    });
};
</script>

<template>
    <ProfilLayout>
        <Head title="Visi, Misi & Sejarah" />
        <form @submit.prevent="submit" class="space-y-6">
            <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <Label for="visi" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Visi</Label>
                <TiptapEditor id="visi" v-model="form.visi" placeholder="Tulis visi desa..." :min-height="'120px'" />
            </div>
            <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <Label for="misi" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Misi</Label>
                <TiptapEditor id="misi" v-model="form.misi" placeholder="Tulis misi desa..." :min-height="'180px'" />
            </div>
            <div class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <Label for="sejarah" class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Sejarah Desa</Label>
                <TiptapEditor id="sejarah" v-model="form.sejarah" placeholder="Tulis sejarah desa..." :min-height="'250px'" />
            </div>
            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing" class="gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Save class="h-4 w-4" /> Simpan
                </Button>
            </div>
        </form>
    </ProfilLayout>
</template>
