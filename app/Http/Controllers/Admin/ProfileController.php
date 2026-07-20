<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /* ───── Data Desa ───── */

    public function dataDesa(): Response
    {
        return Inertia::render('Admin/Profil/DataDesa', [
            'profile' => $this->getProfile(),
            'provinsiList' => $this->getProvinsi(),
            'kabupatenList' => $this->getKabupaten(),
            'kecamatanList' => $this->getKecamatan(),
        ]);
    }

    public function updateDataDesa(Request $request): RedirectResponse
    {
        $profile = Profile::firstOrFail();

        $validated = $request->validate([
            'nama_desa' => ['required', 'string', 'max:200'],
            'kode_desa' => ['nullable', 'string', 'max:20'],
            'kecamatan' => ['required', 'string', 'max:200'],
            'kabupaten' => ['required', 'string', 'max:200'],
            'provinsi' => ['required', 'string', 'max:200'],
            'alamat' => ['nullable', 'string'],
            'kode_pos' => ['nullable', 'string', 'max:10'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:200'],
            'website' => ['nullable', 'url', 'max:200'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        unset($validated['logo']);

        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            try {
                $profile->logo = $request->file('logo')->store('profiles', 'public');
            } catch (\Exception $e) {
                Log::error('Logo upload failed: '.$e->getMessage());

                return back()->with('error', 'Gagal mengupload logo. Silakan coba lagi.');
            }
        }

        $profile->fill($validated)->save();

        return back()->with('success', 'Data desa berhasil diperbarui.');
    }

    /* ───── Jam Kerja ───── */

    public function jamKerja(): Response
    {
        return Inertia::render('Admin/Profil/JamKerja', [
            'profile' => Profile::with('jamKerja')->first(),
        ]);
    }

    public function updateJamKerja(Request $request): RedirectResponse
    {
        $profile = Profile::firstOrFail();

        $validated = $request->validate([
            'jam_kerja' => ['required', 'array', 'size:7'],
            'jam_kerja.*.hari' => ['required', 'string'],
            'jam_kerja.*.jam_buka' => ['nullable', 'date_format:H:i'],
            'jam_kerja.*.jam_tutup' => ['nullable', 'date_format:H:i'],
            'jam_kerja.*.is_libur' => ['nullable', 'boolean'],
        ]);

        $profile->jamKerja()->delete();
        foreach ($validated['jam_kerja'] as $jk) {
            $profile->jamKerja()->create([
                'hari' => $jk['hari'],
                'jam_buka' => $jk['is_libur'] ? null : ($jk['jam_buka'] ?? null),
                'jam_tutup' => $jk['is_libur'] ? null : ($jk['jam_tutup'] ?? null),
                'is_libur' => $jk['is_libur'] ?? false,
            ]);
        }

        return back()->with('success', 'Jam kerja berhasil diperbarui.');
    }

    /* ───── Media ───── */

    public function media(): Response
    {
        return Inertia::render('Admin/Profil/Media', [
            'profile' => $this->getProfile(),
        ]);
    }

    public function updateMedia(Request $request): RedirectResponse
    {
        $profile = Profile::firstOrFail();

        $validated = $request->validate([
            'facebook' => ['nullable', 'url', 'max:200'],
            'instagram' => ['nullable', 'url', 'max:200'],
            'youtube' => ['nullable', 'url', 'max:200'],
            'tiktok' => ['nullable', 'url', 'max:200'],
        ]);

        $profile->fill($validated)->save();

        return back()->with('success', 'Media sosial berhasil diperbarui.');
    }

    /* ───── Geografi ───── */

    public function geografi(): Response
    {
        return Inertia::render('Admin/Profil/Geografi', [
            'profile' => $this->getProfile(),
        ]);
    }

    public function updateGeografi(Request $request): RedirectResponse
    {
        $profile = Profile::firstOrFail();

        $validated = $request->validate([
            'luas_wilayah' => ['nullable', 'string', 'max:50'],
            'batas_utara' => ['nullable', 'string'],
            'batas_selatan' => ['nullable', 'string'],
            'batas_timur' => ['nullable', 'string'],
            'batas_barat' => ['nullable', 'string'],
            'orbitasi_ke_kecamatan' => ['nullable', 'string', 'max:50'],
            'orbitasi_ke_kabupaten' => ['nullable', 'string', 'max:50'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ]);

        $profile->fill($validated)->save();

        return back()->with('success', 'Data geografi berhasil diperbarui.');
    }

    /* ───── Tampilan ───── */

    public function tampilan(): Response
    {
        return Inertia::render('Admin/Tampilan', [
            'profile' => $this->getProfile(),
        ]);
    }

    public function updateTampilan(Request $request): RedirectResponse
    {
        $profile = Profile::firstOrFail();

        $validated = $request->validate([
            'header_style' => ['required', 'string', 'in:default,modern,classic,minimal'],
            'footer_style' => ['required', 'string', 'in:default,modern,classic,minimal'],
            'font_style' => ['required', 'string', 'in:inter,serif,mono,rounded,poppins,playfair,bricolage,jakarta'],
        ]);

        $profile->fill($validated)->save();

        return back()->with('success', 'Pengaturan tampilan berhasil disimpan.');
    }

    /* ───── Visi ───── */

    public function visi(): Response
    {
        return Inertia::render('Admin/Profil/Visi', [
            'profile' => $this->getProfile(),
        ]);
    }

    public function updateVisi(Request $request): RedirectResponse
    {
        $profile = Profile::firstOrFail();

        $validated = $request->validate([
            'visi' => ['nullable', 'string'],
            'misi' => ['nullable', 'string'],
            'sejarah' => ['nullable', 'string'],
        ]);

        $profile->fill($validated)->save();

        return back()->with('success', 'Visi, misi & sejarah berhasil diperbarui.');
    }

    /* ───── Helpers ───── */

    private function getProfile(): Profile
    {
        $profile = Profile::first();
        if (! $profile) {
            $profile = Profile::create(['nama_desa' => 'Desa Digital']);
        }

        return $profile;
    }

    private function getProvinsi()
    {
        return Wilayah::whereRaw('CHAR_LENGTH(kode) = 2')->orderBy('nama')->get(['kode', 'nama']);
    }

    private function getKabupaten()
    {
        return Wilayah::whereRaw('CHAR_LENGTH(kode) = 5')->orderBy('nama')->get(['kode', 'nama']);
    }

    private function getKecamatan()
    {
        return Wilayah::whereRaw('CHAR_LENGTH(kode) = 8')->orderBy('nama')->get(['kode', 'nama']);
    }
}
