<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\ComplaintCategory;
use App\Models\Development;
use App\Models\DevelopmentCategory;
use App\Models\DevelopmentImage;
use App\Models\Download;
use App\Models\DownloadCategory;
use App\Models\Family;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\GovernmentStaff;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\LetterCategory;
use App\Models\LetterRequirement;
use App\Models\Page;
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
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ],
        );
        $admin->forceFill(['role' => 'super_admin'])->save();

        // 2. Profile Desa
        Profile::firstOrCreate(
            ['nama_desa' => 'Desa Digital'],
            [
                'kode_desa' => '32.05.01.2001',
                'kecamatan' => 'Kecamatan Digital',
                'kabupaten' => 'Kabupaten Digital',
                'provinsi' => 'Jawa Barat',
                'alamat' => 'Jl. Pahlawan Digital No. 1, RT 001 RW 001',
                'kode_pos' => '44150',
                'telepon' => '0262-123456',
                'email' => 'info@desadigital.id',
                'website' => 'https://desadigital.id',
                'latitude' => '-7.250445',
                'longitude' => '107.915817',
                'visi' => "Terwujudnya Desa Digital yang mandiri, inovatif, dan berdaya saing melalui pemanfaatan teknologi informasi serta pemberdayaan masyarakat yang berlandaskan nilai-nilai gotong royong dan kearifan lokal.\n\nDesa Digital bercita-cita menjadi desa percontohan di Jawa Barat dalam hal digitalisasi pelayanan publik dan pemberdayaan ekonomi berbasis teknologi.",
                'misi' => "1. Meningkatkan kualitas pelayanan publik berbasis teknologi informasi yang transparan, cepat, dan akuntabel.\n2. Mendorong perekonomian desa melalui pengembangan UMKM digital dan BUMDes yang profesional.\n3. Memperkuat sumber daya manusia melalui pendidikan formal, pelatihan keterampilan, dan literasi digital.\n4. Membangun infrastruktur desa yang memadai dan merata untuk menunjang aktivitas sosial-ekonomi masyarakat.\n5. Melestarikan budaya lokal dan kearifan tradisional sebagai identitas Desa Digital.",
                'sejarah' => "Desa Digital merupakan desa yang terbentuk dari pemekaran wilayah pada tahun 1980-an, awalnya merupakan bagian dari Kecamatan Induk yang dimekarkan menjadi Kecamatan Digital. Nama \"Digital\" sendiri diambil dari harapan agar desa ini mampu menjadi pionir dalam adopsi teknologi di wilayah Kabupaten Digital.\n\nSejak awal berdirinya, Desa Digital telah mengalami tiga kali pergantian kepemimpinan kepala desa. Masing-masing periode membawa kemajuan yang signifikan, mulai dari pembangunan jalan desa, pendirian balai desa, hingga peresmian kantor pelayanan terpadu.\n\nDi era modern ini, Desa Digital telah bertransformasi menjadi desa yang mengedepankan sistem pemerintahan berbasis elektronik dan terus berupaya meningkatkan kesejahteraan warganya melalui berbagai program inovatif.",
                'luas_wilayah' => '525,75 Ha',
                'batas_utara' => 'Desa Mekarjaya',
                'batas_selatan' => 'Desa Sukamaju',
                'batas_timur' => 'Desa Cibeureum',
                'batas_barat' => 'Desa Karyamukti',
                'orbitasi_ke_kecamatan' => '3 km',
                'orbitasi_ke_kabupaten' => '15 km',
                'facebook' => 'https://facebook.com/desadigital',
                'instagram' => 'https://instagram.com/desadigital',
                'youtube' => 'https://youtube.com/@desadigital',
                'tiktok' => 'https://tiktok.com/@desadigital',
            ],
        );

        // 3. Government Staff (10)
        if (GovernmentStaff::count() === 0) {
            GovernmentStaff::factory(10)->create();
        }

        // 4. Institutions with members
        if (Institution::count() === 0) {
            $institutionData = [
                [
                    'nama' => 'Badan Permusyawaratan Desa',
                    'singkatan' => 'BPD',
                    'jenis' => 'BPD',
                    'deskripsi' => 'Badan Permusyawaratan Desa merupakan lembaga yang melaksanakan fungsi pemerintahan di tingkat desa. BPD berfungsi menetapkan peraturan desa bersama Kepala Desa, menampung dan menyalurkan aspirasi masyarakat, serta melakukan pengawasan terhadap kinerja Kepala Desa.',
                ],
                [
                    'nama' => 'Lembaga Pemberdayaan Masyarakat',
                    'singkatan' => 'LPM',
                    'jenis' => 'LPM',
                    'deskripsi' => 'Lembaga Pemberdayaan Masyarakat adalah mitra pemerintah desa dalam merencanakan, melaksanakan, dan mengawasi pembangunan partisipatif. LPM berperan aktif dalam menggerakkan swadaya gotong royong dan pemberdayaan masyarakat desa.',
                ],
                [
                    'nama' => 'Pemberdayaan Kesejahteraan Keluarga',
                    'singkatan' => 'PKK',
                    'jenis' => 'PKK',
                    'deskripsi' => 'PKK adalah organisasi kemasyarakatan yang memberdayakan wanita untuk turut berpartisipasi dalam pembangunan. PKK Desa Digital aktif dalam 10 program pokok PKK, mulai dari penghayatan Pancasila hingga kesehatan dan kelestarian lingkungan.',
                ],
                [
                    'nama' => 'Karang Taruna',
                    'singkatan' => 'Karang Taruna',
                    'jenis' => 'Karang Taruna',
                    'deskripsi' => 'Karang Taruna Desa Digital merupakan wadah pengembangan generasi muda yang bergerak di bidang kesejahteraan sosial. Organisasi ini aktif dalam kegiatan kepemudaan, olahraga, seni budaya, dan bakti sosial di masyarakat.',
                ],
                [
                    'nama' => 'Linmas',
                    'singkatan' => 'Linmas',
                    'jenis' => 'Linmas',
                    'deskripsi' => 'Perlindungan Masyarakat (Linmas) Desa Digital bertugas membantu keamanan dan ketertiban desa, penanggulangan bencana, serta pengamanan kegiatan masyarakat dan pemilu. Anggota Linmas merupakan warga terpilih yang telah mendapatkan pelatihan dasar.',
                ],
            ];

            $memberJabatans = [
                ['Ketua', 1],
                ['Wakil Ketua', 2],
                ['Sekretaris', 3],
                ['Bendahara', 4],
                ['Anggota', 5],
            ];

            foreach ($institutionData as $data) {
                $institution = Institution::create($data);

                $memberCount = rand(3, 5);
                for ($i = 0; $i < $memberCount; $i++) {
                    InstitutionMember::create([
                        'institution_id' => $institution->id,
                        'nama' => fake()->name(),
                        'jabatan' => $memberJabatans[$i][0],
                        'telepon' => fake()->phoneNumber(),
                        'urutan' => $memberJabatans[$i][1],
                    ]);
                }
            }
        }

        // ========== FASE 5-7 SEED DATA ==========

        // 5. Galleries
        $galleryData = [
            ['nama' => 'Kegiatan Posyandu RW 01', 'deskripsi' => 'Kegiatan rutin posyandu yang dilaksanakan setiap bulan di RW 01 Desa Digital.'],
            ['nama' => 'Gotong Royong Bersih Desa', 'deskripsi' => 'Kegiatan gotong royong membersihkan lingkungan desa yang diikuti seluruh warga.'],
            ['nama' => 'Perayaan HUT RI ke-79', 'deskripsi' => 'Perayaan Hari Ulang Tahun Republik Indonesia ke-79 di Lapangan Desa Digital.'],
        ];

        if (Gallery::count() === 0) {
            foreach ($galleryData as $i => $data) {
                $gallery = Gallery::create([
                    'nama' => $data['nama'],
                    'slug' => Str::slug($data['nama']),
                    'deskripsi' => $data['deskripsi'],
                    'user_id' => $admin->id,
                ]);

                $imageCount = rand(4, 6);
                for ($j = 0; $j < $imageCount; $j++) {
                    GalleryImage::create([
                        'gallery_id' => $gallery->id,
                        'judul' => 'Foto '.($j + 1).' - '.$data['nama'],
                        'file' => 'seed/gallery-'.($i + 1).'-'.($j + 1).'.jpg',
                        'urutan' => $j,
                    ]);
                }
            }
        }

        // 6. Videos
        if (Video::count() === 0) {
            $videos = [
                ['judul' => 'Profil Desa Digital', 'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'deskripsi' => 'Video profil Desa Digital.'],
                ['judul' => 'Kegiatan Vaksinasi Massal', 'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'deskripsi' => 'Dokumentasi kegiatan vaksinasi massal di balai desa.'],
            ];
            foreach ($videos as $v) {
                Video::create($v);
            }
        }

        // 7. UMKM Categories & UMKMs
        $umkmCategories = ['Kuliner', 'Kerajinan', 'Pertanian', 'Fashion', 'Jasa'];

        if (UmkmCategory::count() === 0) {
            foreach ($umkmCategories as $cat) {
                UmkmCategory::create([
                    'nama' => $cat,
                    'slug' => Str::slug($cat),
                ]);
            }
        }

        if (Umkm::count() === 0) {
            $umkmData = [
                ['nama_usaha' => 'Keripik Singkong Makmur', 'pemilik' => 'Budi Santoso', 'alamat' => 'RT 002 RW 001', 'deskripsi' => 'Keripik singkong renyah dengan berbagai varian rasa.'],
                ['nama_usaha' => 'Batik Tulis Digital', 'pemilik' => 'Siti Aminah', 'alamat' => 'RT 003 RW 002', 'deskripsi' => 'Batik tulis khas Desa Digital dengan motif kontemporer.'],
                ['nama_usaha' => 'Sayur Organik Sejahtera', 'pemilik' => 'Pak Haryono', 'alamat' => 'RT 001 RW 003', 'deskripsi' => 'Pertanian sayur organik tanpa pestisida.'],
                ['nama_usaha' => 'Jahit Busana Cantik', 'pemilik' => 'Rina Marlina', 'alamat' => 'RT 004 RW 001', 'deskripsi' => 'Jasa jahit busana wanita dan anak dengan model terkini.'],
                ['nama_usaha' => 'Kue Basah Bunda', 'pemilik' => 'Ani Rahayu', 'alamat' => 'RT 001 RW 002', 'deskripsi' => 'Aneka kue basah tradisional dan modern.'],
                ['nama_usaha' => 'Anyaman Bambu Kreatif', 'pemilik' => 'Wawan Hermawan', 'alamat' => 'RT 005 RW 001', 'deskripsi' => 'Kerajinan anyaman bambu untuk hiasan rumah.'],
                ['nama_usaha' => 'Ternak Lele Berkah', 'pemilik' => 'Asep Saepudin', 'alamat' => 'RT 002 RW 004', 'deskripsi' => 'Budidaya lele sistem bioflok hasil melimpah.'],
                ['nama_usaha' => 'Laundry Kilat Bersih', 'pemilik' => 'Dewi Kusuma', 'alamat' => 'RT 003 RW 001', 'deskripsi' => 'Jasa laundry kilat untuk warga Desa Digital.'],
            ];

            foreach ($umkmData as $u) {
                $umkm = Umkm::create([
                    'umkm_category_id' => UmkmCategory::inRandomOrder()->first()->id,
                    'nama_usaha' => $u['nama_usaha'],
                    'slug' => Str::slug($u['nama_usaha']),
                    'pemilik' => $u['pemilik'],
                    'alamat' => $u['alamat'],
                    'telepon' => '08'.fake()->randomNumber(8, true),
                    'deskripsi' => $u['deskripsi'],
                    'latitude' => '-7.250'.rand(100, 999),
                    'longitude' => '107.915'.rand(100, 999),
                ]);

                $imgCount = rand(2, 3);
                for ($j = 0; $j < $imgCount; $j++) {
                    UmkmImage::create([
                        'umkm_id' => $umkm->id,
                        'file' => 'seed/umkm-'.$umkm->id.'-'.($j + 1).'.jpg',
                        'urutan' => $j,
                    ]);
                }
            }
        }

        // 8. Potential Categories & Potentials
        $potentialCategories = ['Wisata Alam', 'Budaya & Tradisi', 'Sumber Daya Alam'];

        if (PotentialCategory::count() === 0) {
            foreach ($potentialCategories as $cat) {
                PotentialCategory::create([
                    'nama' => $cat,
                    'slug' => Str::slug($cat),
                ]);
            }
        }

        if (Potential::count() === 0) {
            $potentialData = [
                ['nama' => 'Curug Cinta', 'deskripsi' => 'Air terjun alami dengan ketinggian 25 meter yang dikelilingi hutan tropis.', 'lokasi' => 'Dusun Cibodas', 'cat' => 'Wisata Alam'],
                ['nama' => 'Wisata Sawah Terasering', 'deskripsi' => 'Pemandangan sawah terasering yang indah dengan latar pegunungan.', 'lokasi' => 'Dusun Sukamaju', 'cat' => 'Wisata Alam'],
                ['nama' => 'Festival Panen Raya', 'deskripsi' => 'Tradisi tahunan syukuran hasil panen dengan berbagai kesenian tradisional.', 'lokasi' => 'Balai Desa', 'cat' => 'Budaya & Tradisi'],
                ['nama' => 'Tarian Topeng Desa', 'deskripsi' => 'Tarian topeng khas yang diwariskan turun-temurun sejak abad ke-18.', 'lokasi' => 'Sanggar Seni Desa', 'cat' => 'Budaya & Tradisi'],
                ['nama' => 'Batu Kapur Premium', 'deskripsi' => 'Tambang batu kapur kualitas tinggi untuk bahan bangunan dan industri.', 'lokasi' => 'Dusun Karangjaya', 'cat' => 'Sumber Daya Alam'],
                ['nama' => 'Hutan Bambu Lestari', 'deskripsi' => 'Kawasan hutan bambu seluas 15 hektar untuk kerajinan dan konservasi.', 'lokasi' => 'Dusun Bambu Kuning', 'cat' => 'Sumber Daya Alam'],
            ];

            foreach ($potentialData as $p) {
                $cat = PotentialCategory::where('nama', $p['cat'])->first();
                Potential::create([
                    'potential_category_id' => $cat?->id,
                    'nama' => $p['nama'],
                    'slug' => Str::slug($p['nama']),
                    'deskripsi' => $p['deskripsi'],
                    'lokasi' => $p['lokasi'],
                ]);
            }
        }

        // 9. APBDes Budget
        if (BudgetCategory::count() === 0) {
            $budgetCategories = [
                ['nama' => 'Pendapatan Asli Desa', 'slug' => 'pendapatan-asli-desa', 'tipe' => 'Pendapatan'],
                ['nama' => 'Dana Desa', 'slug' => 'dana-desa', 'tipe' => 'Pendapatan'],
                ['nama' => 'Alokasi Dana Desa', 'slug' => 'alokasi-dana-desa', 'tipe' => 'Pendapatan'],
                ['nama' => 'Bidang Pemerintahan', 'slug' => 'bidang-pemerintahan', 'tipe' => 'Belanja'],
                ['nama' => 'Bidang Pembangunan', 'slug' => 'bidang-pembangunan', 'tipe' => 'Belanja'],
                ['nama' => 'Bidang Pemberdayaan', 'slug' => 'bidang-pemberdayaan', 'tipe' => 'Belanja'],
                ['nama' => 'Silpa', 'slug' => 'silpa', 'tipe' => 'Pembiayaan'],
                ['nama' => 'Penyertaan Modal', 'slug' => 'penyertaan-modal', 'tipe' => 'Pembiayaan'],
            ];

            foreach ($budgetCategories as $bc) {
                BudgetCategory::create($bc);
            }
        }

        if (Budget::count() === 0) {
            $tahunData = [2024, 2025, 2026];
            $budgetEntries = [
                ['nama' => 'Pendapatan Asli Desa', 'anggaran' => 50000000],
                ['nama' => 'Dana Desa', 'anggaran' => 350000000],
                ['nama' => 'Alokasi Dana Desa', 'anggaran' => 200000000],
                ['nama' => 'Bidang Pemerintahan', 'anggaran' => 150000000],
                ['nama' => 'Bidang Pembangunan', 'anggaran' => 300000000],
                ['nama' => 'Bidang Pemberdayaan', 'anggaran' => 100000000],
                ['nama' => 'Silpa', 'anggaran' => 10000000],
                ['nama' => 'Penyertaan Modal', 'anggaran' => 5000000],
            ];

            foreach ($tahunData as $tahun) {
                foreach ($budgetEntries as $entry) {
                    $category = BudgetCategory::where('nama', $entry['nama'])->first();
                    $realisasi = rand(60, 100);
                    Budget::create([
                        'budget_category_id' => $category->id,
                        'tahun' => $tahun,
                        'anggaran' => $entry['anggaran'],
                        'realisasi' => round($entry['anggaran'] * $realisasi / 100, 2),
                        'keterangan' => $category->nama.' tahun '.$tahun,
                    ]);
                }
            }
        }

        // 10. Development Categories & Developments
        if (DevelopmentCategory::count() === 0) {
            $devCategories = ['Infrastruktur', 'Pendidikan', 'Kesehatan', 'Ekonomi'];
            foreach ($devCategories as $cat) {
                DevelopmentCategory::create([
                    'nama' => $cat,
                    'slug' => Str::slug($cat),
                ]);
            }
        }

        if (Development::count() === 0) {
            $devData = [
                ['nama' => 'Pembangunan Jalan Desa', 'deskripsi' => 'Pembangunan jalan desa sepanjang 2,5 km dengan beton.', 'status' => 'selesai', 'tahun' => 2024, 'sumber_dana' => 'Dana Desa', 'anggaran' => 250000000, 'cat' => 'Infrastruktur'],
                ['nama' => 'Renovasi PAUD Mawar', 'deskripsi' => 'Renovasi gedung PAUD Mawar beserta pengadaan alat peraga edukatif.', 'status' => 'berjalan', 'tahun' => 2025, 'sumber_dana' => 'ADD', 'anggaran' => 85000000, 'cat' => 'Pendidikan'],
                ['nama' => 'Posyandu Sehat Bersama', 'deskripsi' => 'Pembangunan gedung posyandu baru dengan fasilitas lengkap.', 'status' => 'rencana', 'tahun' => 2026, 'sumber_dana' => 'Dana Desa', 'anggaran' => 120000000, 'cat' => 'Kesehatan'],
                ['nama' => 'Pasar Desa Modern', 'deskripsi' => 'Revitalisasi pasar desa menjadi pasar modern dengan 50 kios.', 'status' => 'berjalan', 'tahun' => 2025, 'sumber_dana' => 'APBD Kabupaten', 'anggaran' => 500000000, 'cat' => 'Ekonomi'],
            ];

            $statuses = ['rencana', 'berjalan', 'selesai'];
            foreach ($devData as $i => $d) {
                $cat = DevelopmentCategory::where('nama', $d['cat'])->first();
                $dev = Development::create([
                    'development_category_id' => $cat?->id,
                    'nama' => $d['nama'],
                    'slug' => Str::slug($d['nama']),
                    'lokasi' => 'RT 00'.(rand(1, 5)).' RW 00'.(rand(1, 4)),
                    'deskripsi' => $d['deskripsi'],
                    'anggaran' => $d['anggaran'],
                    'sumber_dana' => $d['sumber_dana'],
                    'tahun' => $d['tahun'],
                    'status' => $d['status'],
                    'latitude' => '-7.250'.rand(100, 999),
                    'longitude' => '107.915'.rand(100, 999),
                ]);

                for ($j = 0; $j < rand(2, 4); $j++) {
                    DevelopmentImage::create([
                        'development_id' => $dev->id,
                        'file' => 'seed/development-'.($i + 1).'-'.($j + 1).'.jpg',
                        'keterangan' => 'Foto '.($j + 1).' - '.$d['nama'],
                    ]);
                }
            }
        }

        // 11. Pages
        if (Page::count() === 0) {
            $pages = [
                [
                    'judul' => 'Tentang Desa',
                    'slug' => 'tentang',
                    'konten' => '<h2>Tentang Desa Digital</h2><p>Desa Digital adalah desa yang terletak di Kecamatan Digital, Kabupaten Digital, Jawa Barat. Desa ini memiliki visi menjadi desa mandiri berbasis teknologi informasi.</p><p>Dengan luas wilayah 525,75 Ha, Desa Digital terdiri dari 3 dusun dan 15 RT dengan jumlah penduduk sekitar 4.500 jiwa.</p>',
                    'status' => 'publish',
                ],
                [
                    'judul' => 'Kontak Kami',
                    'slug' => 'kontak',
                    'konten' => '<h2>Hubungi Kami</h2><p>Kantor Desa Digital beralamat di Jl. Pahlawan Digital No. 1, RT 001 RW 001, Kecamatan Digital, Kabupaten Digital, Jawa Barat 44150.</p><p><strong>Telepon:</strong> 0262-123456</p><p><strong>Email:</strong> info@desadigital.id</p><p><strong>Jam Pelayanan:</strong> Senin - Jumat, 08.00 - 15.00 WIB</p>',
                    'status' => 'publish',
                ],
                [
                    'judul' => 'FAQ',
                    'slug' => 'faq',
                    'konten' => '<h2>Pertanyaan yang Sering Diajukan</h2><h3>Bagaimana cara mengurus KTP?</h3><p>Silakan datang ke kantor desa dengan membawa KK dan surat pengantar RT/RW.</p><h3>Kapan jadwal posyandu?</h3><p>Posyandu dilaksanakan setiap tanggal 15 setiap bulannya.</p><h3>Bagaimana cara mengajukan proposal bantuan?</h3><p>Proposal dapat disampaikan langsung ke kantor desa dengan tembusan BPD.</p>',
                    'status' => 'publish',
                ],
            ];

            foreach ($pages as $page) {
                Page::create($page);
            }
        }

        // 12. Sliders
        if (Slider::count() === 0) {
            $sliders = [
                ['judul' => 'Selamat Datang di Desa Digital', 'deskripsi' => 'Desa mandiri berbasis teknologi informasi', 'link' => '/tentang', 'urutan' => 0],
                ['judul' => 'Layanan Administrasi Online', 'deskripsi' => 'Mudah, cepat, dan transparan', 'link' => '/layanan', 'urutan' => 1],
                ['judul' => 'UMKM Desa Digital', 'deskripsi' => 'Dukung produk lokal desa kita', 'link' => '/umkm', 'urutan' => 2],
            ];

            foreach ($sliders as $s) {
                Slider::create([
                    'judul' => $s['judul'],
                    'deskripsi' => $s['deskripsi'],
                    'image' => 'seed/slider-'.($s['urutan'] + 1).'.jpg',
                    'link' => $s['link'],
                    'urutan' => $s['urutan'],
                    'is_active' => true,
                ]);
            }
        }

        // 13. Announcements
        if (Announcement::count() === 0) {
            Announcement::create([
                'judul' => 'Pendaftaran BPJS Kesehatan Dibuka',
                'konten' => 'Pendaftaran BPJS Kesehatan bagi warga Desa Digital dibuka mulai 1-15 Agustus 2024 di Kantor Desa. Silakan membawa KTP dan KK.',
                'tipe' => 'info',
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(30),
                'is_active' => true,
            ]);

            Announcement::create([
                'judul' => 'Waspada Musim Hujan',
                'konten' => 'Masyarakat diimbau untuk waspada terhadap potensi banjir dan longsor selama musim hujan. Segera laporkan ke perangkat desa jika terjadi bencana.',
                'tipe' => 'warning',
                'tanggal_mulai' => now(),
                'tanggal_selesai' => now()->addDays(60),
                'is_active' => true,
            ]);
        }

        // 14. Download Categories & Downloads
        if (DownloadCategory::count() === 0) {
            $dlCategories = ['Peraturan Desa', 'Laporan Keuangan', 'Formulir'];
            foreach ($dlCategories as $cat) {
                DownloadCategory::create([
                    'nama' => $cat,
                    'slug' => Str::slug($cat),
                ]);
            }
        }

        if (Download::count() === 0) {
            $downloads = [
                ['nama' => 'Perdes APBDes 2025', 'cat' => 'Peraturan Desa'],
                ['nama' => 'Perdes RKPDes 2025', 'cat' => 'Peraturan Desa'],
                ['nama' => 'Laporan Keuangan Semester 1 2025', 'cat' => 'Laporan Keuangan'],
                ['nama' => 'Formulir Permohonan KK', 'cat' => 'Formulir'],
            ];

            foreach ($downloads as $d) {
                $cat = DownloadCategory::where('nama', $d['cat'])->first();
                Download::create([
                    'download_category_id' => $cat?->id,
                    'nama' => $d['nama'],
                    'file' => 'seed/downloads/'.Str::slug($d['nama']).'.pdf',
                ]);
            }
        }

        // 15. Letter Categories & Requirements
        if (LetterCategory::count() === 0) {
            $categories = [
                ['nama' => 'Surat Keterangan Domisili', 'slug' => 'sk-domisili', 'urutan' => '1'],
                ['nama' => 'Surat Keterangan Tidak Mampu', 'slug' => 'sktm', 'urutan' => '2'],
                ['nama' => 'Surat Keterangan Usaha', 'slug' => 'sku', 'urutan' => '3'],
                ['nama' => 'Surat Keterangan Catatan Kepolisian', 'slug' => 'skck', 'urutan' => '4'],
                ['nama' => 'Surat Keterangan Penghasilan', 'slug' => 'skp', 'urutan' => '5'],
                ['nama' => 'Surat Keterangan Kematian', 'slug' => 'sk-kematian', 'urutan' => '6'],
                ['nama' => 'Surat Keterangan Kelahiran', 'slug' => 'sk-kelahiran', 'urutan' => '7'],
            ];
            foreach ($categories as $cat) {
                $letterCat = LetterCategory::create($cat);
                LetterRequirement::create([
                    'letter_category_id' => $letterCat->id,
                    'nama' => 'Fotokopi KTP / KK',
                    'wajib' => true,
                ]);
                LetterRequirement::create([
                    'letter_category_id' => $letterCat->id,
                    'nama' => 'Surat Pengantar RT/RW',
                    'wajib' => true,
                ]);
            }
        }

        // 16. Complaint Categories
        if (ComplaintCategory::count() === 0) {
            $compCats = ['Infrastruktur', 'Pelayanan', 'Keamanan', 'Lingkungan', 'Lainnya'];
            foreach ($compCats as $name) {
                ComplaintCategory::create(['nama' => $name, 'slug' => Str::slug($name)]);
            }
        }

        // 17. Families and Residents
        if (Family::count() === 0) {
            Family::factory(50)->create()->each(function ($family) {
                // Create kepala keluarga
                Resident::factory()->create([
                    'family_id' => $family->id,
                    'hubungan_keluarga' => 'Kepala Keluarga',
                    'jenis_kelamin' => 'L',
                    'dusun' => $family->dusun,
                    'rt' => $family->rt,
                    'rw' => $family->rw,
                ]);

                // Create 2-4 anggota per KK
                $count = rand(2, 4);
                for ($i = 0; $i < $count; $i++) {
                    $hubungan = $i === 0 ? 'Istri' : fake()->randomElement(['Anak', 'Anak', 'Anak', 'Orang Tua']);
                    $gender = $hubungan === 'Istri' ? 'P' : fake()->randomElement(['L', 'P']);
                    Resident::factory()->create([
                        'family_id' => $family->id,
                        'hubungan_keluarga' => $hubungan,
                        'jenis_kelamin' => $gender,
                        'dusun' => $family->dusun,
                        'rt' => $family->rt,
                        'rw' => $family->rw,
                    ]);
                }
            });
        }
    }
}
