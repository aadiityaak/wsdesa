# PRD & Rencana Implementasi — Website Desa (SID)

> **Status:** Draft — Menunggu Review  
> **Target:** Laravel 13 + Vue 3 + Inertia v3 + TailwindCSS v4  
> **Standar Acuan:** Sistem Informasi Desa (SID) Kemendagri  
> **Tipe:** Public Website + Admin Panel

---

## 1. Ringkasan Eksekutif

Membangun website desa lengkap berbasis **Laravel 13** dengan **Inertia v3 + Vue 3 + TailwindCSS v4**, mengacu pada standar **SID Kemendagri**. Website terdiri dari dua sisi:

1. **Website Publik** — diakses warga dan masyarakat umum, menampilkan profil desa, berita, data, galeri, dan layanan.
2. **Panel Admin** — diakses petugas desa (Kepala Desa, Sekdes, Kasi, Kaur, operator) untuk mengelola seluruh konten.

---

## 2. Kondisi Saat Ini (Baseline)

Project ini adalah **Laravel 13 starter kit** dengan:

| Komponen | Status |
|---|---|
| Autentikasi (Fortify) | Sudah ada: login, register, forgot/reset password, 2FA, email verifikasi |
| UI Component Library | Sudah ada: Reka UI (shadcn-style) ~90+ komponen |
| Layout | Sudah ada: AppLayout (sidebar/header), AuthLayout |
| Database | Fresh — baru tabel users, sessions, cache, jobs |
| Role System | Belum ada |
| Model Bisnis | Belum ada satupun |

**Yang perlu dibangun dari nol:** seluruh model bisnis, migrasi, controller, API resource, admin panel, halaman publik, dan seeder.

---

## 3. Arsitektur Umum

```
┌─────────────────────────────────────────────────┐
│                  Web Desa                       │
├────────────────────┬────────────────────────────┤
│   Publik (Guest)   │    Admin (Authenticated)    │
│   / (halaman)      │    /admin/*                 │
│   /berita/*        │                             │
│   /layanan/*       │                             │
│   /galeri/*        │                             │
│   /profil          │                             │
│   /umkm            │                             │
│   /statistik       │                             │
│   /kontak          │                             │
└────────────────────┴────────────────────────────┘
```

Teknologi: Laravel 13 (backend), Inertia v3 + Vue 3 (frontend), TailwindCSS v4 + Reka UI (UI), SQLite/MySQL (database).

---

## 4. Role & Permission System

| Role | Deskripsi | Akses |
|---|---|---|
| `super_admin` | Developer / pengelola pusat | Semua akses |
| `kepala_desa` | Kepala Desa | Semua modul, approval |
| `sekdes` | Sekretaris Desa | Semua modul kecuali approval tertentu |
| `kasi_pemerintahan` | Kasi Pemerintahan | Pemerintahan, kependudukan, surat |
| `kasi_kesra` | Kasi Kesejahteraan | Kemasyarakatan, bansos, pembangunan |
| `kasi_pelayanan` | Kasi Pelayanan | Layanan surat, pengaduan |
| `kaur_umum` | Kaur Umum | Berita, galeri, inventaris, UMKM |
| `kaur_keuangan` | Kaur Keuangan | APBDes, keuangan |
| `operator` | Operator | Entry data dasar |

---

## 5. Modul & Fitur

### 5.1 Modul Umum / Profil Desa

**Tabel:** `profiles` (singleton — 1 record per desa)

| Fitur | Publik | Admin |
|---|---|---|
| Data dasar desa (nama, kode, alamat, kontak, koordinat) | Tampil | CRUD |
| Visi & Misi | Tampil | CRUD |
| Sejarah desa | Tampil | CRUD |
| Struktur organisasi | Tampil | CRUD |
| Geografi (luas, batas, orbitasi) | Tampil | CRUD |
| Demografi umum (jumlah penduduk, KK, agama, pekerjaan) | Tampil | Read + update via data penduduk |
| Logo desa | Tampil | Upload |
| Sosial media links | Tampil | CRUD |

### 5.2 Modul Pemerintahan

**Tabel:** `government_staff`

