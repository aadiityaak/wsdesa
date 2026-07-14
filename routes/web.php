<?php

use App\Http\Controllers\Publik\AgendaController;
use App\Http\Controllers\Publik\ApbdesController;
use App\Http\Controllers\Publik\BeritaController;
use App\Http\Controllers\Publik\GaleriController;
use App\Http\Controllers\Publik\LayananSuratController;
use App\Http\Controllers\Publik\PembangunanController;
use App\Http\Controllers\Publik\PengaduanController;
use App\Http\Controllers\Publik\PotensiController;
use App\Http\Controllers\Publik\StatistikController;
use App\Http\Controllers\Publik\UmkmController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::prefix('berita')->group(function () {
    Route::get('/', [BeritaController::class, 'index'])->name('berita.index');
    Route::post('/{post}/komentar', [BeritaController::class, 'storeComment'])->name('berita.comment.store');
    Route::get('/{slug}', [BeritaController::class, 'show'])->name('berita.show');
});

Route::prefix('agenda')->group(function () {
    Route::get('/', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/{id}', [AgendaController::class, 'show'])->name('agenda.show');
});

Route::prefix('layanan-surat')->group(function () {
    Route::get('/', [LayananSuratController::class, 'index'])->name('layanan-surat.index');
    Route::post('/ajukan', [LayananSuratController::class, 'store'])->name('layanan-surat.store');
    Route::get('/cek-status', [LayananSuratController::class, 'cekStatus'])->name('layanan-surat.cek-status');
    Route::post('/cek-status', [LayananSuratController::class, 'showStatus'])->name('layanan-surat.status');
});

Route::prefix('pengaduan')->group(function () {
    Route::get('/', [PengaduanController::class, 'index'])->name('pengaduan.index');
    Route::post('/', [PengaduanController::class, 'store'])->name('pengaduan.store');
    Route::get('/cek-status', [PengaduanController::class, 'cekStatus'])->name('pengaduan.cek-status');
    Route::post('/cek-status', [PengaduanController::class, 'showStatus'])->name('pengaduan.status');
});

Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik.index');

Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri.index');
Route::get('/galeri/{gallery}', [GaleriController::class, 'show'])->name('galeri.show');
Route::get('/umkm', [UmkmController::class, 'index'])->name('umkm.index');
Route::get('/umkm/{umkm}', [UmkmController::class, 'show'])->name('umkm.show');
Route::get('/potensi', [PotensiController::class, 'index'])->name('potensi.index');
Route::get('/apbdes', [ApbdesController::class, 'index'])->name('apbdes.index');
Route::get('/pembangunan', [PembangunanController::class, 'index'])->name('pembangunan.index');
Route::get('/halaman/{slug}', [GaleriController::class, 'page'])->name('halaman.show');
Route::get('/download', [GaleriController::class, 'downloads'])->name('download.index');
require __DIR__.'/settings.php';
