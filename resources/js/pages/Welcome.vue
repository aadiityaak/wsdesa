<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ArrowRight, Calendar, ChevronLeft, ChevronRight, ExternalLink, FileText, MapPin, MessageSquare, ScrollText } from '@lucide/vue';

interface SliderItem {
    id: number;
    judul: string;
    deskripsi: string;
    image_url: string | null;
    link: string | null;
}

interface PostItem {
    id: number;
    judul: string;
    slug: string;
    ringkasan: string | null;
    gambar: string | null;
    published_at: string;
    category: { nama: string; slug: string } | null;
}

interface EventItem {
    id: number;
    judul: string;
    slug: string;
    deskripsi: string | null;
    lokasi: string | null;
    tanggal_mulai: string;
    tanggal_selesai: string;
}

const props = defineProps<{
    sliders: SliderItem[];
    latestPosts: PostItem[];
    upcomingEvents: EventItem[];
}>();

// Hero slider
const currentSlide = ref(0);

function nextSlide() {
    if (props.sliders.length > 0) {
        currentSlide.value = (currentSlide.value + 1) % props.sliders.length;
    }
}

function prevSlide() {
    if (props.sliders.length > 0) {
        currentSlide.value = (currentSlide.value - 1 + props.sliders.length) % props.sliders.length;
    }
}

let autoSlide: ReturnType<typeof setInterval> | null = null;

onMounted(() => {
    if (props.sliders.length > 1) {
        autoSlide = setInterval(nextSlide, 5000);
    }
});

onUnmounted(() => {
    if (autoSlide) clearInterval(autoSlide);
});

function formatDate(dateStr: string) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
}
</script>

