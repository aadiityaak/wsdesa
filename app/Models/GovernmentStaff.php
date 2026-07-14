<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GovernmentStaff extends Model
{
    use HasFactory;

    protected $table = 'government_staff';

    protected $fillable = [
        'nama', 'nip', 'jabatan', 'foto', 'alamat', 'telepon',
        'pendidikan_terakhir', 'tanggal_mulai_jabatan', 'tanggal_akhir_jabatan', 'urutan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai_jabatan' => 'date',
            'tanggal_akhir_jabatan' => 'date',
        ];
    }
}
