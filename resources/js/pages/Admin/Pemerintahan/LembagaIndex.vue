<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
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
import { Plus, Pencil, Trash2, UserPlus, ChevronLeft } from '@lucide/vue';

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
    jenis: string;
    deskripsi: string;
    foto: string | null;
    members: Member[];
}

const props = defineProps<{
    institutions: Institution[];
}>();

// Institution state
const instDialogOpen = ref(false);
const editingInst = ref<Institution | null>(null);
const deleteInstConfirmId = ref<number | null>(null);

// Member state
const selectedInst = ref<Institution | null>(null);
const memberDialogOpen = ref(false);
const editingMember = ref<Member | null>(null);
const deleteMemberConfirmId = ref<number | null>(null);

// Institution form
const instForm = useForm({
    nama: '',
    singkatan: '',
    jenis: '',
    deskripsi: '',
    foto: null as File | null,
});

// Member form
const memberForm = useForm({
    nama: '',
    jabatan: '',
    foto: null as File | null,
    telepon: '',
    urutan: 0,
});

// Institution dialog
const instDialogTitle = computed(() => (editingInst.value ? 'Edit Lembaga' : 'Tambah Lembaga'));

const openAddInst = () => {
    editingInst.value = null;
    instForm.reset();
    instForm.clearErrors();
    instDialogOpen.value = true;
};

const openEditInst = (inst: Institution) => {
    editingInst.value = inst;
    instForm.nama = inst.nama;
    instForm.singkatan = inst.singkatan;
    instForm.jenis = inst.jenis;
    instForm.deskripsi = inst.deskripsi;
    instForm.foto = null;
    instForm.clearErrors();
    instDialogOpen.value = true;
};

const submitInst = () => {
    if (editingInst.value) {
        instForm.put(`/admin/pemerintahan/lembaga/${editingInst.value.id}`, {
            onSuccess: () => {
                instDialogOpen.value = false;
                toast.success('Data lembaga berhasil diperbarui.');
            },
        });
    } else {
        instForm.post('/admin/pemerintahan/lembaga', {
            onSuccess: () => {
                instDialogOpen.value = false;
                toast.success('Lembaga baru berhasil ditambahkan.');
            },
        });
    }
};

const confirmDeleteInst = (inst: Institution) => {
    deleteInstConfirmId.value = inst.id;
};

const executeDeleteInst = () => {
    if (deleteInstConfirmId.value) {
        instForm.delete(`/admin/pemerintahan/lembaga/${deleteInstConfirmId.value}`, {
            onSuccess: () => {
                deleteInstConfirmId.value = null;
                toast.success('Data lembaga berhasil dihapus.');
            },
        });
    }
};

// Member actions
const viewMembers = (inst: Institution) => {
    selectedInst.value = inst;
};

const backToList = () => {
    selectedInst.value = null;
};

