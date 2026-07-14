<?php

namespace App\Models;

use Database\Factories\ResidentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resident extends Model
{
    /** @use HasFactory<ResidentFactory> */
    use HasFactory;

    protected $fillable = [
        'family_id',
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'pendidikan',
        'gol_darah',
        'dusun',
        'rt',
        'rw',
        'status',
        'hubungan_keluarga',
        'foto',
        'telepon',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }
}
