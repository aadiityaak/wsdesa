<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { ref, computed } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pencil, Trash2, FileText } from '@lucide/vue';
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
</script>

<template>
    <Head title="Kategori Surat" />

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white">Kategori Surat</h1>
            <Button @click="openAddCategory">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kategori
            </Button>
        </div>

        <div class="space-y-4">
            <Card v-for="cat in categories" :key="cat.id">
                <CardHeader class="pb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle class="text-lg">{{ cat.nama }}</CardTitle>
                            <p class="text-sm text-zinc-500">Slug: {{ cat.slug }} | Urutan: {{ cat.urutan }}</p>
                        </div>
                        <div class="flex items-center gap-1">
                            <Button variant="outline" size="sm" @click="openAddTemplate(cat.id)">
                                <FileText class="size-3" /> Tambah Template
                            </Button>
                            <Button variant="ghost" size="sm" @click="openEditCategory(cat)">
                                <Pencil class="size-3" />
                            </Button>
                            <Button variant="ghost" size="sm" @click="confirmDeleteCategory(cat)">
                                <Trash2 class="size-3 text-red-500" />
                            </Button>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="cat.templates.length === 0" class="py-4 text-center text-sm text-zinc-400">
                        Belum ada template surat.
                    </div>
                    <div v-else class="space-y-2">
                        <div
                            v-for="tpl in cat.templates"
                            :key="tpl.id"
                            class="flex items-center justify-between rounded-lg border p-3"
                        >
                            <div>
                                <h4 class="font-medium">{{ tpl.nama }}</h4>
                                <p class="text-xs text-zinc-500">{{ tpl.persyaratan }}</p>
                            </div>
                            <div class="flex gap-1">
                                <Button variant="ghost" size="icon-sm" @click="openEditTemplate(tpl)">
                                    <Pencil class="size-4" />
                                </Button>
                                <Button variant="ghost" size="icon-sm" @click="confirmDeleteTemplate(tpl)">
                                    <Trash2 class="size-4 text-red-500" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div v-if="categories.length === 0" class="py-12 text-center text-zinc-500">
            Belum ada kategori surat.
        </div>

        <!-- Category Dialog -->
        <Dialog v-model:open="categoryDialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{{ categoryDialogTitle }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitCategory" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="nama">Nama Kategori</Label>
                        <Input id="nama" v-model="categoryForm.nama" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="urutan">Urutan</Label>
                        <Input id="urutan" v-model.number="categoryForm.urutan" type="number" />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="categoryDialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="categoryForm.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Category -->
        <Dialog :open="deleteCategoryId !== null" @update:open="deleteCategoryId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>Yakin hapus kategori ini?</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteCategoryId = null">Batal</Button>
                    <Button variant="destructive" @click="executeDeleteCategory">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Template Dialog -->
        <Dialog v-model:open="templateDialogOpen">
            <DialogContent class="sm:max-w-lg">
                <DialogHeader>
                    <DialogTitle>{{ templateDialogTitle }}</DialogTitle>
                </DialogHeader>
                <form @submit.prevent="submitTemplate" class="space-y-4">
                    <div class="grid gap-1.5">
                        <Label for="tnama">Nama Template</Label>
                        <Input id="tnama" v-model="templateForm.nama" required />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="konten">Konten (HTML)</Label>
                        <textarea
                            id="konten"
                            v-model="templateForm.konten"
                            rows="6"
                            required
                            class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm font-mono shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                        ></textarea>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="persyaratan">Persyaratan</Label>
                        <Input id="persyaratan" v-model="templateForm.persyaratan" required />
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="outline" @click="templateDialogOpen = false">Batal</Button>
                        <Button type="submit" :disabled="templateForm.processing">Simpan</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- Delete Template -->
        <Dialog :open="deleteTemplateId !== null" @update:open="deleteTemplateId = null">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Konfirmasi Hapus</DialogTitle>
                    <DialogDescription>Yakin hapus template ini?</DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="deleteTemplateId = null">Batal</Button>
                    <Button variant="destructive" @click="executeDeleteTemplate">Hapus</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