const memberDialogTitle = computed(() => (editingMember.value ? 'Edit Anggota' : 'Tambah Anggota'));

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
        memberForm.put(`/admin/pemerintahan/lembaga/${instId}/anggota/${editingMember.value.id}`, {
            onSuccess: () => {
                memberDialogOpen.value = false;
                toast.success('Data anggota berhasil diperbarui.');
            },
        });
    } else {
        memberForm.post(`/admin/pemerintahan/lembaga/${instId}/anggota`, {
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
        memberForm.delete(`/admin/pemerintahan/lembaga/${selectedInst.value.id}/anggota/${deleteMemberConfirmId.value}`, {
            onSuccess: () => {
                deleteMemberConfirmId.value = null;
                toast.success('Data anggota berhasil dihapus.');
            },
        });
    }
};

const getJenisBadgeVariant = (jenis: string): 'default' | 'secondary' | 'outline' => {
    const lower = jenis.toLowerCase();
    if (lower === 'bpd') return 'default';
    if (lower === 'lpm') return 'secondary';
    return 'outline';
};

const truncate = (text: string, max: number) => {
    if (!text) return '';
    return text.length > max ? text.slice(0, max) + '...' : text;
};
</script>

<template>
    <Head title="Lembaga" />

    <!-- Member View -->
    <template v-if="selectedInst">
        <div class="space-y-6">
            <button
                class="inline-flex items-center gap-1 text-sm text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white"
                @click="backToList"
            >
                <ChevronLeft class="size-4" />
                Kembali
            </button>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">
                        {{ selectedInst.nama }}
                    </h1>
                    <p class="mt-1 text-sm text-zinc-500">{{ selectedInst.deskripsi }}</p>
                </div>
                <Button @click="openAddMember">
                    <UserPlus class="size-4" />
                    Tambah Anggota
                </Button>
            </div>

            <Card>
                <CardHeader class="pb-3">
                    <CardTitle>Daftar Anggota</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
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
                                    <td class="px-4 py-3 font-medium">{{ member.nama }}</td>
                                    <td class="px-4 py-3">{{ member.jabatan }}</td>
                                    <td class="px-4 py-3 text-zinc-500">{{ member.telepon || '-' }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <Button variant="ghost" size="icon-sm" @click="openEditMember(member)">
                                                <Pencil class="size-4" />
                                            </Button>
                                            <Button variant="ghost" size="icon-sm" @click="confirmDeleteMember(member)">
                                                <Trash2 class="size-4 text-red-500" />
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
                </CardContent>
            </Card>
        </div>
    </template>

    <!-- Institution Grid View -->
    <template v-else>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Lembaga</h1>
                <Button @click="openAddInst">
                    <Plus class="size-4" />
                    Tambah Lembaga
                </Button>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="inst in institutions"
                    :key="inst.id"
                    class="group transition-shadow hover:shadow-md"
                >
                    <CardHeader class="pb-2">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <Badge
                                    :variant="getJenisBadgeVariant(inst.jenis)"
                                    class="mb-2"
                                >
                                    {{ inst.jenis }}
                                </Badge>
                                <CardTitle class="text-base">
                                    <button
                                        class="text-left hover:underline"
                                        @click="viewMembers(inst)"
                                    >
                                        {{ inst.nama }}
                                    </button>
                                </CardTitle>
                                <p class="mt-0.5 text-sm font-medium text-zinc-500">
                                    {{ inst.singkatan }}
                                </p>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">
                            {{ truncate(inst.deskripsi, 120) }}
                        </p>
                        <p class="mt-2 text-xs text-zinc-400">
                            {{ inst.members.length }} anggota
                        </p>
                        <div class="mt-3 flex items-center gap-1">
                            <Button variant="outline" size="sm" @click="openEditInst(inst)">
                                <Pencil class="size-3.5" />
                                Edit
                            </Button>
                            <Button variant="outline" size="sm" @click="confirmDeleteInst(inst)">
                                <Trash2 class="size-3.5 text-red-500" />
                                Hapus
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="institutions.length === 0" class="py-12 text-center text-zinc-500">
                Belum ada data lembaga.
            </div>
        </div>
    </template>

    <!-- Institution Dialog -->
    <Dialog v-model:open="instDialogOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ instDialogTitle }}</DialogTitle>
                <DialogDescription>
                    {{ editingInst ? 'Perbarui informasi lembaga.' : 'Tambahkan lembaga baru.' }}
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitInst" class="space-y-4">
                <div class="grid gap-1.5">
                    <Label for="inst-nama">Nama</Label>
                    <Input id="inst-nama" v-model="instForm.nama" required />
                </div>
                <div class="grid gap-1.5">
                    <Label for="inst-singkatan">Singkatan</Label>
                    <Input id="inst-singkatan" v-model="instForm.singkatan" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="inst-jenis">Jenis</Label>
                    <Input id="inst-jenis" v-model="instForm.jenis" placeholder="BPD, LPM, PKK, Karang Taruna, ..." />
                </div>
                <div class="grid gap-1.5">
                    <Label for="inst-deskripsi">Deskripsi</Label>
                    <Input id="inst-deskripsi" v-model="instForm.deskripsi" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="inst-foto">Foto</Label>
                    <Input
                        id="inst-foto"
                        type="file"
                        accept="image/*"
                        @input="(e: Event) => (instForm.foto = (e.target as HTMLInputElement).files?.[0] ?? null)"
                    />
                </div>

                <DialogFooter class="mt-6">
                    <Button type="button" variant="outline" @click="instDialogOpen = false">Batal</Button>
                    <Button type="submit" :disabled="instForm.processing">
                        {{ editingInst ? 'Simpan' : 'Tambah' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Delete Institution Confirm Dialog -->
    <Dialog :open="deleteInstConfirmId !== null" @update:open="() => (deleteInstConfirmId = null)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Konfirmasi Hapus</DialogTitle>
                <DialogDescription>
                    Apakah Anda yakin ingin menghapus lembaga ini beserta seluruh anggotanya? Tindakan ini tidak dapat dibatalkan.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button variant="outline" @click="deleteInstConfirmId = null">Batal</Button>
                <Button variant="destructive" :disabled="instForm.processing" @click="executeDeleteInst">Hapus</Button>
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
                    <Input id="member-nama" v-model="memberForm.nama" required />
                </div>
                <div class="grid gap-1.5">
                    <Label for="member-jabatan">Jabatan</Label>
                    <Input id="member-jabatan" v-model="memberForm.jabatan" required />
                </div>
                <div class="grid gap-1.5">
                    <Label for="member-telepon">Telepon</Label>
                    <Input id="member-telepon" v-model="memberForm.telepon" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="member-foto">Foto</Label>
                    <Input
                        id="member-foto"
                        type="file"
                        accept="image/*"
                        @input="(e: Event) => (memberForm.foto = (e.target as HTMLInputElement).files?.[0] ?? null)"
                    />
                </div>
                <div class="grid gap-1.5">
                    <Label for="member-urutan">Urutan</Label>
                    <Input id="member-urutan" v-model="memberForm.urutan" type="number" />
                </div>

                <DialogFooter class="mt-6">
                    <Button type="button" variant="outline" @click="memberDialogOpen = false">Batal</Button>
                    <Button type="submit" :disabled="memberForm.processing">
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
                <Button variant="outline" @click="deleteMemberConfirmId = null">Batal</Button>
                <Button variant="destructive" :disabled="memberForm.processing" @click="executeDeleteMember">Hapus</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
