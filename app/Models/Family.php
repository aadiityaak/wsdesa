<?php

namespace App\Models;

use Database\Factories\FamilyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    /** @use HasFactory<FamilyFactory> */
    use HasFactory;

    protected $fillable = [
        'no_kk',
        'alamat',
        'rt',
        'rw',
        'dusun',
    ];

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    public function getKepalaKeluargaAttribute(): ?Resident
    {
        return $this->residents()->where('hubungan_keluarga', 'Kepala Keluarga')->first();
    }
}
