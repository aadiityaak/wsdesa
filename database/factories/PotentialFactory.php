<?php

namespace Database\Factories;

use App\Models\Potential;
use App\Models\PotentialCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Potential>
 */
class PotentialFactory extends Factory
{
    protected $model = Potential::class;

    public function definition(): array
    {
        $nama = $this->faker->words(3, true);

        return [
            'potential_category_id' => PotentialCategory::factory(),
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'deskripsi' => $this->faker->paragraph(),
            'thumbnail' => null,
            'lokasi' => $this->faker->city(),
        ];
    }
}