| Fitur | Publik | Admin |
|---|---|---|
| Daftar perangkat desa (nama, jabatan, foto, NIP) | Tampil | CRUD |
| Bagan struktur organisasi visual | Tampil | Auto-generate |
| BPD (Badan Permusyawaratan Desa) | Tampil | CRUD |
| LPM (Lembaga Pemberdayaan Masyarakat) | Tampil | CRUD |
| PKK / Karang Taruna / Lembaga Kemasyarakatan lain | Tampil | CRUD |
| Masa jabatan | — | CRUD |

### 5.3 Modul Berita & Informasi

**Tabel:** `posts` + `post_categories` + `post_tags`

| Fitur | Publik | Admin |
|---|---|---|
| Daftar berita (pagination, search) | Tampil | CRUD |
| Kategori berita | Filter | CRUD |
| Tag / label | Filter | CRUD |
| Berita populer (berdasarkan views) | Sidebar | View count |
| Rich text editor (wysiwyg) | — | TinyMCE / TipTap |
| Upload gambar thumbnail | Auto-resize | CRUD |
| Status publish / draft / archive | — | CRUD |
| Share ke media sosial | Button | — |
| Komentar berita | Baca/tulis | Moderate |
| RSS Feed | Auto-generate | — |

### 5.4 Modul Agenda / Kegiatan

**Tabel:** `events`

| Fitur | Publik | Admin |
|---|---|---|
| Kalender kegiatan desa | Tampil | CRUD |
| Jenis kegiatan: musyawarah, gotong royong, pelatihan, keagamaan, dll | Filter | CRUD |
| Tanggal, waktu, lokasi, deskripsi | Tampil | CRUD |
| Status: upcoming, ongoing, done | Tampil | CRUD |

### 5.5 Modul Kependudukan

**Tabel:** `residents` + `families` + `resident_members`

| Fitur | Publik | Admin |
|---|---|---|
| Statistik agregat penduduk (per dusun, usia, gender, agama, pekerjaan, pendidikan) | Tampil | Dashboard |
| Data penduduk per KK | — | CRUD |
| Kartu Keluarga (KK) — no_kk, alamat, anggota | — | CRUD |
| Data individu: NIK, nama, tempat/tgl lahir, gender, agama, status kawin, pekerjaan, pendidikan, gol darah, dusun, RT/RW | — | CRUD |
| Data kelahiran | — | CRUD |
| Data kematian | — | CRUD |
| Data pindah masuk/keluar | — | CRUD |
| Import / Export Excel | — | Import/Export |
| Cetak data | — | Print |

### 5.6 Modul Layanan Surat

**Tabel:** `letter_templates` + `letter_requests`

| Fitur | Publik | Admin |
|---|---|---|
| Form pengajuan surat online | Submit | Manage |
| Jenis surat: SK Domisili, SKTM, SKU, SKCK, SKP, Keterangan Lahir, Keterangan Kematian, Ahli Waris, Nikah, Pengantar Perwalian, dll | Daftar | CRUD template |
| Status pengajuan: pending, diproses, selesai, ditolak | Cek status | Update status |
| Tracking nomor surat | — | Auto-generate |
| Cetak surat (PDF) | — | Generate & cetak |
| Notifikasi status pengajuan | Via halaman | Via dashboard |
| Persyaratan dokumen | Info | CRUD |

### 5.7 Modul Galeri

**Tabel:** `galleries` + `gallery_images`

| Fitur | Publik | Admin |
|---|---|---|
| Album foto kegiatan | Tampil (grid) | CRUD album |
| Upload multiple foto | — | Upload + crop |
| Kategori album | Filter | CRUD |
| Video embed (YouTube) | Tampil | CRUD |
| Lightbox viewer | Ya | — |

### 5.8 Modul Pengaduan / Aspirasi

**Tabel:** `complaints`

| Fitur | Publik | Admin |
|---|---|---|
| Form pengaduan warga | Submit | Manage |
| Kategori pengaduan: infrastruktur, pelayanan, keamanan, lingkungan, lainnya | Pilih | Filter |
| Status: baru, diproses, selesai, ditolak | Cek status | Update |
| Anonim / dengan identitas | Pilih | — |
| Balasan / tindak lanjut | Lihat | CRUD |
| Notifikasi ke admin | — | Notifikasi |

