<?php

namespace App\Models;

use Database\Factories\SliderFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    /** @use HasFactory<SliderFactory> */
    use HasFactory;

    protected $fillable = [
        'judul', 'deskripsi', 'image', 'link', 'urutan', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
