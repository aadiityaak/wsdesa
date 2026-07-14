<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
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
    gambar: string | null;
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
    gambar: null as File | null,
    status: props.post?.status || 'draft',
});

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
                        <textarea
                            id="ringkasan"
                            v-model="form.ringkasan"
                            rows="3"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="konten">Konten</Label>
                        <textarea
                            id="konten"
                            v-model="form.konten"
                            required
                            rows="12"
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring font-mono"
                        ></textarea>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="gambar">Thumbnail</Label>
                        <Input
                            id="gambar"
                            type="file"
                            accept="image/*"
                            @input="(e: Event) => (form.gambar = (e.target as HTMLInputElement).files?.[0] ?? null)"
                        />
                        <p
                            v-if="isEdit && post?.gambar"
                            class="text-sm text-zinc-500"
                        >
                            Upload gambar baru untuk mengganti gambar yang sudah ada.
                        </p>
                    </div>

                    <div class="grid gap-1.5">
                        <Label for="status">Status</Label>
                        <Select v-model="form.status">
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="published">Dipublikasikan</SelectItem>
                                <SelectItem value="archived">Diarsipkan</SelectItem>
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
