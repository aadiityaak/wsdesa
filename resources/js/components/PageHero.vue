<script setup lang="ts">
defineProps<{
    title: string;
    description?: string;
}>();
</script>

<template>
    <section class="relative bg-gradient-to-br from-blue-600 to-indigo-900 px-4 py-12 md:py-16">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative mx-auto max-w-7xl text-center">
            <div v-if="$slots.icon" class="mb-3 flex justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-sm">
                    <slot name="icon" />
                </div>
            </div>
            <h1 class="text-3xl font-bold text-white sm:text-4xl">{{ title }}</h1>
            <p v-if="description" class="mt-3 text-lg text-blue-100">{{ description }}</p>
        </div>
    </section>
</template>
