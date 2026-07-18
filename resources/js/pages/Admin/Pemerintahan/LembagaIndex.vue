<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Plus, Pencil, Trash2, Building2, ArrowUp, Users, ChevronLeft } from '@lucide/vue';

interface Member {
    id: number;
    nama: string;
    jabatan: string;
    foto: string | null;
    telepon: string;
    urutan: number;
}

interface Institution {
    id: number;
    nama: string;
    singkatan: string;
    deskripsi: string;
    logo: string | null;
    members: Member[];
}

const props = defineProps<{
    institutions: Institution[];
}>();

// Delete state
const deleteConfirmId = ref<number | null>(null);
const deleteForm = useForm({});

// Member view state
const selectedInst = ref<Institution | null>(null);

// Member dialog state
const memberDialogOpen = ref(false);
const editingMember = ref<Member | null>(null);
const deleteMemberConfirmId = ref<number | null>(null);

const memberForm = useForm({
    nama: '',
    jabatan: '',
    foto: null as File | null,
    telepon: '',
    urutan: 0,
});

const memberDialogTitle = computed(() => (editingMember.value ? 'Edit Anggota' : 'Tambah Anggota'));

const confirmDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const executeDelete = () => {
    if (deleteConfirmId.value) {
        deleteForm.delete(`/admin/lembaga/${deleteConfirmId.value}`, {
            onSuccess: () => {
                deleteConfirmId.value = null;
                toast.success('Lembaga berhasil dihapus.');
            },
        });
    }
};

const truncate = (text: string, max: number) => {
    if (!text) return '';
    return text.length > max ? text.slice(0, max) + '...' : text;
};

// Members
const viewMembers = (inst: Institution) => {
    selectedInst.value = inst;
};

const backToList = () => {
    selectedInst.value = null;
};

const openAddMember = () => {
    editingMember.value = null;
    memberForm.reset();
    memberForm.clearErrors();
    memberDialogOpen.value = true;
};

const openEditMember = (member: Member) => {
    editingMember.value = member;
    memberForm.nama = member.nama;
    memberForm.jabatan = member.jabatan;
    memberForm.foto = null;
    memberForm.telepon = member.telepon ?? '';
    memberForm.urutan = member.urutan ?? 0;
    memberForm.clearErrors();
    memberDialogOpen.value = true;
};

const submitMember = () => {
    const instId = selectedInst.value!.id;
    if (editingMember.value) {
        memberForm.put(`/admin/lembaga/${instId}/anggota/${editingMember.value.id}`, {
            onSuccess: () => {
                memberDialogOpen.value = false;
                toast.success('Data anggota berhasil diperbarui.');
            },
        });
    } else {
        memberForm.post(`/admin/lembaga/${instId}/anggota`, {
            onSuccess: () => {
                memberDialogOpen.value = false;
                toast.success('Anggota baru berhasil ditambahkan.');
            },
        });
    }
};

const confirmDeleteMember = (member: Member) => {
    deleteMemberConfirmId.value = member.id;
};

const executeDeleteMember = () => {
    if (deleteMemberConfirmId.value && selectedInst.value) {
        memberForm.delete(`/admin/lembaga/${selectedInst.value.id}/anggota/${deleteMemberConfirmId.value}`, {
            onSuccess: () => {
                deleteMemberConfirmId.value = null;
                toast.success('Data anggota berhasil dihapus.');
            },
        });
    }
};

