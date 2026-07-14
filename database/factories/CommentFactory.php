<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'nama' => fake('id_ID')->name(),
            'email' => fake()->safeEmail(),
            'konten' => fake('id_ID')->paragraph(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
