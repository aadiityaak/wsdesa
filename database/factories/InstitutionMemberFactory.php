<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\InstitutionMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionMember>
 */
class InstitutionMemberFactory extends Factory
{
    protected $model = InstitutionMember::class;

    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory(),
            'nama' => $this->faker->name(),
            'jabatan' => $this->faker->jobTitle(),
            'foto' => null,
            'telepon' => $this->faker->phoneNumber(),
            'urutan' => $this->faker->numberBetween(0, 10),
        ];
    }
}
