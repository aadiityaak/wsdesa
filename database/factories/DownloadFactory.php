<?php

namespace Database\Factories;

use App\Models\Download;
use App\Models\DownloadCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DownloadFactory extends Factory
{
    protected $model = Download::class;

    public function definition(): array
    {
        return [
            'download_category_id' => DownloadCategory::factory(),
            'nama' => fake()->sentence(3),
            'file' => fake()->word().'.pdf',
            'counter' => fake()->numberBetween(0, 500),
        ];
    }
}