### 5.9 Modul UMKM & Potensi Desa

**Tabel:** `umkm` + `umkm_categories`

| Fitur | Publik | Admin |
|---|---|---|
| Direktori UMKM desa | Tampil (grid) | CRUD |
| Nama usaha, pemilik, alamat, kontak, deskripsi, foto produk | Tampil | CRUD |
| Kategori: kuliner, kerajinan, jasa, pertanian, fashion, dll | Filter | CRUD |
| Galeri produk | Tampil | Upload multiple |
| Peta lokasi (embed) | Tampil | Input koordinat |

### 5.10 Modul Potensi Desa / SDA

**Tabel:** `potentials` + `potential_categories`

| Fitur | Publik | Admin |
|---|---|---|
| Potensi wisata | Tampil | CRUD |
| Potensi pertanian / perkebunan | Tampil | CRUD |
| Potensi perikanan / peternakan | Tampil | CRUD |
| Potensi tambang / energi | Tampil | CRUD |
| Statistik dan grafik | Tampil | Dashboard |

### 5.11 Modul APBDes / Keuangan

**Tabel:** `budgets` + `budget_categories`

| Fitur | Publik | Admin |
|---|---|---|
| APBDes tahunan (ringkasan) | Tampil grafik | CRUD |
| Pendapatan desa | Tampil | CRUD |
| Belanja desa | Tampil | CRUD |
| Pembiayaan | Tampil | CRUD |
| Realisasi per kegiatan | Tampil | CRUD |
| Download PDF laporan | Download | Generate |
| Filter per tahun | Ya | Ya |

### 5.12 Modul Pembangunan

**Tabel:** `developments`

| Fitur | Publik | Admin |
|---|---|---|
| Daftar proyek pembangunan | Tampil | CRUD |
| Lokasi, anggaran, sumber dana, tahun | Tampil | CRUD |
| Status: rencana, berjalan, selesai | Filter | CRUD |
| Foto progress | Tampil | Upload |
| Peta lokasi | Tampil | Input koordinat |

### 5.13 Modul Halaman Statis

**Tabel:** `pages`

| Fitur | Publik | Admin |
|---|---|---|
| Halaman statis (Tentang, Kontak, FAQ, dll) | Tampil | CRUD |
| Rich text editor | — | Ya |
| Slug URL | Auto | Customizable |

### 5.14 Modul Slider / Hero

**Tabel:** `sliders`

| Fitur | Publik | Admin |
|---|---|---|
| Carousel hero di homepage | Tampil | CRUD |
| Gambar, judul, deskripsi, link | — | CRUD |
| Urutan tampil | — | Drag & drop |

### 5.15 Modul Pengumuman

**Tabel:** `announcements`

| Fitur | Publik | Admin |
|---|---|---|
| Pengumuman penting (teks berjalan / banner) | Tampil | CRUD |
| Masa berlaku (tanggal mulai - selesai) | — | CRUD |
| Urgensi: info, warning, urgent | Badge | CRUD |

### 5.16 Modul Download / Arsip

**Tabel:** `downloads`

| Fitur | Publik | Admin |
|---|---|---|
| File publik yang bisa diunduh | Download | CRUD |
| Kategori: perdes, LPJ, laporan, form, dll | Filter | CRUD |
| Hitung jumlah download | — | Counter |

### 5.17 Modul Statistik & Dashboard

| Fitur | Publik | Admin |
|---|---|---|
| Dashboard statistik admin (chart jumlah penduduk, pengaduan, surat, dll) | — | Ya |
| Grafik kependudukan | Ya (agregat) | Ya (detail) |
| Grafik APBDes | Ya | Ya |
| Grafik pengaduan / layanan | — | Ya |

### 5.18 Modul Pengunjung / Visitor Counter

**Tabel:** `visitors` + `page_views`

| Fitur | Publik | Admin |
|---|---|---|
| Hitung pengunjung harian | — | Stats |
| Sumber traffic (direct, search, social) | — | Stats |
| Halaman paling banyak dikunjungi | — | Stats |

