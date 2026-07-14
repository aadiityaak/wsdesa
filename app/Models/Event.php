<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $fillable = [
        'event_category_id', 'judul', 'slug', 'deskripsi', 'lokasi',
        'tanggal_mulai', 'tanggal_selesai', 'status', 'image',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }
}
