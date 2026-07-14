<?php

namespace App\Models;

use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    protected $fillable = [
        'judul', 'konten', 'tipe', 'tanggal_mulai',
        'tanggal_selesai', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('tanggal_mulai')->orWhere('tanggal_mulai', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('tanggal_selesai')->orWhere('tanggal_selesai', '>=', now());
            });
    }
}
