<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UmkmCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'slug', 'icon',
    ];

    public function umkm(): HasMany
    {
        return $this->hasMany(Umkm::class);
    }
}
