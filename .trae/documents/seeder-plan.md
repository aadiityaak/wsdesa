# Plan: Seeder Lengkap untuk Semua Data

## Summary

Perbaiki dan lengkapi semua seed data aplikasi Desa Digital: isi 8 factory yang kosong, buat factory untuk model yang belum punya, tambahkan seeder untuk Post (berita), Event (agenda), Comment (komentar), dan Complaint (pengaduan) dengan data dummy yang realistis menggunakan factory — bukan hardcode.

---

## Current State Analysis

### DatabaseSeeder.php (17 entitas)
Seeder saat ini sudah mencakup: Admin, Profile, GovernmentStaff (10), Institution (5 + members), Gallery (3 + images), Video (2), UMKM (8 + images + 5 categories), Potential (6 + 3 categories), Budget (8 categories x 3 tahun), Development (4 + images + 4 categories), Page (3), Slider (3), Announcement (2), Download (4 + 3 categories), Letter (7 categories + requirements), ComplaintCategory (5), Family (50 KK + ~150 residents).

### Yang Belum Ada
- **Berita (Post)**: tidak ada seed sama sekali
- **Agenda (Event)**: tidak ada seed, tapi factory sudah berfungsi penuh
- **Komentar (Comment)**: tidak ada seed, tapi factory sudah berfungsi penuh
- **Pengaduan (Complaint)**: hanya kategori (5), tidak ada data pengaduan

### Factory Kosong (return `[]`)
1. `AnnouncementFactory` — perlu diisi
2. `BudgetFactory` — perlu diisi
3. `DevelopmentFactory` — perlu diisi
4. `GalleryFactory` — perlu diisi
5. `PageFactory` — perlu diisi
6. `PotentialFactory` — perlu diisi
7. `ProfileFactory` — perlu diisi
8. `SliderFactory` — perlu diisi

### Model Tanpa Factory (butuh dibuat)
1. `Umkm` + `UmkmCategory` + `UmkmImage`
2. `InstitutionMember`
3. `Video`
4. `BudgetCategory` + `DevelopmentCategory` + `PotentialCategory` + `DownloadCategory`
5. `DevelopmentImage` + `GalleryImage` + `Download`
6. `ComplaintCategory` + `ComplaintReply`
7. `LetterCategory` + `LetterTemplate` + `LetterRequirement` + `LetterRequest`
8. `ResidentMutation`

---

## Proposed Changes

### Step 1: Isi 8 Factory yang Kosong

| File | What |
|---|---|
| `database/factories/AnnouncementFactory.php` | `judul`: sentence, `konten`: paragraph, `tipe`: randomElement, `tanggal_mulai`: now()-days, `tanggal_selesai`: now()+days, `is_active`: true |
| `database/factories/BudgetFactory.php` | `tahun`: 2024/2025/2026, `anggaran`: randomFloat, `realisasi`: randomFloat, `keterangan`: sentence |
| `database/factories/DevelopmentFactory.php` | `nama`: sentence(3), `slug`: dari nama, `lokasi`: city, `deskripsi`: paragraph, `anggaran`: randomFloat, `sumber_dana`: randomElement, `tahun`: 2024/2025/2026, `status`: randomElement, `latitude`/`longitude`: randomFloat |
| `database/factories/GalleryFactory.php` | `nama`: words(3), `slug`: dari nama, `deskripsi`: paragraph, `thumbnail`: null, `user_id`: User::factory() |
| `database/factories/PageFactory.php` | `judul`: sentence, `slug`: dari judul, `konten`: paragraphs(3), `status`: 'publish' |
| `database/factories/PotentialFactory.php` | `nama`: words(3), `slug`: dari nama, `deskripsi`: paragraph, `thumbnail`: null, `lokasi`: city |
| `database/factories/ProfileFactory.php` | Semua field profil desa: desa/kecamatan/kabupaten/provinsi dummy |
| `database/factories/SliderFactory.php` | `judul`: sentence(4), `deskripsi`: sentence, `image`: null, `link`: null, `urutan`: sequence, `is_active`: true |

### Step 2: Buat Factory Baru (17 factory)

**Category factories** (7 file) — cukup sederhana, hanya `nama` + `slug`:
- `UmkmCategoryFactory`, `BudgetCategoryFactory`, `DevelopmentCategoryFactory`, `PotentialCategoryFactory`, `DownloadCategoryFactory`, `ComplaintCategoryFactory`, `LetterCategoryFactory`

**Entity factories** (10 file):
- `UmkmFactory` — nama_usaha, pemilik, alamat, telepon, deskripsi, lat/lng
- `UmkmImageFactory` — file (null), urutan
- `VideoFactory` — judul, url (youtube dummy), deskripsi
- `GalleryImageFactory` — file (null), judul, urutan
- `DevelopmentImageFactory` — file (null), keterangan
- `DownloadFactory` — nama, file (null), counter
- `InstitutionMemberFactory` — nama, jabatan, telepon, urutan
- `LetterTemplateFactory` — nama, konten, persyaratan
- `LetterRequirementFactory` — nama, wajib
- `ComplaintFactory` — sudah ada dan berfungsi penuh (tidak perlu buat)

