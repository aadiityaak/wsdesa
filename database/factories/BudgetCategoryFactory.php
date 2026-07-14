<?php

namespace Database\Factories;

use App\Models\BudgetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BudgetCategory>
 */
class BudgetCategoryFactory extends Factory
{
    protected $model = BudgetCategory::class;

    public function definition(): array
    {
        $nama = $this->faker->unique()->words(2, true);

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'tipe' => $this->faker->randomElement(['Pendapatan', 'Belanja', 'Pembiayaan']),
        ];
    }
}
