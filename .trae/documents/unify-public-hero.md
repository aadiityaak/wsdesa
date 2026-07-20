# Unify Public Page Hero / Sub-Header

## Ringkasan

Membuat komponen `PageHero` dan `Breadcrumb` yang shared untuk semua halaman publik, menggantikan hero section yang tidak konsisten dan menambahkan hero ke halaman yang belum memilikinya.

## Current State

| Halaman | Punya Hero? | Warna Gradient | Overlay | Icon |
|---------|-------------|----------------|---------|------|
| Welcome | Ya (2 varian: slider & fallback) | blue→indigo-900 / slider | bg-black/50 | Tidak |
| Berita/Index | Ya | blue→indigo-900 | bg-black/20 | Newspaper (boxed) |
| Berita/Show | **Tidak** | - | - | - |
| Galeri | **Tidak** | - | - | - |
| GaleriShow | **Tidak** | - | - | - |
| Agenda | **Tidak** | - | - | - |
| AgendaShow | **Tidak** | - | - | - |
| Umkm | Ya | emerald→emerald-800 | Tidak | Store (plain) |
| UmkmShow | **Tidak** | - | - | - |
| Kontak | Ya | sky→sky-800 | Tidak | Mail (plain) |
| Pemerintahan | Ya | blue→indigo-**800** | bg-black/20 | Tidak |
| Profil | Ya | blue→indigo-900 | bg-black/20 | Logo desa |
| Potensi | **Tidak** | - | - | - |
| Pembangunan | **Tidak** | - | - | - |
| LayananSurat | **Tidak** | - | - | - |
| Pengaduan | **Tidak** | - | - | - |
| Statistik | **Tidak** | - | - | - |
| Download | **Tidak** | - | - | - |
| Infografis/Penduduk | **Tidak** | - | - | - |
| Apbdes | **Tidak** | - | - | - |
| Halaman | **Tidak** | - | - | - |
| PengaduanStatus | **Tidak** | - | - | - |

**Inkonsistensi:** 4 halaman punya hero berbeda (3 warna gradient berbeda, 3 padding berbeda, 2 tanpa overlay), 17+ halaman tidak punya hero sama sekali.

## Proposed Changes

### 1. `resources/js/components/PageHero.vue` — Komponen baru

Komponen hero yang konsisten dengan props:
- `title` (string, required) — judul halaman
- `description` (string, optional) — deskripsi sub-judul
- **Slot `#icon`** — icon opsional (default: tanpa icon)
- Style standar: `bg-gradient-to-br from-blue-600 to-indigo-900 py-12 md:py-16`, overlay `bg-black/20`, teks putih center
- Welcome.vue **tidak diubah** karena hero-nya unik (slider + konten berbeda)

### 2. `resources/js/components/Breadcrumb.vue` — Komponen baru

Komponen breadcrumb sederhana dengan props:
- `items` (array `{ label: string, href?: string }[]`)

### 3. Update semua halaman publik (17+ halaman)

Setiap halaman yang TIDAK punya hero → tambahkan `<PageHero>` di atas konten.
Setiap halaman yang SUDAH punya hero → ganti dengan `<PageHero>`.

**Daftar halaman yang diubah:**

| File | Action |
|------|--------|
| `Publik/Galeri.vue` | Tambah PageHero |
| `Publik/GaleriShow.vue` | Tambah PageHero (via prop gallery.nama) |
| `Publik/Agenda.vue` | Tambah PageHero |
| `Publik/AgendaShow.vue` | Tambah PageHero + Breadcrumb |
| `Publik/Umkm.vue` | Ganti hero manual → PageHero (emerald → blue standard) |
| `Publik/UmkmShow.vue` | Tambah PageHero + Breadcrumb |
| `Publik/Kontak.vue` | Ganti hero manual → PageHero |
| `Publik/Pemerintahan.vue` | Ganti hero manual → PageHero (`to-indigo-800` → `to-indigo-900` unified) |
| `Publik/Berita/Index.vue` | Ganti hero manual → PageHero |
| `Publik/Berita/Show.vue` | Tambah PageHero + Breadcrumb |
| `Publik/Potensi.vue` | Tambah PageHero |
| `Publik/Pembangunan.vue` | Tambah PageHero |
| `Publik/LayananSurat.vue` | Tambah PageHero |
| `Publik/Pengaduan.vue` | Tambah PageHero |
| `Publik/PengaduanStatus.vue` | Tambah PageHero |
| `Publik/Statistik.vue` | Tambah PageHero |
| `Publik/Download.vue` | Tambah PageHero |
| `Publik/Infografis/Penduduk.vue` | Tambah PageHero |
| `Publik/Apbdes.vue` | Tambah PageHero |
| `Publik/Halaman.vue` | Tambah PageHero (pakai nama halaman statis) |
| `Publik/Profil.vue` | Hero Profil unik (logo + nama desa) — **tidak diubah** |

### 4. Tidak diubah
- **Welcome.vue** — hero unik dengan slider, tidak cocok dengan PageHero generik
- **Profil.vue** — hero dengan logo desa, layout spesial
- **PublicLayout.vue** — tidak perlu diubah

## PageHero Spec

```vue
<!-- Template -->
<section class="relative bg-gradient-to-br from-blue-600 to-indigo-900 py-12 md:py-16">
    <div class="absolute inset-0 bg-black/20"></div>
    <div class="relative mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
        <!-- Optional slot: icon -->
        <div v-if="$slots.icon" class="mb-3 flex justify-center">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-sm">
                <slot name="icon" />
            </div>
        </div>
        <h1 class="text-3xl font-bold text-white sm:text-4xl">{{ title }}</h1>
        <p v-if="description" class="mt-3 text-lg text-blue-100">{{ description }}</p>
    </div>
</section>
```

## Breadcrumb Spec

```vue
<nav class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
    <ol class="flex items-center gap-1.5 text-sm text-zinc-500 dark:text-zinc-400">
        <li><Link href="/" class="hover:text-zinc-700 dark:hover:text-zinc-200">Beranda</Link></li>
        <li v-for="item in items" :key="item.label" class="flex items-center gap-1.5">
            <span class="text-zinc-300 dark:text-zinc-600">/</span>
            <Link v-if="item.href" :href="item.href" class="hover:text-zinc-700 dark:hover:text-zinc-200">{{ item.label }}</Link>
            <span v-else class="text-zinc-700 dark:text-zinc-200">{{ item.label }}</span>
        </li>
    </ol>
</nav>
```

## Verifikasi

1. Buka setiap halaman publik — semua harus punya hero biru-indigo konsisten
2. Halaman detail (Show) harus punya breadcrumb (Beranda > Berita > Judul)
3. Welcome.vue dan Profil.vue tidak berubah
4. Build sukses
