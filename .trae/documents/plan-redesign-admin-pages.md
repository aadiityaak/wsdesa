# Plan: Redesign Semua Halaman Admin ke Desain Baru

> **Status:** Draft — Menunggu Persetujuan
> **Target:** Laravel 13 + Vue 3 + Inertia v3 + TailwindCSS v4
> **Tanggal:** 2026-07-18

---

## 1. Ringkasan (Summary)

Menerapkan desain baru yang sudah ada di halaman `/admin/profil`, `/admin/berita`, `/admin/agenda`, `/admin/komentar` ke **semua** halaman admin lainnya (~22 halaman). Desain baru mencakup hero banner, stats cards, search bar, card-based data display, empty state, custom pagination, scroll-to-top button, delete confirmation dialog, dan dark mode support — semua dengan aksen warna **rose**.

---

## 2. Analisis Kondisi Saat Ini (Current State)

### 2.1 Halaman yang SUDAH Desain Baru (Referensi)

| Halaman | File | Pola |
|---|---|---|
| Profil Desa | `Profil/Edit.vue` | Hero + tab + sections + TipTap + sticky save |
| Berita Index | `Berita/Index.vue` | Hero + stats (3 col) + search + card list + empty state + pagination + scroll-to-top + delete dialog |
| Berita Form | `Berita/Form.vue` | Hero + 2-col layout (main + sidebar) + TipTap |
| Agenda Index | `Agenda/Index.vue` | Sama dengan Berita Index, icon `Calendar`, stats 4 col |
| Agenda Form | `Agenda/Form.vue` | Sama dengan Berita Form + VueDatePicker |
| Komentar Index | `Komentar/Index.vue` | Sama dengan Berita Index, icon `MessageSquareText`, stats 3 col |
| Pemerintahan Staff | `Pemerintahan/StaffIndex.vue` | Hero + stats + card grid + form dialog (2-col) + VueDatePicker + delete |
| Dashboard | `Dashboard.vue` | Simple cards — perlu di-upgrade |

### 2.2 Design System yang Digunakan (Standar)

**Hero Banner:**
```html
<div class="relative mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-zinc-50 to-white px-6 py-10 shadow-sm ring-1 ring-zinc-100 dark:from-zinc-900 dark:to-zinc-950 dark:ring-zinc-800 sm:px-10 sm:py-12">
  <!-- decorative blur circles -->
  <div class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-rose-100/40 blur-3xl dark:bg-rose-900/10" />
  <div class="pointer-events-none absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-amber-100/30 blur-3xl dark:bg-amber-900/10" />
  <!-- header: icon box + title + description -->
  <div class="flex items-center gap-3">
    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400">
      <Icon class="h-6 w-6" />
    </div>
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-white sm:text-3xl">Title</h1>
      <p class="mt-0.5 text-sm text-zinc-500 dark:text-zinc-400">Description</p>
    </div>
  </div>
</div>
```

**Action Button (Primary):**
```html
<Button class="gap-2 rounded-full bg-rose-500 text-white shadow-sm hover:bg-rose-600">
  <Plus class="h-4 w-4" /> Tambah
</Button>
```

**Stats Cards:**
```html
<div class="rounded-xl border border-zinc-100 bg-white/60 px-4 py-3 text-center backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-900/60">
  <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ count }}</p>
  <p class="text-xs text-zinc-500 dark:text-zinc-400">Label</p>
</div>
```

**Search Bar:**
```html
<div class="relative flex-1 max-w-sm">
  <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
  <Input v-model="search" placeholder="Cari..." class="rounded-xl border-zinc-200 pl-9 focus:border-rose-300 focus:ring-rose-200 dark:border-zinc-700" @keyup.enter="performSearch" />
</div>
<Button variant="outline" class="rounded-xl" @click="performSearch">Cari</Button>
```

**Data Cards:**
```html
<div class="group rounded-2xl border border-zinc-100 bg-white p-5 shadow-sm transition-all duration-200 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
  <!-- content -->
</div>
```

