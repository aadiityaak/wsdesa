<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class StatistikController extends Controller
{
    public function index(): Response
    {
        $totalResidents = Resident::count();

        $byGender = Resident::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->pluck('total', 'jenis_kelamin');

        $byAgeGroup = [
            '0-5' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 0 AND 5')->count(),
            '6-12' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 6 AND 12')->count(),
            '13-17' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 13 AND 17')->count(),
            '18-25' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 18 AND 25')->count(),
            '26-40' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 26 AND 40')->count(),
            '41-60' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 41 AND 60')->count(),
            '60+' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) >= 61')->count(),
        ];

        $byAgama = Resident::select('agama', DB::raw('count(*) as total'))
            ->groupBy('agama')
            ->pluck('total', 'agama');

        $byPekerjaan = Resident::select('pekerjaan', DB::raw('count(*) as total'))
            ->whereNotNull('pekerjaan')
            ->groupBy('pekerjaan')
            ->pluck('total', 'pekerjaan');

        $byPendidikan = Resident::select('pendidikan', DB::raw('count(*) as total'))
            ->groupBy('pendidikan')
            ->pluck('total', 'pendidikan');

        return Inertia::render('Publik/Statistik', [
            'totalResidents' => $totalResidents,
            'byGender' => $byGender,
            'byAgeGroup' => $byAgeGroup,
            'byAgama' => $byAgama,
            'byPekerjaan' => $byPekerjaan,
            'byPendidikan' => $byPendidikan,
        ]);
    }
}
