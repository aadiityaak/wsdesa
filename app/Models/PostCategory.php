<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostCategory extends Model
{
    protected $fillable = ['nama', 'slug', 'deskripsi'];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
