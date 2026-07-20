# Perbaiki Typography di Halaman Depan

## Ringkasan

Mekanisme CSS `.font-style-*` saat ini hanya mengubah CSS custom property `--font-sans`, tetapi tidak mendeklarasikan `font-family` secara eksplisit. Karena `<body>` di `app.blade.php` sudah men-set `class="font-sans"` (yang me-resolve `font-family` ke `Instrument Sans`), override `--font-sans` di dalam `<div>` PublicLayout tidak pernah efektif — browser sudah mewarisi computed `font-family` dari body.

**Perbaikan:** Setiap kelas `.font-style-*` harus mendeklarasikan `font-family` secara langsung, bukan hanya `--font-sans`.

## Current State

- 8 font terdaftar di `vite.config.ts` via Bunny CDN
- 8 CSS custom properties (`--font-*`) dan 8 utility classes (`.font-style-*`) ada di `app.css`
- `PublicLayout.vue` sudah mengaplikasikan class `font-style-${fontStyle}` pada root div
- Namun: tidak ada font yang benar-benar berubah di halaman publik

## Proposed Changes

### 1. `resources/css/app.css` — Perbaiki semua 8 kelas `.font-style-*`

Setiap kelas perlu menambahkan deklarasi `font-family` secara eksplisit. Ubah dari:

```css
.font-style-inter {
    --font-sans: 'Instrument Sans', ui-sans-serif, ...;
}
```

Menjadi:

```css
.font-style-inter {
    --font-sans: 'Instrument Sans', ui-sans-serif, ...;
    font-family: 'Instrument Sans', ui-sans-serif, ...;
}
```

**File:** `d:\dev\wsdesa\resources\css\app.css` (baris 97-154)
**Apa yang berubah:** Tambahkan `font-family` pada 8 kelas: `.font-style-inter`, `.font-style-serif`, `.font-style-mono`, `.font-style-rounded`, `.font-style-poppins`, `.font-style-playfair`, `.font-style-bricolage`, `.font-style-jakarta`
**Mengapa:** CSS custom property override tidak cukup — browser mewarisi computed `font-family` dari `<body>`. Deklarasi `font-family` eksplisit memaksa browser menggunakan font yang benar.

## Verifikasi

1. Buka `http://localhost:8000/` (halaman Beranda)
2. Di admin panel, buka `/admin/tampilan`, pilih font berbeda (misal Poppins), simpan
3. Refresh halaman depan — font harus berubah sesuai pilihan
4. Uji semua 8 font untuk memastikan semuanya bekerja
5. Pastikan dark mode tidak terpengaruh

## Asumsi & Keputusan

- Tidak perlu mengubah `PublicLayout.vue` — class sudah diaplikasikan dengan benar pada root div
- Tidak perlu mengubah individual public pages — semua sudah di bawah PublicLayout
- Tidak perlu mengubah `vite.config.ts` — semua font sudah ter-load
- Tidak perlu migrasi database baru
- Tidak perlu perubahan controller
