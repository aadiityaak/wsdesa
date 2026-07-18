<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pencil, Trash2, FileText, Plus, ArrowUp } from '@lucide/vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

interface Template {
    id: number;
    letter_category_id: number;
    nama: string;
    konten: string;
    persyaratan: string;
}

interface Category {
    id: number;
    nama: string;
    slug: string;
    urutan: number;
    templates: Template[];
    requirements: { id: number; nama: string; wajib: boolean }[];
}

const props = defineProps<{
    categories: Category[];
}>();

const categoryDialogOpen = ref(false);
const templateDialogOpen = ref(false);
const editingCategory = ref<Category | null>(null);
const editingTemplate = ref<Template | null>(null);
const selectedCategoryId = ref<number | null>(null);
const deleteCategoryId = ref<number | null>(null);
const deleteTemplateId = ref<number | null>(null);

const categoryForm = useForm({
    nama: '',
    urutan: 0,
});

const templateForm = useForm({
    letter_category_id: 0,
    nama: '',
    konten: '',
    persyaratan: '',
});

const categoryDialogTitle = computed(() => editingCategory.value ? 'Edit Kategori' : 'Tambah Kategori');
const templateDialogTitle = computed(() => editingTemplate.value ? 'Edit Template' : 'Tambah Template');

const openAddCategory = () => {
    editingCategory.value = null;
    categoryForm.reset();
    categoryForm.clearErrors();
    categoryDialogOpen.value = true;
};

const openEditCategory = (cat: Category) => {
    editingCategory.value = cat;
    categoryForm.nama = cat.nama;
    categoryForm.urutan = cat.urutan;
    categoryForm.clearErrors();
    categoryDialogOpen.value = true;
};

const submitCategory = () => {
    if (editingCategory.value) {
        categoryForm.put('/admin/layanan-surat/kategori/' + editingCategory.value.id, {
            onSuccess: () => { categoryDialogOpen.value = false; toast.success('Kategori berhasil diperbarui.'); },
        });
    } else {
        categoryForm.post('/admin/layanan-surat/kategori', {
            onSuccess: () => { categoryDialogOpen.value = false; toast.success('Kategori berhasil ditambahkan.'); },
        });
    }
};

const confirmDeleteCategory = (cat: Category) => { deleteCategoryId.value = cat.id; };

const executeDeleteCategory = () => {
    if (deleteCategoryId.value) {
        categoryForm.delete('/admin/layanan-surat/kategori/' + deleteCategoryId.value, {
            onSuccess: () => { deleteCategoryId.value = null; toast.success('Kategori berhasil dihapus.'); },
        });
    }
};

const openAddTemplate = (categoryId: number) => {
    selectedCategoryId.value = categoryId;
    editingTemplate.value = null;
    templateForm.reset();
    templateForm.letter_category_id = categoryId;
    templateForm.clearErrors();
    templateDialogOpen.value = true;
};

const openEditTemplate = (tpl: Template) => {
    editingTemplate.value = tpl;
    selectedCategoryId.value = tpl.letter_category_id;
    templateForm.letter_category_id = tpl.letter_category_id;
    templateForm.nama = tpl.nama;
    templateForm.konten = tpl.konten;
    templateForm.persyaratan = tpl.persyaratan;
    templateForm.clearErrors();
    templateDialogOpen.value = true;
};

const submitTemplate = () => {
    if (editingTemplate.value) {
        templateForm.put('/admin/layanan-surat/template/' + editingTemplate.value.id, {
            onSuccess: () => { templateDialogOpen.value = false; toast.success('Template berhasil diperbarui.'); },
        });
    } else {
        templateForm.post('/admin/layanan-surat/template', {
            onSuccess: () => { templateDialogOpen.value = false; toast.success('Template berhasil ditambahkan.'); },
        });
    }
};

const confirmDeleteTemplate = (tpl: Template) => { deleteTemplateId.value = tpl.id; };

const executeDeleteTemplate = () => {
    if (deleteTemplateId.value) {
        templateForm.delete('/admin/layanan-surat/template/' + deleteTemplateId.value, {
            onSuccess: () => { deleteTemplateId.value = null; toast.success('Template berhasil dihapus.'); },
        });
    }
};

const stats = computed(() => {
    const totalTemplates = props.categories.reduce((sum, c) => sum + c.templates.length, 0);
    return {
        totalCategories: props.categories.length,
        totalTemplates,
    };
});

const showScrollTop = ref(false);
const onScroll = () => { showScrollTop.value = window.scrollY > 400; };
const scrollToTop = () => { window.scrollTo({ top: 0, behavior: 'smooth' }); };
if (typeof window !== 'undefined') {
    window.addEventListener('scroll', onScroll, { passive: true });
}
</script>

