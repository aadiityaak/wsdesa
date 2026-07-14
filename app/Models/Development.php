<?php

namespace App\Models;

use Database\Factories\DevelopmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Development extends Model
{
    /** @use HasFactory<DevelopmentFactory> */
    use HasFactory;

    protected $fillable = [
        'development_category_id', 'nama', 'slug', 'lokasi',
        'deskripsi', 'anggaran', 'sumber_dana', 'tahun',
        'status', 'latitude', 'longitude',
    ];

    protected function casts(): array
    {
        return [
            'anggaran' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DevelopmentCategory::class, 'development_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(DevelopmentImage::class);
    }
}
