<?php

namespace Database\Factories;

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
            'file' => fake()->word().'.jpg',
            'urutan' => fake()->numberBetween(0, 10),
        ];
    }
}
