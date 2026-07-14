<?php

namespace App\Models;

use Database\Factories\ComplaintFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Complaint extends Model
{
    /** @use HasFactory<ComplaintFactory> */
    use HasFactory;

    protected $fillable = [
        'complaint_category_id',
        'nama',
        'email',
        'telepon',
        'isi',
        'status',
        'kode_tracking',
        'user_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'complaint_category_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ComplaintReply::class);
    }
}
