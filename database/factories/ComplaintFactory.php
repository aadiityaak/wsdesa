<?php

namespace Database\Factories;

use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Complaint>
 */
class ComplaintFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->safeEmail(),
            'telepon' => fake()->phoneNumber(),
            'isi' => fake()->paragraph(3),
            'kode_tracking' => Str::random(10),
            'status' => fake()->randomElement(['baru', 'diproses', 'selesai', 'ditolak']),
        ];
    }
}