### Step 3: Tambah Seeder untuk Data yang Belum Ada

Di `DatabaseSeeder.php`, tambahkan di akhir `run()` method:

**Berita & Kategori:**
```
PostCategory::factory(5)->create();           // 5 kategori berita
User::factory(3)->sequence(...)->create();     // 3 user tambahan untuk author
Post::factory(20)->create();                   // 20 berita dengan kategori random + author random
```

**Agenda & Kategori:**
```
EventCategory::factory(4)->create();           // 4 kategori agenda  
Event::factory(12)->create();                  // 12 agenda (upcoming/ongoing/done)
```

**Komentar:**
```
Comment::factory(40)->create();                // 40 komentar (pending/approved/rejected)
```

**Pengaduan:**
```
ComplaintCategory::factory(5)->create();       // 5 kategori pengaduan
Complaint::factory(15)->create();              // 15 pengaduan
```

### Step 4: Refactor Seeder yang Ada agar Pakai Factory

Ubah seeder yang saat ini hardcode menjadi pakai factory:

| Entitas | Saat ini | Menjadi |
|---|---|---|
| GovernmentStaff | hardcode 10 jabatan | `GovernmentStaff::factory(10)->create()` |
| Institution + members | hardcode 5 institusi + anggota | `Institution::factory(5)->has(InstitutionMember::factory()->count(4), 'members')->create()` |
| Gallery + images | hardcode 3 galeri + 4-6 gambar | `Gallery::factory(3)->has(GalleryImage::factory()->count(5), 'images')->create()` |
| Video | hardcode 2 video | `Video::factory(5)->create()` |
| UMKM + images + categories | hardcode 5 kategori + 8 UMKM | `UmkmCategory::factory(5)->create()` + `Umkm::factory(8)->has(UmkmImage::factory()->count(3), 'images')->create()` |
| Potential + categories | hardcode | `PotentialCategory::factory(3)->create()` + `Potential::factory(6)->create()` |
| Budget + categories | hardcode | `BudgetCategory::factory(8)->sequence(...)->create()` + `Budget::factory(24)->create()` |
| Development + images + categories | hardcode | `DevelopmentCategory::factory(4)->create()` + `Development::factory(6)->has(DevelopmentImage::factory()->count(3), 'images')->create()` |
| Pages | hardcode 3 | `Page::factory(5)->create()` |
| Sliders | hardcode 3 | `Slider::factory(5)->create()` |
| Announcements | hardcode 2 | `Announcement::factory(3)->create()` |
| Downloads + categories | hardcode | `DownloadCategory::factory(3)->create()` + `Download::factory(8)->create()` |
| Letter categories + requirements | hardcode 7 + 14 requirements | `LetterCategory::factory(7)->has(LetterRequirement::factory()->count(3))->create()` |

### Step 5: Sequence untuk Data Spesifik

Beberapa kategori perlu nama spesifik. Gunakan `sequence()`:

**PostCategory** (5): `['Berita Desa', 'Pengumuman', 'Kegiatan', 'Pembangunan', 'Pemerintahan']`

**EventCategory** (4): `['Gotong Royong', 'Rapat Desa', 'Perlombaan', 'Keagamaan']`

**BudgetCategory** (8): 
```
tipe Pendapatan: ['Pendapatan Asli Desa', 'Dana Desa', 'Alokasi Dana Desa', 'Bantuan Keuangan']
tipe Belanja: ['Belanja Pegawai', 'Belanja Barang/Jasa', 'Belanja Modal']
tipe Pembiayaan: ['Pembiayaan Netto']
```

**DevelopmentCategory** (4): `['Infrastruktur', 'Pendidikan', 'Kesehatan', 'Ekonomi']`

**PotentialCategory** (3): `['Pertanian', 'Pariwisata', 'Kerajinan']`

**UmkmCategory** (5): `['Kuliner', 'Kerajinan', 'Fashion', 'Pertanian', 'Jasa']`

**DownloadCategory** (3): `['Dokumen Desa', 'Peraturan', 'Formulir']`

**ComplaintCategory** (5): `['Pelayanan Publik', 'Infrastruktur', 'Lingkungan', 'Sosial', 'Lainnya']`

**LetterCategory** (7): `['Surat Keterangan Domisili', 'Surat Keterangan Usaha', 'Surat Keterangan Tidak Mampu', 'Surat Keterangan Kelahiran', 'Surat Keterangan Kematian', 'Surat Pengantar KTP', 'Surat Keterangan Nikah']`

### Step 6: Post & Comment — Relasi yang Benar

- Post membutuhkan `PostCategory` + `User` (author)
- Comment membutuhkan `Post`
- Gunakan factory relationship chains: `Post::factory()->for(User::factory())->for(PostCategory::factory())`
- Comment: `Comment::factory()->for(Post::factory()->published())`

### Tambahan: HasFactory Trait