**Empty State:**
```html
<div class="rounded-2xl border border-zinc-100 bg-white px-6 py-16 text-center shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
  <Icon class="mx-auto h-12 w-12 text-zinc-300 dark:text-zinc-600" />
  <p class="mt-4 text-base font-medium text-zinc-600 dark:text-zinc-400">Belum ada data</p>
  <p class="mt-1 text-sm text-zinc-400 dark:text-zinc-500">Deskripsi.</p>
</div>
```

**Custom Pagination:**
```html
<div class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-zinc-100 bg-white px-5 py-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:flex-row">
  <p class="text-sm text-zinc-500 dark:text-zinc-400">Menampilkan {{ from }}–{{ to }} dari {{ total }}</p>
  <div class="flex items-center gap-1">
    <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === 1" @click="goToPage(currentPage - 1)">Sebelumnya</Button>
    <!-- page numbers -->
    <Button variant="outline" size="sm" class="rounded-lg" :disabled="currentPage === lastPage" @click="goToPage(currentPage + 1)">Selanjutnya</Button>
  </div>
</div>
```

**Scroll to Top:**
```html
<Transition enter-active-class="transition duration-200 ease-out" enter-from-class="translate-y-2 opacity-0" leave-active-class="transition duration-150 ease-in" leave-to-class="translate-y-2 opacity-0">
  <button v-if="showScrollTop" class="fixed bottom-8 right-8 z-50 flex h-11 w-11 items-center justify-center rounded-full bg-zinc-900 text-white shadow-lg transition hover:scale-105 hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200" @click="scrollToTop">
    <ArrowUp class="h-5 w-5" />
  </button>
</Transition>
```

**Delete Dialog:**
```html
<Dialog :open="deleteConfirmId !== null" @update:open="() => (deleteConfirmId = null)">
  <DialogContent class="sm:max-w-md">
    <DialogHeader>
      <DialogTitle>Konfirmasi Hapus</DialogTitle>
      <DialogDescription>Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.</DialogDescription>
    </DialogHeader>
    <DialogFooter>
      <Button variant="outline" class="rounded-full" @click="deleteConfirmId = null">Batal</Button>
      <Button variant="destructive" class="rounded-full" @click="executeDelete">Hapus</Button>
    </DialogFooter>
  </DialogContent>
</Dialog>
```

**Warna Aksen:** Rose (`rose-100`, `rose-500`, `rose-600`, `focus:border-rose-300`, `focus:ring-rose-200`)

### 2.3 Halaman yang BELUM Desain Baru

Dari hasil eksplorasi, semua halaman "old design" menggunakan pola `Card > CardHeader > CardTitle` + `<table>` HTML, dengan pagination berbasis `links` dari Laravel backend. Berikut klasifikasinya:

#### Tipe A: "Index/List" dengan Data Cards (seperti Berita/Agenda/Komentar Index)

Halaman-halaman ini menggunakan card-based layout untuk menampilkan data. Transformasi: tambahkan hero banner, stats, search, perbaiki styling cards, ganti pagination.

| # | Halaman | File | Catatan Khusus |
|---|---|---|---|
| 1 | Lembaga | `Pemerintahan/LembagaIndex.vue` | Grid cards + sub-view tabel anggota. Perlu hero + stats. |
| 2 | Galeri | `Galeri/Index.vue` | Grid cards + foto management dialog. Sudah card-based tapi tanpa hero. |
| 3 | Galeri Video | `Galeri/Video.vue` | List/grid video. Perlu dicek dulu. |

#### Tipe B: "Index/List" dengan Data Table

Halaman-halaman ini menggunakan `<table>` HTML. Transformasi: tambahkan hero banner, stats, search, perbaiki styling table atau konversi ke cards, ganti pagination.

