<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'judul' => $this->faker->sentence(),
            'konten' => $this->faker->paragraph(),
            'tipe' => $this->faker->randomElement(['info', 'warning', 'urgent']),
            'tanggal_mulai' => Carbon::now()->subDays($this->faker->numberBetween(1, 30)),
            'tanggal_selesai' => Carbon::now()->addDays($this->faker->numberBetween(7, 90)),
            'is_active' => true,
        ];
    }
}
