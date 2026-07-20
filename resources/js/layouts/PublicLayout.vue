<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import AppLogo from '@/components/AppLogo.vue';
import { useAppearance } from '@/composables/useAppearance';
import { X, Menu, Sun, Moon } from '@lucide/vue';

const page = usePage();
const sharedProfile = computed<{ nama_desa?: string; header_style?: string; footer_style?: string } | null>(() => page.props.sharedProfile as { nama_desa?: string; header_style?: string; footer_style?: string } | null);
const desaName = computed(() => sharedProfile.value?.nama_desa || 'Desa Digital');
const headerStyle = computed(() => sharedProfile.value?.header_style || 'default');
const footerStyle = computed(() => sharedProfile.value?.footer_style || 'default');

const isMobileMenuOpen = ref(false);
const { appearance, updateAppearance } = useAppearance();

function toggleAppearance() {
    updateAppearance(appearance.value === 'dark' ? 'light' : 'dark');
}

const headerClass = computed(() => {
    const classes: Record<string, string> = {
        default: 'border-b bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:bg-zinc-950/80',
        modern: 'bg-gradient-to-r from-blue-600 to-indigo-600 shadow-md',
        classic: 'bg-zinc-800 shadow-md dark:bg-zinc-900',
        minimal: 'bg-zinc-50/80 backdrop-blur supports-[backdrop-filter]:bg-zinc-50/60 dark:bg-zinc-900/80',
    };
    return classes[headerStyle.value] || classes.default;
});

const navLinkClass = computed(() => {
    if (headerStyle.value === 'modern') {
        return 'rounded-md px-3 py-2 text-sm font-medium text-white/80 transition-colors hover:bg-white/10 hover:text-white';
    }
    if (headerStyle.value === 'classic') {
        return 'rounded-md px-3 py-2 text-sm font-medium text-zinc-300 transition-colors hover:bg-zinc-700 hover:text-white';
    }
    return 'rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white';
});

const brandTextClass = computed(() => {
    if (headerStyle.value === 'modern' || headerStyle.value === 'classic') {
        return 'text-lg font-bold text-white';
    }
    return 'text-lg font-bold text-zinc-900 dark:text-white';
});

const themeBtnClass = computed(() => {
    if (headerStyle.value === 'modern') {
        return 'hidden rounded-full p-2 text-white/70 transition-colors hover:bg-white/10 hover:text-white md:block';
    }
    if (headerStyle.value === 'classic') {
        return 'hidden rounded-full p-2 text-zinc-300 transition-colors hover:bg-zinc-700 hover:text-white md:block';
    }
    return 'hidden rounded-full p-2 text-zinc-600 transition-colors hover:bg-zinc-100 md:block dark:text-zinc-400 dark:hover:bg-zinc-800';
});

const mobileBtnClass = computed(() => {
    if (headerStyle.value === 'modern') {
        return 'rounded-md p-2 text-white/80 md:hidden';
    }
    if (headerStyle.value === 'classic') {
        return 'rounded-md p-2 text-zinc-300 md:hidden';
    }
    return 'rounded-md p-2 text-zinc-700 md:hidden dark:text-zinc-300';
});

const footerClass = computed(() => {
    const classes: Record<string, string> = {
        default: 'border-t bg-zinc-50 dark:bg-zinc-950',
        modern: 'bg-zinc-900 text-white',
        classic: 'border-t-4 border-amber-500 bg-zinc-100 dark:bg-zinc-900',
        minimal: 'border-t bg-white dark:bg-zinc-950',
    };
    return classes[footerStyle.value] || classes.default;
});

const footerHeadingClass = computed(() => {
    if (footerStyle.value === 'modern') {
        return 'text-sm font-semibold text-white';
    }
    if (footerStyle.value === 'classic') {
        return 'text-sm font-semibold text-zinc-800 dark:text-zinc-100';
    }
    return 'text-sm font-semibold text-zinc-900 dark:text-white';
});

const footerTextClass = computed(() => {
    if (footerStyle.value === 'modern') {
        return 'text-sm text-zinc-400';
    }
    return 'text-sm text-zinc-600 dark:text-zinc-400';
});

const footerLinkClass = computed(() => {
    if (footerStyle.value === 'modern') {
        return 'text-sm text-zinc-400 hover:text-white';
    }
    return 'text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white';
});

const footerDividerClass = computed(() => {
    if (footerStyle.value === 'modern') {
        return 'border-zinc-800';
    }
    return 'border-zinc-200 dark:border-zinc-800';
});

