<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Event;
use App\Models\GovernmentStaff;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Resident;
use App\Models\Slider;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function index(): Response
    {
        $sliders = Slider::active()->get()->map(function ($slider) {
            return [
                'id' => $slider->id,
                'judul' => $slider->judul,
                'deskripsi' => $slider->deskripsi,
                'image_url' => $slider->image ? asset('storage/'.$slider->image) : null,
                'link' => $slider->link,
            ];
        });

        $latestPosts = Post::with('category')
            ->where('status', 'publish')
            ->latest('published_at')
            ->take(4)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'judul' => $post->judul,
                    'slug' => $post->slug,
                    'ringkasan' => $post->ringkasan,
                    'gambar' => $post->thumbnail_url,
                    'published_at' => $post->published_at,
                    'category' => $post->category ? [
                        'nama' => $post->category->nama,
                        'slug' => $post->category->slug,
                    ] : null,
                ];
            });

        $upcomingEvents = Event::where('status', 'upcoming')
            ->where('tanggal_mulai', '>=', now())
            ->orderBy('tanggal_mulai')
            ->take(4)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'judul' => $event->judul,
                    'slug' => $event->slug,
                    'deskripsi' => $event->deskripsi,
                    'lokasi' => $event->lokasi,
                    'tanggal_mulai' => $event->tanggal_mulai,
                    'tanggal_selesai' => $event->tanggal_selesai,
                ];
            });

        // Statistik penduduk
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

        // APBDes ringkasan per tahun terbaru
        $latestYear = Budget::max('tahun');
        $budgetSummary = [];

        if ($latestYear) {
            $budgets = Budget::with('category')
                ->where('tahun', $latestYear)
                ->get();

            $grouped = $budgets->groupBy(fn ($b) => $b->category?->tipe ?? 'Lainnya');

            $budgetSummary = [
                'tahun' => $latestYear,
                'pendapatan' => [
                    'anggaran' => (float) ($grouped->get('Pendapatan')?->sum('anggaran') ?? 0),
                    'realisasi' => (float) ($grouped->get('Pendapatan')?->sum('realisasi') ?? 0),
                ],
                'belanja' => [
                    'anggaran' => (float) ($grouped->get('Belanja')?->sum('anggaran') ?? 0),
                    'realisasi' => (float) ($grouped->get('Belanja')?->sum('realisasi') ?? 0),
                ],
                'pembiayaan' => [
                    'anggaran' => (float) ($grouped->get('Pembiayaan')?->sum('anggaran') ?? 0),
                    'realisasi' => (float) ($grouped->get('Pembiayaan')?->sum('realisasi') ?? 0),
                ],
            ];
        }

        return Inertia::render('Welcome', [
            'sliders' => $sliders,
            'latestPosts' => $latestPosts,
            'upcomingEvents' => $upcomingEvents,
            'stats' => [
                'totalResidents' => $totalResidents,
                'byGender' => $byGender,
                'byAgeGroup' => $byAgeGroup,
            ],
            'budgetSummary' => $budgetSummary,
            'jamKerja' => fn () => Profile::first()?->jamKerja()->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")->get(),
            'staff' => fn () => GovernmentStaff::orderBy('urutan')->get()->map(fn ($s) => [
                'id' => $s->id,
                'nama' => $s->nama,
                'nip' => $s->nip,
                'jabatan' => $s->jabatan,
                'foto_url' => $s->foto ? asset('storage/'.$s->foto) : null,
            ])->values(),
        ]);
    }
}
