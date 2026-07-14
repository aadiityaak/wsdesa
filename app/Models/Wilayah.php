<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    public $timestamps = false;

    protected $primaryKey = 'kode';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['kode', 'nama'];
}
