<?php

namespace App\Imports;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;

class PendudukImport implements ToModel
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        return new Resident([
            //
        ]);
    }
}
