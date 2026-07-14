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
        title: 'Log in ke akun Anda',
        description: 'Masukkan email dan kata sandi untuk masuk',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
    >
        <div
            v-if="status"
            class="mb-5 rounded-lg border border-green-200 bg-green-50 px-3.5 py-2.5 text-sm font-medium text-green-700"
        >
            {{ status }}
        </div>
    </transition>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-5"
    >
        <div class="grid gap-5">
            <div class="grid gap-2">
                <Label for="email" class="text-[#1a1410]">Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="nama@desa.id"
                    class="border-[#e3ddd2] bg-white py-5.5 text-[15px] shadow-sm transition-all duration-200 placeholder:text-[#a89c8b] focus-visible:border-[#d4a056] focus-visible:ring-[#d4a056]/30"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-[#1a1410]"
                        >Kata sandi</Label
                    >
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs font-medium text-[#8a6d3b] no-underline transition-colors hover:text-[#d4a056]"
                        :tabindex="5"
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
                    class="border-[#e3ddd2] bg-white py-5.5 text-[15px] shadow-sm transition-all duration-200 placeholder:text-[#a89c8b] focus-visible:border-[#d4a056] focus-visible:ring-[#d4a056]/30"
                />
                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                        class="border-[#d4c9b4] data-[state=checked]:border-[#d4a056] data-[state=checked]:bg-[#d4a056] data-[state=checked]:text-[#1a1410]"
                    />
                    <span class="text-sm text-[#5a5142]">Ingat saya</span>
                </Label>
            </div>

            <Button
                type="submit"
                class="mt-1.5 h-11 w-full bg-[#1a1410] text-[15px] font-medium tracking-tight text-[#f0e6d8] shadow-sm transition-all duration-200 hover:bg-[#2a211a] focus-visible:ring-[#d4a056]/40"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" class="text-[#d4a056]" />
                Masuk
            </Button>
        </div>

        <div class="mt-1 text-center text-sm text-[#7a7062]">
            Belum punya akun?
            <TextLink
                :href="register()"
                :tabindex="5"
                class="font-medium text-[#8a6d3b] no-underline transition-colors hover:text-[#d4a056]"
                >Daftar</TextLink
            >
        </div>
    </Form>
</template>

<style scoped>
:deep(.py-5\.5) {
    padding-top: 0.875rem;
    padding-bottom: 0.875rem;
}
</style>
