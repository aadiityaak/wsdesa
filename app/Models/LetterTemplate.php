<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterTemplate extends Model
{
    protected $fillable = [
        'letter_category_id',
        'nama',
        'konten',
        'persyaratan',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(LetterCategory::class, 'letter_category_id');
    }
}