const stats = computed(() => ({
    total: props.institutions.length,
}));

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Lembaga Desa" />

    <!-- Member View -->
    <template v-if="selectedInst">
        <div class="space-y-6">
            <button
                class="inline-flex items-center gap-1 text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                @click="backToList"
            >
                <ChevronLeft class="h-4 w-4" />
                Kembali
            </button>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        {{ selectedInst.nama }}
                    </h1>
                    <p class="mt-1 text-sm text-zinc-500">{{ selectedInst.deskripsi }}</p>
                </div>
                <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddMember">
                    <Users class="h-4 w-4" />
                    Tambah Anggota
                </Button>
            </div>

            <div class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-zinc-50 dark:bg-zinc-800/50">
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">#</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Jabatan</th>
                                <th class="px-4 py-3 text-left font-medium text-zinc-600 dark:text-zinc-400">Telepon</th>
                                <th class="px-4 py-3 text-right font-medium text-zinc-600 dark:text-zinc-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="(member, index) in selectedInst.members"
                                :key="member.id"
                                class="border-b transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50"
                            >
                                <td class="px-4 py-3 text-zinc-500">{{ index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-zinc-900 dark:text-white">{{ member.nama }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ member.jabatan }}</td>
                                <td class="px-4 py-3 text-zinc-500">{{ member.telepon || '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEditMember(member)">
                                            <Pencil class="h-4 w-4" />
                                        </Button>
                                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDeleteMember(member)">
                                            <Trash2 class="h-4 w-4 text-red-500" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="selectedInst.members.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-zinc-500">
                                    Belum ada anggota.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </template>

    <!-- Main List View -->
    <template v-else>
        <div class="relative">
            <!-- Scroll to top -->
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

            <!-- Hero banner -->
            <div class="relative mb-10 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
                <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" aria-hidden="true" />
                <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" aria-hidden="true" />

                <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
                                <Building2 class="h-6 w-6" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Lembaga Desa</h1>
                                <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola data lembaga kemasyarakatan desa</p>
                            </div>
                        </div>
                    </div>
                    <Button as="a" href="/admin/lembaga/tambah" class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
                        <Plus class="h-4 w-4" />
                        Tambah Lembaga
                    </Button>
                </div>

                <!-- Stats -->
                <div class="relative mt-6">
                    <div class="inline-flex items-center gap-2 rounded-xl border border-zinc-100 bg-white/60 px-5 py-3 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                        <Building2 class="h-5 w-5 text-zinc-400" />
                        <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.total }}</span>
                        <span class="text-sm text-zinc-500 dark:text-zinc-400">Lembaga</span>
                    </div>
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="institutions.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <Building2 class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
                <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada data lembaga</p>
                <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan lembaga kemasyarakatan desa.</p>
                <Button as="a" href="/admin/lembaga/tambah" class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600">
                    <Plus class="h-4 w-4" />
                    Tambah Lembaga
                </Button>
            </div>

            <!-- Card Grid -->
            <div v-else class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="inst in institutions"
                    :key="inst.id"
                    class="group rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900"
                >
                    <div class="flex items-start gap-4">
                        <!-- Logo -->
                        <div class="shrink-0 overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-800">
                            <img
                                v-if="inst.logo"
                                :src="`/storage/${inst.logo}`"
                                :alt="inst.nama"
                                class="h-16 w-16 object-cover transition duration-300 group-hover:scale-105 sm:h-20 sm:w-20"
                            />
                            <div v-else class="flex h-16 w-16 items-center justify-center text-zinc-300 dark:text-zinc-600 sm:h-20 sm:w-20">
                                <Building2 class="h-8 w-8" />
                            </div>
                        </div>

                        <div class="min-w-0 flex-1">
                            <button
                                class="text-left font-semibold text-zinc-900 hover:underline dark:text-white"
                                @click="viewMembers(inst)"
                            >
                                {{ inst.nama }}
                            </button>
                            <p class="text-sm font-medium text-zinc-500">{{ inst.singkatan || '-' }}</p>
                            <p class="mt-1 text-xs text-zinc-400 dark:text-zinc-500 line-clamp-2">{{ truncate(inst.deskripsi, 100) }}</p>
                            <p class="mt-1 text-xs text-zinc-400">
                                {{ inst.members?.length || 0 }} anggota
                            </p>

                            <div class="mt-3 flex items-center gap-1">
                                <Button variant="ghost" size="icon-sm" as="a" :href="`/admin/lembaga/${inst.id}/edit`" class="rounded-lg">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDelete(inst.id)">
                                    <Trash2 class="h-4 w-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Delete Institution Confirm -->
    <Dialog :open="deleteConfirmId !== null" @update:open="() => (deleteConfirmId = null)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin menghapus lembaga ini beserta seluruh anggotanya? Tindakan ini tidak dapat dibatalkan.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
                <Button variant="destructive" class="rounded-full" :disabled="deleteForm.processing" @click="executeDelete">Hapus</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Member Dialog -->
    <Dialog v-model:open="memberDialogOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ memberDialogTitle }}</DialogTitle>
                <DialogDescription>
                    {{ editingMember ? 'Perbarui informasi anggota.' : 'Tambahkan anggota baru.' }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitMember" class="space-y-4">
                <div class="grid gap-1.5">
                    <Label for="member-nama">Nama</Label>
                    <Input id="member-nama" v-model="memberForm.nama" required class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="member-jabatan">Jabatan</Label>
                    <Input id="member-jabatan" v-model="memberForm.jabatan" required class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="member-telepon">Telepon</Label>
                    <Input id="member-telepon" v-model="memberForm.telepon" class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="member-urutan">Urutan</Label>
                    <Input id="member-urutan" v-model="memberForm.urutan" type="number" class="rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" />
                </div>

                <DialogFooter class="mt-6">
                    <Button type="button" variant="outline" class="rounded-full" @click="memberDialogOpen = false">Batal</Button>
                    <Button type="submit" :disabled="memberForm.processing" class="rounded-full bg-rose-500 text-white hover:bg-rose-600">
                        {{ editingMember ? 'Simpan' : 'Tambah' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Delete Member Confirm Dialog -->
    <Dialog :open="deleteMemberConfirmId !== null" @update:open="() => (deleteMemberConfirmId = null)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin menghapus anggota ini? Tindakan ini tidak dapat dibatalkan.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" class="rounded-full" @click="deleteMemberConfirmId = null">Batal</Button>
                <Button variant="destructive" class="rounded-full" :disabled="memberForm.processing" @click="executeDeleteMember">Hapus</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
