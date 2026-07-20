<?php

namespace Database\Factories;

use App\Helpers\PlaceholderImage;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $judul = fake('id_ID')->sentence(8);

        return [
            'post_category_id' => PostCategory::query()->inRandomOrder()->value('id'),
            'judul' => $judul,
            'slug' => Str::slug($judul).'-'.fake()->unique()->randomNumber(5),
            'thumbnail' => PlaceholderImage::generate($judul),
            'ringkasan' => fake('id_ID')->paragraph(2),
            'konten' => collect(fake('id_ID')->paragraphs(4))
                ->map(fn ($p) => "<p>{$p}</p>")
                ->implode(''),
            'status' => fake()->randomElement(['draft', 'publish', 'archive']),
            'views' => fake()->randomDigit(),
            'user_id' => User::factory(),
            'published_at' => now(),
        ];
    }
}
