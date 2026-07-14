<?php

namespace Database\Factories;

use App\Models\LetterCategory;
use App\Models\LetterTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LetterTemplate>
 */
class LetterTemplateFactory extends Factory
{
    protected $model = LetterTemplate::class;

    public function definition(): array
    {
        return [
            'letter_category_id' => LetterCategory::factory(),
            'nama' => $this->faker->sentence(3),
            'konten' => implode("\n\n", $this->faker->paragraphs(3)),
            'persyaratan' => $this->faker->sentence(),
        ];
    }
}
