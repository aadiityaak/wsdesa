<?php

namespace Database\Factories;

use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResidentFactory extends Factory
{
    protected $model = Resident::class;

    public function definition(): array
    {
        return [
            'family_id' => null,
            'nik' => fake()->unique()->numerify('###############'),
            'nama' => fake()->name(),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya']),
            'status_perkawinan' => fake()->randomElement(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']),
            'pekerjaan' => fake()->randomElement(['Petani', 'Buruh', 'Wiraswasta', 'PNS', 'Pedagang', 'Nelayan', 'Guru', 'Tenaga Medis', 'Pelajar', 'IRT']),
            'pendidikan' => fake()->randomElement(['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D3', 'S1', 'S2', 'S3']),
            'gol_darah' => fake()->randomElement(['A', 'B', 'AB', 'O']),
            'dusun' => fake()->randomElement(['Dusun 1', 'Dusun 2', 'Dusun 3', 'Dusun 4']),
            'rt' => fake()->numerify('00#'),
            'rw' => fake()->numerify('00#'),
            'hubungan_keluarga' => 'Anak',
        ];
    }

    public function kepalaKeluarga(): static
    {
        return $this->state(fn (array $attributes) => [
            'hubungan_keluarga' => 'Kepala Keluarga',
        ]);
    }
}
