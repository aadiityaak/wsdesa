<?php

namespace Database\Factories;

use App\Models\Family;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Family>
 */
class FamilyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'no_kk' => fake()->unique()->numerify('################'),
            'alamat' => fake()->address(),
            'rt' => fake()->numerify('00#'),
            'rw' => fake()->numerify('00#'),
            'dusun' => fake()->randomElement(['Dusun 1', 'Dusun 2', 'Dusun 3', 'Dusun 4']),
        ];
    }
}
