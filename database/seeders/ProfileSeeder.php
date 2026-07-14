<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::updateOrCreate(['nama_desa' => 'Desa Digital'], [
            'kode_desa' => '3201234567',
            'kecamatan' => 'Kecamatan Contoh',
            'kabupaten' => 'Kabupaten Maju Jaya',
            'provinsi' => 'Jawa Barat',
            'alamat' => 'Jl. Raya Desa No. 1',
            'kode_pos' => '12345',
            'telepon' => '(0265) 123456',
            'email' => 'info@desa-digital.id',
            'website' => 'https://desa-digital.id',
            'latitude' => -6.914744,
            'longitude' => 107.609812,
            'visi' => 'Terwujudnya Desa Digital yang Maju, Mandiri, dan Sejahtera
Berdasarkan Iman dan Taqwa',
            'misi' => "Meningkatkan kualitas pelayanan publik berbasis teknologi digital
Mengembangkan potensi ekonomi desa melalui pemberdayaan masyarakat
Mewujudkan tata kelola pemerintahan desa yang transparan dan akuntabel
Membangun infrastruktur desa yang merata dan berkelanjutan
Melestarikan nilai-nilai budaya dan kearifan lokal",
            'sejarah' => "Desa Digital berdiri sejak tahun 1980 sebagai hasil pemekaran dari Desa Induk. Awalnya merupakan kawasan pertanian yang subur dengan mayoritas penduduk bermata pencaharian sebagai petani.

Seiring berjalannya waktu, Desa Digital mulai berkembang menjadi desa yang dinamis dengan berbagai program pembangunan yang telah dilaksanakan. Pada tahun 2020, Desa Digital dicanangkan sebagai desa berbasis digital dengan penerapan sistem informasi desa untuk mendukung pelayanan publik yang lebih efektif dan efisien.

Kini Desa Digital terus berbenah diri untuk menjadi desa yang mandiri, sejahtera, dan berdaya saing dengan tetap menjunjung tinggi nilai-nilai gotong royong dan kearifan lokal.",
            'luas_wilayah' => '15.5 km²',
            'batas_utara' => 'Desa Sejahtera',
            'batas_selatan' => 'Desa Makmur',
            'batas_timur' => 'Kecamatan Harapan',
            'batas_barat' => 'Desa Damai',
            'orbitasi_ke_kecamatan' => '3 km',
            'orbitasi_ke_kabupaten' => '15 km',
            'facebook' => 'https://facebook.com/desadigital',
            'instagram' => 'https://instagram.com/desadigital',
            'youtube' => 'https://youtube.com/@desadigital',
            'tiktok' => 'https://tiktok.com/@desadigital',
        ]);
    }
}
