<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PotentialCategory extends Model
{
    protected $fillable = [
        'nama', 'slug',
    ];

    public function potentials(): HasMany
    {
        return $this->hasMany(Potential::class);
    }
}
