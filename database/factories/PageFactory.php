<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $judul = $this->faker->sentence();

        return [
            'judul' => $judul,
            'slug' => Str::slug($judul),
            'konten' => implode("\n\n", $this->faker->paragraphs(3)),
            'status' => 'publish',
        ];
    }
}
