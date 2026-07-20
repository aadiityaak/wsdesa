<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Masuk ke akun Anda',
        description: 'Masukkan email dan kata sandi untuk masuk ke sistem',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Masuk" />

    <!-- Status banner -->
    <div
        v-if="status"
        class="mb-6 rounded-2xl border border-[#c7f0da] bg-[#c7f0da]/30 px-4 py-3 text-sm font-medium text-[#103c25]"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-4">
            <!-- Email -->
            <div class="grid gap-1.5">
                <Label for="email" class="text-sm font-semibold text-[#211922]">Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="nama@desa.id"
                    class="h-11 rounded-2xl border-[#dadad3] bg-white px-4 text-[15px] shadow-none placeholder:text-[#91918c] focus:border-[#000] focus:ring-2 focus:ring-[#435ee5]"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Password -->
            <div class="grid gap-1.5">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-sm font-semibold text-[#211922]">Kata sandi</Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        :tabindex="5"
                        class="text-xs font-semibold text-[#62625b] no-underline hover:text-[#211922]"
                    >
                        Lupa kata sandi?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="h-11 rounded-2xl border-[#dadad3] bg-white px-4 text-[15px] shadow-none placeholder:text-[#91918c] focus:border-[#000] focus:ring-2 focus:ring-[#435ee5]"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Remember me -->
            <div class="flex items-center">
                <Label for="remember" class="flex cursor-pointer items-center gap-2.5">
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="h-5 w-5 rounded-md border-[#dadad3] data-[state=checked]:border-[#e60023] data-[state=checked]:bg-[#e60023] data-[state=checked]:text-white"
                    />
                    <span class="text-sm text-[#62625b]">Ingat saya</span>
                </Label>
            </div>

            <!-- Submit -->
            <Button
                type="submit"
                class="mt-1.5 h-11 w-full rounded-2xl bg-[#e60023] text-sm font-bold tracking-tight text-white shadow-none transition-colors duration-150 hover:bg-[#cc001f] focus-visible:ring-2 focus-visible:ring-[#435ee5]"
                :tabindex="4"
                :disabled="processing"
            >
                <Spinner v-if="processing" class="text-white" />
                Masuk
            </Button>
        </div>

        <!-- Register link -->
        <div class="text-center text-sm text-[#62625b]">
            Belum punya akun?
            <TextLink
                :href="register()"
                :tabindex="5"
                class="font-semibold text-[#211922] no-underline hover:text-[#e60023]"
            >
                Daftar
            </TextLink>
        </div>
    </Form>
</template>
