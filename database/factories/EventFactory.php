<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $judul = fake('id_ID')->sentence(6);
        $tanggalMulai = fake()->dateTimeBetween('now', '+3 months');

        return [
            'event_category_id' => EventCategory::factory(),
            'judul' => $judul,
            'slug' => Str::slug($judul),
            'deskripsi' => collect(fake('id_ID')->paragraphs(2))
                ->map(fn ($p) => "<p>{$p}</p>")
                ->implode(''),
            'lokasi' => fake('id_ID')->address(),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => (clone $tanggalMulai)->modify('+3 days'),
            'status' => fake()->randomElement(['upcoming', 'ongoing', 'done']),
        ];
    }
}
