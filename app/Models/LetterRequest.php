<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterRequest extends Model
{
    protected $fillable = [
        'letter_category_id',
        'nama_pemohon',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pekerjaan',
        'alamat',
        'keperluan',
        'no_hp',
        'kode_tracking',
        'status',
        'catatan_admin',
        'nomor_surat',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(LetterCategory::class, 'letter_category_id');
    }
}
