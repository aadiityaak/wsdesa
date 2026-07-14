<?php

namespace App\Exports;

use App\Models\Resident;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class PendudukExport implements FromCollection
{
    /**
     * @return Collection
     */
    public function collection()
    {
        return Resident::all();
    }
}