### 5.19 Modul Pencarian

| Fitur | Publik | Admin |
|---|---|---|
| Pencarian global (berita, halaman, galeri, dll) | Ya | — |
| Hasil dengan highlight | Ya | — |
| Filter per kategori | Ya | — |

---

## 6. Skema Database (Rinci)

### 6.1 Tabel Baru yang Diperlukan

```
profiles             — data dasar desa (singleton)
government_staff     — perangkat desa
institutions         — BPD, LPM, PKK, Karang Taruna, dll
institution_members  — anggota lembaga

post_categories      — kategori berita
posts                — berita / artikel
post_tags            — tag (polymorphic or pivot)
comments             — komentar berita

events               — agenda / kegiatan
event_categories     — kategori kegiatan

families             — kartu keluarga (KK)
residents            — data penduduk per individu
resident_mutations   — mutasi (lahir, mati, pindah)

letter_categories    — kategori/jenis surat
letter_templates     — template surat
letter_requirements  — persyaratan per jenis surat
letter_requests      — pengajuan surat
letter_tracking      — tracking surat keluar

galleries            — album galeri
gallery_images       — foto dalam album
videos               — video embed

complaints           — pengaduan
complaint_categories — kategori pengaduan
complaint_replies    — balasan/tindak lanjut

umkm_categories      — kategori UMKM
umkm                 — data UMKM
umkm_images          — foto produk UMKM

potential_categories — kategori potensi
potentials           — potensi desa/SDA

budget_categories    — kategori anggaran
budgets              — data APBDes

development_categories — kategori pembangunan
developments         — proyek pembangunan
development_images   — foto progress

pages                — halaman statis

sliders              — hero slider

announcements        — pengumuman

download_categories  — kategori download
downloads            — file unduhan

visitors             — data kunjungan
page_views           — halaman yang dilihat
```

---

## 7. Rencana Route

### 7.1 Route Publik (Guest — `routes/web.php`)

```
GET   /                            — Homepage
GET   /profil                      — Profil Desa
GET   /pemerintahan                — Pemerintahan
GET   /berita                      — List Berita
GET   /berita/{slug}               — Detail Berita
GET   /berita/kategori/{slug}      — Berita per Kategori
POST  /berita/{id}/komentar        — Submit Komentar
GET   /agenda                      — Agenda
GET   /agenda/{id}                 — Detail Agenda
GET   /galeri                      — Galeri
GET   /galeri/{id}                 — Detail Album
GET   /galeri/video                — Galeri Video
GET   /umkm                        — Direktori UMKM
GET   /umkm/{id}                   — Detail UMKM
GET   /potensi                     — Potensi Desa
GET   /layanan-surat               — Info Layanan Surat
POST  /layanan-surat/ajukan        — Submit Pengajuan Surat
GET   /layanan-surat/cek-status    — Cek Status Pengajuan
GET   /layanan-surat/cek-status/{kode} — Detail Status
POST  /pengaduan                   — Submit Pengaduan
GET   /pengaduan/cek-status        — Cek Status Pengaduan
GET   /pengaduan/cek-status/{kode} — Detail Status
GET   /apbdes                      — APBDes
GET   /pembangunan                 — Pembangunan
GET   /statistik                   — Statistik
GET   /download                    — Download
GET   /halaman/{slug}              — Halaman Statis
GET   /kontak                      — Kontak
POST  /kontak                      — Kirim Pesan Kontak
GET   /pencarian                   — Pencarian Global
```

### 7.2 Route Admin (Authenticated — `routes/admin.php`)

