<?php

namespace Database\Factories;

use App\Models\PotentialCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PotentialCategory>
 */
class PotentialCategoryFactory extends Factory
{
    protected $model = PotentialCategory::class;

    public function definition(): array
    {
        $nama = $this->faker->unique()->word();

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
        ];
    }
}
