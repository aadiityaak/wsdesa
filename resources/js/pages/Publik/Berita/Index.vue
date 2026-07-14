<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Calendar, MapPin, Eye } from '@lucide/vue';

interface PostCategory {
    id: number;
    nama: string;
    slug: string;
    posts_count: number;
}

interface Post {
    id: number;
    judul: string;
    slug: string;
    ringkasan: string | null;
    gambar: string | null;
    status: string;
    views: number;
    published_at: string;
    category: PostCategory | null;
}

const props = defineProps<{
    posts: {
        data: Post[];
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    categories: PostCategory[];
    activeCategory?: PostCategory;
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const imageUrl = (path: string | null) => {
    if (!path) return null;
    return `/storage/${path}`;
};
</script>

<template>
    <Head title="Berita Desa" />

    <div class="py-8">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl font-bold text-zinc-900 dark:text-white mb-8">
                {{ activeCategory ? `Berita: ${activeCategory.nama}` : 'Berita Desa' }}
            </h1>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Main Content -->
                <div class="flex-1">
                    <div v-if="posts.data.length === 0" class="py-20 text-center">
                        <p class="text-lg text-zinc-500 dark:text-zinc-400">Belum ada berita.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <Card v-for="post in posts.data" :key="post.id" class="overflow-hidden">
                            <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                <img
                                    v-if="post.gambar"
                                    :src="imageUrl(post.gambar)"
                                    :alt="post.judul"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="w-full h-full flex items-center justify-center text-zinc-400">
                                    <Eye class="size-12" />
                                </div>
                            </div>
                            <CardContent class="p-4 space-y-3">
                                <div class="flex items-center gap-2">
                                    <Badge v-if="post.category" variant="secondary">
                                        {{ post.category.nama }}
                                    </Badge>
                                    <span class="text-xs text-zinc-500">{{ formatDate(post.published_at) }}</span>
                                </div>
                                <a :href="`/berita/${post.slug}`" class="block group">
                                    <h3 class="font-semibold text-lg group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                                        {{ post.judul }}
                                    </h3>
                                </a>
                                <p v-if="post.ringkasan" class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-3">
                                    {{ post.ringkasan }}
                                </p>
                                <div class="flex items-center gap-4 text-xs text-zinc-500 pt-1">
                                    <span class="flex items-center gap-1">
                                        <Eye class="size-3" />
                                        {{ post.views }}
                                    </span>
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
                                class="inline-flex items-center justify-center px-3 py-1.5 text-sm rounded-md"
                                :class="{
                                    'bg-blue-600 text-white': link.active,
                                    'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800': !link.active && link.url,
                                    'text-zinc-300 cursor-default': !link.url,
                                }"
                            ></a>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="w-full lg:w-72 shrink-0 space-y-6">
                    <Card>
                        <CardContent class="p-4">
                            <h3 class="font-semibold text-zinc-900 dark:text-white mb-3">Kategori</h3>
                            <div class="space-y-1">
                                <a
                                    href="/berita"
                                    class="block px-3 py-2 rounded-md text-sm transition-colors"
                                    :class="!activeCategory ? 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-medium' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                >
                                    Semua
                                </a>
                                <a
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :href="`/berita/kategori/${cat.slug}`"
                                    class="block px-3 py-2 rounded-md text-sm transition-colors"
                                    :class="activeCategory?.id === cat.id ? 'bg-blue-50 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-medium' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-800'"
                                >
                                    <span class="flex items-center justify-between">
                                        {{ cat.nama }}
                                        <span class="text-xs text-zinc-400">{{ cat.posts_count }}</span>
                                    </span>
                                </a>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
