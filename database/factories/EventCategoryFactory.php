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
        $slug = Str::slug($nama).'-'.fake()->unique()->randomNumber(5);

        return [
            'nama' => $nama,
            'slug' => $slug,
        ];
    }
}
