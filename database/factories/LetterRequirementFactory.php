<?php

namespace Database\Factories;

use App\Models\LetterCategory;
use App\Models\LetterRequirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LetterRequirement>
 */
class LetterRequirementFactory extends Factory
{
    protected $model = LetterRequirement::class;

    public function definition(): array
    {
        return [
            'letter_category_id' => LetterCategory::factory(),
            'nama' => $this->faker->sentence(3),
            'wajib' => $this->faker->boolean(),
        ];
    }
}
