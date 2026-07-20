 <script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';

const page = usePage();
const hasDynamicLogo = computed(() => !!(page.props.sharedProfile as { logo?: string | null } | null)?.logo);
</script>

<template>
    <div
        v-if="hasDynamicLogo"
        class="flex aspect-square size-8 items-center justify-center overflow-hidden rounded-md"
    >
        <AppLogoIcon class="size-full object-contain" />
    </div>
    <div
        v-else
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground"
    >
        <AppLogoIcon class="size-5 fill-current text-white dark:text-black" />
    </div>
</template>
