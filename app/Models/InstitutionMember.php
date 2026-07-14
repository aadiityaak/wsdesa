<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstitutionMember extends Model
{
    protected $fillable = ['institution_id', 'nama', 'jabatan', 'foto', 'telepon', 'urutan'];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }
}
