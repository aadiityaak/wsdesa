<?php

namespace Database\Factories;

use App\Models\Development;
use App\Models\DevelopmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Development>
 */
class DevelopmentFactory extends Factory
{
    protected $model = Development::class;

    public function definition(): array
    {
        $nama = $this->faker->sentence(3);

        return [
            'development_category_id' => DevelopmentCategory::factory(),
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'lokasi' => $this->faker->city(),
            'deskripsi' => $this->faker->paragraph(),
            'anggaran' => $this->faker->randomFloat(2, 5000000, 1000000000),
            'sumber_dana' => $this->faker->randomElement(['APBDes', 'Dana Desa', 'Swadaya', 'Bantuan Provinsi']),
            'tahun' => $this->faker->randomElement([2024, 2025, 2026]),
            'status' => $this->faker->randomElement(['rencana', 'berjalan', 'selesai']),
            'latitude' => -6.1 + $this->faker->randomFloat(4, 0, 2),
            'longitude' => 106.7 + $this->faker->randomFloat(4, 0, 2),
        ];
    }
}
