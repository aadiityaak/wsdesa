 <script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Doughnut, Bar } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement } from 'chart.js';
import {
    ArrowRight,
    Calendar,
    ChevronLeft,
    ChevronRight,
    Clock,
    Download,
    ExternalLink,
    FileText,
    ImageIcon,
    MapPin,
    MessageSquare,
    Newspaper,
    ScrollText,
    Users,
    Landmark,
} from '@lucide/vue';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement);

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

interface StatsData {
    totalResidents: number;
    byGender: Record<string, number>;
    byAgeGroup: Record<string, number>;
}

interface BudgetCategorySummary {
    anggaran: number;
    realisasi: number;
}

interface BudgetSummary {
    tahun: number;
    pendapatan: BudgetCategorySummary;
    belanja: BudgetCategorySummary;
    pembiayaan: BudgetCategorySummary;
}

interface JamKerjaItem {
    id?: number;
    hari: string;
    jam_buka: string | null;
    jam_tutup: string | null;
    is_libur: boolean;
}

const props = defineProps<{
    sliders: SliderItem[];
    latestPosts: PostItem[];
    upcomingEvents: EventItem[];
    stats: StatsData;
    budgetSummary: BudgetSummary;
    jamKerja: JamKerjaItem[] | null;
}>();

// --- Hero Slider ---
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

const failedImages = ref<Set<string>>(new Set());

function handleImgError(url: string) {
    failedImages.value = new Set([...failedImages.value, url]);
}

function showImage(url: string | null): boolean {
    return !!url && !failedImages.value.has(url);
}

function formatCurrency(value: number): string {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
}

// --- Gender chart ---
const genderChartData = computed(() => ({
    labels: ['Laki-laki', 'Perempuan'],
    datasets: [{
        data: [props.stats.byGender?.L ?? 0, props.stats.byGender?.P ?? 0],
        backgroundColor: ['#10b981', '#f472b6'],
        borderWidth: 0,
    }],
}));

const genderChartOptions = {
    cutout: '65%',
    plugins: { legend: { display: true, position: 'bottom' as const, labels: { padding: 16, usePointStyle: true } } },
    maintainAspectRatio: true,
};

// --- Age chart ---
const ageLabels = ['0-5', '6-12', '13-17', '18-25', '26-40', '41-60', '60+'];
const ageColors = ['#a7f3d0', '#6ee7b7', '#34d399', '#10b981', '#059669', '#047857', '#065f46'];

const ageChartData = computed(() => ({
    labels: ageLabels,
    datasets: [{
        label: 'Jumlah',
        data: ageLabels.map(l => props.stats.byAgeGroup?.[l] ?? 0),
        backgroundColor: ageColors,
        borderRadius: 4,
    }],
}));

const ageChartOptions = {
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { stepSize: 1, font: { size: 11 } } },
    },
    maintainAspectRatio: true,
};

// --- APBDes chart ---
const budgetChartData = computed(() => ({
    labels: ['Pendapatan', 'Belanja', 'Pembiayaan'],
    datasets: [
        {
            label: 'Anggaran',
            data: [
                props.budgetSummary?.pendapatan?.anggaran ?? 0,
                props.budgetSummary?.belanja?.anggaran ?? 0,
                props.budgetSummary?.pembiayaan?.anggaran ?? 0,
            ],
            backgroundColor: '#10b981',
            borderRadius: 4,
        },
        {
            label: 'Realisasi',
            data: [
                props.budgetSummary?.pendapatan?.realisasi ?? 0,
                props.budgetSummary?.belanja?.realisasi ?? 0,
                props.budgetSummary?.pembiayaan?.realisasi ?? 0,
            ],
            backgroundColor: '#6ee7b7',
            borderRadius: 4,
        },
    ],
}));

const budgetChartOptions = {
    plugins: { legend: { display: true, position: 'bottom' as const, labels: { padding: 16, usePointStyle: true } } },
    scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { callback: (v: number) => 'Rp' + (v / 1e6).toFixed(0) + 'jt', font: { size: 11 } } },
    },
    maintainAspectRatio: true,
};
</script>

