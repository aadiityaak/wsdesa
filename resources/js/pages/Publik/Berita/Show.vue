<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Eye, Calendar } from '@lucide/vue';
import { computed } from 'vue';
import PageHero from '@/components/PageHero.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';

interface PostCategory {
    id: number;
    nama: string;
    slug: string;
}

interface Comment {
    id: number;
    nama: string;
    konten: string;
    created_at: string;
}

interface Post {
    id: number;
    judul: string;
    slug: string;
    ringkasan: string | null;
    konten: string;
    thumbnail: string | null;
    thumbnail_url: string | null;
    views: number;
    published_at: string;
    category: PostCategory | null;
}

const props = defineProps<{
    post: Post;
    comments: Comment[];
}>();

const commentForm = useForm({
    nama: '',
    email: '',
    konten: '',
});

const submitComment = () => {
    commentForm.post(`/berita/${window.location.pathname.split('/').pop()}/komentar`, {
        preserveScroll: true,
        onSuccess: () => commentForm.reset(),
    });
};

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

const imageUrl = computed(() => {
    if (props.post.thumbnail_url) return props.post.thumbnail_url;
    if (props.post.thumbnail) return `/storage/${props.post.thumbnail}`;
    return null;
});

const shareUrl = typeof window !== 'undefined' ? window.location.href : '';
</script>

<template>
    <Head :title="post.judul" />

    <article class="mx-auto max-w-4xl px-4 py-4 sm:px-6 lg:px-8">
        <Breadcrumb :items="[
            { label: 'Berita', href: '/berita' },
            { label: post.judul },
        ]" />

        <div class="mb-4 flex flex-wrap items-center gap-3">
            <Badge v-if="post.category" variant="secondary">{{ post.category.nama }}</Badge>
            <span class="flex items-center gap-1 text-sm text-zinc-500">
                <Calendar class="size-4" />
                {{ formatDate(post.published_at) }}
            </span>
            <span class="flex items-center gap-1 text-sm text-zinc-500">
                <Eye class="size-4" />
                {{ post.views }} dilihat
            </span>
        </div>

        <!-- Title -->
        <h1 class="mb-6 text-3xl font-bold text-zinc-900 dark:text-white sm:text-4xl">
            {{ post.judul }}
        </h1>

        <!-- Thumbnail -->
        <div v-if="imageUrl" class="mb-8 overflow-hidden rounded-lg">
            <img :src="imageUrl" :alt="post.judul" class="w-full object-cover" />
        </div>

        <!-- Content -->
        <div
            class="prose prose-zinc max-w-none dark:prose-invert prose-headings:text-zinc-900 dark:prose-headings:text-white prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-img:rounded-lg mb-8"
            v-html="post.konten"
        ></div>

        <!-- Share Buttons -->
        <div class="mb-12 flex items-center gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
            <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Bagikan:</span>
            <a
                :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700"
            >
                Facebook
            </a>
            <a
                :href="`https://twitter.com/intent/tweet?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(post.judul)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="rounded-md bg-sky-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-sky-600"
            >
                Twitter
            </a>
            <a
                :href="`https://wa.me/?text=${encodeURIComponent(post.judul + ' ' + shareUrl)}`"
                target="_blank"
                rel="noopener noreferrer"
                class="rounded-md bg-green-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-green-700"
            >
                WhatsApp
            </a>
        </div>

        <!-- Comments Section -->
        <section>
            <h2 class="mb-6 text-2xl font-bold text-zinc-900 dark:text-white">Komentar</h2>

            <div v-if="comments.length === 0" class="mb-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                Belum ada komentar. Jadilah yang pertama berkomentar!
            </div>

            <div v-else class="mb-8 space-y-4">
                <Card v-for="comment in comments" :key="comment.id">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-sm font-semibold">{{ comment.nama }}</CardTitle>
                        <p class="text-xs text-zinc-500">{{ formatDate(comment.created_at) }}</p>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300">{{ comment.konten }}</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Comment Form -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Tinggalkan Komentar</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitComment" class="space-y-4">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label for="nama">Nama</Label>
                                <Input id="nama" v-model="commentForm.nama" placeholder="Nama Anda" required />
                            </div>
                            <div class="space-y-2">
                                <Label for="email">Email (opsional)</Label>
                                <Input id="email" v-model="commentForm.email" type="email" placeholder="email@contoh.com" />
                            </div>
                        </div>
                        <div class="space-y-2">
                            <Label for="konten">Komentar</Label>
                            <textarea
                                id="konten"
                                v-model="commentForm.konten"
                                rows="4"
                                required
                                placeholder="Tulis komentar Anda..."
                                class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50"
                            ></textarea>
                        </div>
                        <Button type="submit" :disabled="commentForm.processing">
                            Kirim Komentar
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </section>
    </article>
</template>
