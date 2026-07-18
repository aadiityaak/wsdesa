<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\Resident;
use Inertia\Inertia;
use Inertia\Response;

class StatistikController extends Controller
{
    public function index(): Response
    {
        $totalResidents = Resident::count();
        $totalKK = Family::count();

        $byGender = Resident::selectRaw('jenis_kelamin, count(*) as total')
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin');

        $byAgeGroup = [];
        $ranges = [
            '0-5' => [0, 5],
            '6-12' => [6, 12],
            '13-17' => [13, 17],
            '18-25' => [18, 25],
            '26-40' => [26, 40],
            '41-60' => [41, 60],
            '60+' => [61, 999],
        ];
        foreach ($ranges as $label => [$min, $max]) {
            $byAgeGroup[$label] = Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN ? AND ?', [$min, $max])->count();
        }

        $transform = function ($collection) {
            return $collection->map(fn ($total, $key) => ['nama' => $key, 'total' => $total])->values();
        };

        return Inertia::render('Publik/Statistik', [
            'totalResidents' => $totalResidents,
            'totalKK' => $totalKK,
            'byGender' => [
                'laki' => $byGender['L'] ?? 0,
                'perempuan' => $byGender['P'] ?? 0,
            ],
            'byAgeGroup' => $transform(collect($byAgeGroup)),
            'byAgama' => $transform(
                Resident::selectRaw('agama, count(*) as total')->whereNotNull('agama')->groupBy('agama')->orderByDesc('total')->pluck('total', 'agama')
            ),
            'byPekerjaan' => $transform(
                Resident::selectRaw('pekerjaan, count(*) as total')->whereNotNull('pekerjaan')->groupBy('pekerjaan')->orderByDesc('total')->pluck('total', 'pekerjaan')
            ),
            'byPendidikan' => $transform(
                Resident::selectRaw('pendidikan, count(*) as total')->whereNotNull('pendidikan')->groupBy('pendidikan')->orderByDesc('total')->pluck('total', 'pendidikan')
            ),
            'byPerkawinan' => $transform(
                Resident::selectRaw('status_perkawinan, count(*) as total')->whereNotNull('status_perkawinan')->groupBy('status_perkawinan')->orderByDesc('total')->pluck('total', 'status_perkawinan')
            ),
            'byDusun' => $transform(
                Resident::selectRaw("COALESCE(dusun, 'Tidak Diketahui') as dusun, count(*) as total")->groupBy('dusun')->orderByDesc('total')->pluck('total', 'dusun')
            ),
        ]);
    }
}
