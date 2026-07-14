<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InstitutionFactory extends Factory
{
    public function definition(): array
    {
        $institutions = [
            ['nama' => 'Badan Permusyawaratan Desa', 'singkatan' => 'BPD', 'jenis' => 'BPD'],
            ['nama' => 'Lembaga Pemberdayaan Masyarakat', 'singkatan' => 'LPM', 'jenis' => 'LPM'],
            ['nama' => 'Pemberdayaan Kesejahteraan Keluarga', 'singkatan' => 'PKK', 'jenis' => 'PKK'],
            ['nama' => 'Karang Taruna', 'singkatan' => 'Karang Taruna', 'jenis' => 'Karang Taruna'],
            ['nama' => 'Linmas', 'singkatan' => 'Linmas', 'jenis' => 'Linmas'],
        ];

        $inst = fake()->randomElement($institutions);

        return [
            'nama' => $inst['nama'],
            'singkatan' => $inst['singkatan'],
            'jenis' => $inst['jenis'],
            'deskripsi' => fake()->paragraph(),
        ];
    }
}
