<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Calendar, Eye, Search, Newspaper, ArrowRight } from '@lucide/vue';
import { computed } from 'vue';
import PageHero from '@/components/PageHero.vue';

interface PostCategory {
    id: number;
    nama: string;
    slug: string;
    posts_count?: number;
}

interface PostItem {
    id: number;
    judul: string;
    slug: string;
    ringkasan: string | null;
    thumbnail: string | null;
    thumbnail_url: string | null;
    views: number;
    published_at: string;
    category: PostCategory | null;
    user?: { name: string } | null;
}

const props = defineProps<{
    posts: {
        data: PostItem[];
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

const formatDateShort = (date: string) =>
    new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

const imageUrl = (post: PostItem) =>
    post.thumbnail_url || (post.thumbnail ? `/storage/${post.thumbnail}` : null);

const activeCategoryName = computed(() =>
    props.categories.find((c) => c.slug === props.categorySlug)?.nama ?? null,
);

const uniqueCategories = computed(() => {
    const seen = new Set<string>();
    return props.categories.filter((cat) => {
        if (seen.has(cat.slug)) return false;
        seen.add(cat.slug);
        return true;
    });
});
</script>

<template>
    <Head :title="categorySlug ? `Berita - ${activeCategoryName || categorySlug}` : 'Berita'" />

    <PageHero :title="categorySlug && activeCategoryName ? activeCategoryName : 'Berita Desa'" :description="categorySlug ? 'Kumpulan berita kategori ' + (activeCategoryName || categorySlug) : 'Informasi, kegiatan, dan pengumuman terbaru seputar desa kami'">
        <template #icon>
            <Newspaper class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Search & Category Filters -->
        <div class="mb-8 space-y-4">
            <!-- Search -->
            <form class="mx-auto w-full max-w-lg">
                <div class="relative">
                    <Search class="absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-zinc-400" />
                    <Input
                        name="search"
                        :value="search"
                        placeholder="Cari berita berdasarkan judul..."
                        class="h-11 rounded-xl border-zinc-200 pl-11 text-sm shadow-sm placeholder:text-zinc-400 focus:border-blue-400 focus:ring-blue-200 dark:border-zinc-700 dark:bg-zinc-800/50"
                    />
                </div>
            </form>

            <!-- Category Pills -->
            <div class="flex flex-wrap justify-center gap-2">
                <a
                    href="/berita"
                    class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-all duration-200"
                    :class="!categorySlug
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-200 dark:shadow-blue-900/40'
                        : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700'"
                >
                    <Newspaper class="size-3.5" />
                    Semua
                </a>
                <a
                    v-for="cat in uniqueCategories"
                    :key="cat.slug"
                    :href="`/berita/kategori/${cat.slug}`"
                    class="inline-flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-medium transition-all duration-200"
                    :class="categorySlug === cat.slug
                        ? 'bg-blue-600 text-white shadow-md shadow-blue-200 dark:shadow-blue-900/40'
                        : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700'"
                >
                    {{ cat.nama }}
                    <span
                        class="ml-0.5 rounded-full bg-zinc-200/60 px-1.5 py-0.5 text-[10px] font-semibold"
                        :class="categorySlug === cat.slug
                            ? 'bg-white/20 text-white'
                            : 'text-zinc-500 dark:bg-zinc-700 dark:text-zinc-400'"
                    >
                        {{ cat.posts_count ?? 0 }}
                    </span>
                </a>
            </div>
        </div>

        <!-- Posts Grid -->
        <div v-if="posts.data.length === 0" class="flex min-h-[40vh] items-center justify-center">
            <div class="text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                    <Newspaper class="h-10 w-10 text-zinc-300 dark:text-zinc-600" />
                </div>
                <h3 class="mt-5 text-lg font-semibold text-zinc-500 dark:text-zinc-400">
                    {{ search ? `Tidak ada hasil untuk "${search}"` : 'Belum ada berita' }}
                </h3>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">
                    {{ search ? 'Coba kata kunci lain atau jelajahi kategori di atas.' : 'Berita akan ditampilkan di sini setelah dipublikasikan.' }}
                </p>
                <a
                    v-if="search"
                    href="/berita"
                    class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400"
                >
                    Lihat semua berita <ArrowRight class="size-3.5" />
                </a>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card
                v-for="post in posts.data"
                :key="post.id"
                class="group flex flex-col overflow-hidden border-zinc-200/60 py-0 transition-all duration-300 hover:shadow-lg hover:-translate-y-1 dark:border-zinc-700/60 dark:bg-zinc-900"
            >
                <!-- Thumbnail -->
                <Link :href="`/berita/${post.slug}`" class="relative aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                    <img
                        v-if="imageUrl(post)"
                        :src="imageUrl(post)!"
                        :alt="post.judul"
                        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                    <div v-else class="flex h-full w-full items-center justify-center">
                        <Newspaper class="h-16 w-16 text-zinc-300 dark:text-zinc-600" />
                    </div>
                    <!-- Category badge overlay -->
                    <Badge
                        v-if="post.category"
                        class="absolute left-3 top-3 border-0 bg-white/90 text-xs font-semibold text-blue-700 shadow-sm backdrop-blur-sm dark:bg-zinc-900/90 dark:text-blue-400"
                    >
                        {{ post.category.nama }}
                    </Badge>
                </Link>

                <!-- Content -->
                <CardContent class="flex flex-1 flex-col gap-3 p-5">
                    <!-- Meta -->
                    <div class="flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1">
                            <Calendar class="size-3" />
                            {{ formatDateShort(post.published_at) }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <Eye class="size-3" />
                            {{ post.views }}
                        </span>
                    </div>

                    <!-- Title -->
                    <Link :href="`/berita/${post.slug}`" class="group/link flex-1">
                        <h3 class="text-base font-bold leading-snug text-zinc-900 transition-colors line-clamp-2 group-hover/link:text-blue-600 dark:text-white dark:group-hover/link:text-blue-400">
                            {{ post.judul }}
                        </h3>
                    </Link>

                    <!-- Excerpt -->
                    <p v-if="post.ringkasan" class="text-sm leading-relaxed text-zinc-500 line-clamp-3 dark:text-zinc-400" v-html="post.ringkasan" />

                    <!-- Read more -->
                    <Link
                        :href="`/berita/${post.slug}`"
                        class="mt-auto inline-flex items-center gap-1 pt-1 text-sm font-medium text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                    >
                        Baca selengkapnya <ArrowRight class="size-3.5 transition-transform group-hover/link:translate-x-0.5" />
                    </Link>
                </CardContent>
            </Card>
        </div>

        <!-- Pagination -->
        <div v-if="posts.last_page > 1" class="mt-10 flex justify-center">
            <div class="flex items-center gap-1 rounded-xl border border-zinc-200 bg-white p-1 shadow-sm dark:border-zinc-700 dark:bg-zinc-800">
                <a
                    v-for="link in posts.links"
                    :key="link.label"
                    :href="link.url || undefined"
                    v-html="link.label"
                    class="inline-flex h-9 min-w-[2.25rem] items-center justify-center rounded-lg px-3 text-sm font-medium transition-colors"
                    :class="{
                        'bg-blue-600 text-white shadow-sm': link.active,
                        'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-700': !link.active && link.url,
                        'cursor-default text-zinc-300 dark:text-zinc-600': !link.url,
                    }"
                ></a>
            </div>
        </div>
    </div>
</template>
