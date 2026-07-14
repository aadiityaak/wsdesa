<?php

namespace Database\Factories;

use App\Models\DevelopmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DevelopmentCategory>
 */
class DevelopmentCategoryFactory extends Factory
{
    protected $model = DevelopmentCategory::class;

    public function definition(): array
    {
        $nama = $this->faker->unique()->word();

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
        ];
    }
}
