<?php

namespace Database\Factories;

use App\Models\PostCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PostCategory>
 */
class PostCategoryFactory extends Factory
{
    protected $model = PostCategory::class;

    public function definition(): array
    {
        $nama = fake()->randomElement(['Berita Desa', 'Kegiatan', 'Pengumuman', 'Pembangunan', 'Kesehatan']);
        $slug = Str::slug($nama).'-'.fake()->unique()->randomNumber(5);

        return [
            'nama' => $nama,
            'slug' => $slug,
            'deskripsi' => fake()->sentence(),
        ];
    }
}
