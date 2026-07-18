<?php

namespace Database\Factories;

use App\Helpers\PlaceholderImage;
use App\Models\Gallery;
use App\Models\GalleryImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class GalleryImageFactory extends Factory
{
    protected $model = GalleryImage::class;

    public function definition(): array
    {
        return [
            'gallery_id' => Gallery::factory(),
            'judul' => fake()->sentence(2),
            'file' => PlaceholderImage::generate(fake()->word()),
            'urutan' => fake()->numberBetween(0, 10),
        ];
    }
}
