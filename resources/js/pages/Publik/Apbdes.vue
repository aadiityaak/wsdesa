<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

interface Budget {
    id: number;
    tahun: number;
    kategori: string;
    anggaran: number;
    realisasi: number;
}

const props = defineProps<{
    budgets: Budget[];
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

const percentage = (realisasi: number, anggaran: number) =>
    anggaran > 0 ? ((Number(realisasi) / Number(anggaran)) * 100).toFixed(1) : '0.0';
</script>

<template>
    <Head title="APBDes" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-zinc-900 dark:text-white">APBDes</h1>

        <!-- Year Tabs -->
        <div class="mb-6 flex flex-wrap gap-2">
            <button
                v-for="year in years"
                :key="year"
                class="rounded-full px-4 py-2 text-sm font-medium transition-colors"
                :class="selectedYear === year ? 'bg-blue-600 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700'"
                @click="selectedYear = year"
            >
                {{ year }}
            </button>
        </div>

        <Card v-if="filtered.length === 0">
            <CardContent class="py-12 text-center text-zinc-500 dark:text-zinc-400">
                Belum ada data APBDes untuk tahun ini.
            </CardContent>
        </Card>

        <Card v-else>
            <CardHeader>
                <CardTitle>Anggaran Tahun {{ selectedYear }}</CardTitle>
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
    </div>
</template>
