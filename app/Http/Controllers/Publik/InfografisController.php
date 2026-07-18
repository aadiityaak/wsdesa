<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\Resident;
use Inertia\Inertia;
use Inertia\Response;

class InfografisController extends Controller
{
    public function penduduk(): Response
    {
        $totalResidents = Resident::count();
        $totalKK = Family::count();

        $byGender = Resident::selectRaw('jenis_kelamin, count(*) as total')
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin');

        $byAgeGroup = [];
        $ranges = [
            '0-4' => [0, 4],
            '5-9' => [5, 9],
            '10-14' => [10, 14],
            '15-19' => [15, 19],
            '20-24' => [20, 24],
            '25-29' => [25, 29],
            '30-34' => [30, 34],
            '35-39' => [35, 39],
            '40-44' => [40, 44],
            '45-49' => [45, 49],
            '50-54' => [50, 54],
            '55-59' => [55, 59],
            '60-64' => [60, 64],
            '65-69' => [65, 69],
            '70+' => [70, 999],
        ];

        foreach ($ranges as $label => [$min, $max]) {
            $byAgeGroup[$label] = Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN ? AND ?', [$min, $max])->count();
        }

        $byPendidikan = Resident::selectRaw('pendidikan, count(*) as total')
            ->whereNotNull('pendidikan')
            ->groupBy('pendidikan')
            ->orderByDesc('total')
            ->pluck('total', 'pendidikan');

        $byPekerjaan = Resident::selectRaw('pekerjaan, count(*) as total')
            ->whereNotNull('pekerjaan')
            ->groupBy('pekerjaan')
            ->orderByDesc('total')
            ->pluck('total', 'pekerjaan');

        $byAgama = Resident::selectRaw('agama, count(*) as total')
            ->whereNotNull('agama')
            ->groupBy('agama')
            ->orderByDesc('total')
            ->pluck('total', 'agama');

        $byPerkawinan = Resident::selectRaw('status_perkawinan, count(*) as total')
            ->whereNotNull('status_perkawinan')
            ->groupBy('status_perkawinan')
            ->orderByDesc('total')
            ->pluck('total', 'status_perkawinan');

        $byDusun = Resident::selectRaw("COALESCE(dusun, 'Tidak Diketahui') as dusun, count(*) as total")
            ->groupBy('dusun')
            ->orderByDesc('total')
            ->pluck('total', 'dusun');

        return Inertia::render('Publik/Infografis/Penduduk', [
            'totalResidents' => $totalResidents,
            'totalKK' => $totalKK,
            'byGender' => $byGender,
            'byAgeGroup' => $byAgeGroup,
            'byPendidikan' => $byPendidikan,
            'byPekerjaan' => $byPekerjaan,
            'byAgama' => $byAgama,
            'byPerkawinan' => $byPerkawinan,
            'byDusun' => $byDusun,
        ]);
    }
}
