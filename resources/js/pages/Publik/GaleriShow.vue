<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, ImageIcon, X, ChevronLeft, ChevronRight } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';
import Breadcrumb from '@/components/Breadcrumb.vue';

interface GalleryImage {
    id: number;
    file: string;
    judul?: string;
}

interface Gallery {
    id: number;
    nama: string;
    slug: string;
    deskripsi?: string;
    images: GalleryImage[];
}

const props = defineProps<{
    gallery: Gallery;
}>();

const imageUrl = (path: string) => `/storage/${path}`;

const selectedIndex = ref<number | null>(null);
const showModal = ref(false);

const openModal = (index: number) => {
    selectedIndex.value = index;
    showModal.value = true;
    document.body.style.overflow = 'hidden';
};

const closeModal = () => {
    showModal.value = false;
    selectedIndex.value = null;
    document.body.style.overflow = '';
};

const goPrev = () => {
    if (selectedIndex.value !== null && selectedIndex.value > 0) {
        selectedIndex.value--;
    }
};

const goNext = () => {
    if (selectedIndex.value !== null && selectedIndex.value < props.gallery.images.length - 1) {
        selectedIndex.value++;
    }
};

const handleKeydown = (e: KeyboardEvent) => {
    if (!showModal.value) return;
    if (e.key === 'Escape') closeModal();
    if (e.key === 'ArrowLeft') goPrev();
    if (e.key === 'ArrowRight') goNext();
};

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));
</script>

<template>
    <Head :title="gallery.nama" />

    <PageHero :title="gallery.nama" description="Album galeri foto">
        <template #icon>
            <ImageIcon class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <Breadcrumb :items="[
            { label: 'Galeri', href: '/galeri' },
            { label: gallery.nama },
        ]" />
    </div>

    <div class="mx-auto max-w-7xl px-4 pb-8 sm:px-6 lg:px-8">

        <h1 class="mb-2 text-3xl font-bold text-zinc-900 dark:text-white">{{ gallery.nama }}</h1>
        <p v-if="gallery.deskripsi" class="mb-8 text-zinc-500 dark:text-zinc-400" v-html="gallery.deskripsi" />

        <div v-if="gallery.images.length === 0" class="flex min-h-[30vh] items-center justify-center rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="text-center p-8">
                <ImageIcon class="mx-auto size-12 text-zinc-300 dark:text-zinc-600" />
                <h3 class="mt-4 text-lg font-medium text-zinc-500 dark:text-zinc-400">Belum ada foto</h3>
            </div>
        </div>

        <div v-else class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            <div
                v-for="(image, index) in gallery.images"
                :key="image.id"
                class="group relative aspect-square cursor-pointer overflow-hidden rounded-lg bg-zinc-100 dark:bg-zinc-800"
                @click="openModal(index)"
            >
                <img
                    :src="imageUrl(image.file)"
                    :alt="image.judul || gallery.nama"
                    class="h-full w-full object-cover transition-transform group-hover:scale-105"
                />
                <div v-if="image.judul" class="absolute inset-x-0 bottom-0 bg-black/50 p-2 opacity-0 transition-opacity group-hover:opacity-100">
                    <p class="text-xs text-white">{{ image.judul }}</p>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <Teleport to="body">
            <div
                v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm"
                @click.self="closeModal"
            >
                <!-- Close button -->
                <button
                    class="absolute right-4 top-4 z-10 rounded-full bg-white/10 p-2 text-white transition-colors hover:bg-white/20"
                    @click="closeModal"
                >
                    <X class="size-6" />
                </button>

                <!-- Counter -->
                <div class="absolute left-4 top-4 z-10 rounded-full bg-white/10 px-3 py-1 text-sm text-white">
                    {{ (selectedIndex ?? 0) + 1 }} / {{ gallery.images.length }}
                </div>

                <!-- Previous -->
                <button
                    v-if="selectedIndex !== null && selectedIndex > 0"
                    class="absolute left-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition-colors hover:bg-white/20"
                    @click.stop="goPrev"
                >
                    <ChevronLeft class="size-6" />
                </button>

                <!-- Next -->
                <button
                    v-if="selectedIndex !== null && selectedIndex < gallery.images.length - 1"
                    class="absolute right-4 top-1/2 z-10 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white transition-colors hover:bg-white/20"
                    @click.stop="goNext"
                >
                    <ChevronRight class="size-6" />
                </button>

                <!-- Image -->
                <div class="flex max-h-[90vh] max-w-[90vw] flex-col items-center" @click.stop>
                    <img
                        v-if="selectedIndex !== null"
                        :src="imageUrl(gallery.images[selectedIndex].file)"
                        :alt="gallery.images[selectedIndex].judul || gallery.nama"
                        class="max-h-[80vh] max-w-[90vw] rounded-lg object-contain shadow-2xl"
                    />
                    <p
                        v-if="selectedIndex !== null && gallery.images[selectedIndex].judul"
                        class="mt-3 text-center text-sm text-white/80"
                    >
                        {{ gallery.images[selectedIndex].judul }}
                    </p>
                </div>
            </div>
        </Teleport>
    </div>
</template>
