<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_desa', 'kode_desa', 'kecamatan', 'kabupaten', 'provinsi',
        'alamat', 'kode_pos', 'telepon', 'email', 'website',
        'logo', 'latitude', 'longitude',
        'visi', 'misi', 'sejarah',
        'luas_wilayah',
        'batas_utara', 'batas_selatan', 'batas_timur', 'batas_barat',
        'orbitasi_ke_kecamatan', 'orbitasi_ke_kabupaten',
        'facebook', 'instagram', 'youtube', 'tiktok',
    ];
}
