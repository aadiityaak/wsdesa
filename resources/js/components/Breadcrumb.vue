<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface BreadcrumbItem {
    label: string;
    href?: string;
}

defineProps<{
    items: BreadcrumbItem[];
}>();
</script>

<template>
    <nav class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
            <li>
                <Link href="/" class="transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">Beranda</Link>
            </li>
            <template v-for="(item, index) in items" :key="index">
                <li class="text-zinc-300 dark:text-zinc-600">/</li>
                <li>
                    <Link
                        v-if="item.href"
                        :href="item.href"
                        class="transition-colors hover:text-zinc-700 dark:hover:text-zinc-200"
                    >
                        {{ item.label }}
                    </Link>
                    <span v-else class="font-medium text-zinc-700 dark:text-zinc-200">{{ item.label }}</span>
                </li>
            </template>
        </ol>
    </nav>
</template>
