 <script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Doughnut, Bar } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement } from 'chart.js';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Users, Home, ArrowRight, User, Briefcase, BookOpen, Heart, MapPin, Church } from '@lucide/vue';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement);

const props = defineProps<{
    totalResidents: number;
    totalKK: number;
    byGender: Record<string, number>;
    byAgeGroup: Record<string, number>;
    byPendidikan: Record<string, number>;
    byPekerjaan: Record<string, number>;
    byAgama: Record<string, number>;
    byPerkawinan: Record<string, number>;
    byDusun: Record<string, number>;
}>();

const genderChart = computed(() => ({
    labels: ['Laki-laki', 'Perempuan'],
    datasets: [{
        data: [props.byGender?.L ?? 0, props.byGender?.P ?? 0],
        backgroundColor: ['#10b981', '#f472b6'],
        borderWidth: 0,
    }],
}));

const genderOpts = { cutout: '60%', plugins: { legend: { position: 'bottom' as const, labels: { usePointStyle: true, padding: 16 } } }, maintainAspectRatio: true };

const ageLabels = Object.keys(props.byAgeGroup);
const ageChart = computed(() => ({
    labels: ageLabels,
    datasets: [{
        label: 'Jumlah',
        data: ageLabels.map(k => props.byAgeGroup[k] ?? 0),
        backgroundColor: '#10b981',
        borderRadius: 4,
    }],
}));

const ageOpts = {
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { stepSize: 1, font: { size: 10 } } },
    },
    maintainAspectRatio: true,
};

const pendidikanLabels = Object.keys(props.byPendidikan);
const pendidikanChart = computed(() => ({
    labels: pendidikanLabels,
    datasets: [{
        label: 'Jumlah',
        data: pendidikanLabels.map(k => props.byPendidikan[k] ?? 0),
        backgroundColor: '#6366f1',
        borderRadius: 4,
    }],
}));

const pendidikanOpts = {
    indexAxis: 'y' as const,
    plugins: { legend: { display: false } },
    scales: {
        x: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { stepSize: 1, font: { size: 10 } } },
        y: { grid: { display: false }, ticks: { font: { size: 10 } } },
    },
    maintainAspectRatio: true,
};

