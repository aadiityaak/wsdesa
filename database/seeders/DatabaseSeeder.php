<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Comment;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Development;
use App\Models\DevelopmentCategory;
use App\Models\DevelopmentImage;
use App\Models\Download;
use App\Models\DownloadCategory;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Family;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\GovernmentStaff;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\LetterCategory;
use App\Models\LetterRequirement;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Potential;
use App\Models\PotentialCategory;
use App\Models\Profile;
use App\Models\Resident;
use App\Models\Slider;
use App\Models\Umkm;
use App\Models\UmkmCategory;
use App\Models\UmkmImage;
use App\Models\User;
use App\Models\Video;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === ADMIN USER ===
        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
        ]);

        // === PROFILE DESA ===
        $this->call(ProfileSeeder::class);

        // === WILAYAH (data dari Kepmendagri) ===
        $this->call(WilayahSeeder::class);

        // === PEMERINTAHAN ===
        GovernmentStaff::factory(10)->create();

        // === LEMBAGA DESA ===
        $institutionNames = ['BPD', 'LPM', 'PKK', 'Karang Taruna', 'Linmas'];
        foreach ($institutionNames as $i => $name) {
            Institution::factory()->create([
                'nama' => $name,
                'singkatan' => $name,
                'jenis' => $name,
            ])->members()->createMany(
                InstitutionMember::factory()->count($i === 0 ? 5 : 4)->make()->toArray()
            );
        }

        // === GALERI ===
        Gallery::factory(3)->has(GalleryImage::factory()->count(5), 'images')->create();

        // === VIDEO ===
        Video::factory(5)->create();

        // === KATEGORI BERITA + BERITA ===
        $postCategoryNames = ['Berita Desa', 'Pengumuman', 'Kegiatan', 'Pembangunan', 'Pemerintahan'];
        foreach ($postCategoryNames as $nama) {
            PostCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama)]);
        }

        User::factory(3)->sequence(
            ['name' => 'Admin Berita', 'role' => 'operator'],
            ['name' => 'Editor Desa', 'role' => 'operator'],
            ['name' => 'Kontributor', 'role' => 'operator'],
        )->create();

        Post::factory(20)->create();

        // === AGENDA ===
        $eventCategoryNames = ['Gotong Royong', 'Rapat Desa', 'Perlombaan', 'Keagamaan'];
        foreach ($eventCategoryNames as $nama) {
            EventCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama)]);
        }

        Event::factory(12)->create();

        // === KOMENTAR ===
        Comment::factory(40)->create();

        // === LAYANAN SURAT ===
        $letterNames = [
            'Surat Keterangan Domisili',
            'Surat Keterangan Usaha',
            'Surat Keterangan Tidak Mampu',
            'Surat Keterangan Kelahiran',
            'Surat Keterangan Kematian',
            'Surat Pengantar KTP',
            'Surat Keterangan Nikah',
        ];
        foreach ($letterNames as $i => $nama) {
            LetterCategory::factory()->create([
                'nama' => $nama,
                'slug' => Str::slug($nama),
                'urutan' => $i,
            ])->requirements()->createMany(
                LetterRequirement::factory()->count(3)->make()->toArray()
            );
        }

        // === PENGADUAN ===
        $complaintNames = ['Pelayanan Publik', 'Infrastruktur', 'Lingkungan', 'Sosial', 'Lainnya'];
        foreach ($complaintNames as $nama) {
            ComplaintCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama)]);
        }

        Complaint::factory(15)->create();

        // === KEPENDUDUKAN ===
        for ($i = 0; $i < 50; $i++) {
            $family = Family::factory()->create();
            $family->residents()->createMany(
                [Resident::factory()->kepalaKeluarga()->make()->toArray()]
            );
            $family->residents()->createMany(
                Resident::factory()->count(rand(2, 4))->make()->toArray()
            );
        }

        // === UMKM ===
        $umkmNames = ['Kuliner', 'Kerajinan', 'Fashion', 'Pertanian', 'Jasa'];
        foreach ($umkmNames as $nama) {
            UmkmCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama)]);
        }

        Umkm::factory(8)->has(UmkmImage::factory()->count(3), 'images')->create();

        // === POTENSI DESA ===
        $potCatNames = ['Pertanian', 'Pariwisata', 'Kerajinan'];
        foreach ($potCatNames as $nama) {
            PotentialCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama)]);
        }

        Potential::factory(6)->create();

        // === APBDES ===
        $budgetData = [
            ['Pendapatan Asli Desa', 'Pendapatan'],
            ['Dana Desa', 'Pendapatan'],
            ['Alokasi Dana Desa', 'Pendapatan'],
            ['Bantuan Keuangan', 'Pendapatan'],
            ['Belanja Pegawai', 'Belanja'],
            ['Belanja Barang/Jasa', 'Belanja'],
            ['Belanja Modal', 'Belanja'],
            ['Pembiayaan Netto', 'Pembiayaan'],
        ];
        foreach ($budgetData as [$nama, $tipe]) {
            BudgetCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama), 'tipe' => $tipe]);
        }

        Budget::factory(24)->create();

        // === PEMBANGUNAN ===
        $devNames = ['Infrastruktur', 'Pendidikan', 'Kesehatan', 'Ekonomi'];
        foreach ($devNames as $nama) {
            DevelopmentCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama)]);
        }

        Development::factory(6)->has(DevelopmentImage::factory()->count(3), 'images')->create();

        // === HALAMAN ===
        Page::factory()->sequence(
            ['judul' => 'Tentang Desa', 'slug' => 'tentang-desa'],
            ['judul' => 'Visi & Misi', 'slug' => 'visi-misi'],
            ['judul' => 'Sejarah Desa', 'slug' => 'sejarah-desa'],
            ['judul' => 'Struktur Organisasi', 'slug' => 'struktur-organisasi'],
            ['judul' => 'Kontak', 'slug' => 'kontak'],
        )->create();

        // === SLIDER ===
        Slider::factory(5)->create();

        // === PENGUMUMAN ===
        Announcement::factory(3)->create();

        // === DOWNLOAD ===
        $dlNames = ['Dokumen Desa', 'Peraturan', 'Formulir'];
        foreach ($dlNames as $nama) {
            DownloadCategory::factory()->create(['nama' => $nama, 'slug' => Str::slug($nama)]);
        }

        Download::factory(8)->create();
    }
}
