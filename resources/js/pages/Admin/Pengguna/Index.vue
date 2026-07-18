<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { UsersRound, ArrowUp } from '@lucide/vue';

interface User {
    id: number;
    name: string;
    email: string;
    role: string | null;
    created_at: string;
}

interface Paginated {
    data: User[];
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

const props = defineProps<{
    users: Paginated;
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
};

const currentPage = computed(() => props.users.current_page);
const lastPage = computed(() => props.users.last_page);

const pageNumbers = computed(() => {
    const pages: (number | string)[] = [];
    const current = currentPage.value;
    const last = lastPage.value;
    if (last <= 7) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current > 3) pages.push('...');
        const start = Math.max(2, current - 1);
        const end = Math.min(last - 1, current + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        if (current < last - 2) pages.push('...');
        pages.push(last);
    }
    return pages;
});

const goToPage = (page: number) => {
    router.get('/admin/pengguna', { page }, { preserveState: true });
};

const stats = computed(() => ({
    total: props.users.total,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Pengguna" />

    <div class="relative">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            leave-active-class="transition duration-150 ease-in"
            leave-to-class="translate-y-2 opacity-0"
        >
            <button
                v-if="showScrollTop"
                type="button"
                class="fixed bottom-8 right-8 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition hover:scale-105 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
                @click="scrollToTop"
            >
                <ArrowUp class="h-5 w-5" />
            </button>
        </Transition>

        <div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
            <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
            <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

            <div class="relative">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                        <UsersRound class="h-6 w-6" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Pengguna</h1>
                        <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola akun pengguna website desa</p>
                    </div>
                </div>
            </div>

            <div class="relative mt-6">
                <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-5 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <UsersRound class="h-5 w-5 text-zinc-400" />
                    <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</span>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Pengguna</span>
                </div>
            </div>
        </div>

        <div v-if="users.data.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <UsersRound class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada pengguna</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Data pengguna akan muncul di sini.</p>
        </div>

        <div v-else class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Email</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Role</th>
                            <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Tanggal Daftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, index) in users.data" :key="user.id" class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3 text-zinc-500">{{ (users.from ?? 1) + index }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ user.name }}</td>
                            <td class="px-4 py-3 text-zinc-500">{{ user.email }}</td>
                            <td class="px-4 py-3">
                                <Badge variant="outline" class="rounded-full">{{ user.role || 'User' }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-zinc-500">{{ formatDate(user.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-if="lastPage > 1" class="flex flex-col items-center justify-between gap-3 border-t px-5 py-4 sm:flex-row">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Menampilkan {{ users.from }}–{{ users.to }} dari {{ users.total }}
                </p>
                <div class="flex items-center gap-1">
                    <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">Sebelumnya</Button>
                    <template v-for="page in pageNumbers" :key="page">
                        <span v-if="page === '...'" class="px-2 text-zinc-400">...</span>
                        <Button v-else size="sm" class="rounded-lg" :variant="currentPage === page ? 'default' : 'outline'" @click="goToPage(page as number)">{{ page }}</Button>
                    </template>
                    <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)">Selanjutnya</Button>
                </div>
            </div>
        </div>
    </div>
</template>