const agamaData = computed(() => ({
    labels: Object.keys(props.byAgama),
    datasets: [{
        data: Object.values(props.byAgama),
        backgroundColor: ['#10b981', '#6366f1', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
        borderWidth: 0,
    }],
}));

const agamaOpts = { plugins: { legend: { position: 'right' as const, labels: { usePointStyle: true, padding: 12, font: { size: 11 } } } }, maintainAspectRatio: true };

const perkawinanData = computed(() => ({
    labels: Object.keys(props.byPerkawinan),
    datasets: [{
        data: Object.values(props.byPerkawinan),
        backgroundColor: ['#10b981', '#f59e0b', '#6366f1', '#ef4444'],
        borderWidth: 0,
    }],
}));

const perkawinanOpts = { plugins: { legend: { position: 'right' as const, labels: { usePointStyle: true, padding: 12, font: { size: 11 } } } }, maintainAspectRatio: true };
</script>

<template>
    <Head title="Infografis Penduduk" />

    <!-- Hero Stats -->
    <section class="bg-gradient-to-br from-emerald-600 to-emerald-800 py-12 md:py-16">
        <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold text-white sm:text-3xl">Demografi Penduduk</h1>
            <p class="mt-2 text-emerald-100">Data kependudukan Desa Digital</p>

            <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur-sm">
                    <Users class="mx-auto size-6 text-emerald-200" />
                    <p class="mt-2 text-3xl font-bold text-white">{{ totalResidents }}</p>
                    <p class="text-xs text-emerald-200">Total Penduduk</p>
                </div>
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur-sm">
                    <Home class="mx-auto size-6 text-emerald-200" />
                    <p class="mt-2 text-3xl font-bold text-white">{{ totalKK }}</p>
                    <p class="text-xs text-emerald-200">Kepala Keluarga</p>
                </div>
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur-sm">
                    <User class="mx-auto size-6 text-emerald-200" />
                    <p class="mt-2 text-3xl font-bold text-white">{{ byGender?.L ?? 0 }}</p>
                    <p class="text-xs text-emerald-200">Laki-Laki</p>
                </div>
                <div class="rounded-xl bg-white/10 p-5 backdrop-blur-sm">
                    <User class="mx-auto size-6 text-emerald-200" />
                    <p class="mt-2 text-3xl font-bold text-white">{{ byGender?.P ?? 0 }}</p>
                    <p class="text-xs text-emerald-200">Perempuan</p>
                </div>
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl space-y-10 px-4 py-12 sm:px-6 lg:px-8 md:py-16">
        <!-- Gender + Age -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader><CardTitle class="text-base flex items-center gap-2"><User class="size-4 text-emerald-600" /> Komposisi Gender</CardTitle></CardHeader>
                <CardContent v-if="(byGender?.L ?? 0) + (byGender?.P ?? 0) > 0" class="flex justify-center">
                    <div class="w-full max-w-[260px]"><Doughnut :data="genderChart" :options="genderOpts" /></div>
                </CardContent>
                <CardContent v-else class="py-8 text-center text-sm text-zinc-400">Belum ada data.</CardContent>
            </Card>
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader><CardTitle class="text-base flex items-center gap-2"><Users class="size-4 text-emerald-600" /> Kelompok Usia</CardTitle></CardHeader>
                <CardContent v-if="ageLabels.some(k => (props.byAgeGroup[k] ?? 0) > 0)">
                    <Bar :data="ageChart" :options="ageOpts" />
                </CardContent>
                <CardContent v-else class="py-8 text-center text-sm text-zinc-400">Belum ada data.</CardContent>
            </Card>
        </div>

        <!-- Pendidikan + Dusun -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader><CardTitle class="text-base flex items-center gap-2"><BookOpen class="size-4 text-indigo-600" /> Pendidikan</CardTitle></CardHeader>
                <CardContent v-if="pendidikanLabels.length > 0">
                    <Bar :data="pendidikanChart" :options="pendidikanOpts" />
                </CardContent>
                <CardContent v-else class="py-8 text-center text-sm text-zinc-400">Belum ada data.</CardContent>
            </Card>
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader><CardTitle class="text-base flex items-center gap-2"><MapPin class="size-4 text-emerald-600" /> Berdasarkan Dusun</CardTitle></CardHeader>
                <CardContent>
                    <div v-if="Object.keys(byDusun).length > 0" class="space-y-3">
                        <div v-for="(total, dusun) in byDusun" :key="dusun" class="flex items-center justify-between">
                            <span class="text-sm text-zinc-700 dark:text-zinc-300">{{ dusun }}</span>
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-32 overflow-hidden rounded-full bg-zinc-100 sm:w-48 dark:bg-zinc-800">
                                    <div class="h-full rounded-full bg-emerald-500" :style="{ width: (total / Math.max(...Object.values(byDusun)) * 100) + '%' }" />
                                </div>
                                <span class="w-10 text-right text-sm font-medium text-zinc-900 dark:text-white">{{ total }}</span>
                            </div>
                        </div>
                    </div>
                    <p v-else class="py-4 text-center text-sm text-zinc-400">Belum ada data.</p>
                </CardContent>
            </Card>
        </div>

        <!-- Agama + Perkawinan -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader><CardTitle class="text-base flex items-center gap-2"><Church class="size-4 text-amber-600" /> Agama</CardTitle></CardHeader>
                <CardContent v-if="Object.keys(byAgama).length > 0" class="flex justify-center">
                    <div class="w-full max-w-[320px]"><Doughnut :data="agamaData" :options="agamaOpts" /></div>
                </CardContent>
                <CardContent v-else class="py-8 text-center text-sm text-zinc-400">Belum ada data.</CardContent>
            </Card>
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader><CardTitle class="text-base flex items-center gap-2"><Heart class="size-4 text-rose-600" /> Status Perkawinan</CardTitle></CardHeader>
                <CardContent v-if="Object.keys(byPerkawinan).length > 0" class="flex justify-center">
                    <div class="w-full max-w-[320px]"><Doughnut :data="perkawinanData" :options="perkawinanOpts" /></div>
                </CardContent>
                <CardContent v-else class="py-8 text-center text-sm text-zinc-400">Belum ada data.</CardContent>
            </Card>
        </div>

        <!-- Pekerjaan -->
        <Card class="border-zinc-200/60 dark:border-zinc-700/60">
            <CardHeader><CardTitle class="text-base flex items-center gap-2"><Briefcase class="size-4 text-emerald-600" /> Pekerjaan</CardTitle></CardHeader>
            <CardContent>
                <div v-if="Object.keys(byPekerjaan).length > 0" class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                                <th class="pb-2 pr-4 font-medium">Jenis Pekerjaan</th>
                                <th class="pb-2 text-right font-medium">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(total, pekerjaan) in byPekerjaan" :key="pekerjaan" class="border-b border-zinc-100 dark:border-zinc-800">
                                <td class="py-2 pr-4 text-zinc-700 dark:text-zinc-300">{{ pekerjaan }}</td>
                                <td class="py-2 text-right font-medium text-zinc-900 dark:text-white">{{ total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="py-4 text-center text-sm text-zinc-400">Belum ada data.</p>
            </CardContent>
        </Card>

        <!-- Link ke Statistik -->
        <div class="text-center">
            <Link href="/statistik" class="inline-flex items-center gap-1 text-sm font-medium text-emerald-600 hover:text-emerald-700 dark:text-emerald-400">
                Lihat Statistik Lengkap <ArrowRight class="size-4" />
            </Link>
        </div>
    </div>
</template>