Model berikut ini belum punya `HasFactory` trait dan perlu ditambahkan agar factory bisa digunakan:
- `InstitutionMember`
- `Video`, `GalleryImage`
- `Umkm`, `UmkmCategory`, `UmkmImage`
- `BudgetCategory`, `DevelopmentCategory`, `DevelopmentImage`, `PotentialCategory`
- `DownloadCategory`, `Download`
- `ComplaintCategory`, `ComplaintReply`
- `LetterCategory`, `LetterTemplate`, `LetterRequirement`, `LetterRequest`
- `ResidentMutation`

---

## Files to Create/Modify

### Factory files — isi 8 + buat 17 = 25 factory

| # | File | Action |
|---|---|---|
| F1 | `database/factories/AnnouncementFactory.php` | Isi |
| F2 | `database/factories/BudgetFactory.php` | Isi |
| F3 | `database/factories/DevelopmentFactory.php` | Isi |
| F4 | `database/factories/GalleryFactory.php` | Isi |
| F5 | `database/factories/PageFactory.php` | Isi |
| F6 | `database/factories/PotentialFactory.php` | Isi |
| F7 | `database/factories/ProfileFactory.php` | Isi |
| F8 | `database/factories/SliderFactory.php` | Isi |
| F9 | `database/factories/UmkmCategoryFactory.php` | Buat |
| F10 | `database/factories/UmkmFactory.php` | Buat |
| F11 | `database/factories/UmkmImageFactory.php` | Buat |
| F12 | `database/factories/VideoFactory.php` | Buat |
| F13 | `database/factories/GalleryImageFactory.php` | Buat |
| F14 | `database/factories/DevelopmentImageFactory.php` | Buat |
| F15 | `database/factories/InstitutionMemberFactory.php` | Buat |
| F16 | `database/factories/BudgetCategoryFactory.php` | Buat |
| F17 | `database/factories/DevelopmentCategoryFactory.php` | Buat |
| F18 | `database/factories/PotentialCategoryFactory.php` | Buat |
| F19 | `database/factories/DownloadCategoryFactory.php` | Buat |
| F20 | `database/factories/DownloadFactory.php` | Buat |
| F21 | `database/factories/ComplaintCategoryFactory.php` | Buat |
| F22 | `database/factories/LetterCategoryFactory.php` | Buat |
| F23 | `database/factories/LetterTemplateFactory.php` | Buat |
| F24 | `database/factories/LetterRequirementFactory.php` | Buat |
| F25 | `database/factories/LetterRequestFactory.php` | Buat |

### Model files — tambah HasFactory (17 model)

| File | Action |
|---|---|
| `app/Models/InstitutionMember.php` | Tambah `use HasFactory` |
| `app/Models/Video.php` | Tambah `use HasFactory` |
| `app/Models/GalleryImage.php` | Tambah `use HasFactory` |
| `app/Models/Umkm.php` | Tambah `use HasFactory` |
| `app/Models/UmkmCategory.php` | Tambah `use HasFactory` |
| `app/Models/UmkmImage.php` | Tambah `use HasFactory` |
| `app/Models/BudgetCategory.php` | Tambah `use HasFactory` |
| `app/Models/DevelopmentCategory.php` | Tambah `use HasFactory` |
| `app/Models/DevelopmentImage.php` | Tambah `use HasFactory` |
| `app/Models/PotentialCategory.php` | Tambah `use HasFactory` |
| `app/Models/DownloadCategory.php` | Tambah `use HasFactory` |
| `app/Models/Download.php` | Tambah `use HasFactory` |
| `app/Models/ComplaintCategory.php` | Tambah `use HasFactory` |
| `app/Models/ComplaintReply.php` | Tambah `use HasFactory` |
| `app/Models/LetterCategory.php` | Tambah `use HasFactory` |
| `app/Models/LetterTemplate.php` | Tambah `use HasFactory` |
| `app/Models/LetterRequirement.php` | Tambah `use HasFactory` |

### Seeder file

| File | Action |
|---|---|
| `database/seeders/DatabaseSeeder.php` | Rewrite untuk pakai factory + tambah Post/Event/Comment/Complaint |

---

## Verification

1. `php artisan db:seed --no-interaction` — harus sukses tanpa error
2. `vendor/bin/pint --dirty --format agent` — semua PHP files terformat
3. Kunjungi semua halaman admin untuk verifikasi data tampil:
   - `/admin/berita` — 20 berita
   - `/admin/agenda` — 12 agenda
   - `/admin/komentar` — 40 komentar
   - `/admin/pengaduan` — 15 pengaduan
   - `/admin/galeri` — 3 galeri + gambar
   - `/admin/video` — 5 video
   - `/admin/umkm` — 8 UMKM + gambar
   - `/admin/potensi` — 6 potensi
   - `/admin/apbdes` — 24 budget entry
   - `/admin/pembangunan` — 6 pembangunan + gambar
   - `/admin/halaman` — 5 halaman
   - `/admin/slider` — 5 slider
   - `/admin/pengumuman` — 3 pengumuman
   - `/admin/download` — 8 download
   - `/admin/layanan-surat` — 7 kategori surat
   - `/admin/kependudukan` — ~50 KK + ~150 penduduk