```
# Dashboard
GET   /admin                                     — Dashboard

# Profil Desa
GET   /admin/profil                              — Edit Profil
PUT   /admin/profil                              — Update Profil

# Pemerintahan
GET   /admin/pemerintahan                        — List Perangkat
POST  /admin/pemerintahan                        — Tambah Perangkat
GET   /admin/pemerintahan/{id}                   — Detail Perangkat
PUT   /admin/pemerintahan/{id}                   — Update Perangkat
DELETE /admin/pemerintahan/{id}                  — Hapus Perangkat
# Lembaga
GET   /admin/lembaga                             — List Lembaga
POST  /admin/lembaga                             — Tambah Lembaga
PUT   /admin/lembaga/{id}                        — Update Lembaga
DELETE /admin/lembaga/{id}                       — Hapus Lembaga
POST  /admin/lembaga/{id}/anggota                — Tambah Anggota
DELETE /admin/lembaga/{id}/anggota/{memberId}    — Hapus Anggota

# Berita
GET   /admin/berita                              — List Berita
GET   /admin/berita/tambah                       — Form Tambah
POST  /admin/berita                              — Simpan Berita
GET   /admin/berita/{id}/edit                    — Form Edit
PUT   /admin/berita/{id}                         — Update Berita
DELETE /admin/berita/{id}                        — Hapus Berita
# Kategori Berita
GET   /admin/kategori-berita                     — List Kategori
POST  /admin/kategori-berita                     — Tambah Kategori
PUT   /admin/kategori-berita/{id}                — Update Kategori
DELETE /admin/kategori-berita/{id}               — Hapus Kategori
# Komentar
GET   /admin/komentar                            — Moderate Komentar
PUT   /admin/komentar/{id}/approve               — Approve
DELETE /admin/komentar/{id}                      — Hapus

# Agenda
GET   /admin/agenda                              — List Agenda
POST  /admin/agenda                              — Tambah Agenda
PUT   /admin/agenda/{id}                         — Update Agenda
DELETE /admin/agenda/{id}                        — Hapus Agenda

# Kependudukan
GET   /admin/kependudukan                        — Dashboard
GET   /admin/kependudukan/kk                     — List KK
POST  /admin/kependudukan/kk                     — Tambah KK
PUT   /admin/kependudukan/kk/{id}                — Update KK
GET   /admin/kependudukan/penduduk               — List Penduduk
POST  /admin/kependudukan/penduduk               — Tambah Penduduk
GET   /admin/kependudukan/penduduk/{id}          — Detail Penduduk
PUT   /admin/kependudukan/penduduk/{id}          — Update Penduduk
DELETE /admin/kependudukan/penduduk/{id}         — Hapus Penduduk
GET   /admin/kependudukan/export                 — Export Excel
POST  /admin/kependudukan/import                 — Import Excel
# Mutasi
GET   /admin/kependudukan/mutasi                 — List Mutasi
POST  /admin/kependudukan/mutasi                 — Tambah Mutasi

# Layanan Surat
GET   /admin/layanan-surat                       — Dashboard & List Pengajuan
GET   /admin/layanan-surat/{id}                  — Detail Pengajuan
PUT   /admin/layanan-surat/{id}/status           — Update Status
GET   /admin/layanan-surat/{id}/cetak            — Cetak PDF
# Template & Kategori
GET   /admin/layanan-surat/kategori              — List Kategori
POST  /admin/layanan-surat/kategori              — Tambah Kategori
PUT   /admin/layanan-surat/kategori/{id}         — Update Kategori
DELETE /admin/layanan-surat/kategori/{id}        — Hapus Kategori
GET   /admin/layanan-surat/template              — List Template
POST  /admin/layanan-surat/template              — Tambah Template
PUT   /admin/layanan-surat/template/{id}         — Update Template
DELETE /admin/layanan-surat/template/{id}        — Hapus Template

# Galeri
GET   /admin/galeri                              — List Album
POST  /admin/galeri                              — Tambah Album
PUT   /admin/galeri/{id}                         — Update Album
DELETE /admin/galeri/{id}                        — Hapus Album
POST  /admin/galeri/{id}/foto                    — Upload Foto
DELETE /admin/galeri/{id}/foto/{fotoId}          — Hapus Foto
# Video
GET   /admin/video                               — List Video
POST  /admin/video                               — Tambah Video
PUT   /admin/video/{id}                          — Update Video
DELETE /admin/video/{id}                         — Hapus Video

# Pengaduan
GET   /admin/pengaduan                           — List Pengaduan
GET   /admin/pengaduan/{id}                      — Detail
PUT   /admin/pengaduan/{id}/status               — Update Status
POST  /admin/pengaduan/{id}/balasan              — Tambah Balasan

# UMKM
GET   /admin/umkm                                — List UMKM
POST  /admin/umkm                                — Tambah UMKM
PUT   /admin/umkm/{id}                           — Update UMKM
DELETE /admin/umkm/{id}                          — Hapus UMKM
POST  /admin/umkm/{id}/foto                      — Upload Foto
DELETE /admin/umkm/{id}/foto/{fotoId}            — Hapus Foto
# Kategori UMKM
GET   /admin/kategori-umkm                       — List Kategori
POST  /admin/kategori-umkm                       — Tambah Kategori
PUT   /admin/kategori-umkm/{id}                  — Update Kategori
DELETE /admin/kategori-umkm/{id}                 — Hapus Kategori

# Potensi Desa
GET   /admin/potensi                             — List Potensi
POST  /admin/potensi                             — Tambah Potensi
PUT   /admin/potensi/{id}                        — Update Potensi
DELETE /admin/potensi/{id}                       — Hapus Potensi

# APBDes
GET   /admin/apbdes                              — List per Tahun
POST  /admin/apbdes                              — Tambah Data
PUT   /admin/apbdes/{id}                         — Update Data
DELETE /admin/apbdes/{id}                        — Hapus Data

# Pembangunan
GET   /admin/pembangunan                         — List Proyek
POST  /admin/pembangunan                         — Tambah Proyek
PUT   /admin/pembangunan/{id}                    — Update Proyek
DELETE /admin/pembangunan/{id}                   — Hapus Proyek
POST  /admin/pembangunan/{id}/foto               — Upload Foto Progress

# Halaman Statis
GET   /admin/halaman                             — List Halaman
POST  /admin/halaman                             — Tambah Halaman
PUT   /admin/halaman/{id}                        — Update Halaman
DELETE /admin/halaman/{id}                       — Hapus Halaman

# Slider
GET   /admin/slider                              — List Slider
POST  /admin/slider                              — Tambah Slider
PUT   /admin/slider/{id}                         — Update Slider
DELETE /admin/slider/{id}                        — Hapus Slider
PUT   /admin/slider/sort                         — Sort Ulang

# Pengumuman
GET   /admin/pengumuman                          — List Pengumuman
POST  /admin/pengumuman                          — Tambah Pengumuman
PUT   /admin/pengumuman/{id}                     — Update Pengumuman
DELETE /admin/pengumuman/{id}                    — Hapus Pengumuman

# Download
GET   /admin/download                            — List File
POST  /admin/download                            — Upload File
DELETE /admin/download/{id}                      — Hapus File

# Pengguna (User Management)
GET   /admin/pengguna                            — List User
POST  /admin/pengguna                            — Tambah User
PUT   /admin/pengguna/{id}                       — Update User
DELETE /admin/pengguna/{id}                      — Hapus User
```

