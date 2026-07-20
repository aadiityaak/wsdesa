<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DevelopmentController;
use App\Http\Controllers\Admin\DownloadController;
use App\Http\Controllers\Admin\EventCategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\FamilyController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\GovernmentStaffController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\LetterController;
use App\Http\Controllers\Admin\MutationController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PostCategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PotentialController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\UmkmController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Desa
    Route::redirect('/profil', '/admin/profil/data-desa');

    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/data-desa', [ProfileController::class, 'dataDesa'])->name('data-desa.edit');
        Route::match(['put', 'post'], '/data-desa', [ProfileController::class, 'updateDataDesa'])->name('data-desa.update');

        Route::get('/jam-kerja', [ProfileController::class, 'jamKerja'])->name('jam-kerja.edit');
        Route::match(['put', 'post'], '/jam-kerja', [ProfileController::class, 'updateJamKerja'])->name('jam-kerja.update');

        Route::get('/media', [ProfileController::class, 'media'])->name('media.edit');
        Route::match(['put', 'post'], '/media', [ProfileController::class, 'updateMedia'])->name('media.update');

        Route::get('/geografi', [ProfileController::class, 'geografi'])->name('geografi.edit');
        Route::match(['put', 'post'], '/geografi', [ProfileController::class, 'updateGeografi'])->name('geografi.update');

        Route::get('/visi', [ProfileController::class, 'visi'])->name('visi.edit');
        Route::match(['put', 'post'], '/visi', [ProfileController::class, 'updateVisi'])->name('visi.update');
    });

    // Pemerintahan - Perangkat Desa
    Route::get('/pemerintahan', [GovernmentStaffController::class, 'index'])->name('pemerintahan.index');
    Route::get('/pemerintahan/tambah', [GovernmentStaffController::class, 'create'])->name('pemerintahan.create');
    Route::post('/pemerintahan', [GovernmentStaffController::class, 'store'])->name('pemerintahan.store');
    Route::get('/pemerintahan/{staff}/edit', [GovernmentStaffController::class, 'edit'])->name('pemerintahan.edit');
    Route::put('/pemerintahan/{staff}', [GovernmentStaffController::class, 'update'])->name('pemerintahan.update');
    Route::delete('/pemerintahan/{staff}', [GovernmentStaffController::class, 'destroy'])->name('pemerintahan.destroy');

    // Pemerintahan - Lembaga
    Route::get('/lembaga', [InstitutionController::class, 'index'])->name('lembaga.index');
    Route::get('/lembaga/tambah', [InstitutionController::class, 'create'])->name('lembaga.create');
    Route::post('/lembaga', [InstitutionController::class, 'store'])->name('lembaga.store');
    Route::get('/lembaga/{institution}/edit', [InstitutionController::class, 'edit'])->name('lembaga.edit');
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
    Route::get('/kategori-berita/tambah', [PostCategoryController::class, 'create'])->name('kategori-berita.create');
    Route::post('/kategori-berita', [PostCategoryController::class, 'store'])->name('kategori-berita.store');
    Route::get('/kategori-berita/{postCategory}/edit', [PostCategoryController::class, 'edit'])->name('kategori-berita.edit');
    Route::put('/kategori-berita/{postCategory}', [PostCategoryController::class, 'update'])->name('kategori-berita.update');
    Route::delete('/kategori-berita/{postCategory}', [PostCategoryController::class, 'destroy'])->name('kategori-berita.destroy');

    // Komentar
    Route::get('/komentar', [CommentController::class, 'index'])->name('komentar.index');
    Route::put('/komentar/{comment}/approve', [CommentController::class, 'approve'])->name('komentar.approve');
    Route::delete('/komentar/{comment}', [CommentController::class, 'destroy'])->name('komentar.destroy');

    // Agenda
    Route::get('/agenda', [EventController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/tambah', [EventController::class, 'create'])->name('agenda.create');
    Route::post('/agenda', [EventController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{event}/edit', [EventController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{event}', [EventController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{event}', [EventController::class, 'destroy'])->name('agenda.destroy');

    // Kategori Agenda
    Route::get('/kategori-agenda', [EventCategoryController::class, 'index'])->name('kategori-agenda.index');
    Route::post('/kategori-agenda', [EventCategoryController::class, 'store'])->name('kategori-agenda.store');
    Route::put('/kategori-agenda/{eventCategory}', [EventCategoryController::class, 'update'])->name('kategori-agenda.update');
    Route::delete('/kategori-agenda/{eventCategory}', [EventCategoryController::class, 'destroy'])->name('kategori-agenda.destroy');

    // Layanan Surat
    Route::get('/layanan-surat', [LetterController::class, 'index'])->name('layanan-surat.index');
    Route::get('/layanan-surat/{request}/detail', [LetterController::class, 'show'])->name('layanan-surat.show');
    Route::put('/layanan-surat/{request}/status', [LetterController::class, 'updateStatus'])->name('layanan-surat.update-status');
    Route::get('/layanan-surat/{request}/cetak', [LetterController::class, 'cetak'])->name('layanan-surat.cetak');
    Route::get('/layanan-surat/kategori', [LetterController::class, 'categoryIndex'])->name('layanan-surat.kategori.index');
    Route::post('/layanan-surat/kategori', [LetterController::class, 'categoryStore'])->name('layanan-surat.kategori.store');
    Route::put('/layanan-surat/kategori/{category}', [LetterController::class, 'categoryUpdate'])->name('layanan-surat.kategori.update');
    Route::delete('/layanan-surat/kategori/{category}', [LetterController::class, 'categoryDestroy'])->name('layanan-surat.kategori.destroy');
    Route::post('/layanan-surat/template', [LetterController::class, 'templateStore'])->name('layanan-surat.template.store');
    Route::put('/layanan-surat/template/{template}', [LetterController::class, 'templateUpdate'])->name('layanan-surat.template.update');
    Route::delete('/layanan-surat/template/{template}', [LetterController::class, 'templateDestroy'])->name('layanan-surat.template.destroy');

    // Pengaduan
    Route::get('/pengaduan', [ComplaintController::class, 'index'])->name('pengaduan.index');
    Route::get('/pengaduan/{complaint}', [ComplaintController::class, 'show'])->name('pengaduan.show');
    Route::put('/pengaduan/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('pengaduan.update-status');
    Route::post('/pengaduan/{complaint}/balasan', [ComplaintController::class, 'storeReply'])->name('pengaduan.reply.store');

    // Kependudukan
    Route::get('/kependudukan', [ResidentController::class, 'index'])->name('kependudukan.index');
    Route::get('/kependudukan/kk', [FamilyController::class, 'index'])->name('kependudukan.kk.index');
    Route::post('/kependudukan/kk', [FamilyController::class, 'store'])->name('kependudukan.kk.store');
    Route::put('/kependudukan/kk/{family}', [FamilyController::class, 'update'])->name('kependudukan.kk.update');
    Route::get('/kependudukan/kk/{family}', [FamilyController::class, 'show'])->name('kependudukan.kk.show');
    Route::delete('/kependudukan/kk/{family}', [FamilyController::class, 'destroy'])->name('kependudukan.kk.destroy');
    Route::get('/kependudukan/penduduk', [ResidentController::class, 'index'])->name('kependudukan.penduduk.index');
    Route::post('/kependudukan/penduduk', [ResidentController::class, 'store'])->name('kependudukan.penduduk.store');
    Route::put('/kependudukan/penduduk/{resident}', [ResidentController::class, 'update'])->name('kependudukan.penduduk.update');
    Route::delete('/kependudukan/penduduk/{resident}', [ResidentController::class, 'destroy'])->name('kependudukan.penduduk.destroy');
    Route::get('/kependudukan/export', [ResidentController::class, 'export'])->name('kependudukan.export');
    Route::post('/kependudukan/import', [ResidentController::class, 'import'])->name('kependudukan.import');
    Route::get('/kependudukan/mutasi', [MutationController::class, 'index'])->name('kependudukan.mutasi.index');
    Route::post('/kependudukan/mutasi', [MutationController::class, 'store'])->name('kependudukan.mutasi.store');

    // Galeri
    Route::get('/galeri', [GalleryController::class, 'index'])->name('galeri.index');
    Route::post('/galeri', [GalleryController::class, 'store'])->name('galeri.store');
    Route::put('/galeri/{gallery}', [GalleryController::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{gallery}', [GalleryController::class, 'destroy'])->name('galeri.destroy');
    Route::post('/galeri/{gallery}/foto', [GalleryController::class, 'storeImage'])->name('galeri.image.store');
    Route::delete('/galeri/{gallery}/foto/{image}', [GalleryController::class, 'destroyImage'])->name('galeri.image.destroy');

    // Video
    Route::get('/video', [GalleryController::class, 'videoIndex'])->name('video.index');
    Route::post('/video', [GalleryController::class, 'videoStore'])->name('video.store');
    Route::put('/video/{video}', [GalleryController::class, 'videoUpdate'])->name('video.update');
    Route::delete('/video/{video}', [GalleryController::class, 'videoDestroy'])->name('video.destroy');

    // UMKM
    Route::get('/umkm', [UmkmController::class, 'index'])->name('umkm.index');
    Route::get('/umkm/tambah', [UmkmController::class, 'create'])->name('umkm.create');
    Route::post('/umkm', [UmkmController::class, 'store'])->name('umkm.store');
    Route::get('/umkm/{umkm}/edit', [UmkmController::class, 'edit'])->name('umkm.edit');
    Route::put('/umkm/{umkm}', [UmkmController::class, 'update'])->name('umkm.update');
    Route::delete('/umkm/{umkm}', [UmkmController::class, 'destroy'])->name('umkm.destroy');
    Route::post('/umkm/{umkm}/foto', [UmkmController::class, 'storeImage'])->name('umkm.image.store');
    Route::delete('/umkm/{umkm}/foto/{image}', [UmkmController::class, 'destroyImage'])->name('umkm.image.destroy');
    Route::get('/kategori-umkm', [UmkmController::class, 'categoryIndex'])->name('umkm.kategori.index');
    Route::post('/kategori-umkm', [UmkmController::class, 'categoryStore'])->name('umkm.kategori.store');
    Route::put('/kategori-umkm/{category}', [UmkmController::class, 'categoryUpdate'])->name('umkm.kategori.update');
    Route::delete('/kategori-umkm/{category}', [UmkmController::class, 'categoryDestroy'])->name('umkm.kategori.destroy');

    // Potensi
    Route::get('/potensi', [PotentialController::class, 'index'])->name('potensi.index');
    Route::get('/potensi/tambah', [PotentialController::class, 'create'])->name('potensi.create');
    Route::post('/potensi', [PotentialController::class, 'store'])->name('potensi.store');
    Route::get('/potensi/{potential}/edit', [PotentialController::class, 'edit'])->name('potensi.edit');
    Route::put('/potensi/{potential}', [PotentialController::class, 'update'])->name('potensi.update');
    Route::delete('/potensi/{potential}', [PotentialController::class, 'destroy'])->name('potensi.destroy');

    // APBDes
    Route::get('/apbdes', [BudgetController::class, 'index'])->name('apbdes.index');
    Route::get('/apbdes/tambah', [BudgetController::class, 'create'])->name('apbdes.create');
    Route::post('/apbdes', [BudgetController::class, 'store'])->name('apbdes.store');
    Route::get('/apbdes/{budget}/edit', [BudgetController::class, 'edit'])->name('apbdes.edit');
    Route::put('/apbdes/{budget}', [BudgetController::class, 'update'])->name('apbdes.update');
    Route::delete('/apbdes/{budget}', [BudgetController::class, 'destroy'])->name('apbdes.destroy');

    // Pembangunan
    Route::get('/pembangunan', [DevelopmentController::class, 'index'])->name('pembangunan.index');
    Route::get('/pembangunan/tambah', [DevelopmentController::class, 'create'])->name('pembangunan.create');
    Route::post('/pembangunan', [DevelopmentController::class, 'store'])->name('pembangunan.store');
    Route::get('/pembangunan/{development}/edit', [DevelopmentController::class, 'edit'])->name('pembangunan.edit');
    Route::put('/pembangunan/{development}', [DevelopmentController::class, 'update'])->name('pembangunan.update');
    Route::delete('/pembangunan/{development}', [DevelopmentController::class, 'destroy'])->name('pembangunan.destroy');
    Route::post('/pembangunan/{development}/foto', [DevelopmentController::class, 'storeImage'])->name('pembangunan.image.store');
    Route::delete('/pembangunan/{development}/foto/{image}', [DevelopmentController::class, 'destroyImage'])->name('pembangunan.image.destroy');

    // Halaman
    Route::get('/halaman', [PageController::class, 'index'])->name('halaman.index');
    Route::get('/halaman/tambah', [PageController::class, 'create'])->name('halaman.create');
    Route::post('/halaman', [PageController::class, 'store'])->name('halaman.store');
    Route::get('/halaman/{page}/edit', [PageController::class, 'edit'])->name('halaman.edit');
    Route::put('/halaman/{page}', [PageController::class, 'update'])->name('halaman.update');
    Route::delete('/halaman/{page}', [PageController::class, 'destroy'])->name('halaman.destroy');

    // Slider
    Route::get('/slider', [SliderController::class, 'index'])->name('slider.index');
    Route::get('/slider/tambah', [SliderController::class, 'create'])->name('slider.create');
    Route::post('/slider', [SliderController::class, 'store'])->name('slider.store');
    Route::get('/slider/{slider}/edit', [SliderController::class, 'edit'])->name('slider.edit');
    Route::put('/slider/{slider}', [SliderController::class, 'update'])->name('slider.update');
    Route::delete('/slider/{slider}', [SliderController::class, 'destroy'])->name('slider.destroy');
    Route::put('/slider/sort', [SliderController::class, 'sort'])->name('slider.sort');

    // Pengumuman
    Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('pengumuman.index');
    Route::get('/pengumuman/tambah', [AnnouncementController::class, 'create'])->name('pengumuman.create');
    Route::post('/pengumuman', [AnnouncementController::class, 'store'])->name('pengumuman.store');
    Route::get('/pengumuman/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('pengumuman.edit');
    Route::put('/pengumuman/{announcement}', [AnnouncementController::class, 'update'])->name('pengumuman.update');
    Route::delete('/pengumuman/{announcement}', [AnnouncementController::class, 'destroy'])->name('pengumuman.destroy');

    // Download
    Route::get('/download', [DownloadController::class, 'index'])->name('download.index');
    Route::get('/download/tambah', [DownloadController::class, 'create'])->name('download.create');
    Route::post('/download', [DownloadController::class, 'store'])->name('download.store');
    Route::get('/download/{download}/edit', [DownloadController::class, 'edit'])->name('download.edit');
    Route::put('/download/{download}', [DownloadController::class, 'update'])->name('download.update');
    Route::delete('/download/{download}', [DownloadController::class, 'destroy'])->name('download.destroy');

    // Pengguna
    Route::get('/pengguna', [DashboardController::class, 'users'])->name('users.index');
});
