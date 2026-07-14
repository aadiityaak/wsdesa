<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Download extends Model
{
    protected $fillable = [
        'download_category_id', 'nama', 'file', 'counter',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DownloadCategory::class, 'download_category_id');
    }
}
