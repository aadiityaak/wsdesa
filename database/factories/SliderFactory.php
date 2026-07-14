<?php

namespace Database\Factories;

use App\Models\Slider;
use Illuminate\Database\Eloquent\Factories\Factory;

class SliderFactory extends Factory
{
    protected $model = Slider::class;

    public function definition(): array
    {
        return [
            'judul' => fake()->sentence(4),
            'deskripsi' => fake()->sentence(),
            'image' => fake()->word().'.jpg',
            'link' => null,
            'urutan' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
