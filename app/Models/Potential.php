<?php

namespace App\Models;

use Database\Factories\PotentialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Potential extends Model
{
    /** @use HasFactory<PotentialFactory> */
    use HasFactory;

    protected $fillable = [
        'potential_category_id', 'nama', 'slug', 'deskripsi',
        'thumbnail', 'lokasi',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(PotentialCategory::class, 'potential_category_id');
    }
}
