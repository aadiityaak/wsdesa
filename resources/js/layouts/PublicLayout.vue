<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Toaster } from '@/components/ui/sonner';
import AppLogo from '@/components/AppLogo.vue';
</script>

<template>
    <div class="flex min-h-screen flex-col bg-white dark:bg-zinc-900">
        <!-- Navbar -->
        <header class="sticky top-0 z-50 border-b bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:bg-zinc-950/80">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="flex items-center gap-2">
                    <AppLogo class="h-8 w-8" />
                    <span class="text-lg font-bold text-zinc-900 dark:text-white">Desa Digital</span>
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden items-center gap-1 md:flex">
                    <Link href="/" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        Beranda
                    </Link>
                    <Link href="/profil" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        Profil
                    </Link>
                    <Link href="/berita" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        Berita
                    </Link>
                    <Link href="/layanan-surat" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        Layanan
                    </Link>
                    <Link href="/galeri" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        Galeri
                    </Link>
                    <Link href="/umkm" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        UMKM
                    </Link>
                    <Link href="/kontak" class="rounded-md px-3 py-2 text-sm font-medium text-zinc-700 transition-colors hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white">
                        Kontak
                    </Link>
                </nav>

                <!-- Mobile menu button -->
                <button class="rounded-md p-2 text-zinc-700 md:hidden dark:text-zinc-300" @click="() => {}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t bg-zinc-50 dark:bg-zinc-950">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Kontak Kami</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                            Jl. Desa Digital No. 1<br />
                            Kecamatan Digital, Kabupaten Digital<br />
                            Telepon: (021) 1234-5678<br />
                            Email: info@desa-digital.id
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Tautan Penting</h3>
                        <ul class="mt-2 space-y-1">
                            <li>
                                <Link href="/profil" class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Profil Desa</Link>
                            </li>
                            <li>
                                <Link href="/pemerintahan" class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Pemerintahan</Link>
                            </li>
                            <li>
                                <Link href="/berita" class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Berita</Link>
                            </li>
                            <li>
                                <Link href="/download" class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Download</Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Layanan</h3>
                        <ul class="mt-2 space-y-1">
                            <li>
                                <Link href="/layanan-surat" class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Pengajuan Surat</Link>
                            </li>
                            <li>
                                <Link href="/pengaduan/cek-status" class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Pengaduan</Link>
                            </li>
                            <li>
                                <Link href="/statistik" class="text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Statistik Desa</Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="mt-8 border-t border-zinc-200 pt-4 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400">
                    &copy; {{ new Date().getFullYear() }} Desa Digital. Seluruh hak cipta dilindungi.
                </div>
            </div>
        </footer>

        <Toaster />
    </div>
</template>
