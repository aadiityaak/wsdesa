<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

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
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

defineProps<{
    users: Paginated;
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
    });
};
</script>

<template>
    <Head title="Pengguna" />

    <div class="space-y-6">
        <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Pengguna</h1>

        <Card>
            <CardHeader class="pb-3">
                <CardTitle>Daftar Pengguna</CardTitle>
            </CardHeader>
            <CardContent class="p-0">
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
                            <tr
                                v-for="(user, index) in users.data"
                                :key="user.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ (users.current_page - 1) * users.data.length + index + 1 }}</td>
                                <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ user.email }}</td>
                                <td class="px-4 py-3">
                                    <Badge variant="outline">{{ user.role || 'User' }}</Badge>
                                </td>
                                <td class="px-4 py-3 text-zinc-500">{{ formatDate(user.created_at) }}</td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada pengguna.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="users.last_page > 1" class="flex items-center justify-between border-t px-4 py-3">
                    <span class="text-sm text-zinc-500">
                        Halaman {{ users.current_page }} dari {{ users.last_page }}
                    </span>
                    <div class="flex gap-1">
                        <a
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            class="rounded-md px-3 py-1 text-sm transition-colors"
                            :class="link.active
                                ? 'bg-primary text-primary-foreground'
                                : !link.url
                                    ? 'text-zinc-300 pointer-events-none'
                                    : 'text-zinc-600 hover:bg-zinc-100 dark:text-zinc-400 dark:hover:bg-zinc-800'"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
