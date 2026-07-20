# Split Typography: Heading Font & Content Font

## Ringkasan

Mengubah pengaturan typography dari satu font universal menjadi dua pilihan terpisah:
- **Heading Font** - untuk judul (h1-h6)
- **Content Font** - untuk body teks/paragraf

## Current State

- 1 kolom `font_style` di tabel `profiles`
- 1 pilihan font di [Tampilan.vue](file:///d:/dev/wsdesa/resources/js/pages/Admin/Tampilan.vue)
- 1 shared prop `font_style` via [HandleInertiaRequests.php](file:///d:/dev/wsdesa/app/Http/Middleware/HandleInertiaRequests.php)
- 1 kelas CSS `font-style-*` diterapkan ke root div di [PublicLayout.vue](file:///d:/dev/wsdesa/resources/js/layouts/PublicLayout.vue)
- 8 font tersedia: inter, serif, mono, rounded, poppins, playfair, bricolage, jakarta

## Proposed Changes

### 1. Database — Tambah kolom `heading_font`

**File:** migrasi baru `add_heading_font_to_profiles_table`

```php
$table->string('heading_font')->default('inter')->after('font_style');
```

Kolom `font_style` tetap sebagai content/body font. `heading_font` untuk heading.

### 2. Model — Tambah `heading_font` ke fillable

**File:** `d:\dev\wsdesa\app\Models\Profile.php`

Tambahkan `'heading_font'` ke `$fillable`.

### 3. Controller — Tambah validasi `heading_font`

**File:** `d:\dev\wsdesa\app\Http\Controllers\Admin\ProfileController.php`

```php
'font_style'    => ['required', 'string', 'in:inter,serif,mono,rounded,poppins,playfair,bricolage,jakarta'],
'heading_font'  => ['required', 'string', 'in:inter,serif,mono,rounded,poppins,playfair,bricolage,jakarta'],
```

### 4. Middleware — Share `heading_font`

**File:** `d:\dev\wsdesa\app\Http\Middleware\HandleInertiaRequests.php`

```php
'heading_font' => $profile->heading_font ?? 'inter',
```

### 5. CSS — Tambah kelas `.heading-font-*`

**File:** `d:\dev\wsdesa\resources\css\app.css`

Tambahkan 8 kelas baru `.heading-font-*` di `@layer utilities` — mirip dengan `.font-style-*` tapi khusus untuk heading. Ini akan digunakan untuk meng-override `font-family` pada elemen h1-h6.

### 6. Tampilan.vue — Split UI menjadi dua section

**File:** `d:\dev\wsdesa\resources\js\pages\Admin\Tampilan.vue`

- Interface Profile: tambah `heading_font: string`
- Form: tambah `heading_font` field
- Ganti section "Typography" menjadi 2 section:
  - **Heading Font** — pilih font untuk judul
  - **Content Font** — pilih font untuk isi/body

### 7. PublicLayout.vue — Terapkan kedua font

**File:** `d:\dev\wsdesa\resources\js\layouts\PublicLayout.vue`

- Baca `headingFont` dari sharedProfile
- Root div tetap pakai `font-style-${bodyFont}` untuk body text
- Tambahkan class `heading-font-${headingFont}` pada root div, lalu CSS selector `h1,h2,h3,h4,h5,h6` akan menggunakan heading font
- Cara implementasi: gunakan CSS nesting `.heading-font-xxx h1,h2,h3,h4,h5,h6 { font-family: ... }`

## Verifikasi

1. Buka `/admin/tampilan`, pilih heading font berbeda dari content font, simpan
2. Buka halaman depan — heading (judul berita, nama desa, dll) harus pakai heading font, body teks pakai content font
3. Uji beberapa kombinasi (misal Playfair untuk heading + Inter untuk body)
4. Pastikan semua 20+ halaman publik terpengaruh (karena class di PublicLayout)

## Asumsi & Keputusan

- Kolom `font_style` tetap sebagai content/body font (tidak di-rename), sehingga backward compatible
- Kedua dropdown menggunakan daftar 8 font yang sama
- Heading font diterapkan via CSS selector `h1,h2,h3,h4,h5,h6` di dalam `.heading-font-*` class
- Tidak perlu migrasi data — default `heading_font = 'inter'`
