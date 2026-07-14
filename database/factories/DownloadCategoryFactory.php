<?php

namespace Database\Factories;

use App\Models\DownloadCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DownloadCategory>
 */
class DownloadCategoryFactory extends Factory
{
    protected $model = DownloadCategory::class;

    public function definition(): array
    {
        $nama = $this->faker->unique()->words(2, true);

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
        ];
    }
}
