<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterCategory extends Model
{
    protected $fillable = [
        'nama',
        'slug',
        'urutan',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(LetterTemplate::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(LetterRequirement::class);
    }

    public function requests(): HasMany
    {
        return $this->hasMany(LetterRequest::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function ($query) {
            $query->orderBy('urutan');
        });
    }
}
