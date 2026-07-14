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
            'nama_desa' => 'Desa '.$this->faker->city(),
            'kode_desa' => $this->faker->numerify('##########'),
            'kecamatan' => $this->faker->city(),
            'kabupaten' => $this->faker->city(),
            'provinsi' => $this->faker->state(),
            'alamat' => $this->faker->address(),
            'kode_pos' => $this->faker->postcode(),
            'telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'website' => 'https://'.$this->faker->domainName(),
            'logo' => null,
            'latitude' => -6.1 + $this->faker->randomFloat(7, 0, 2),
            'longitude' => 106.7 + $this->faker->randomFloat(7, 0, 2),
            'visi' => $this->faker->sentence(),
            'misi' => $this->faker->paragraph(),
            'sejarah' => implode("\n\n", $this->faker->paragraphs(3)),
            'luas_wilayah' => $this->faker->randomFloat(2, 1, 50).' km2',
            'batas_utara' => 'Desa '.$this->faker->city(),
            'batas_selatan' => 'Desa '.$this->faker->city(),
            'batas_timur' => 'Desa '.$this->faker->city(),
            'batas_barat' => 'Desa '.$this->faker->city(),
            'orbitasi_ke_kecamatan' => $this->faker->numberBetween(1, 15).' km',
            'orbitasi_ke_kabupaten' => $this->faker->numberBetween(5, 50).' km',
        ];
    }
}
