<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

interface PageData {
    id: number;
    judul: string;
    konten: string;
}

defineProps<{
    page: PageData;
}>();
</script>

<template>
    <Head :title="page.judul" />

    <PageHero :title="page.judul" description="Halaman informasi">
        <template #icon>
            <FileText class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">

        <div
            class="prose prose-zinc max-w-none dark:prose-invert prose-headings:text-zinc-900 dark:prose-headings:text-white prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-img:rounded-lg"
            v-html="page.konten"
        ></div>
    </div>
</template>
