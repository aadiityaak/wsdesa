<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { MapPin, Phone, Mail, Clock } from '@lucide/vue';

const form = ref({
    nama: '',
    email: '',
    pesan: '',
});

const sent = ref(false);

const submitForm = () => {
    // Send contact message — for now just show success
    sent.value = true;
    form.value = { nama: '', email: '', pesan: '' };
    setTimeout(() => (sent.value = false), 5000);
};
</script>

<template>
    <Head title="Kontak" />

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <h1 class="mb-2 text-3xl font-bold text-zinc-900 dark:text-white">Kontak</h1>
        <p class="mb-8 text-zinc-500 dark:text-zinc-400">Hubungi kami melalui informasi kontak di bawah ini atau kirim pesan melalui formulir.</p>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            <!-- Contact Info -->
            <div class="space-y-4">
                <Card>
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-950">
                            <MapPin class="size-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Alamat</h3>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                                Jl. Desa Digital No. 1<br />
                                Kecamatan Digital, Kabupaten Digital<br />
                                Provinsi Digital
                            </p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-green-50 dark:bg-green-950">
                            <Phone class="size-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Telepon</h3>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">(021) 1234-5678</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950">
                            <Mail class="size-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Email</h3>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">info@desa-digital.id</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950">
                            <Clock class="size-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Jam Operasional</h3>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Senin - Jumat, 08:00 - 16:00 WIB</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Contact Form -->
            <Card>
                <CardHeader>
                    <CardTitle>Kirim Pesan</CardTitle>
                    <CardDescription>Isi formulir di bawah ini untuk mengirim pesan kepada kami.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="sent" class="mb-4 rounded-lg bg-green-50 p-3 text-sm text-green-700 dark:bg-green-950 dark:text-green-300">
                        Pesan berhasil dikirim! Kami akan menghubungi Anda segera.
                    </div>
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="nama">Nama <span class="text-red-500">*</span></Label>
                            <Input id="nama" v-model="form.nama" placeholder="Nama lengkap" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="email">Email <span class="text-red-500">*</span></Label>
                            <Input id="email" v-model="form.email" type="email" placeholder="email@contoh.com" required />
                        </div>
                        <div class="space-y-2">
                            <Label for="pesan">Pesan <span class="text-red-500">*</span></Label>
                            <textarea
                                id="pesan"
                                v-model="form.pesan"
                                rows="5"
                                required
                                placeholder="Tulis pesan Anda..."
                                class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50"
                            ></textarea>
                        </div>
                        <Button type="submit" class="w-full">
                            Kirim Pesan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
