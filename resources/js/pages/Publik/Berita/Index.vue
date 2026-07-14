<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Eye, Search } from '@lucide/vue';

interface PostCategory {
    id: number;
    nama: string;
    slug: string;
    posts_count?: number;
}

interface Post {
    id: number;
    judul: string;
    slug: string;
    ringkasan: string | null;
    gambar: string | null;
    views: number;
    published_at: string;
    category: PostCategory | null;
}

defineProps<{
    posts: {
        data: Post[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        links?: { url: string | null; label: string; active: boolean }[];
    };
    categories: PostCategory[];
    search?: string;
    categorySlug?: string;
}>();

const formatDate = (date: string) =>
    new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

const imageUrl = (path: string | null) => (path ? `/storage/${path}` : null);
</script>

<template>
    <Head title="Berita" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-zinc-900 dark:text-white">Berita Desa</h1>

        <!-- Search & Category -->
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <form class="relative w-full sm:w-80">
                <Search class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
                <Input
                    name="search"
                    :value="search"
                    placeholder="Cari berita..."
                    class="pl-9"
                />
            </form>

            <div class="flex flex-wrap gap-2">
                <a
                    href="/berita"
                    class="rounded-full px-3 py-1.5 text-sm font-medium transition-colors"
                    :class="!categorySlug ? 'bg-blue-600 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700'"
                >
                    Semua
                </a>
                <a
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="`/berita/kategori/${cat.slug}`"
                    class="rounded-full px-3 py-1.5 text-sm font-medium transition-colors"
                    :class="categorySlug === cat.slug ? 'bg-blue-600 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700'"
                >
                    {{ cat.nama }}
                </a>
            </div>
        </div>

        <!-- Posts Grid -->
        <div v-if="posts.data.length === 0" class="flex min-h-[40vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada berita</h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Berita akan ditampilkan di sini setelah tersedia.</p>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card v-for="post in posts.data" :key="post.id" class="overflow-hidden">
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                    <img
                        v-if="post.gambar"
                        :src="imageUrl(post.gambar)"
                        :alt="post.judul"
                        class="h-full w-full object-cover"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center text-zinc-400">
                        <Eye class="size-12" />
                    </div>
                </div>
                <CardContent class="space-y-3 p-4">
                    <div class="flex items-center gap-2">
                        <Badge v-if="post.category" variant="secondary">
                            {{ post.category.nama }}
                        </Badge>
                        <span class="text-xs text-zinc-500">{{ formatDate(post.published_at) }}</span>
                    </div>
                    <Link :href="`/berita/${post.slug}`" class="group block">
                        <h3 class="text-lg font-semibold transition-colors line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                            {{ post.judul }}
                        </h3>
                    </Link>
                    <p v-if="post.ringkasan" class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-3">
                        {{ post.ringkasan }}
                    </p>
                    <div class="flex items-center gap-1 pt-1 text-xs text-zinc-500">
                        <Eye class="size-3" />
                        {{ post.views }}
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Pagination -->
        <div v-if="posts.last_page > 1" class="mt-8 flex justify-center">
            <div class="flex items-center gap-1">
                <a
                    v-for="link in posts.links"
                    :key="link.label"
                    :href="link.url || '#'"
                    v-html="link.label"
                    class="inline-flex items-center justify-center rounded-md px-3 py-1.5 text-sm"
                    :class="{
                        'bg-blue-600 text-white': link.active,
                        'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800': !link.active && link.url,
                        'cursor-default text-zinc-300': !link.url,
                    }"
                ></a>
            </div>
        </div>
    </div>
</template>