<template>
    <Head title="Beranda" />

    <!-- Hero Slider -->
    <section v-if="sliders.length > 0" class="relative h-[70vh] min-h-[400px] overflow-hidden">
        <div
            v-for="(slide, index) in sliders"
            :key="slide.id"
            class="absolute inset-0 transition-opacity duration-700"
            :class="index === currentSlide ? 'opacity-100' : 'opacity-0'"
        >
            <div v-if="slide.image_url" class="h-full w-full bg-cover bg-center" :style="{ backgroundImage: `url(${slide.image_url})` }" />
            <div v-else class="h-full w-full bg-gradient-to-br from-emerald-600 to-emerald-900" />
            <div class="absolute inset-0 bg-black/50" />
            <div class="relative flex h-full items-center justify-center">
                <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                        {{ slide.judul }}
                    </h1>
                    <p v-if="slide.deskripsi" class="mt-4 text-lg text-white/80 sm:text-xl">
                        {{ slide.deskripsi }}
                    </p>
                    <div v-if="slide.link" class="mt-8">
                        <Link :href="slide.link" class="inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-emerald-700 transition-colors hover:bg-emerald-50">
                            Selengkapnya
                            <ExternalLink class="size-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slider controls -->
        <button v-if="sliders.length > 1" @click="prevSlide" class="absolute left-4 top-1/2 -translate-y-1/2 rounded-full bg-white/20 p-2 text-white backdrop-blur-sm hover:bg-white/30">
            <ChevronLeft class="size-6" />
        </button>
        <button v-if="sliders.length > 1" @click="nextSlide" class="absolute right-4 top-1/2 -translate-y-1/2 rounded-full bg-white/20 p-2 text-white backdrop-blur-sm hover:bg-white/30">
            <ChevronRight class="size-6" />
        </button>

        <div v-if="sliders.length > 1" class="absolute bottom-6 left-1/2 flex -translate-x-1/2 gap-2">
            <button
                v-for="(_, index) in sliders"
                :key="index"
                @click="currentSlide = index"
                class="h-2 w-2 rounded-full transition-all"
                :class="index === currentSlide ? 'w-6 bg-white' : 'bg-white/50 hover:bg-white/70'"
            />
        </div>
    </section>

    <!-- Hero fallback if no sliders -->
    <section v-else class="relative bg-gradient-to-br from-emerald-600 to-emerald-900 py-24">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
            <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl">
                Selamat Datang di Desa Digital
            </h1>
            <p class="mt-4 text-lg text-white/80 sm:text-xl">
                Website resmi Pemerintah Desa Digital — Sumber informasi dan layanan masyarakat
            </p>
        </div>
    </section>

    <!-- Quick Services -->
    <section class="relative -mt-16 z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <Link href="/layanan-surat" class="group rounded-xl bg-white p-6 shadow-lg transition-all hover:shadow-xl dark:bg-zinc-800">
                <div class="flex size-14 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                    <FileText class="size-7" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Pengajuan Surat</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Ajukan surat desa secara online</p>
            </Link>
            <Link href="/pengaduan" class="group rounded-xl bg-white p-6 shadow-lg transition-all hover:shadow-xl dark:bg-zinc-800">
                <div class="flex size-14 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                    <MessageSquare class="size-7" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Pengaduan</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Sampaikan aspirasi dan pengaduan</p>
            </Link>
            <Link href="/pengaduan/cek-status" class="group rounded-xl bg-white p-6 shadow-lg transition-all hover:shadow-xl dark:bg-zinc-800">
                <div class="flex size-14 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
                    <ScrollText class="size-7" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Cek Status</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Cek status pengajuan & pengaduan</p>
            </Link>
            <Link href="/download" class="group rounded-xl bg-white p-6 shadow-lg transition-all hover:shadow-xl dark:bg-zinc-800">
                <div class="flex size-14 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-950 dark:text-purple-400">
                    <FileText class="size-7" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Download</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Unduh dokumen dan berkas penting</p>
            </Link>
        </div>
    </section>

    <!-- Latest News -->
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">Berita Terbaru</h2>
                <p class="mt-2 text-zinc-500 dark:text-zinc-400">Informasi terkini seputar desa</p>
            </div>
            <Link :href="route('berita.index')" class="hidden items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 sm:flex dark:text-emerald-400">
                Lihat Semua
                <ArrowRight class="size-4" />
            </Link>
        </div>

        <div v-if="latestPosts.length === 0" class="rounded-lg border border-zinc-200 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-800">
            <p class="text-zinc-500 dark:text-zinc-400">Belum ada berita.</p>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <Card v-for="post in latestPosts" :key="post.id" class="overflow-hidden transition-shadow hover:shadow-lg">
                <Link :href="route('berita.show', post.slug)">
                    <div class="aspect-[16/9] bg-zinc-100 dark:bg-zinc-700">
                        <img v-if="post.gambar" :src="post.gambar" :alt="post.judul" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                            <FileText class="size-10" />
                        </div>
                    </div>
                </Link>
                <CardContent class="p-4">
                    <Badge v-if="post.category" variant="secondary" class="mb-2">{{ post.category.nama }}</Badge>
                    <Link :href="route('berita.show', post.slug)">
                        <h3 class="line-clamp-2 font-semibold text-zinc-900 transition-colors hover:text-emerald-600 dark:text-white dark:hover:text-emerald-400">
                            {{ post.judul }}
                        </h3>
                    </Link>
                    <p v-if="post.ringkasan" class="mt-1 line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ post.ringkasan }}
                    </p>
                    <p class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">
                        {{ formatDate(post.published_at) }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <div class="mt-6 text-center sm:hidden">
            <Link :href="route('berita.index')" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                Lihat Semua Berita
                <ArrowRight class="size-4" />
            </Link>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="bg-zinc-50 py-20 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-zinc-900 dark:text-white">Agenda Mendatang</h2>
                    <p class="mt-2 text-zinc-500 dark:text-zinc-400">Kegiatan dan acara desa</p>
                </div>
                <Link :href="route('agenda.index')" class="hidden items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 sm:flex dark:text-emerald-400">
                    Lihat Semua
                    <ArrowRight class="size-4" />
                </Link>
            </div>

            <div v-if="upcomingEvents.length === 0" class="rounded-lg border border-zinc-200 bg-white p-12 text-center dark:border-zinc-700 dark:bg-zinc-800">
                <Calendar class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-3 text-zinc-500 dark:text-zinc-400">Belum ada agenda mendatang.</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <Card v-for="event in upcomingEvents" :key="event.id" class="transition-shadow hover:shadow-lg">
                    <CardHeader class="pb-3">
                        <div class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                            {{ formatDate(event.tanggal_mulai) }}
                        </div>
                        <CardTitle class="text-lg">
                            <Link :href="route('agenda.show', event.id)" class="hover:text-emerald-600 dark:hover:text-emerald-400">
                                {{ event.judul }}
                            </Link>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="event.deskripsi" class="line-clamp-2 text-sm text-zinc-500 dark:text-zinc-400">
                            {{ event.deskripsi }}
                        </p>
                        <p v-if="event.lokasi" class="mt-2 flex items-center gap-1 text-xs text-zinc-400 dark:text-zinc-500">
                            <MapPin class="size-3" />
                            {{ event.lokasi }}
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="mt-6 text-center sm:hidden">
                <Link :href="route('agenda.index')" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                    Lihat Semua Agenda
                    <ArrowRight class="size-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-br from-emerald-600 to-emerald-800 py-16">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
            <h2 class="text-3xl font-bold text-white">Butuh Bantuan?</h2>
            <p class="mt-3 text-lg text-emerald-100">
                Hubungi kami melalui kontak yang tersedia atau kunjungi kantor desa
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <Link :href="route('kontak.index')" class="inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-emerald-700 transition-colors hover:bg-emerald-50">
                    Hubungi Kami
                    <ArrowRight class="size-4" />
                </Link>
                <Link href="/profil" class="inline-flex items-center gap-2 rounded-lg border border-white/30 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/10">
                    Profil Desa
                </Link>
            </div>
        </div>
    </section>
</template>
