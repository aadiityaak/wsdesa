<?php

use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GovernmentStaffController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Desa
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');

    // Pemerintahan - Perangkat Desa
    Route::get('/pemerintahan', [GovernmentStaffController::class, 'index'])->name('pemerintahan.index');
    Route::post('/pemerintahan', [GovernmentStaffController::class, 'store'])->name('pemerintahan.store');
    Route::put('/pemerintahan/{staff}', [GovernmentStaffController::class, 'update'])->name('pemerintahan.update');
    Route::delete('/pemerintahan/{staff}', [GovernmentStaffController::class, 'destroy'])->name('pemerintahan.destroy');

    // Pemerintahan - Lembaga
    Route::get('/lembaga', [InstitutionController::class, 'index'])->name('lembaga.index');
    Route::post('/lembaga', [InstitutionController::class, 'store'])->name('lembaga.store');
    Route::put('/lembaga/{institution}', [InstitutionController::class, 'update'])->name('lembaga.update');
    Route::delete('/lembaga/{institution}', [InstitutionController::class, 'destroy'])->name('lembaga.destroy');
    Route::post('/lembaga/{institution}/anggota', [InstitutionController::class, 'storeMember'])->name('lembaga.member.store');
    Route::delete('/lembaga/{institution}/anggota/{member}', [InstitutionController::class, 'destroyMember'])->name('lembaga.member.destroy');

    // Berita
    Route::get('/berita', [PostController::class, 'index'])->name('berita.index');
    Route::get('/berita/tambah', [PostController::class, 'create'])->name('berita.create');
    Route::post('/berita', [PostController::class, 'store'])->name('berita.store');
    Route::get('/berita/{post}/edit', [PostController::class, 'edit'])->name('berita.edit');
    Route::put('/berita/{post}', [PostController::class, 'update'])->name('berita.update');
    Route::delete('/berita/{post}', [PostController::class, 'destroy'])->name('berita.destroy');

    // Kategori Berita
    Route::get('/kategori-berita', [PostCategoryController::class, 'index'])->name('kategori-berita.index');
    Route::post('/kategori-berita', [PostCategoryController::class, 'store'])->name('kategori-berita.store');
    Route::put('/kategori-berita/{postCategory}', [PostCategoryController::class, 'update'])->name('kategori-berita.update');
    Route::delete('/kategori-berita/{postCategory}', [PostCategoryController::class, 'destroy'])->name('kategori-berita.destroy');

    // Komentar
    Route::get('/komentar', [CommentController::class, 'index'])->name('komentar.index');
    Route::put('/komentar/{comment}/approve', [CommentController::class, 'approve'])->name('komentar.approve');
    Route::delete('/komentar/{comment}', [CommentController::class, 'destroy'])->name('komentar.destroy');

    // Agenda
    Route::get('/agenda', [EventController::class, 'index'])->name('agenda.index');
    Route::post('/agenda', [EventController::class, 'store'])->name('agenda.store');
    Route::put('/agenda/{event}', [EventController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{event}', [EventController::class, 'destroy'])->name('agenda.destroy');

    // Kategori Agenda
    Route::get('/kategori-agenda', [EventCategoryController::class, 'index'])->name('kategori-agenda.index');
    Route::post('/kategori-agenda', [EventCategoryController::class, 'store'])->name('kategori-agenda.store');
    Route::put('/kategori-agenda/{eventCategory}', [EventCategoryController::class, 'update'])->name('kategori-agenda.update');
    Route::delete('/kategori-agenda/{eventCategory}', [EventCategoryController::class, 'destroy'])->name('kategori-agenda.destroy');
});
