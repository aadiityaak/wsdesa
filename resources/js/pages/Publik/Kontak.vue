<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import kontak from '@/routes/kontak';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { MapPin, Phone, Mail, Clock, Send, CheckCircle2 } from '@lucide/vue';
import PageHero from '@/components/PageHero.vue';

const form = useForm({
    nama: '',
    email: '',
    pesan: '',
});

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success as string | undefined);

const submitForm = () => {
    form.post(kontak.store().url, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Kontak" />

    <PageHero title="Hubungi Kami" description="Kami siap membantu. Kirim pesan atau kunjungi kantor desa.">
        <template #icon>
            <Mail class="size-6 text-white" />
        </template>
    </PageHero>

    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">

        <!-- Flash Success -->
        <div v-if="flashSuccess" class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950">
            <CheckCircle2 class="size-5 shrink-0 text-green-600 dark:text-green-400" />
            <p class="text-sm font-medium text-green-700 dark:text-green-300">{{ flashSuccess }}</p>
        </div>

        <!-- Form Success -->
        <div v-if="form.recentlySuccessful && !flashSuccess" class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-950">
            <CheckCircle2 class="size-5 shrink-0 text-green-600 dark:text-green-400" />
            <p class="text-sm font-medium text-green-700 dark:text-green-300">Pesan berhasil dikirim! Kami akan menghubungi Anda segera.</p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-5">
            <!-- Contact Info — 2 kolom -->
            <div class="space-y-4 lg:col-span-2">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">Informasi Kontak</h2>

                <Card class="transition-shadow hover:shadow-md">
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-sky-50 dark:bg-sky-950">
                            <MapPin class="size-5 text-sky-600 dark:text-sky-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Alamat</h3>
                            <p class="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                                Jl. Desa Digital No. 1<br />
                                Kecamatan Digital, Kabupaten Digital<br />
                                Provinsi Digital
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="transition-shadow hover:shadow-md">
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-950">
                            <Phone class="size-5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Telepon</h3>
                            <a href="tel:+622112345678" class="mt-1 block text-sm text-zinc-600 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">(021) 1234-5678</a>
                        </div>
                    </CardContent>
                </Card>

                <Card class="transition-shadow hover:shadow-md">
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-950">
                            <Mail class="size-5 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Email</h3>
                            <a href="mailto:info@desa-digital.id" class="mt-1 block text-sm text-zinc-600 transition-colors hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">info@desa-digital.id</a>
                        </div>
                    </CardContent>
                </Card>

                <Card class="transition-shadow hover:shadow-md">
                    <CardContent class="flex items-start gap-4 p-5">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-950">
                            <Clock class="size-5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Jam Operasional</h3>
                            <p class="mt-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                                Senin – Jumat<br />
                                08:00 – 16:00 WIB
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Map Placeholder -->
                <Card class="overflow-hidden">
                    <div class="flex aspect-[16/7] items-center justify-center bg-gradient-to-br from-sky-100 to-sky-200 dark:from-sky-950 dark:to-sky-900">
                        <div class="text-center">
                            <MapPin class="mx-auto size-8 text-sky-500" />
                            <p class="mt-1 text-xs text-sky-600 dark:text-sky-400">Kantor Desa Digital</p>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Form — 3 kolom -->
            <div class="lg:col-span-3">
                <Card>
                    <CardHeader>
                        <CardTitle>Kirim Pesan</CardTitle>
                        <CardDescription>Isi formulir di bawah, kami akan merespon secepatnya.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submitForm" class="space-y-5">
                            <div class="space-y-2">
                                <Label for="nama">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </Label>
                                <div class="relative">
                                    <Mail class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
                                    <Input id="nama" v-model="form.nama" type="text" placeholder="Nama lengkap" class="pl-10" required />
                                </div>
                                <InputError :message="form.errors.nama" />
                            </div>

                            <div class="space-y-2">
                                <Label for="email">
                                    Email <span class="text-red-500">*</span>
                                </Label>
                                <div class="relative">
                                    <Mail class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-zinc-400" />
                                    <Input id="email" v-model="form.email" type="email" placeholder="email@contoh.com" class="pl-10" required />
                                </div>
                                <InputError :message="form.errors.email" />
                            </div>

                            <div class="space-y-2">
                                <Label for="pesan">
                                    Pesan <span class="text-red-500">*</span>
                                </Label>
                                <textarea
                                    id="pesan"
                                    v-model="form.pesan"
                                    rows="6"
                                    required
                                    placeholder="Tulis pesan atau pertanyaan Anda..."
                                    class="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 resize-y"
                                ></textarea>
                                <InputError :message="form.errors.pesan" />
                            </div>

                            <Button type="submit" class="w-full" size="lg" :disabled="form.processing">
                                <Send class="mr-2 size-4" />
                                {{ form.processing ? 'Mengirim...' : 'Kirim Pesan' }}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