<template>
    <Head title="Beranda" />

    <!-- ==================== Hero Slider ==================== -->
    <section
        v-if="sliders.length > 0"
        role="region"
        aria-roledescription="carousel"
        aria-label="Slider Beranda"
        class="relative min-h-[50vh] overflow-hidden md:min-h-[60vh]"
    >
        <div
            v-for="(slide, index) in sliders"
            :key="slide.id"
            role="group"
            :aria-roledescription="`slide ${index + 1} of ${sliders.length}`"
            :aria-hidden="index !== currentSlide"
            class="absolute inset-0 transition-opacity duration-700"
            :class="index === currentSlide ? 'opacity-100' : 'opacity-0'"
        >
            <img
                v-if="showImage(slide.image_url)"
                :src="slide.image_url"
                alt=""
                class="h-full w-full object-cover"
                @error="handleImgError(slide.image_url!)"
            />
            <div v-if="!showImage(slide.image_url)" class="h-full w-full bg-gradient-to-br from-blue-600 to-indigo-900" />
            <div class="absolute inset-0 bg-black/50" />
            <div class="relative flex h-full items-center justify-center">
                <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
                    <h1 class="text-balance text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        {{ slide.judul }}
                    </h1>
                    <p v-if="slide.deskripsi" class="mt-4 text-base text-white/80 sm:text-lg">
                        {{ slide.deskripsi }}
                    </p>
                    <div v-if="slide.link" class="mt-8">
                        <Link
                            :href="slide.link"
                            class="inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-emerald-700 transition-colors hover:bg-emerald-50"
                        >
                            Selengkapnya
                            <ExternalLink class="size-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <button
            v-if="sliders.length > 1"
            aria-label="Slide sebelumnya"
            @click="prevSlide"
            class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/30 p-2 text-white backdrop-blur-sm transition-colors hover:bg-white/50"
        >
            <ChevronLeft class="size-5 md:size-6" />
        </button>
        <button
            v-if="sliders.length > 1"
            aria-label="Slide berikutnya"
            @click="nextSlide"
            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/30 p-2 text-white backdrop-blur-sm transition-colors hover:bg-white/50"
        >
            <ChevronRight class="size-5 md:size-6" />
        </button>

        <!-- Dots -->
        <div v-if="sliders.length > 1" class="absolute bottom-5 left-1/2 flex -translate-x-1/2 gap-2">
            <button
                v-for="(_, index) in sliders"
                :key="index"
                :aria-label="`Slide ${index + 1}`"
                @click="currentSlide = index"
                class="h-2 rounded-full transition-all"
                :class="index === currentSlide ? 'w-6 bg-white' : 'w-2 bg-white/50 hover:bg-white/70'"
            />
        </div>
    </section>

    <!-- Hero Fallback (no sliders) -->
    <section v-else class="relative bg-gradient-to-br from-blue-600 to-indigo-900 py-20 md:py-28">
        <div class="mx-auto max-w-3xl px-4 text-center sm:px-6">
            <h1 class="text-balance text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">
                Selamat Datang di Desa Digital
            </h1>
            <p class="mt-4 text-base text-white/80 sm:text-lg">
                Website resmi Pemerintah Desa Digital — Sumber informasi dan layanan masyarakat
            </p>
        </div>
    </section>

    <!-- ==================== Quick Services ==================== -->
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <Link
                href="/layanan-surat"
                class="group rounded-xl border border-zinc-200/60 bg-white p-6 shadow-sm ring-1 ring-zinc-200/50 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:bg-zinc-800 dark:ring-zinc-700/50"
            >
                <div class="flex size-12 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-950 dark:text-emerald-400">
                    <FileText class="size-6" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Pengajuan Surat</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Ajukan surat desa secara online</p>
            </Link>

            <Link
                href="/pengaduan"
                class="group rounded-xl border border-zinc-200/60 bg-white p-6 shadow-sm ring-1 ring-zinc-200/50 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:bg-zinc-800 dark:ring-zinc-700/50"
            >
                <div class="flex size-12 items-center justify-center rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-950 dark:text-blue-400">
                    <MessageSquare class="size-6" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Pengaduan</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Sampaikan aspirasi dan pengaduan</p>
            </Link>

            <Link
                href="/pengaduan/cek-status"
                class="group rounded-xl border border-zinc-200/60 bg-white p-6 shadow-sm ring-1 ring-zinc-200/50 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:bg-zinc-800 dark:ring-zinc-700/50"
            >
                <div class="flex size-12 items-center justify-center rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-950 dark:text-amber-400">
                    <ScrollText class="size-6" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Cek Status</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Cek status pengajuan &amp; pengaduan</p>
            </Link>

            <Link
                href="/download"
                class="group rounded-xl border border-zinc-200/60 bg-white p-6 shadow-sm ring-1 ring-zinc-200/50 transition-all duration-200 hover:-translate-y-1 hover:shadow-lg dark:border-zinc-700/60 dark:bg-zinc-800 dark:ring-zinc-700/50"
            >
                <div class="flex size-12 items-center justify-center rounded-lg bg-purple-50 text-purple-600 dark:bg-purple-950 dark:text-purple-400">
                    <Download class="size-6" />
                </div>
                <h3 class="mt-4 font-semibold text-zinc-900 dark:text-white">Download</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Unduh dokumen dan berkas penting</p>
            </Link>
        </div>
    </section>

    <!-- ==================== Jam Kerja ==================== -->
    <section v-if="jamKerja && jamKerja.length > 0" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600">
                <Clock class="size-5" />
            </div>
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 sm:text-3xl dark:text-white">Jam Kerja</h2>
                <p class="mt-1 text-zinc-500 dark:text-zinc-400">Jadwal operasional kantor desa</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7">
            <div
                v-for="jk in jamKerja"
                :key="jk.hari"
                class="flex flex-col items-center rounded-xl border p-4 text-center transition-all hover:shadow-sm"
                :class="jk.is_libur
                    ? 'border-rose-200 bg-rose-50 dark:border-rose-900/50 dark:bg-rose-950/30'
                    : 'border-zinc-200/60 bg-white dark:border-zinc-700/60 dark:bg-zinc-900'"
            >
                <span
                    class="text-sm font-semibold"
                    :class="jk.is_libur ? 'text-rose-500 dark:text-rose-400' : 'text-zinc-800 dark:text-zinc-200'"
                >
                    {{ jk.hari }}
                </span>
                <template v-if="jk.is_libur">
                    <span class="mt-2 rounded-full bg-rose-100 px-3 py-0.5 text-xs font-medium text-rose-600 dark:bg-rose-900/40 dark:text-rose-400">Libur</span>
                </template>
                <template v-else>
                    <span class="mt-2 text-xs text-zinc-400 dark:text-zinc-500">Buka</span>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ jk.jam_buka?.slice(0, 5) ?? '—' }}</span>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">Tutup</span>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ jk.jam_tutup?.slice(0, 5) ?? '—' }}</span>
                </template>
            </div>
        </div>
    </section>

    <!-- ==================== Section Divider ==================== -->
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <hr class="border-zinc-200 dark:border-zinc-800" />
    </div>

    <!-- ==================== Latest News ==================== -->
    <section class="mx-auto max-w-7xl px-4 py-16 md:py-20 sm:px-6 lg:px-8 scroll-mt-20">
        <div class="mb-10 flex items-end justify-between">
            <div>
                <div class="flex items-center gap-3">
                    <Newspaper class="size-6 text-emerald-600 dark:text-emerald-400" />
                    <h2 class="text-2xl font-bold text-zinc-900 sm:text-3xl dark:text-white">Berita Terbaru</h2>
                </div>
                <p class="mt-1.5 text-zinc-500 dark:text-zinc-400">Informasi terkini seputar desa</p>
            </div>
            <Link
                    v-if="latestPosts.length > 0"
                    href="/berita"
                    class="hidden items-center gap-1 text-sm font-medium text-emerald-600 transition-colors hover:text-emerald-700 sm:inline-flex dark:text-emerald-400 dark:hover:text-emerald-300"
                >
                Lihat Semua
                <ArrowRight class="size-4" />
            </Link>
        </div>

        <div
            v-if="latestPosts.length === 0"
            class="flex min-h-[200px] items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-12 dark:border-zinc-700 dark:bg-zinc-900"
        >
            <div class="text-center">
                <Newspaper class="mx-auto size-10 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-3 font-medium text-zinc-500 dark:text-zinc-400">Belum ada berita</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Berita akan muncul di sini setelah dipublikasikan.</p>
            </div>
        </div>

        <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <Card
                v-for="post in latestPosts"
                :key="post.id"
                class="overflow-hidden border-zinc-200/60 py-0 transition-all duration-200 hover:shadow-lg dark:border-zinc-700/60"
            >
                <Link :href="`/berita/${post.slug}`" class="group block">
                    <div class="aspect-[16/9] overflow-hidden bg-zinc-100 dark:bg-zinc-700">
                        <img
                            v-if="showImage(post.gambar)"
                            :src="post.gambar"
                            :alt="post.judul"
                            class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            @error="handleImgError(post.gambar!)"
                        />
                        <div v-if="!showImage(post.gambar)" class="flex h-full items-center justify-center text-zinc-300 dark:text-zinc-600">
                            <ImageIcon class="size-10" />
                        </div>
                    </div>
                </Link>
                <CardContent class="p-4">
                    <Badge v-if="post.category" variant="secondary" class="mb-2">{{ post.category.nama }}</Badge>
                    <Link :href="`/berita/${post.slug}`">
                        <h3 class="line-clamp-2 font-semibold text-zinc-900 transition-colors group-hover:text-emerald-600 dark:text-white dark:group-hover:text-emerald-400">
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

        <!-- Mobile "Lihat Semua" -->
        <div v-if="latestPosts.length > 0" class="mt-8 text-center sm:hidden">
            <Link
                href="/berita"
                class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400"
            >
                Lihat Semua Berita
                <ArrowRight class="size-4" />
            </Link>
        </div>
    </section>

    <!-- ==================== Upcoming Events ==================== -->
    <section class="border-t-2 border-emerald-100 bg-zinc-50 py-16 md:py-20 scroll-mt-20 dark:border-emerald-900 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-end justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <Calendar class="size-6 text-emerald-600 dark:text-emerald-400" />
                        <h2 class="text-2xl font-bold text-zinc-900 sm:text-3xl dark:text-white">Agenda Mendatang</h2>
                    </div>
                    <p class="mt-1.5 text-zinc-500 dark:text-zinc-400">Kegiatan dan acara desa</p>
                </div>
                <Link
                    v-if="upcomingEvents.length > 0"
                    href="/agenda"
                    class="hidden items-center gap-1 text-sm font-medium text-emerald-600 transition-colors hover:text-emerald-700 sm:inline-flex dark:text-emerald-400 dark:hover:text-emerald-300"
                >
                    Lihat Semua
                    <ArrowRight class="size-4" />
                </Link>
            </div>

            <div
                v-if="upcomingEvents.length === 0"
                class="flex min-h-[200px] items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-white p-12 dark:border-zinc-700 dark:bg-zinc-900"
            >
                <div class="text-center">
                    <Calendar class="mx-auto size-10 text-zinc-300 dark:text-zinc-600" />
                    <p class="mt-3 font-medium text-zinc-500 dark:text-zinc-400">Belum ada agenda mendatang</p>
                    <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Agenda akan muncul di sini setelah dijadwalkan.</p>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="event in upcomingEvents"
                    :key="event.id"
                    class="border-zinc-200/60 bg-white transition-all duration-200 hover:shadow-lg dark:border-zinc-700/60 dark:bg-zinc-900"
                >
                    <CardHeader class="pb-3">
                        <div class="mb-2 inline-flex items-center gap-1.5 rounded-md border-l-2 border-emerald-500 bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                            <Calendar class="size-3" />
                            {{ formatDate(event.tanggal_mulai) }}
                        </div>
                        <CardTitle class="text-lg">
                            <Link
                                :href="`/agenda/${event.id}`"
                                class="text-zinc-900 transition-colors hover:text-emerald-600 dark:text-white dark:hover:text-emerald-400"
                            >
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

            <!-- Mobile "Lihat Semua" -->
            <div v-if="upcomingEvents.length > 0" class="mt-8 text-center sm:hidden">
                <Link
                    href="/agenda"
                    class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400"
                >
                    Lihat Semua Agenda
                    <ArrowRight class="size-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- ==================== Population Statistics ==================== -->
    <section class="mx-auto max-w-7xl px-4 py-16 md:py-20 sm:px-6 lg:px-8 scroll-mt-20">
        <div class="mb-10 flex items-center gap-3">
            <Users class="size-6 text-emerald-600 dark:text-emerald-400" />
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 sm:text-3xl dark:text-white">Statistik Penduduk</h2>
                <p class="mt-1 text-zinc-500 dark:text-zinc-400">Data kependudukan Desa Digital</p>
            </div>
        </div>

        <!-- Summary cards -->
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardContent class="p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.totalResidents ?? 0 }}</p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Total Jiwa</p>
                </CardContent>
            </Card>
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardContent class="p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.byGender?.L ?? 0 }}</p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Laki-laki</p>
                </CardContent>
            </Card>
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardContent class="p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.byGender?.P ?? 0 }}</p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Perempuan</p>
                </CardContent>
            </Card>
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardContent class="p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ Math.round((stats.totalResidents ?? 0) / 3.5) }}</p>
                    <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">Kepala Keluarga</p>
                </CardContent>
            </Card>
        </div>

        <!-- Charts grid -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader>
                    <CardTitle class="text-base">Komposisi Gender</CardTitle>
                </CardHeader>
                <CardContent v-if="(stats.byGender?.L ?? 0) + (stats.byGender?.P ?? 0) > 0" class="flex justify-center">
                    <div class="w-full max-w-[240px]">
                        <Doughnut :data="genderChartData" :options="genderChartOptions" />
                    </div>
                </CardContent>
                <CardContent v-else class="py-8 text-center text-sm text-zinc-400 dark:text-zinc-500">
                    Belum ada data penduduk.
                </CardContent>
            </Card>
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader>
                    <CardTitle class="text-base">Kelompok Usia</CardTitle>
                </CardHeader>
                <CardContent v-if="ageLabels.some(l => (props.stats.byAgeGroup?.[l] ?? 0) > 0)">
                    <Bar :data="ageChartData" :options="ageChartOptions" />
                </CardContent>
                <CardContent v-else class="py-8 text-center text-sm text-zinc-400 dark:text-zinc-500">
                    Belum ada data penduduk.
                </CardContent>
            </Card>
        </div>

        <div class="mt-6 text-center">
            <Link href="/statistik" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                Lihat Statistik Lengkap
                <ArrowRight class="size-4" />
            </Link>
        </div>
    </section>

    <!-- ==================== APBDes ==================== -->
    <section class="border-t-2 border-emerald-100 bg-zinc-50 py-16 md:py-20 scroll-mt-20 dark:border-emerald-900 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-10 flex items-center gap-3">
                <Landmark class="size-6 text-emerald-600 dark:text-emerald-400" />
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 sm:text-3xl dark:text-white">APBDes {{ budgetSummary?.tahun ?? '' }}</h2>
                    <p class="mt-1 text-zinc-500 dark:text-zinc-400">Anggaran Pendapatan dan Belanja Desa</p>
                </div>
            </div>

            <div v-if="budgetSummary?.tahun" class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Summary cards -->
                <div class="space-y-4">
                    <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="flex items-center justify-between p-5">
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pendapatan</p>
                                <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(budgetSummary.pendapatan.anggaran) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Realisasi</p>
                                <p class="text-lg font-semibold text-zinc-900 dark:text-white">{{ formatCurrency(budgetSummary.pendapatan.realisasi) }}</p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="flex items-center justify-between p-5">
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Belanja</p>
                                <p class="text-xl font-bold text-rose-600 dark:text-rose-400">{{ formatCurrency(budgetSummary.belanja.anggaran) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Realisasi</p>
                                <p class="text-lg font-semibold text-zinc-900 dark:text-white">{{ formatCurrency(budgetSummary.belanja.realisasi) }}</p>
                            </div>
                        </CardContent>
                    </Card>
                    <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                        <CardContent class="flex items-center justify-between p-5">
                            <div>
                                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pembiayaan</p>
                                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ formatCurrency(budgetSummary.pembiayaan.anggaran) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-zinc-400 dark:text-zinc-500">Realisasi</p>
                                <p class="text-lg font-semibold text-zinc-900 dark:text-white">{{ formatCurrency(budgetSummary.pembiayaan.realisasi) }}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                <!-- Chart -->
                <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                    <CardHeader>
                        <CardTitle class="text-base">Anggaran vs Realisasi</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Bar :data="budgetChartData" :options="budgetChartOptions" />
                    </CardContent>
                </Card>
            </div>

            <div v-else class="flex min-h-[160px] items-center justify-center rounded-xl border border-dashed border-zinc-300 bg-white p-12 dark:border-zinc-700 dark:bg-zinc-900">
                <div class="text-center">
                    <Landmark class="mx-auto size-10 text-zinc-300 dark:text-zinc-600" />
                    <p class="mt-3 font-medium text-zinc-500 dark:text-zinc-400">Belum ada data APBDes</p>
                    <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data akan muncul setelah diinput oleh admin.</p>
                </div>
            </div>

            <div v-if="budgetSummary?.tahun" class="mt-6 text-center">
                <Link href="/apbdes" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                    Lihat Detail APBDes
                    <ArrowRight class="size-4" />
                </Link>
            </div>
        </div>
    </section>

    <!-- ==================== CTA Section ==================== -->
    <section class="relative bg-gradient-to-br from-emerald-700 to-teal-700 py-16 md:py-20">
        <!-- Subtle pattern overlay -->
        <div class="pointer-events-none absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 20px 20px;" />
        <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6">
            <h2 class="text-balance text-2xl font-bold text-white sm:text-3xl">Butuh Bantuan?</h2>
            <p class="mt-3 text-base text-emerald-100 sm:text-lg">
                Hubungi kami melalui kontak yang tersedia atau kunjungi kantor desa
            </p>
            <div class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <Link
                    href="/kontak"
                    class="inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-emerald-700 shadow-sm transition-colors hover:bg-emerald-50"
                >
                    Hubungi Kami
                    <ArrowRight class="size-4" />
                </Link>
                <Link
                    href="/profil"
                    class="inline-flex items-center gap-2 rounded-lg border border-white/30 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/10"
                >
                    Profil Desa
                </Link>
            </div>
        </div>
    </section>
</template>
