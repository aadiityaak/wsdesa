# Plan: Selesaikan UI Beranda (Clean & Informatif)

## Ringkasan

Perbaiki tampilan halaman beranda (`/`) agar lebih clean, modern, dan informatif untuk web desa. Fokus pada proporsi, spacing, interaksi, dan konsistensi visual tanpa mengubah struktur data atau backend.

---

## 1. Current State Analysis

### Hero Section
- `h-[70vh]` — terlalu besar, di mobile hampir full screen
- Overlap `-mt-16 z-10` untuk quick services — rawan break di viewport tertentu
- Slider controls pakai `bg-white/20` — kontras kurang di beberapa gambar
- Typography headline `text-4xl sm:text-5xl lg:text-6xl` — terlalu besar di layar kecil
- Slider fallback gradient identik dengan CTA section gradient

### Quick Services
- Kartu "Download" pakai ikon `FileText` — duplikat dengan kartu "Pengajuan Surat"
- Tidak ada animasi hover interaktif (hanya shadow)
- Background warna ikon tidak konsisten: emerald, blue, amber, purple — terlalu ramai
- Card menggunakan `shadow-lg` — agak berat untuk 4 item bersebelahan

### Berita Terbaru
- Grid 4 kolom di lg — terlalu sempit untuk card berita dgn gambar + teks
- Empty state terlalu sederhana (hanya teks)
- "Lihat Semua" link dobel (desktop + mobile) — bisa pakai satu aja

### Agenda Mendatang
- Background `bg-zinc-50` — fine, tapi batas antar section kurang jelas
- Card border tipis (`border`) + shadow ringan — kurang kontras dengan background
- Tanggal di card header — kecil dan mudah terlewat

### CTA Section
- Gradient `from-emerald-600 to-emerald-800` — hampir sama dengan hero fallback
- Padding `py-16` — lebih kecil dari section lain (`py-20`)

### Masalah Umum
- Tidak ada transisi/animasi halus antar section saat scroll
- Ketiadaan ikon untuk section heading
- Tidak ada `aria-label` / aksesibilitas pada tombol slider
- Warna emerald hardcoded di template, tidak pakai CSS variables

---

## 2. Proposed Changes

Hanya 1 file yang diubah: **`resources/js/pages/Welcome.vue`** — tulis ulang template.

### A. Hero Slider
- Tinggi: `min-h-[60vh]` di desktop, `min-h-[50vh]` di mobile
- Hapus overlap (`-mt-16 z-10`), ganti quick services ke section sendiri dengan `py-16`
- Slider controls: `bg-white/30 hover:bg-white/50` biar lebih visible
- Typography: `text-3xl sm:text-4xl lg:text-5xl` — lebih proporsional
- Fallback gradient: pakai `from-blue-600 to-indigo-800` beda dari CTA
- Tambah `aria-label` pada prev/next buttons
- Tambah `role="region"` dan `aria-roledescription="carousel"` pada slider

### B. Quick Services
- Ikon "Download" ganti `FileText` → `DownloadCloud` (lucide)
- Warna ikon konsisten: semuanya pakai `text-primary` dengan background ringan `bg-primary/10` atau pakai warna solid masing-masing tapi lebih soft
- Efek hover: `hover:-translate-y-1 hover:shadow-xl transition-all duration-200`
- Margin/padding konsisten: py-16 untuk section
- Tambah subtle `ring-1 ring-zinc-200/50 dark:ring-zinc-700/50` pada card

### C. Berita Terbaru
- Grid: `sm:grid-cols-2 lg:grid-cols-3` — 3 kolom lebih lega
- Tambah ikon `Newspaper` di samping heading
- "Lihat Semua" — cukup satu link dengan `hidden sm:inline-flex`
- Image placeholder: pakai `Image` icon bukan `FileText`
- Card: tambah `group` effect pada gambar (scale sedikit di hover)

### D. Agenda Mendatang
- Grid: `sm:grid-cols-2 lg:grid-cols-3` — 3 kolom
- Background: tambah subtle pattern atau border-top dekoratif via `border-t-2 border-emerald-200 dark:border-emerald-800`
- Tanggal: lebih visible dengan border-left accent atau badge kecil
- Tambah ikon `Calendar` di samping heading

### E. CTA Section
- Gradient: variasi berbeda `from-emerald-700 to-teal-700` biar beda dari hero
- Padding: konsisten `py-20` dengan section lain
- Tambah efek subtle `bg-grid` pattern overlay (CSS pseudo-element)
- Heading: tambah `text-balance` untuk wrapping rapi

### F. Polishing Umum
- Konsistensi padding: semua section `py-16` di mobile, `py-20` di desktop
- Tambah `scroll-mt-20` untuk anchor offset (antisipasi future anchor nav)
- Heading section pakai `flex items-center gap-3` dengan ikon
- Loading state: tambah `transition-all duration-300` pada konten dynamic
- Hapus unused imports (`CardDescription` karena tidak dipakai di template)

---

## 3. File Changes Summary

| # | File | Action | What |
|---|------|--------|------|
| 1 | `resources/js/pages/Welcome.vue` | **Tulis ulang** | Template + script (hanya ubah class & struktur visual, data props tetap) |

Tidak ada perubahan di backend, routes, layout, CSS variable, atau file lain.

---

## 4. Assumptions & Decisions

- **Tidak ubah CSS variables**: Tema shadcn-vue sudah OK, cukup pakai utility classes Tailwind
- **Tidak tambah dependencies**: Semua ikon lucide sudah tersedia di project
- **Warna aksen tetap emerald**: Konsisten dengan warna existing di seluruh app (PublicLayout, dll)
- **Slider fallback hijau diganti biru**: Biar ada variasi visual antara hero fallback, CTA, dan section lain
- **Grid 4→3 kolom**: 4 card per row terlalu sempit untuk grid yg menampilkan gambar + teks

---

## 5. Verification

1. `npx vite build` — pastikan kompilasi tanpa error
2. Buka `http://127.0.0.1:8000/` — cek visual semua section
3. Test responsive: 320px, 768px, 1024px, 1440px
4. Test slider: auto-slide, prev/next, dot nav
5. Test dark mode
6. Test dengan data kosong (slider, berita, agenda = [])
