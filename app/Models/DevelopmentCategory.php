<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DevelopmentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama', 'slug',
    ];

    public function developments(): HasMany
    {
        return $this->hasMany(Development::class);
    }
}
