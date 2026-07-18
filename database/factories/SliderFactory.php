<?php

namespace Database\Factories;

use App\Helpers\PlaceholderImage;
use App\Models\Slider;
use Illuminate\Database\Eloquent\Factories\Factory;

class SliderFactory extends Factory
{
    protected $model = Slider::class;

    public function definition(): array
    {
        $judul = fake()->sentence(4);

        return [
            'judul' => $judul,
            'deskripsi' => fake()->sentence(),
            'image' => PlaceholderImage::generate($judul),
            'link' => null,
            'urutan' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
