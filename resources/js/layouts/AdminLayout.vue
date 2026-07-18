<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Toaster } from '@/components/ui/sonner';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarInset,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarProvider,
    SidebarTrigger,
} from '@/components/ui/sidebar';
import NavUser from '@/components/NavUser.vue';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import {
    LayoutDashboard,
    Building2,
    Newspaper,
    Calendar,
    Users,
    FileText,
    Image,
    MessageSquare,
    Store,
    TrendingUp,
    Wallet,
    Construction,
    File,
    SlidersHorizontal,
    Megaphone,
    Download,
    UsersRound,
} from '@lucide/vue';
import type { Component } from 'vue';

type NavItem = {
    title: string;
    href: string;
    icon: Component;
};

type NavGroup = {
    label: string;
    items: NavItem[];
};

const navGroups: NavGroup[] = [
    {
        label: 'Utama',
        items: [
            { title: 'Dashboard', href: '/admin', icon: LayoutDashboard },
            { title: 'Profil Desa', href: '/admin/profil', icon: Building2 },
        ],
    },
    {
        label: 'Konten',
        items: [
            { title: 'Berita', href: '/admin/berita', icon: Newspaper },
            { title: 'Agenda', href: '/admin/agenda', icon: Calendar },
            { title: 'Komentar', href: '/admin/komentar', icon: MessageSquare },
        ],
    },
    {
        label: 'Data Desa',
        items: [
            { title: 'Pemerintahan', href: '/admin/pemerintahan', icon: UsersRound },
            { title: 'Kependudukan', href: '/admin/kependudukan', icon: Users },
            { title: 'APBDes', href: '/admin/apbdes', icon: Wallet },
            { title: 'Pembangunan', href: '/admin/pembangunan', icon: Construction },
        ],
    },
    {
        label: 'Layanan & Media',
        items: [
            { title: 'Layanan Surat', href: '/admin/layanan-surat', icon: FileText },
            { title: 'Pengaduan', href: '/admin/pengaduan', icon: MessageSquare },
            { title: 'Galeri', href: '/admin/galeri', icon: Image },
            { title: 'Video', href: '/admin/video', icon: Image },
            { title: 'UMKM', href: '/admin/umkm', icon: Store },
            { title: 'Potensi Desa', href: '/admin/potensi', icon: TrendingUp },
        ],
    },
    {
        label: 'Lainnya',
        items: [
            { title: 'Halaman', href: '/admin/halaman', icon: File },
            { title: 'Slider', href: '/admin/slider', icon: SlidersHorizontal },
            { title: 'Pengumuman', href: '/admin/pengumuman', icon: Megaphone },
            { title: 'Download', href: '/admin/download', icon: Download },
            { title: 'Pengguna', href: '/admin/pengguna', icon: UsersRound },
        ],
    },
];

const page = usePage();
</script>

<template>
    <SidebarProvider>
        <Sidebar variant="sidebar" collapsible="icon">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" as-child>
                            <Link href="/admin">
                                <AppLogoIcon class="size-8 rounded-md" />
                                <span class="font-semibold">Admin Desa</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
                <SidebarGroup v-for="group in navGroups" :key="group.label">
                    <SidebarGroupLabel>{{ group.label }}</SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            <SidebarMenuItem v-for="item in group.items" :key="item.href">
                                <SidebarMenuButton
                                    as-child
                                    :is-active="page.url.startsWith(item.href)"
                                    :tooltip="item.title"
                                >
                                    <Link :href="item.href">
                                        <component :is="item.icon" />
                                        <span>{{ item.title }}</span>
                                    </Link>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>
            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>

        <SidebarInset>
            <!-- Top header bar with trigger -->
            <header class="sticky top-0 z-10 flex h-14 shrink-0 items-center gap-2 border-b bg-white px-4 dark:bg-zinc-950">
                <SidebarTrigger class="-ml-1" />
                <div class="flex-1" />
                <AppearanceTabs />
            </header>

            <main class="flex-1">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </SidebarInset>

        <Toaster />
    </SidebarProvider>
</template>