| # | Halaman | File | Catatan Khusus |
|---|---|---|---|
| 4 | Layanan Surat | `LayananSurat/Index.vue` | Table + detail dialog + status update dialog |
| 5 | Layanan Surat Kategori | `LayananSurat/Kategori.vue` | Table kategori surat |
| 6 | Pengaduan | `Pengaduan/Index.vue` | Table + detail dialog + status update + reply |
| 7 | Kependudukan Penduduk | `Kependudukan/PendudukIndex.vue` | Table + search + export/import. Sudah punya custom pagination. |
| 8 | Kependudukan KK | `Kependudukan/KKIndex.vue` | Table KK |
| 9 | Kependudukan Mutasi | `Kependudukan/MutasiIndex.vue` | Table mutasi |
| 10 | UMKM | `Umkm/Index.vue` | Table + dialog CRUD + foto upload |
| 11 | Potensi | `Potensi/Index.vue` | Table + dialog CRUD |
| 12 | APBDes | `Apbdes/Index.vue` | Table + dialog CRUD |
| 13 | Pembangunan | `Pembangunan/Index.vue` | Table + dialog CRUD + foto upload |
| 14 | Halaman | `Halaman/Index.vue` | Table + dialog CRUD |
| 15 | Slider | `Slider/Index.vue` | Table/grid + dialog CRUD + sort |
| 16 | Pengumuman | `Pengumuman/Index.vue` | Table + dialog CRUD |
| 17 | Download | `Download/Index.vue` | Table + upload dialog |
| 18 | Pengguna | `Pengguna/Index.vue` | Table pengguna |
| 19 | Kategori | `Kategori/Index.vue` | Shared: berita & agenda. Table + dialog CRUD |

#### Tipe C: Detail/Show Pages

| # | Halaman | File | Catatan Khusus |
|---|---|---|---|
| 20 | Layanan Surat Detail | `LayananSurat/Show.vue` | Detail view + status update |
| 21 | Pengaduan Detail | `Pengaduan/Show.vue` | Detail view + balasan + status |
| 22 | KK Detail | `Kependudukan/KKShow.vue` | Detail KK + daftar anggota |

#### Tipe D: Dashboard

| # | Halaman | File | Catatan Khusus |
|---|---|---|---|
| 23 | Dashboard | `Dashboard.vue` | Perlu hero + stats upgrade |

---

## 3. Rencana Perubahan (Proposed Changes)

### 3.1 Strategi Umum

Untuk setiap halaman, lakukan perubahan berikut sesuai tipenya:

**Semua halaman Index:**
1. Tambahkan **hero banner** dengan gradient + decorative blur circles + icon box + judul + deskripsi
2. Tambahkan **stats cards** (minimal: total data)
3. Tambahkan **search bar** dengan styling rose
4. Ganti tampilan data: table-based dikonversi ke atau dipercantik card-based styling
5. Tambahkan **empty state** yang didesain dengan baik
6. Ganti pagination dari `links` (HTML-based) ke **custom button-based pagination** (atau pertahankan yang sudah ada jika sudah custom)
7. Tambahkan **scroll-to-top button**
8. Styling **delete dialog** dengan tombol `rounded-full`
9. Styling tombol aksi dengan `gap-2 rounded-full bg-rose-500`

**Halaman Detail/Show:**
1. Tambahkan hero banner
2. Perbaiki styling konten

**Halaman Form (halaman terpisah — seperti Berita/Form.vue):**
1. Hero banner dengan back button (ArrowLeft, rounded-full ghost button)
2. Layout 2 kolom: konten utama + sidebar (thumbnail, kategori, status, tombol submit)
3. Input `rounded-xl border-zinc-200 focus:border-rose-300 focus:ring-rose-200`
4. Tombol submit `rounded-full bg-rose-500`

**Dashboard:**
1. Tambahkan hero banner
2. Perbaiki stat cards styling

### 3.2 Urutan Pengerjaan (Prioritas)

Pengerjaan dikelompokkan berdasarkan kompleksitas dan dependensi, dari yang paling sederhana:

