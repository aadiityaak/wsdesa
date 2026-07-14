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
        <!-- Left decorative panel -->
        <div
            class="relative hidden flex-col justify-between overflow-hidden bg-[#1a1410] p-10 lg:flex"
        >
            <!-- Subtle grain texture overlay -->
            <div
                class="pointer-events-none absolute inset-0 opacity-[0.03]"
                style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E'); background-size: 200px 200px;"
            />

            <!-- Decorative geometric pattern (top-right) -->
            <div class="pointer-events-none absolute -top-10 -right-10 opacity-[0.06]">
                <svg width="320" height="320" viewBox="0 0 320 320" fill="none">
                    <circle cx="160" cy="160" r="120" stroke="#d4a056" stroke-width="0.5" />
                    <circle cx="160" cy="160" r="90" stroke="#d4a056" stroke-width="0.5" />
                    <circle cx="160" cy="160" r="60" stroke="#d4a056" stroke-width="0.5" />
                    <circle cx="160" cy="160" r="30" stroke="#d4a056" stroke-width="0.5" />
                    <line x1="40" y1="160" x2="280" y2="160" stroke="#d4a056" stroke-width="0.5" />
                    <line x1="160" y1="40" x2="160" y2="280" stroke="#d4a056" stroke-width="0.5" />
                    <line x1="75" y1="75" x2="245" y2="245" stroke="#d4a056" stroke-width="0.5" />
                    <line x1="245" y1="75" x2="75" y2="245" stroke="#d4a056" stroke-width="0.5" />
                </svg>
            </div>

            <!-- Decorative pattern (bottom-left) -->
            <div class="pointer-events-none absolute -bottom-16 -left-16 opacity-[0.04]">
                <svg width="400" height="400" viewBox="0 0 400 400" fill="none">
                    <path
                        d="M200 20 L260 80 L380 80 L290 140 L320 260 L200 200 L80 260 L110 140 L20 80 L140 80 Z"
                        stroke="#d4a056"
                        stroke-width="0.8"
                        fill="none"
                    />
                    <path
                        d="M200 60 L240 100 L320 100 L260 140 L280 220 L200 180 L120 220 L140 140 L80 100 L160 100 Z"
                        stroke="#d4a056"
                        stroke-width="0.8"
                        fill="none"
                    />
                </svg>
            </div>

            <!-- Warm gradient accent -->
            <div
                class="pointer-events-none absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-[#2a1f15] to-transparent"
            />

            <!-- Top: Brand -->
            <Link
                :href="home()"
                class="relative z-10 flex items-center gap-3"
            >
                <div
                    class="flex size-10 items-center justify-center rounded-lg bg-[#d4a056]/10 ring-1 ring-[#d4a056]/20"
                >
                    <AppLogoIcon class="size-6 fill-current text-[#d4a056]" />
                </div>
                <span class="text-lg font-semibold tracking-tight text-[#f0e6d8]">
                    {{ appName }}
                </span>
            </Link>

            <!-- Center: Tagline -->
            <div class="relative z-10 max-w-sm">
                <blockquote class="space-y-4">
                    <p
                        class="text-2xl/snug font-light leading-relaxed tracking-tight text-[#e8dcc8]"
                    >
                        Mengelola administrasi desa
                        <span class="font-medium text-[#d4a056]">dengan lebih mudah</span>
                        dan terorganisir.
                    </p>
                </blockquote>
            </div>

            <!-- Bottom: Footer -->
            <div class="relative z-10">
                <p class="text-xs tracking-wide text-[#8a7d6b]">
                    &copy; {{ new Date().getFullYear() }} {{ appName }}. Sistem Informasi Desa.
                </p>
            </div>
        </div>

        <!-- Right form panel -->
        <div
            class="flex flex-col items-center justify-center gap-6 bg-[#faf8f5] p-6 sm:p-8 md:p-12"
        >
            <div class="auth-form-container w-full max-w-[380px]">
                <!-- Mobile logo (hidden on lg+) -->
                <Link
                    :href="home()"
                    class="mb-8 flex items-center justify-center gap-2.5 lg:hidden"
                >
                    <div
                        class="flex size-9 items-center justify-center rounded-lg bg-[#1a1410]/5 ring-1 ring-[#1a1410]/10"
                    >
                        <AppLogoIcon
                            class="size-5 fill-current text-[#1a1410]"
                        />
                    </div>
                    <span class="text-base font-semibold tracking-tight text-[#1a1410]">
                        {{ appName }}
                    </span>
                </Link>

                <!-- Header -->
                <div class="mb-8 space-y-1.5">
                    <h1
                        class="text-[22px] font-semibold tracking-tight text-[#1a1410] leading-tight"
                    >
                        {{ title }}
                    </h1>
                    <p class="text-sm leading-relaxed text-[#7a7062]">
                        {{ description }}
                    </p>
                </div>

                <!-- Form slot -->
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.auth-form-container {
    animation: fadeInUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
}
</style>
