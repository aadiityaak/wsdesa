<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JamKerja extends Model
{
    protected $fillable = [
        'profile_id',
        'hari',
        'jam_buka',
        'jam_tutup',
        'is_libur',
    ];

    protected $casts = [
        'is_libur' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