#### Batch 1 — Sederhana (Table-based Index dengan sedikit fitur)
1. **Kategori/Index.vue** — Tabel CRUD sederhana
2. **Halaman/Index.vue** — Tabel CRUD sederhana
3. **Potensi/Index.vue** — Tabel CRUD sederhana
4. **Download/Index.vue** — Tabel + upload
5. **Pengumuman/Index.vue** — Tabel CRUD
6. **Pengguna/Index.vue** — Tabel pengguna
7. **Slider/Index.vue** — Tabel/grid + upload gambar
8. **LayananSurat/Kategori.vue** — Kategori surat

#### Batch 2 — Menengah (Table-based dengan dialog detail/status)
9. **Pengaduan/Index.vue** — Table + detail + status
10. **Pengaduan/Show.vue** — Detail view
11. **LayananSurat/Index.vue** — Table + detail + status + cetak
12. **LayananSurat/Show.vue** — Detail view

#### Batch 3 — Kompleks (Card-based atau banyak fitur)
13. **Galeri/Index.vue** — Cards + foto management
14. **Galeri/Video.vue** — Video management
15. **Umkm/Index.vue** — Table + foto upload
16. **Pembangunan/Index.vue** — Table + foto upload
17. **APBDes/Index.vue** — Table CRUD

#### Batch 4 — Kependudukan (Table + search + pagination khusus)
18. **Kependudukan/PendudukIndex.vue** — Sudah punya custom pagination, perlu hero + stats + perbaikan styling
19. **Kependudukan/KKIndex.vue** — Tabel KK
20. **Kependudukan/KKShow.vue** — Detail KK
21. **Kependudukan/MutasiIndex.vue** — Tabel mutasi

#### Batch 5 — Pemerintahan & Dashboard
22. **Pemerintahan/LembagaIndex.vue** — Grid + sub-view (perlu penanganan khusus)
23. **Dashboard.vue** — Hero + stat cards upgrade

### 3.3 Detail Perubahan Per Halaman

#### 3.3.1 Kategori/Index.vue
- **Saat ini:** Card > CardHeader > CardTitle > Table + `links` pagination
- **Perubahan:** Tambah hero banner (icon: `Tag`/`Folder`), stats (total kategori), search, konversi table ke cards atau perbaiki styling table, empty state, custom pagination, scroll-to-top, delete dialog rounded-full
- **Props yang diperiksa:** `categories` (Paginated)

