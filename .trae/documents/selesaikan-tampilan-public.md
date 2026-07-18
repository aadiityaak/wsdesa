# Plan: Selesaikan Tampilan Public Visitor

## Ringkasan

Menyelesaikan halaman publik untuk pengunjung website Desa Digital. Beberapa halaman sudah ada tapi belum terhubung (routing, controller), ada bug controller, mobile navbar tidak berfungsi, dan halaman beranda (`/`) masih menggunakan template default Laravel.

---

## 1. Current State Analysis

### Rute publik bermasalah:
| Route | Masalah |
|-------|---------|
| `GET /` | Masih `Welcome.vue` (default Laravel), **tidak pakai PublicLayout** |
| `GET /profil` | **Tidak terdefinisi** di `web.php` — Controller & Page sudah ada |
| `GET /pemerintahan` | **Tidak terdefinisi** di `web.php` — Controller & Page sudah ada |
| `GET /kontak` | **Tidak terdefinisi** — Page sudah ada, Controller belum ada |
| `GET /halaman/{slug}` | **Bug:** pakai `GaleriController::page` — perlu controller sendiri |
| `GET /download` | **Bug:** pakai `GaleriController::downloads` — perlu controller sendiri |

### Masalah lain:
- **Mobile navbar** di PublicLayout: `@click="() => {}"` — tidak ada menu dropdown
- **Welcome.vue** tidak menggunakan PublicLayout, kontennya placeholder Laravel
- **Kontak.vue** form submit hanya state lokal, tidak kirim ke server
- **Announcement** (pengumuman) tidak ada halaman publik
- **Hero slider** tidak dipakai di homepage

### Yang sudah benar:
- PublicLayout.vue — struktur navbar + footer OK
- Semua halaman `Publik/*.vue` sudah dibuat dengan layout proper
- Controller `Publik\ProfilController`, `Publik\PemerintahanController` sudah siap
- Model Slider, Announcement, Post, Event, Gallery, dll sudah siap

---

## 2. Proposed Changes

### A. Halaman Beranda (Homepage) — Ganti Welcome.vue
**File:** `resources/js/pages/Welcome.vue` → **tulis ulang**
**Layout:** Pakai `PublicLayout` (ubah resolver di app.ts)
**Data dari server:** Slider (hero), Post terbaru, Event terbaru, Layanan cepat statis
**Konten:**
- Hero section dengan slider dari DB
- Section Layanan Cepat (kartu: Pengajuan Surat, Pengaduan, Cek Status)
- Section Berita Terbaru (3-4 kartu)
- Section Agenda Mendatang (3-4 kartu)
- Section Galeri/UMKM highlight
- CTA Footer dalam section

### B. Perbaiki Layout Resolver
**File:** `resources/js/app.ts`
- Ubah `case name === 'Welcome': return null;` → `case name === 'Welcome': return PublicLayout;`

### C. Tambah Route Publik yang Hilang
**File:** `routes/web.php`
- Tambah `Route::get('/profil', [ProfilController::class, 'index'])->name('profil.index');`
- Tambah `Route::get('/pemerintahan', [PemerintahanController::class, 'index'])->name('pemerintahan.index');`
- Tambah `Route::get('/kontak', [KontakController::class, 'index'])->name('kontak.index');`
- Tambah `Route::post('/kontak', [KontakController::class, 'store'])->name('kontak.store');`

### D. Buat KontakController Publik
**File:** `app/Http/Controllers/Publik/KontakController.php` (baru)
- `index()`: render `Publik/Kontak`
- `store()`: validasi input, simpan ke database (tabel `contacts` atau simpan di session flash), redirect balik

### E. Perbaiki Bug Controller untuk /halaman & /download
**File:** `routes/web.php`
- Pindah `Route::get('/halaman/{slug}', ...)` ke controller yang tepat
- Buat `app/Http/Controllers/Publik/PageController.php`
- Buat `app/Http/Controllers/Publik/DownloadController.php` (atau pakai existing DownloadController)
- Pindahkan method `page()` dari GaleriController ke PageController
- Pindahkan method `downloads()` dari GaleriController ke DownloadController

### F. Mobile Navbar Fungsional
**File:** `resources/js/layouts/PublicLayout.vue`
- Tambah state `isMobileMenuOpen` ref
- Ubah `@click="() => {}"` jadi `@click="isMobileMenuOpen = !isMobileMenuOpen"`
- Tambah mobile menu dropdown di bawah navbar

### G. Ubah Pagination Link ke Inertia Link
**File:** `resources/js/pages/Publik/Berita/Index.vue` (dan halaman publik lain yg pakai pagination)
- Ganti tag `<a>` di pagination dengan `<Link>` Inertia

### H. (Opsional) Halaman Pengumuman Publik
**File:** `routes/web.php` + `resources/js/pages/Publik/Pengumuman.vue` (baru)
- Route `/pengumuman` → `Publik/Pengumuman` page
- Tampilkan daftar announcement aktif

---

## 3. File Changes Summary

| # | File | Action | What |
|---|------|--------|------|
| 1 | `routes/web.php` | Edit | Tambah route profil, pemerintahan, kontak, perbaiki page & download |
| 2 | `resources/js/app.ts` | Edit | Ubah layout Welcome → PublicLayout |
| 3 | `resources/js/pages/Welcome.vue` | **Tulis ulang** | Homepage desa dengan hero slider, berita, agenda, layanan |
| 4 | `resources/js/layouts/PublicLayout.vue` | Edit | Mobile navbar fungsional |
| 5 | `app/Http/Controllers/Publik/KontakController.php` | **Baru** | Handle kontak page + form submit |
| 6 | `app/Http/Controllers/Publik/PageController.php` | **Baru** | Handle halaman statis (dari model Page) |
| 7 | `app/Http/Controllers/Publik/DownloadController.php` | **Baru** | Handle download page |
| 8 | `app/Models/Contact.php` | **Baru** | Model untuk pesan kontak (jika perlu simpan ke DB) |
| 9 | `resources/js/pages/Publik/Berita/Index.vue` | Edit | Pagination pakai Inertia Link |

---

## 4. Assumptions & Decisions

- **Homepage slider**: ambil dari model `Slider::active()->get()` via Inertia share atau langsung di controller Welcome (sebelum ini Welcome hanya static inertia page, perlu diubah jadi controller-backed page)
- **Welcome route**: Ubah dari `Route::inertia('/', 'Welcome')` jadi `Route::get('/', [WelcomeController::class, 'index'])->name('home')` agar bisa kirim data.
- **Kontak form**: Simpan ke DB pakai model `Contact` (migration + model baru minimalis)
- **Tidak buat migration baru untuk slider/berita/agenda** — semua sudah ada di DB
- **Tidak mengubah controller GaleriController** yang sudah ada — hanya pindahkan method yg salah tempat

---

## 5. Verification

1. `php artisan route:list --name=home,profil,pemerintahan,kontak,page,download` — pastikan semua route terdaftar
2. `php artisan test --compact —filter=Welcome` — pastikan test untuk homepage jalan
3. Buka `/` — lihat hero slider, berita terbaru, agenda
4. Buka `/profil` — lihat data profil desa
5. Buka `/pemerintahan` — lihat staff & lembaga
6. Buka `/kontak` — lihat form, submit test
7. Test mobile navbar — hamburger menu muncul di viewport kecil
8. `vendor/bin/pint --format agent` — fix formatting
