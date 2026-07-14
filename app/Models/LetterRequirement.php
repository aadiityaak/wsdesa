<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_category_id',
        'nama',
        'wajib',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(LetterCategory::class, 'letter_category_id');
    }
}
