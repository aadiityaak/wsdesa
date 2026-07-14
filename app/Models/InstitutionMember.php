<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionMember extends Model
{
    use HasFactory;

    protected $fillable = ['institution_id', 'nama', 'jabatan', 'foto', 'telepon', 'urutan'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