<template>
    <Head title="Kategori Surat" />

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
                            <FileText class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Kategori Surat</h1>
                            <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Kelola kategori layanan surat</p>
                        </div>
                    </div>
                </div>
                <Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600" @click="openAddCategory">
                    <Plus class="h-4 w-4" />
                    Tambah Kategori
                </Button>
            </div>

            <!-- Stats -->
            <div class="relative mt-6 grid grid-cols-2 gap-3 sm:gap-4">
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ stats.totalCategories }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Kategori</p>
                </div>
                <div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
                    <p class="text-2xl font-bold text-rose-600 dark:text-rose-400">{{ stats.totalTemplates }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Template</p>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div v-if="categories.length === 0" class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <FileText class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
            <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada kategori surat</p>
            <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Tambahkan kategori untuk mengelola template surat.</p>
            <Button class="mt-4 gap-2 rounded-full bg-rose-500 text-white hover:bg-rose-600" @click="openAddCategory">
                <Plus class="h-4 w-4" />
                Tambah Kategori
            </Button>
        </div>

        <!-- Category cards -->
        <div v-else class="space-y-4">
            <div v-for="cat in categories" :key="cat.id" class="rounded-2xl border border-zinc-100 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center justify-between border-b border-zinc-100 px-5 py-4 dark:border-zinc-800">
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ cat.nama }}</h3>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500">Slug: {{ cat.slug }} · Urutan: {{ cat.urutan }}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <Button variant="outline" size="sm" class="rounded-lg" @click="openAddTemplate(cat.id)">
                            <FileText class="size-3.5" /> <span class="ml-1 hidden sm:inline">Tambah Template</span>
                        </Button>
                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEditCategory(cat)">
                            <Pencil class="size-4" />
                        </Button>
                        <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDeleteCategory(cat)">
                            <Trash2 class="size-4 text-red-500" />
                        </Button>
                    </div>
                </div>
                <div class="px-5 py-4">
                    <div v-if="cat.templates.length === 0" class="py-6 text-center text-sm text-zinc-400 dark:text-zinc-500">
                        Belum ada template surat.
                    </div>
                    <div v-else class="space-y-2">
                        <div
                            v-for="tpl in cat.templates"
                            :key="tpl.id"
                            class="flex items-center justify-between rounded-xl border border-zinc-100 p-3 transition-colors hover:bg-zinc-50 dark:border-zinc-800 dark:hover:bg-zinc-800/50"
                        >
                            <div class="min-w-0 flex-1">
                                <h4 class="truncate font-medium text-zinc-900 dark:text-white">{{ tpl.nama }}</h4>
                                <p class="truncate text-xs text-zinc-400 dark:text-zinc-500">{{ tpl.persyaratan }}</p>
                            </div>
                            <div class="flex shrink-0 gap-1">
                                <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="openEditTemplate(tpl)">
                                    <Pencil class="size-4" />
                                </Button>
                                <Button variant="ghost" size="icon-sm" class="rounded-lg" @click="confirmDeleteTemplate(tpl)">
                                    <Trash2 class="size-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Dialog -->
        <Dialog v-model:open="categoryDialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ categoryDialogTitle }}</DialogTitle>
                    <DialogDescription v-if="!editingCategory">Tambahkan kategori baru untuk mengelompokkan template surat.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitCategory" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="nama">Nama Kategori</Label>
                        <Input id="nama" v-model="categoryForm.nama" required class="rounded-xl" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="urutan">Urutan</Label>
                        <Input id="urutan" v-model.number="categoryForm.urutan" type="number" class="rounded-xl" />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" class="rounded-full" @click="categoryDialogOpen = false">Batal</Button>
                        <Button type="submit" class="rounded-full" :disabled="categoryForm.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Category Dialog -->
        <Dialog :open="deleteCategoryId !== null" @update:open="() => (deleteCategoryId = null)">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" class="rounded-full" @click="deleteCategoryId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" :disabled="categoryForm.processing" @click="executeDeleteCategory">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Template Dialog -->
        <Dialog v-model:open="templateDialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ templateDialogTitle }}</DialogTitle>
                    <DialogDescription v-if="!editingTemplate">Tambahkan template surat baru untuk kategori ini.</DialogDescription>
                </DialogHeader>
                <form @submit.prevent="submitTemplate" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="tnama">Nama Template</Label>
                        <Input id="tnama" v-model="templateForm.nama" required class="rounded-xl" />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="konten">Konten (HTML)</Label>
                        <textarea
                            id="konten"
                            v-model="templateForm.konten"
                            rows="6"
                            required
                            class="flex w-full rounded-xl border border-input bg-transparent px-3 py-2 text-sm font-mono shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="persyaratan">Persyaratan</Label>
                        <Input id="persyaratan" v-model="templateForm.persyaratan" required class="rounded-xl" />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" class="rounded-full" @click="templateDialogOpen = false">Batal</Button>
                        <Button type="submit" class="rounded-full" :disabled="templateForm.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Template Dialog -->
        <Dialog :open="deleteTemplateId !== null" @update:open="() => (deleteTemplateId = null)">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>Apakah Anda yakin ingin menghapus template ini? Tindakan ini tidak dapat dibatalkan.</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" class="rounded-full" @click="deleteTemplateId = null">Batal</Button>
                    <Button variant="destructive" class="rounded-full" :disabled="templateForm.processing" @click="executeDeleteTemplate">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
