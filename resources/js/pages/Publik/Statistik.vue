<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Users, UserCheck, UserRound, Home, Heart, MapPin } from '@lucide/vue';

defineProps<{
    totalResidents: number;
    totalKK: number;
    byGender: { laki: number; perempuan: number };
    byAgeGroup: { nama: string; total: number }[];
    byAgama: { nama: string; total: number }[];
    byPekerjaan: { nama: string; total: number }[];
    byPendidikan: { nama: string; total: number }[];
    byPerkawinan: { nama: string; total: number }[];
    byDusun: { nama: string; total: number }[];
}>();

const maxValue = (arr: { total: number }[]) => Math.max(...arr.map((i) => i.total), 1);
</script>

<template>
    <Head title="Statistik Penduduk" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-8 text-3xl font-bold text-zinc-900 dark:text-white">Statistik Penduduk</h1>

        <!-- Stat Cards -->
        <div class="mb-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
            <Card>
                <CardContent class="flex items-center gap-4 p-5">
                    <div class="flex size-12 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                        <Users class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Total Penduduk</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ totalResidents.toLocaleString('id-ID') }}</p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-4 p-5">
                    <div class="flex size-12 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                        <UserCheck class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Laki-laki</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ byGender.laki.toLocaleString('id-ID') }}</p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-4 p-5">
                    <div class="flex size-12 items-center justify-center rounded-full bg-pink-100 dark:bg-pink-900">
                        <UserRound class="size-6 text-pink-600 dark:text-pink-400" />
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Perempuan</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ byGender.perempuan.toLocaleString('id-ID') }}</p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-4 p-5">
                    <div class="flex size-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900">
                        <Home class="size-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-xs text-zinc-500">Kepala Keluarga</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ totalKK.toLocaleString('id-ID') }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Gender -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Jenis Kelamin</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Laki-laki</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ byGender.laki.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-green-500" :style="{ width: `${(byGender.laki / Math.max(byGender.laki + byGender.perempuan, 1)) * 100}%` }"></div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Perempuan</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ byGender.perempuan.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-pink-500" :style="{ width: `${(byGender.perempuan / Math.max(byGender.laki + byGender.perempuan, 1)) * 100}%` }"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Age -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Kelompok Umur</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="item in byAgeGroup" :key="item.nama">
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">{{ item.nama }}</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ item.total.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-4 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-blue-500" :style="{ width: `${(item.total / maxValue(byAgeGroup)) * 100}%` }"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Agama -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Agama</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="item in byAgama" :key="item.nama">
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">{{ item.nama }}</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ item.total.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-4 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-purple-500" :style="{ width: `${(item.total / maxValue(byAgama)) * 100}%` }"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Perkawinan -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-lg"><Heart class="size-4 text-rose-500" /> Status Perkawinan</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="item in byPerkawinan" :key="item.nama">
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">{{ item.nama }}</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ item.total.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-4 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-rose-500" :style="{ width: `${(item.total / maxValue(byPerkawinan)) * 100}%` }"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Pekerjaan -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Pekerjaan</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="item in byPekerjaan" :key="item.nama">
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="truncate text-zinc-600 dark:text-zinc-400">{{ item.nama }}</span>
                            <span class="ml-2 shrink-0 font-medium text-zinc-900 dark:text-white">{{ item.total.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-4 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-amber-500" :style="{ width: `${(item.total / maxValue(byPekerjaan)) * 100}%` }"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Pendidikan -->
            <Card>
                <CardHeader>
                    <CardTitle class="text-lg">Pendidikan</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="item in byPendidikan" :key="item.nama">
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">{{ item.nama }}</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ item.total.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-4 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-emerald-500" :style="{ width: `${(item.total / maxValue(byPendidikan)) * 100}%` }"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Dusun -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-lg"><MapPin class="size-4 text-sky-500" /> Berdasarkan Dusun</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div v-for="item in byDusun" :key="item.nama">
                        <div class="mb-1 flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">{{ item.nama }}</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ item.total.toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="h-4 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-sky-500" :style="{ width: `${(item.total / maxValue(byDusun)) * 100}%` }"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
