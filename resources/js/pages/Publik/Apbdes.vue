<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Bar, Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement } from 'chart.js';
import { Landmark, TrendingUp, CheckCircle, PieChart } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement);

interface Budget {
    id: number;
    tahun: number;
    kategori: string;
    anggaran: number;
    realisasi: number;
    tipe?: string | null;
}

interface Tahunan {
    tahun: number;
    anggaran: number;
    realisasi: number;
}

const props = defineProps<{
    budgets: Budget[];
    tahunan: Tahunan[];
}>();

const selectedYear = ref<number | null>(null);
const years = computed(() => {
    const unique = [...new Set(props.budgets.map((b) => b.tahun))].sort((a, b) => b - a);
    if (!selectedYear.value && unique.length > 0) {
        selectedYear.value = unique[0];
    }
    return unique;
});

const filtered = computed(() => props.budgets.filter((b) => b.tahun === selectedYear.value));

const totalAnggaran = computed(() => filtered.value.reduce((sum, b) => sum + Number(b.anggaran), 0));
const totalRealisasi = computed(() => filtered.value.reduce((sum, b) => sum + Number(b.realisasi), 0));

const formatRupiah = (value: number) =>
    'Rp ' + Number(value).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

const formatRupiahPendek = (value: number) => {
    if (value >= 1e9) return 'Rp ' + (value / 1e9).toFixed(1) + ' M';
    if (value >= 1e6) return 'Rp ' + (value / 1e6).toFixed(0) + ' jt';
    return formatRupiah(value);
};

const percentage = (realisasi: number, anggaran: number) =>
    anggaran > 0 ? ((Number(realisasi) / Number(anggaran)) * 100).toFixed(1) : '0.0';

const persentaseTotal = computed(() => percentage(totalRealisasi.value, totalAnggaran.value));

// --- Chart: Anggaran vs Realisasi per Kategori (Bar) ---
const kategoriChartData = computed(() => ({
    labels: filtered.value.map((b) => b.kategori),
    datasets: [
        {
            label: 'Anggaran',
            data: filtered.value.map((b) => Number(b.anggaran)),
            backgroundColor: '#10b981',
            borderRadius: 6,
            borderSkipped: false,
        },
        {
            label: 'Realisasi',
            data: filtered.value.map((b) => Number(b.realisasi)),
            backgroundColor: '#6ee7b7',
            borderRadius: 6,
            borderSkipped: false,
        },
    ],
}));

const kategoriChartOptions = {
    responsive: true,
    plugins: {
        legend: { display: true, position: 'bottom' as const, labels: { padding: 16, usePointStyle: true } },
        tooltip: { callbacks: { label: (ctx: any) => formatRupiah(ctx.raw) } },
    },
    scales: {
        x: { grid: { display: false }, ticks: { maxRotation: 45, minRotation: 0, font: { size: 11 } } },
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { callback: (v: number) => formatRupiahPendek(v), font: { size: 11 } } },
    },
    maintainAspectRatio: true,
};

// --- Chart: Tren Tahunan (Bar) ---
const trenChartData = computed(() => ({
    labels: props.tahunan.map((t) => t.tahun.toString()),
    datasets: [
        {
            label: 'Anggaran',
            data: props.tahunan.map((t) => Number(t.anggaran)),
            backgroundColor: '#10b981',
            borderRadius: 6,
        },
        {
            label: 'Realisasi',
            data: props.tahunan.map((t) => Number(t.realisasi)),
            backgroundColor: '#6ee7b7',
            borderRadius: 6,
        },
    ],
}));

const trenChartOptions = {
    responsive: true,
    plugins: {
        legend: { display: true, position: 'bottom' as const, labels: { padding: 16, usePointStyle: true } },
        tooltip: { callbacks: { label: (ctx: any) => formatRupiah(ctx.raw) } },
    },
    scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' }, ticks: { callback: (v: number) => formatRupiahPendek(v), font: { size: 11 } } },
    },
    maintainAspectRatio: true,
};

// --- Chart: Komposisi per Tipe (Doughnut) ---
const tipeChartData = computed(() => {
    const tipeOrder = ['Pendapatan', 'Belanja', 'Pembiayaan'];
    const grouped: Record<string, number> = {};
    filtered.value.forEach((b) => {
        const key = b.tipe || 'Lainnya';
        grouped[key] = (grouped[key] || 0) + Number(b.anggaran);
    });
    const colors: Record<string, string> = { Pendapatan: '#10b981', Belanja: '#f43f5e', Pembiayaan: '#3b82f6', Lainnya: '#a1a1aa' };
    const labels = tipeOrder.filter((k) => grouped[k]);
    if (grouped['Lainnya']) labels.push('Lainnya');
    return {
        labels,
        datasets: [{
            data: labels.map((l) => grouped[l] || 0),
            backgroundColor: labels.map((l) => colors[l] || '#a1a1aa'),
            borderWidth: 0,
        }],
    };
});

const tipeChartOptions = {
    cutout: '65%',
    plugins: { legend: { display: true, position: 'bottom' as const, labels: { padding: 16, usePointStyle: true } }, tooltip: { callbacks: { label: (ctx: any) => formatRupiah(ctx.raw) } } },
    maintainAspectRatio: true,
};
</script>

