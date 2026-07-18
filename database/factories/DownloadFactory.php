<?php

namespace Database\Factories;

use App\Helpers\PlaceholderImage;
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
            'file' => PlaceholderImage::generatePdf(fake()->sentence(2)),
            'counter' => fake()->numberBetween(0, 500),
        ];
    }
}