---

## 8. Rencana Teknis

### 8.1 Backend (Laravel)

| Komponen | Pendekatan |
|---|---|
| Autentikasi | Gunakan Laravel Fortify yang sudah ada. Tambahkan role & permission system manual (trait `HasRole`). |
| Upload file | Gunakan Laravel Storage (disk `public`). Buat folder terstruktur: `profiles/`, `posts/`, `galleries/`, `umkm/`, `sliders/`, `downloads/`. |
| Rich text | Integrasikan TipTap editor (Vue component) untuk post, pages, announcements. |
| PDF | Gunakan `barryvdh/laravel-dompdf` untuk cetak surat dan laporan. |
| Excel | Gunakan `maatwebsite/laravel-excel` untuk import/export data penduduk. |
| Validasi | Gunakan Form Request classes untuk setiap modul. |
| API Resource | Gunakan Eloquent API Resources (jika diperlukan API mobile di masa depan). |
| Cache | Cache halaman profil desa, berita popular, sidebar dengan TTL. |
| Queue | Gunakan queue untuk notifikasi (email) — optional, fase berikutnya. |

### 8.2 Frontend (Vue 3 + Inertia v3)

| Komponen | Pendekatan |
|---|---|
| Layout | Pakai layout terpisah: `PublicLayout.vue` (navbar + footer) dan `AdminLayout.vue` (sidebar + header) |
| Komponen Publik | Hero slider, berita card, galeri grid, statistik counter, agenda list, breadcrumb |
| Komponen Admin | DataTable reusable, form reusable, modal konfirmasi, upload komponen, rich text editor, chart komponen |
| Chart | Gunakan `chart.js` + `vue-chartjs` untuk statistik dan grafik |
| Editor | Gunakan `@tiptap/vue-3` untuk rich text |
| Icon | Gunakan `lucide-vue` (sudah terinstall) |
| Toast | Gunakan `vue-sonner` (sudah terinstall) |

