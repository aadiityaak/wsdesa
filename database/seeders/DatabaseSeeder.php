<?php

namespace Database\Seeders;

use App\Models\ComplaintCategory;
use App\Models\Family;
use App\Models\GovernmentStaff;
use App\Models\Institution;
use App\Models\InstitutionMember;
use App\Models\LetterCategory;
use App\Models\LetterRequirement;
use App\Models\Profile;
use App\Models\Resident;
use App\Models\User;
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

        // 5. Families and Residents
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

        // 6. Letter Categories & Requirements
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

        // 7. Complaint Categories
        if (ComplaintCategory::count() === 0) {
            $compCats = ['Infrastruktur', 'Pelayanan', 'Keamanan', 'Lingkungan', 'Lainnya'];
            foreach ($compCats as $name) {
                ComplaintCategory::create(['nama' => $name, 'slug' => Str::slug($name)]);
            }
        }
    }
}
