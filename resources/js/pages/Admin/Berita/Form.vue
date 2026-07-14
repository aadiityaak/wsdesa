<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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

interface PostCategory {
    id: number;
    nama: string;
    slug: string;
}

interface Post {
    id: number;
    judul: string;
    slug: string;
    post_category_id: number;
    status: string;
    views: number;
    published_at: string | null;
    ringkasan: string | null;
    konten: string;
    thumbnail: string | null;
}

const props = defineProps<{
    post?: Post;
    categories: PostCategory[];
}>();

const isEdit = computed(() => !!props.post);

const form = useForm({
    judul: props.post?.judul || '',
    post_category_id: props.post?.post_category_id?.toString() || '',
    ringkasan: props.post?.ringkasan || '',
    konten: props.post?.konten || '',
    thumbnail: null as File | null,
    status: props.post?.status || 'draft',
});

const gambarPreview = ref<string | null>(
    props.post?.thumbnail ? `/storage/${props.post.thumbnail}` : null,
);

const onGambarChange = (e: Event) => {
    const file = (e.target as HTMLInputElement).files?.[0] ?? null;
    form.thumbnail = file;
    if (file) {
        gambarPreview.value = URL.createObjectURL(file);
    }
};

const slugify = (text: string) => {
    return text
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
};

const submitForm = () => {
    const data = {
        ...form.data(),
        slug: slugify(form.judul),
    };

    if (isEdit.value) {
        form.put(`/admin/berita/${props.post!.id}`, {
            ...data,
            onSuccess: () => {
                router.visit('/admin/berita');
            },
        });
    } else {
        form.post('/admin/berita', {
            ...data,
            onSuccess: () => {
                router.visit('/admin/berita');
            },
        });
    }
};
</script>

<template>
    <Head :title="isEdit ? 'Edit Berita' : 'Tambah Berita'" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
            {{ isEdit ? 'Edit Berita' : 'Tambah Berita' }}
        </h1>

        <Card>
            <CardHeader>
                <CardTitle>{{ isEdit ? 'Form Edit Berita' : 'Form Tambah Berita' }}</CardTitle>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submitForm" class="space-y-6 max-w-2xl">
                    <div class="grid gap-1.5">
                        <Label for="judul">Judul</Label>
                        <Input id="judul" v-model="form.judul" required />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="post_category_id">Kategori</Label>
                        <Select v-model="form.post_category_id">
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
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="ringkasan">Ringkasan</Label>
                        <TiptapEditor
                            id="ringkasan"
                            v-model="form.ringkasan"
                            placeholder="Tulis ringkasan berita..."
                            :min-height="'100px'"
                        />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="konten">Konten</Label>
                        <TiptapEditor
                            id="konten"
                            v-model="form.konten"
                            placeholder="Tulis konten berita..."
                            :min-height="'300px'"
                        />
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="thumbnail">Thumbnail</Label>
                        <div class="flex items-start gap-4">
                            <div
                                class="flex size-32 shrink-0 items-center justify-center overflow-hidden rounded-lg border bg-zinc-100 dark:bg-zinc-800"
                            >
                                <img
                                    v-if="gambarPreview"
                                    :src="gambarPreview"
                                    alt="Thumbnail preview"
                                    class="size-full object-cover"
                                />
                                <svg
                                    v-else
                                    class="size-8 text-zinc-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <rect width="18" height="18" x="3" y="3" rx="2" ry="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                                </svg>
                            </div>
                            <div class="grid gap-2">
                                <Input
                                    id="thumbnail"
                                    type="file"
                                    accept="image/*"
                                    @input="onGambarChange"
                                />
                                <p class="text-sm text-zinc-500">
                                    Format: JPG, PNG, WEBP. Maksimal 2MB.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="publish">Dipublikasikan</SelectItem>
                                <SelectItem value="archive">Diarsipkan</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="flex items-center gap-4 pt-4">
                        <Button type="submit" :disabled="form.processing">
                            {{ isEdit ? 'Simpan Perubahan' : 'Tambah Berita' }}
                        </Button>
                        <Button type="button" variant="outline" @click="router.visit('/admin/berita')">
                            Batal
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
