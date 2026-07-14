<?php

namespace Database\Factories;

use App\Models\LetterCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<LetterCategory>
 */
class LetterCategoryFactory extends Factory
{
    protected $model = LetterCategory::class;

    public function definition(): array
    {
        $nama = $this->faker->unique()->words(3, true);

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'urutan' => $this->faker->numberBetween(0, 10),
        ];
    }
}
