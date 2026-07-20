<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

const page = usePage();
const appName = page.props.name as string;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid min-h-svh lg:grid-cols-2">
        <!-- Left: warm editorial panel -->
        <div class="relative hidden flex-col justify-between overflow-hidden bg-[#262622] p-12 lg:flex">
            <!-- Grain texture -->
            <div
                class="pointer-events-none absolute inset-0 opacity-[0.04]"
                style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.85%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E'); background-size: 200px 200px;"
            />

            <!-- Top: Brand -->
            <Link :href="home()" class="relative z-10 flex items-center gap-3.5">
                <div class="flex size-11 items-center justify-center rounded-2xl bg-white/[0.08]">
                    <AppLogoIcon class="size-6 fill-current text-[#e60023]" />
                </div>
                <span class="text-lg font-semibold tracking-tight text-white">
                    {{ appName }}
                </span>
            </Link>

            <!-- Center: Quote -->
            <div class="relative z-10 max-w-xs">
                <p class="text-[28px]/tight font-semibold tracking-[-0.8px] text-white">
                    Mengelola administrasi desa
                    <span class="text-[#e60023]">dengan lebih mudah</span>
                    dan terorganisir.
                </p>
                <p class="mt-6 text-base/relaxed text-white/60">
                    Sistem informasi desa terintegrasi untuk pelayanan yang transparan dan efisien.
                </p>
            </div>

            <!-- Bottom -->
            <div class="relative z-10">
                <p class="text-xs text-white/40">
                    &copy; {{ new Date().getFullYear() }} {{ appName }}
                </p>
            </div>
        </div>

        <!-- Right: form panel -->
        <div class="flex flex-col items-center justify-center bg-[#fbfbf9] p-6 sm:p-10">
            <div class="w-full max-w-[380px]">
                <!-- Mobile logo -->
                <Link :href="home()" class="mb-10 flex items-center justify-center gap-3 lg:hidden">
                    <div class="flex size-10 items-center justify-center rounded-2xl bg-[#e5e5e0]">
                        <AppLogoIcon class="size-5 fill-current text-[#e60023]" />
                    </div>
                    <span class="text-base font-semibold tracking-tight text-[#211922]">{{ appName }}</span>
                </Link>

                <!-- Header -->
                <div class="mb-8 space-y-2">
                    <h1 class="text-[28px] font-bold tracking-[-0.8px] text-[#211922] leading-tight">
                        {{ title }}
                    </h1>
                    <p class="text-base/relaxed text-[#62625b]">
                        {{ description }}
                    </p>
                </div>

                <!-- Slot: login form -->
                <slot />
            </div>
        </div>
    </div>
</template>