#### 3.3.2 Halaman/Index.vue
- **Saat ini:** Card > Table + dialog CRUD + `links` pagination
- **Perubahan:** Hero (icon: `File`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top, delete dialog

#### 3.3.3 Potensi/Index.vue
- **Saat ini:** Card > Table + dialog CRUD + `links` pagination
- **Perubahan:** Hero (icon: `TrendingUp`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top, delete dialog

#### 3.3.4 Download/Index.vue
- **Saat ini:** Card > Table + upload dialog + `links` pagination
- **Perubahan:** Hero (icon: `Download`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top, delete dialog

#### 3.3.5 Pengumuman/Index.vue
- **Saat ini:** Card > Table + dialog CRUD + `links` pagination
- **Perubahan:** Hero (icon: `Megaphone`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top, delete dialog

#### 3.3.6 Pengguna/Index.vue
- **Saat ini:** Card > Table + `links` pagination
- **Perubahan:** Hero (icon: `UsersRound`), stats (total user), search, perbaiki table styling, empty state, pagination, scroll-to-top

#### 3.3.7 Slider/Index.vue
- **Saat ini:** Table/grid + dialog CRUD + upload + sort
- **Perubahan:** Hero (icon: `SlidersHorizontal`), stats (total slider), perbaiki grid cards styling, empty state, scroll-to-top, delete dialog
- **Catatan:** Slider mungkin tidak menggunakan pagination (collection, bukan paginated)

#### 3.3.8 LayananSurat/Kategori.vue
- **Saat ini:** Mirip Kategori berita/agenda
- **Perubahan:** Hero (icon: `FileText`), stats, search, perbaiki styling, pagination, scroll-to-top, delete dialog

#### 3.3.9 Pengaduan/Index.vue
- **Saat ini:** Card > Table + detail dialog + status dialog + `links` pagination
- **Perubahan:** Hero (icon: `MessageSquare`), stats (total, baru, diproses, selesai), search, perbaiki table styling, empty state, pagination, scroll-to-top

#### 3.3.10 Pengaduan/Show.vue
- **Saat ini:** Detail view sederhana
- **Perubahan:** Hero banner dengan back button + info pengaduan, perbaiki styling detail + balasan

#### 3.3.11 LayananSurat/Index.vue
- **Saat ini:** Card > Table + detail dialog + status dialog + `links` pagination
- **Perubahan:** Hero (icon: `FileText`), stats (total, pending, diproses, selesai), search, perbaiki table styling, empty state, pagination, scroll-to-top

#### 3.3.12 LayananSurat/Show.vue
- **Saat ini:** Detail view
- **Perubahan:** Hero banner, perbaiki styling

#### 3.3.13 Galeri/Index.vue
- **Saat ini:** Grid cards + dialog CRUD + foto management + video dialog + `links` pagination
- **Perubahan:** Hero (icon: `Images`), stats, search, perbaiki card styling (rounded-2xl, shadow-sm, hover:shadow-md), empty state, pagination, scroll-to-top, delete dialog rounded-full

#### 3.3.14 Galeri/Video.vue
- **Saat ini:** Video management
- **Perubahan:** Hero (icon: `Film`/`Video`), stats, styling, scroll-to-top

#### 3.3.15 Umkm/Index.vue
- **Saat ini:** Table + dialog CRUD + foto upload + `links` pagination
- **Perubahan:** Hero (icon: `Store`), stats, search, perbaiki table/card styling, empty state, pagination, scroll-to-top, delete dialog, dialog form input rounded-xl

#### 3.3.16 Pembangunan/Index.vue
- **Saat ini:** Table + dialog CRUD + foto upload + `links` pagination
- **Perubahan:** Hero (icon: `Construction`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top, delete dialog

#### 3.3.17 APBDes/Index.vue
- **Saat ini:** Table + dialog CRUD + `links` pagination
- **Perubahan:** Hero (icon: `Wallet`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top, delete dialog

#### 3.3.18 Kependudukan/PendudukIndex.vue
- **Saat ini:** Card > Table + search + export/import + dialog CRUD + custom pagination (sudah bagus)
- **Perubahan:** Hero (icon: `Users`), stats (total penduduk, L/P aggregation), perbaiki table styling, scroll-to-top, delete dialog rounded-full
- **Catatan:** Sudah punya custom pagination — pertahankan. Search juga sudah ada — perbaiki styling.

#### 3.3.19 Kependudukan/KKIndex.vue
- **Saat ini:** Table + `links` pagination
- **Perubahan:** Hero (icon: `Users`/`BookOpen`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top, delete dialog

#### 3.3.20 Kependudukan/KKShow.vue
- **Saat ini:** Detail KK + daftar anggota
- **Perubahan:** Hero banner dengan back button, perbaiki styling

#### 3.3.21 Kependudukan/MutasiIndex.vue
- **Saat ini:** Table + `links` pagination
- **Perubahan:** Hero (icon: `ArrowRightLeft`), stats, search, perbaiki table styling, empty state, pagination, scroll-to-top

#### 3.3.22 Pemerintahan/LembagaIndex.vue
- **Saat ini:** Grid cards lembaga (card-based) + sub-view tabel anggota + dialog CRUD
- **Perubahan:** Hero (icon: `Building2`/`Landmark`), stats, search, perbaiki card styling (rounded-2xl, hover:shadow-md), empty state, scroll-to-top, delete dialog rounded-full
- **Catatan:** Perlu hati-hati karena ada nested view (lembaga list vs anggota)

#### 3.3.23 Dashboard.vue
- **Saat ini:** Simple heading + 4 stat cards
- **Perubahan:** Hero banner + perbaiki stat cards (rounded-xl, backdrop-blur border)

### 3.4 Perubahan yang Tidak Dilakukan
- **Galeri/Video.vue**: Perlu dicek apakah file ini standalone atau bagian dari Galeri/Index.vue. Jika standalone, tambahkan desain baru.
- **LayananSurat/Show.vue** dan **Pengaduan/Show.vue**: Mungkin perlu dicek apakah halaman ini standalone atau tidak.
- Tidak mengubah struktur navigasi sidebar.
- Tidak mengubah layout atau komponen shared.
- Tidak membuat komponen baru — semua styling dilakukan inline mengikuti pola yang sudah ada.

---

## 4. Asumsi & Keputusan

1. **Pola Desain**: Semua halaman mengikuti pola yang sama seperti Berita/Agenda/Komentar Index.
2. **CRUD Pattern (UPDATE)**: Semua modul CRUD menggunakan **halaman Form terpisah** (`Index.vue` + `Form.vue`) seperti Berita, BUKAN popup dialog. Ini memerlukan:
   - Penambahan route `create` dan `edit` di `routes/admin.php`
   - Pembuatan file `Form.vue` baru untuk setiap modul
   - Update `Index.vue` — tombol "Tambah" jadi `<a>` link ke form, tombol "Edit" jadi link ke form edit
3. **Warna Aksen**: Tetap **rose** untuk semua halaman admin.
4. **Pagination**: Semua halaman yang menggunakan `links` (HTML-based) akan dikonversi ke custom button-based pagination.
5. **Table vs Cards**: Halaman dengan data tabular tetap menggunakan table dengan styling baru.
6. **Scroll to Top**: Ditambahkan ke semua halaman index.
7. **Delete Dialog**: Dipertahankan di Index.vue (via router.delete) dengan tombol `rounded-full`.
8. **Dark Mode**: Semua perubahan mendukung dark mode.
9. **Backend**: Route `admin.php` perlu ditambah `create` & `edit` untuk modul yang belum punya. Controller perlu method `create()` dan `edit()`.
10. **Icon**: Menggunakan icon dari `lucide-vue` yang relevan.

---

## 5. Verifikasi

Setelah semua perubahan selesai:
- [ ] Setiap halaman admin menampilkan hero banner dengan gradient, decorative blur circles, icon box, dan judul
- [ ] Setiap halaman memiliki stats cards (minimal total)
- [ ] Search bar menggunakan styling rose (`rounded-xl`, `focus:border-rose-300`, `focus:ring-rose-200`)
- [ ] Data cards/table menggunakan styling baru (`rounded-2xl`, `shadow-sm`, `hover:shadow-md`)
- [ ] Empty state didesain dengan baik (icon besar, teks deskriptif)
- [ ] Pagination menggunakan custom button-based (jika ada pagination)
- [ ] Scroll-to-top button muncul saat scroll > 400px
- [ ] Delete confirmation dialog menggunakan tombol `rounded-full`
- [ ] Tombol aksi primer menggunakan `rounded-full bg-rose-500`
- [ ] Dark mode berfungsi di semua halaman
- [ ] Tidak ada error di browser console
- [ ] Semua fungsionalitas CRUD tetap berjalan

---

## 6. Catatan Implementasi

1. **Baca file dulu** — Sebelum mengedit setiap file, baca dulu isinya untuk memahami props, data structure, dan logic yang ada.
2. **Pertahankan logic** — Jangan mengubah logic JavaScript/TypeScript, hanya modifikasi template dan style.
3. **Gunakan SearchReplace** — Gunakan tool SearchReplace untuk modifikasi yang presisi.
4. **Satu file per batch** — Kerjakan satu file sampai selesai sebelum pindah ke file berikutnya.
5. **Tes setelah selesai** — Setelah selesai, pastikan tidak ada error build (tidak perlu menjalankan `npm run build`, cukup pastikan tidak ada syntax error).
