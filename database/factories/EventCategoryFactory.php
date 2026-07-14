<?php

namespace Database\Factories;

use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventCategory>
 */
class EventCategoryFactory extends Factory
{
    protected $model = EventCategory::class;

    public function definition(): array
    {
        $nama = fake()->randomElement(['Musyawarah', 'Gotong Royong', 'Pelatihan', 'Keagamaan', 'Olahraga', 'Sosial']);

        return [
            'nama' => $nama,
            'slug' => Str::slug($nama),
        ];
    }
}
