# Plan: Fix Foto Profil Desa Tidak Tersimpan Setelah Upload

## Summary

User mengupload foto logo desa di halaman admin `/admin/profil`, preview muncul (via `URL.createObjectURL`), tetapi setelah refresh halaman `/profil` foto hilang. Database menunjukkan kolom `logo` selalu `NULL` — upload tidak pernah tersimpan ke storage maupun database.

## Analisis

### Status Saat Ini

1. **Database**: `Profile::first()?->logo` → `NULL` (tidak ada logo yang pernah tersimpan)
2. **Storage**: Folder `storage/app/public/profiles` **tidak ada**, artinya belum pernah ada upload sukses ke folder ini. (Sebagai perbandingan, `storage/app/public/posts` dan `storage/app/public/placeholders` ada dan berisi file — storage berfungsi untuk fitur lain.)
3. **Frontend Admin** ([Edit.vue](file:///d:\dev\wsdesa\resources\js\Pages\Admin\Profil\Edit.vue)):
   - Preview logo menggunakan `URL.createObjectURL(file)` (murni client-side, tidak ada hubungannya dengan backend)
   - `form.logo = file` di-set di `onLogoChange()` (baris 91)
   - Form disubmit via `form.put('/admin/profil', ...)` (baris 97)
4. **Controller** ([ProfileController.php](file:///d:\dev\wsdesa\app\Http\Controllers\Admin\ProfileController.php)):
   - Validasi logo: `'logo' => ['nullable', 'image', 'max:2048']`
   - Upload handling terlihat benar: `$request->hasFile('logo')` → `store('profiles', 'public')`
   - Tidak ada error handling jika `store()` gagal
5. **Halaman publik** ([Profil.vue](file:///d:\dev\wsdesa\resources\js\Pages\Publik\Profil.vue)):
   - Tidak menampilkan logo sama sekali — hanya menampilkan `AppLogoIcon` SVG default di hero

### Root Cause

**Root cause utama: File logo tidak terkirim ke server saat `form.put()`** karena Inertia v3 `useForm` tidak mendeteksi bahwa field `logo` telah berubah dari `null` menjadi `File` object dengan benar.

**Detail teknis:**

Di Inertia v3, `useForm` secara internal menggunakan `reactive()` untuk melacak data. Ketika `form.logo = file` dipanggil di `onLogoChange()`, Inertia seharusnya mendeteksi perubahan. Namun, ada beberapa kemungkinan masalah:

1. **Inertia v3 `form.put()` dengan file attachment**: Ketika `form.logo` diinisialisasi sebagai `null` dan kemudian di-set ke `File`, Inertia mungkin tidak mendeteksi bahwa ini adalah file upload dan tetap mengirim sebagai JSON PUT request (tanpa FormData). Akibatnya, `$request->hasFile('logo')` di controller selalu `false`.
   
2. **Tidak ada error feedback**: Controller tidak memiliki logging atau error handling di sekitar `store()`, sehingga jika upload gagal, user tidak tahu — toast sukses tetap muncul karena `save()` berhasil (hanya data teks yang disimpan).

**Evidence tambahan:**
- Folder `storage/app/public/profiles` tidak pernah dibuat → konfirmasi `store()` tidak pernah dipanggil dengan sukses
- `logo = NULL` di DB → field tidak pernah di-update
- Preview muncul di UI → hanya `URL.createObjectURL()` client-side, bukan actual upload

## Proposed Changes

### Step 1: Fix form submission untuk file upload di Inertia v3

**File**: [Edit.vue](file:///d:\dev\wsdesa\resources\js\Pages\Admin\Profil\Edit.vue) (baris 55-102)

**What**: Ubah cara submit form agar file upload berfungsi dengan Inertia v3

**How**:
- Ubah `form.submit()` dari `form.put()` menjadi `form.submit('put')` yang secara eksplisit menggunakan POST + `_method=PUT` + FormData
- Atau, alternatif: gunakan `form.post('/admin/profil', { _method: 'put', ... })` yang memaksa form dikirim sebagai multipart/form-data

**Why**: Inertia v3 `form.put()` dengan `null` → `File` transition mungkin tidak mengirim sebagai FormData. `form.submit('put')` atau post dengan `_method` secara eksplisit menangani file.

### Step 2: Tambahkan error handling & logging di controller

**File**: [ProfileController.php](file:///d:\dev\wsdesa\app\Http\Controllers\Admin\ProfileController.php) (baris 73-79)

**What**: Tambahkan try/catch dan logging di sekitar operasi upload file

**How**:
```php
if ($request->hasFile('logo')) {
    if ($profile->logo) {
        Storage::disk('public')->delete($profile->logo);
    }
    try {
        $profile->logo = $request->file('logo')->store('profiles', 'public');
    } catch (\Exception $e) {
        \Log::error('Logo upload failed: ' . $e->getMessage());
        return back()->with('error', 'Gagal mengupload logo. Silakan coba lagi.');
    }
}
```

**Why**: Memberikan feedback ke user jika upload gagal, dan memudahkan debugging

### Step 3: Tampilkan logo di halaman publik

**File**: [Profil.vue](file:///d:\dev\wsdesa\resources\js\Pages\Publik\Profil.vue) (baris 33-35)

**What**: Ganti `AppLogoIcon` SVG default dengan gambar logo jika tersedia

**How**: Mirip dengan logika di `AppLogoIcon.vue` — jika `profile.logo` ada, tampilkan `<img :src="'/storage/' + profile.logo">`, jika tidak tampilkan SVG default

**Why**: Saat ini halaman publik tidak pernah menampilkan logo desa meskipun sudah di-upload — ini adalah UX gap yang membuat user tidak bisa melihat hasil uploadnya

### Step 4: Verifikasi storage link

- `php artisan storage:link` sudah dijalankan (symlink `public/storage` → `storage/app/public` sudah ada)
- Tidak perlu perubahan

## Assumptions & Decisions

- Diasumsikan Inertia v3 `useForm` adalah penyebab utama — alternative root cause (mis. PHP config `upload_max_filesize`) akan terlihat dari error handling baru
- Menggunakan `form.submit('put', ...)` sebagai pendekatan utama karena lebih idiomatis untuk Inertia v3
- Tidak mengubah struktur database atau migration — schema sudah benar

## Verification

1. Buka halaman `/admin/profil`, upload foto logo baru
2. Klik "Simpan Perubahan" → harus muncul toast "Profil desa berhasil diperbarui."
3. Refresh halaman `/admin/profil` → logo preview harus tetap muncul (dari `/storage/...`)
4. Buka halaman publik `/profil` → logo desa harus terlihat di hero section
5. Periksa database: `App\Models\Profile::first()?->logo` harus berisi path seperti `profiles/xxx.jpg`
6. Periksa storage: folder `storage/app/public/profiles` harus ada dan berisi file