### 8.3 Testing

- Setiap model memiliki **Factory** dengan data realistis bahasa Indonesia.
- Setiap controller memiliki **Feature Test** (minimal: index, store, update, delete, akses publik vs admin).
- Gunakan `RefreshDatabase` trait.
- Test roles & permissions: pastikan user tanpa role tidak bisa akses admin.

---

## 9. Rencana Implementasi Bertahap (Fase)

### Fase 0 — Foundation (Setup Role, Layout, Menu)
- Buat migration `add_role_to_users_table`
- Buat trait `HasRole`
- Buat middleware `RoleMiddleware` dan `AdminMiddleware`
- Buat `PublicLayout.vue` (navbar, footer, hero area)
- Buat `AdminLayout.vue` (sidebar menu + header)
- Buat sidebar navigation (menu grouped by module)
- Buat route `routes/admin.php` dan registrasi di bootstrap

### Fase 1 — Profil Desa & Pemerintahan
- Model: `Profile`, `GovernmentStaff`, `Institution`, `InstitutionMember`
- Migrations + Factories + Seeders
- Controller: `ProfileController`, `GovernmentStaffController`, `InstitutionController`
- Halaman publik: `/profil`, `/pemerintahan`
- Halaman admin: profil edit, CRUD perangkat, CRUD lembaga

### Fase 2 — Berita & Agenda
- Model: `Post`, `PostCategory`, `Comment`, `Event`, `EventCategory`
- Migrations + Factories + Seeders
- Controller: `PostController`, `PostCategoryController`, `CommentController`, `EventController`
- Halaman publik: `/berita`, `/berita/{slug}`, `/agenda`
- Halaman admin: CRUD berita, kategori, komentar, agenda
- Rich text editor (TipTap)

### Fase 3 — Kependudukan
- Model: `Family`, `Resident`, `ResidentMutation`
- Migrations + Factories + Seeders (generate random data penduduk ~1000+)
- Controller: `FamilyController`, `ResidentController`, `MutationController`
- Halaman admin: CRUD KK, penduduk, mutasi, dashboard statistik
- Import / Export Excel
- Halaman publik: statistik agregat di `/statistik`

### Fase 4 — Layanan Surat & Pengaduan
- Model: `LetterCategory`, `LetterTemplate`, `LetterRequirement`, `LetterRequest`
- Model: `ComplaintCategory`, `Complaint`, `ComplaintReply`
- Migrations + Factories + Seeders
- Controller: `LetterController`, `ComplaintController`
- Halaman publik: form ajukan surat, cek status, form pengaduan, cek status
- Admin: manage pengajuan, cetak PDF, manage pengaduan, balasan

### Fase 5 — Galeri, UMKM & Potensi
- Model: `Gallery`, `GalleryImage`, `Video`, `Umkm`, `UmkmImage`, `Potential`
- Migrations + Factories + Seeders
- Controller: `GalleryController`, `VideoController`, `UmkmController`, `PotentialController`
- Halaman publik: `/galeri`, `/umkm`, `/potensi`
- Admin: CRUD masing-masing

### Fase 6 — APBDes & Pembangunan
- Model: `Budget`, `Development`, `DevelopmentImage`
- Migrations + Factories + Seeders
- Controller: `BudgetController`, `DevelopmentController`
- Halaman publik: `/apbdes`, `/pembangunan`
- Admin: CRUD, chart, generate PDF

