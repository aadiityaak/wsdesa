<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GovernmentStaffFactory extends Factory
{
    public function definition(): array
    {
        $jabatans = ['Kepala Desa', 'Sekretaris Desa', 'Kasi Pemerintahan', 'Kasi Pelayanan', 'Kasi Kesejahteraan', 'Kaur Umum', 'Kaur Keuangan', 'Kaur Perencanaan', 'Kadus I', 'Kadus II', 'Kadus III'];

        return [
            'nama' => fake()->name(),
            'nip' => fake()->unique()->numerify('##################'),
            'jabatan' => fake()->unique()->randomElement($jabatans),
            'pendidikan_terakhir' => fake()->randomElement(['SMA', 'D3', 'S1', 'S2']),
            'alamat' => fake()->address(),
            'telepon' => fake()->phoneNumber(),
            'tanggal_mulai_jabatan' => fake()->date(),
            'urutan' => fake()->numberBetween(1, 20),
        ];
    }
}
