<?php

use App\Http\Controllers\Publik\AgendaController;
use App\Http\Controllers\Publik\BeritaController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

Route::prefix('berita')->group(function () {
    Route::get('/', [BeritaController::class, 'index'])->name('berita.index');
    Route::get('/{slug}', [BeritaController::class, 'show'])->name('berita.show');
    Route::post('/{post}/komentar', [BeritaController::class, 'storeComment'])->name('berita.comment.store');
});

Route::prefix('agenda')->group(function () {
    Route::get('/', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/{id}', [AgendaController::class, 'show'])->name('agenda.show');
});

require __DIR__.'/settings.php';
