<?php

namespace Database\Factories;

use App\Helpers\PlaceholderImage;
use App\Models\Development;
use App\Models\DevelopmentImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class DevelopmentImageFactory extends Factory
{
    protected $model = DevelopmentImage::class;

    public function definition(): array
    {
        return [
            'development_id' => Development::factory(),
            'file' => PlaceholderImage::generate(fake()->word()),
            'keterangan' => fake()->sentence(),
        ];
    }
}
