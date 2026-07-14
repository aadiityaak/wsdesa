<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'nama_desa' => 'Desa '.fake()->city(),
            'kode_desa' => fake()->numerify('##########'),
            'kecamatan' => fake()->city(),
            'kabupaten' => fake()->city(),
            'provinsi' => fake()->state(),
            'alamat' => fake()->address(),
            'kode_pos' => fake()->postcode(),
            'telepon' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'website' => 'https://'.fake()->domainName(),
            'logo' => null,
            'latitude' => -6.1 + fake()->randomFloat(7, 0, 2),
            'longitude' => 106.7 + fake()->randomFloat(7, 0, 2),
            'visi' => fake()->sentence(),
            'misi' => fake()->paragraph(),
            'sejarah' => implode("\n\n", fake()->paragraphs(3)),
            'luas_wilayah' => fake()->randomFloat(2, 1, 50).' km2',
            'batas_utara' => 'Desa '.fake()->city(),
            'batas_selatan' => 'Desa '.fake()->city(),
            'batas_timur' => 'Desa '.fake()->city(),
            'batas_barat' => 'Desa '.fake()->city(),
            'orbitasi_ke_kecamatan' => fake()->numberBetween(1, 15).' km',
            'orbitasi_ke_kabupaten' => fake()->numberBetween(5, 50).' km',
        ];
    }
}
