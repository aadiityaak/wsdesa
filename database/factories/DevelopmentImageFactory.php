<?php

namespace Database\Factories;

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
            'file' => fake()->word().'.jpg',
            'keterangan' => fake()->sentence(),
        ];
    }
}
