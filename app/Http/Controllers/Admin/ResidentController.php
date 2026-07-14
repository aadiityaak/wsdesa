<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PendudukExport;
use App\Http\Controllers\Controller;
use App\Imports\PendudukImport;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResidentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Resident::query()->with('family');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $residents = $query->latest()->paginate(25);

        return Inertia::render('Admin/Kependudukan/PendudukIndex', [
            'residents' => $residents,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'family_id' => ['nullable', 'exists:families,id'],
            'nik' => ['required', 'string', 'max:16', 'unique:residents,nik'],
            'nama' => ['required', 'string', 'max:200'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya'],
            'status_perkawinan' => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'pendidikan' => ['required', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'gol_darah' => ['nullable', 'in:A,B,AB,O'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'status' => ['required', 'in:Tetap,Lahir,Mati,Pindah'],
            'hubungan_keluarga' => ['nullable', 'in:Kepala Keluarga,Istri,Anak,Orang Tua,Menantu,Cucu,Famili Lain'],
            'foto' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        Resident::create($validated);

        return redirect()->back()->with('success', 'Penduduk berhasil ditambahkan.');
    }

    public function update(Request $request, Resident $resident): RedirectResponse
    {
        $validated = $request->validate([
            'family_id' => ['nullable', 'exists:families,id'],
            'nik' => ['required', 'string', 'max:16', 'unique:residents,nik,'.$resident->id],
            'nama' => ['required', 'string', 'max:200'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'agama' => ['required', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya'],
            'status_perkawinan' => ['required', 'in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'pendidikan' => ['required', 'in:Tidak Sekolah,SD,SMP,SMA,D3,S1,S2,S3'],
            'gol_darah' => ['nullable', 'in:A,B,AB,O'],
            'dusun' => ['nullable', 'string', 'max:100'],
            'rt' => ['nullable', 'string', 'max:5'],
            'rw' => ['nullable', 'string', 'max:5'],
            'status' => ['required', 'in:Tetap,Lahir,Mati,Pindah'],
            'hubungan_keluarga' => ['nullable', 'in:Kepala Keluarga,Istri,Anak,Orang Tua,Menantu,Cucu,Famili Lain'],
            'foto' => ['nullable', 'string'],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $resident->update($validated);

        return redirect()->back()->with('success', 'Data penduduk berhasil diperbarui.');
    }

    public function destroy(Resident $resident): RedirectResponse
    {
        $resident->delete();

        return redirect()->route('admin.kependudukan.penduduk.index')->with('success', 'Data penduduk berhasil dihapus.');
    }

    public function export(): BinaryFileResponse|RedirectResponse
    {
        return Excel::download(new PendudukExport, 'penduduk.xlsx');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,csv'],
        ]);

        Excel::import(new PendudukImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data penduduk berhasil diimpor.');
    }
}
