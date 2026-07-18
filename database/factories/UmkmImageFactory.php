<?php

namespace Database\Factories;

use App\Helpers\PlaceholderImage;
use App\Models\Umkm;
use App\Models\UmkmImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class UmkmImageFactory extends Factory
{
    protected $model = UmkmImage::class;

    public function definition(): array
    {
        return [
            'umkm_id' => Umkm::factory(),
            'file' => PlaceholderImage::generate(fake()->word()),
            'urutan' => fake()->numberBetween(0, 10),
        ];
    }
}