<template>
    <Head title="APBDes" />

    <PageHero title="APB Desa" description="Anggaran Pendapatan dan Belanja Desa — transparansi keuangan desa">
        <template #icon>
            <Landmark class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- Year Tabs -->
        <div class="mb-8 flex flex-wrap items-center gap-2">
            <span class="mr-1 text-sm font-medium text-zinc-500 dark:text-zinc-400">Tahun:</span>
            <button
                v-for="year in years"
                :key="year"
                class="rounded-full px-4 py-2 text-sm font-medium transition-all duration-200"
                :class="selectedYear === year ? 'bg-emerald-600 text-white shadow-md shadow-emerald-200 dark:shadow-emerald-900/40' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700'"
                @click="selectedYear = year"
            >
                {{ year }}
            </button>
        </div>

        <!-- Empty state -->
        <Card v-if="filtered.length === 0" class="border-zinc-200/60 dark:border-zinc-700/60">
            <CardContent class="py-16 text-center">
                <Landmark class="mx-auto h-10 w-10 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-3 font-medium text-zinc-500 dark:text-zinc-400">Belum ada data APBDes untuk tahun ini</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data akan muncul setelah diinput oleh admin.</p>
            </CardContent>
        </Card>

        <template v-else>
            <!-- Summary cards -->
            <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <Card class="border-zinc-200/60 bg-gradient-to-br from-emerald-50 to-white dark:border-zinc-700/60 dark:from-emerald-950/30 dark:to-zinc-900">
                    <CardContent class="flex items-center gap-4 p-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400">
                            <TrendingUp class="h-5 w-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Anggaran {{ selectedYear }}</p>
                            <p class="truncate text-lg font-bold text-zinc-900 dark:text-white">{{ formatRupiah(totalAnggaran) }}</p>
                        </div>
                    </CardContent>
                </Card>
                <Card class="border-zinc-200/60 bg-gradient-to-br from-blue-50 to-white dark:border-zinc-700/60 dark:from-blue-950/30 dark:to-zinc-900">
                    <CardContent class="flex items-center gap-4 p-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400">
                            <CheckCircle class="h-5 w-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Realisasi {{ selectedYear }}</p>
                            <p class="truncate text-lg font-bold text-zinc-900 dark:text-white">{{ formatRupiah(totalRealisasi) }}</p>
                        </div>
                    </CardContent>
                </Card>
                <Card class="border-zinc-200/60 bg-gradient-to-br from-purple-50 to-white dark:border-zinc-700/60 dark:from-purple-950/30 dark:to-zinc-900">
                    <CardContent class="flex items-center gap-4 p-5">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-100 text-purple-600 dark:bg-purple-900/40 dark:text-purple-400">
                            <PieChart class="h-5 w-5" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Persentase Realisasi</p>
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">{{ persentaseTotal }}%</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Charts row -->
            <div class="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Bar: Anggaran vs Realisasi per Kategori -->
                <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                    <CardHeader>
                        <CardTitle class="text-base">Anggaran vs Realisasi per Kategori</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Bar :data="kategoriChartData" :options="kategoriChartOptions" />
                    </CardContent>
                </Card>

                <!-- Doughnut: Komposisi per Tipe -->
                <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                    <CardHeader>
                        <CardTitle class="text-base">Komposisi Berdasarkan Tipe</CardTitle>
                    </CardHeader>
                    <CardContent class="flex justify-center">
                        <div class="w-full max-w-[280px]">
                            <Doughnut :data="tipeChartData" :options="tipeChartOptions" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Tren Tahunan (only if >1 year) -->
            <Card v-if="tahunan.length > 1" class="mb-8 border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader>
                    <CardTitle class="text-base">Tren APBDes Tahunan</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="mx-auto max-w-lg">
                        <Bar :data="trenChartData" :options="trenChartOptions" />
                    </div>
                </CardContent>
            </Card>

            <!-- Table -->
            <Card class="border-zinc-200/60 dark:border-zinc-700/60">
                <CardHeader>
                    <CardTitle class="text-base">Rincian Anggaran {{ selectedYear }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                    <th class="py-3 text-left font-semibold text-zinc-900 dark:text-white">Kategori</th>
                                    <th class="py-3 text-right font-semibold text-zinc-900 dark:text-white">Anggaran</th>
                                    <th class="py-3 text-right font-semibold text-zinc-900 dark:text-white">Realisasi</th>
                                    <th class="py-3 text-right font-semibold text-zinc-900 dark:text-white">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="budget in filtered"
                                    :key="budget.id"
                                    class="border-b border-zinc-100 dark:border-zinc-800"
                                >
                                    <td class="py-3 text-zinc-600 dark:text-zinc-400">{{ budget.kategori }}</td>
                                    <td class="py-3 text-right font-mono text-zinc-900 dark:text-white">{{ formatRupiah(budget.anggaran) }}</td>
                                    <td class="py-3 text-right font-mono text-zinc-900 dark:text-white">{{ formatRupiah(budget.realisasi) }}</td>
                                    <td class="py-3 text-right">
                                        <span
                                            class="inline-block rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="Number(percentage(budget.realisasi, budget.anggaran)) >= 100 ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300'"
                                        >
                                            {{ percentage(budget.realisasi, budget.anggaran) }}%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-zinc-200 dark:border-zinc-700 font-semibold">
                                    <td class="py-3 text-zinc-900 dark:text-white">Total</td>
                                    <td class="py-3 text-right font-mono text-zinc-900 dark:text-white">{{ formatRupiah(totalAnggaran) }}</td>
                                    <td class="py-3 text-right font-mono text-zinc-900 dark:text-white">{{ formatRupiah(totalRealisasi) }}</td>
                                    <td class="py-3 text-right text-zinc-900 dark:text-white">{{ percentage(totalRealisasi, totalAnggaran) }}%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </template>
    </div>
</template>
