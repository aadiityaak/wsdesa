<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\BudgetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        $anggaran = $this->faker->randomFloat(2, 1000000, 500000000);

        return [
            'budget_category_id' => BudgetCategory::query()->inRandomOrder()->value('id'),
            'tahun' => $this->faker->randomElement([2024, 2025, 2026]),
            'anggaran' => $anggaran,
            'realisasi' => $this->faker->randomFloat(2, 0, $anggaran),
            'keterangan' => $this->faker->sentence(),
        ];
    }
}
