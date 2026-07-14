<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevelopmentImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'development_id', 'file', 'keterangan',
    ];

    public function development(): BelongsTo
    {
        return $this->belongsTo(Development::class);
    }
}
