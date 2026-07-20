<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import ProfilLayout from './Layout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Save } from '@lucide/vue';

interface Profile { id: number; facebook: string; instagram: string; youtube: string; tiktok: string }

const props = defineProps<{ profile: Profile }>();

const form = useForm({
    facebook: props.profile.facebook ?? '',
    instagram: props.profile.instagram ?? '',
    youtube: props.profile.youtube ?? '',
    tiktok: props.profile.tiktok ?? '',
});

const fields = [
    { key: 'facebook' as const, label: 'Facebook', placeholder: 'https://facebook.com/...' },
    { key: 'instagram' as const, label: 'Instagram', placeholder: 'https://instagram.com/...' },
    { key: 'youtube' as const, label: 'YouTube', placeholder: 'https://youtube.com/@...' },
    { key: 'tiktok' as const, label: 'TikTok', placeholder: 'https://tiktok.com/@...' },
];

const submit = () => {
    form.post('/admin/profil/media', {
        _method: 'put',
        forceFormData: true,
        onSuccess: () => toast.success('Media sosial berhasil disimpan.'),
    });
};
</script>

<template>
    <ProfilLayout>
        <Head title="Media Sosial" />
        <form @submit.prevent="submit" class="rounded-2xl border border-zinc-100 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <h2 class="mb-6 text-lg font-semibold text-zinc-800 dark:text-zinc-200">Media Sosial</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div v-for="field in fields" :key="field.key" class="grid gap-1.5">
                    <Label :for="field.key" class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ field.label }}</Label>
                    <Input :id="field.key" v-model="form[field.key]" type="url" :placeholder="field.placeholder" class="rounded-xl border-zinc-200 dark:border-zinc-700" />
                </div>
            </div>
            <div class="mt-8 flex justify-end">
                <Button type="submit" :disabled="form.processing" class="gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Save class="h-4 w-4" /> Simpan
                </Button>
            </div>
        </form>
    </ProfilLayout>
</template>
