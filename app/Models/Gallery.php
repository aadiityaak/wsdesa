<?php

namespace App\Models;

use Database\Factories\GalleryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    /** @use HasFactory<GalleryFactory> */
    use HasFactory;

    protected $fillable = [
        'nama', 'slug', 'deskripsi', 'thumbnail', 'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(GalleryImage::class)->orderBy('urutan');
    }

    public function getThumbnailAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        $firstImage = $this->images()->first();

        return $firstImage ? $firstImage->file : '';
    }
}