### Fase 7 — Halaman Statis, Slider, Pengumuman, Download, Kontak
- Model: `Page`, `Slider`, `Announcement`, `Download`
- Migrations + Factories + Seeders
- Controller masing-masing
- Halaman publik: halaman statis via slug, download, kontak
- Admin: CRUD masing-masing

### Fase 8 — Statistik, Visitor, Pencarian
- Model: `Visitor`, `PageView`
- Middleware `TrackVisitors`
- Halaman pencarian global
- Dashboard admin comprehensive charts

### Fase 9 — Polish & Testing
- Final styling & responsive untuk semua halaman
- Semua tes: unit + feature
- SEO meta tags (dynamic)
- Sitemap XML
- Performance optimization (query N+1, eager loading, cache)

---

## 10. Konvensi Penamaan

| Item | Konvensi | Contoh |
|---|---|---|
| Model | PascalCase, singular | `Post`, `Resident`, `GovernmentStaff` |
| Tabel | snake_case, plural | `posts`, `residents`, `government_staff` |
| Controller | PascalCase, singular + Controller | `PostController` |
| Form Request | PascalCase, action + model + Request | `StorePostRequest`, `UpdatePostRequest` |
| Route name | dot notation, resource-based | `posts.index`, `posts.store`, `posts.update` |
| Vue Component | PascalCase, Vue file | `PostCard.vue`, `DataTable.vue` |
| Blade/Inertia page | PascalCase, grouped by folder | `Berita/Index.vue`, `Berita/Show.vue` |
| Admin page | PascalCase, grouped in `Admin/` folder | `Admin/Berita/Index.vue`, `Admin/Berita/Form.vue` |

---

## 11. Asumsi & Keputusan

1. **Database:** Gunakan SQLite untuk development (default), MySQL/MariaDB untuk production.
2. **Role System:** Implementasi manual dengan trait + middleware (tidak pakai Spatie Permission agar sederhana dan tidak menambah dependency).
3. **Autentikasi:** Gunakan Fortify yang sudah ada, tidak perlu ganti.
4. **Bahasa:** Semua konten dan UI menggunakan Bahasa Indonesia.
5. **Timezone:** Akan diubah ke `Asia/Jakarta` dari `UTC`.
6. **Locale:** Akan diubah ke `id` dari `en`.
7. **Faker:** Akan diubah ke `id_ID` untuk data dummy yang realistis.
8. **Image upload:** Gunakan Laravel Storage disk public + intervention/image (jika diperlukan resize).
9. **Search:** Implementasi full-text search sederhana lewat MySQL LIKE / SQLite LIKE (tidak pakai Laravel Scout di awal, bisa ditambahkan nanti).
10. **No API:** Tidak ada REST API terpisah di fase ini. Semua lewat Inertia.
11. **File storage:** Simpan di `storage/app/public/`, symlink ke `public/storage/`.

---

## 12. Verifikasi & Acceptance Criteria

Setelah semua fase selesai:
- [ ] Website publik dapat diakses tanpa login dengan semua fitur berfungsi
- [ ] Panel admin hanya bisa diakses setelah login dengan role yang sesuai
- [ ] Semua CRUD di admin berfungsi dengan baik
- [ ] Upload file (gambar, dokumen) berfungsi
- [ ] Pencarian global menghasilkan hasil yang relevan
- [ ] Semua halaman responsive (mobile + desktop)
- [ ] Dark mode berfungsi di semua halaman (mengikuti sistem yang sudah ada)
- [ ] Semua test suite passing: `php artisan test --compact`
- [ ] Tidak ada N+1 query issues (dicek dengan Laravel Debugbar / Telescope)
- [ ] Semua data dummy dari seeder realistis dan mencukupi

---

## 13. Dependencies Tambahan yang Dibutuhkan

```bash
# PDF generation
composer require barryvdh/laravel-dompdf

# Excel import/export  
composer require maatwebsite/laravel-excel

# Frontend (npm)
npm install @tiptap/vue-3 @tiptap/starter-kit @tiptap/extension-image @tiptap/extension-link
npm install chart.js vue-chartjs
```