const footerCopyrightClass = computed(() => {
    if (footerStyle.value === 'modern') {
        return 'text-sm text-zinc-500';
    }
    return 'text-sm text-zinc-500 dark:text-zinc-400';
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white dark:bg-zinc-900">
        <!-- Navbar -->
        <header class="sticky top-0 z-50" :class="headerClass">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2">
                    <AppLogo class="h-8 w-8" />
                    <span :class="brandTextClass">{{ desaName }}</span>
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden items-center gap-1 md:flex">
                    <Link href="/" :class="navLinkClass">Beranda</Link>
                    <Link href="/profil" :class="navLinkClass">Profil</Link>
                    <Link href="/berita" :class="navLinkClass">Berita</Link>
                    <Link href="/statistik" :class="navLinkClass">Statistik</Link>
                    <Link href="/layanan-surat" :class="navLinkClass">Layanan</Link>
                    <Link href="/galeri" :class="navLinkClass">Galeri</Link>
                    <Link href="/umkm" :class="navLinkClass">UMKM</Link>
                    <Link href="/kontak" :class="navLinkClass">Kontak</Link>
                </nav>

                <!-- Theme switcher -->
                <button
                    :class="themeBtnClass"
                    :title="appearance === 'dark' ? 'Mode Terang' : 'Mode Gelap'"
                    @click="toggleAppearance"
                >
                    <Sun v-if="appearance === 'dark'" class="size-5" />
                    <Moon v-else class="size-5" />
                </button>

                <!-- Mobile menu button -->
                <button :class="mobileBtnClass" @click="isMobileMenuOpen = !isMobileMenuOpen">
                    <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
                    <X v-else class="h-6 w-6" />
                </button>
            </div>
        </header>

        <!-- Offcanvas Overlay -->
        <div v-if="isMobileMenuOpen" class="fixed inset-0 z-40 bg-black/50 md:hidden" @click="isMobileMenuOpen = false" />

        <!-- Offcanvas Panel -->
        <aside v-if="isMobileMenuOpen" class="fixed inset-y-0 right-0 z-50 w-72 max-w-[85vw] bg-white shadow-xl md:hidden dark:bg-zinc-900">
                <div class="flex h-full flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b px-5 py-4 dark:border-zinc-800">
                        <span class="text-sm font-semibold text-zinc-900 dark:text-white">Menu</span>
                        <button class="rounded-md p-1 text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            <X class="size-5" />
                        </button>
                    </div>

                    <!-- Nav links -->
                    <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
                        <Link href="/" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Beranda
                        </Link>
                        <Link href="/profil" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Profil
                        </Link>
                        <Link href="/pemerintahan" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Pemerintahan
                        </Link>
                        <Link href="/berita" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Berita
                        </Link>
                        <Link href="/statistik" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Statistik
                        </Link>
                        <Link href="/agenda" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Agenda
                        </Link>
                        <Link href="/layanan-surat" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Layanan
                        </Link>
                        <Link href="/galeri" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Galeri
                        </Link>
                        <Link href="/infografis/penduduk" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Infografis
                        </Link>
                        <Link href="/umkm" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            UMKM
                        </Link>
                        <Link href="/kontak" class="flex rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800" @click="isMobileMenuOpen = false">
                            Kontak
                        </Link>
                    </nav>

                    <!-- Bottom theme toggle -->
                    <div class="border-t px-3 py-4 dark:border-zinc-800">
                        <button
                            class="flex w-full items-center justify-center gap-2 rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-600 transition-colors hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800"
                            @click="toggleAppearance; isMobileMenuOpen = false"
                        >
                            <Sun v-if="appearance === 'dark'" class="size-5" />
                            <Moon v-else class="size-5" />
                            {{ appearance === 'dark' ? 'Mode Terang' : 'Mode Gelap' }}
                        </button>
                    </div>
                </div>
            </aside>

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer :class="footerClass">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <template v-if="footerStyle !== 'minimal'">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div>
                            <h3 :class="footerHeadingClass">Kontak Kami</h3>
                            <p :class="['mt-2', footerTextClass]">
                                Jl. Desa Digital No. 1<br />
                                Kecamatan Digital, Kabupaten Digital<br />
                                Telepon: (021) 1234-5678<br />
                                Email: info@desa-digital.id
                            </p>
                        </div>
                        <div>
                            <h3 :class="footerHeadingClass">Tautan Penting</h3>
                            <ul class="mt-2 space-y-1">
                                <li>
                                    <Link href="/profil" :class="footerLinkClass">Profil Desa</Link>
                                </li>
                                <li>
                                    <Link href="/pemerintahan" :class="footerLinkClass">Pemerintahan</Link>
                                </li>
                                <li>
                                    <Link href="/berita" :class="footerLinkClass">Berita</Link>
                                </li>
                                <li>
                                    <Link href="/download" :class="footerLinkClass">Download</Link>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h3 :class="footerHeadingClass">Layanan</h3>
                            <ul class="mt-2 space-y-1">
                                <li>
                                    <Link href="/layanan-surat" :class="footerLinkClass">Pengajuan Surat</Link>
                                </li>
                                <li>
                                    <Link href="/pengaduan/cek-status" :class="footerLinkClass">Pengaduan</Link>
                                </li>
                                <li>
                                    <Link href="/statistik" :class="footerLinkClass">Statistik Desa</Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </template>
                <div :class="['border-t pt-4 text-center', footerStyle !== 'minimal' ? 'mt-8' : '', footerDividerClass, footerCopyrightClass]">
                    &copy; {{ new Date().getFullYear() }} {{ desaName }}. Seluruh hak cipta dilindungi.
                </div>
            </div>
        </footer>

        <Toaster />
    </div>
</template>
