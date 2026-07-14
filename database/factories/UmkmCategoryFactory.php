<?php

namespace Database\Factories;

use App\Models\UmkmCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UmkmCategory>
 */
class UmkmCategoryFactory extends Factory
{
    protected $model = UmkmCategory::class;

    public function definition(): array
    {
        $nama = $this->faker->unique()->word();

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'icon' => null,
        ];
    }
}
