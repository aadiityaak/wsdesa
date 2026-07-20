<?php

namespace Database\Factories;

use App\Helpers\PlaceholderImage;
use App\Models\Umkm;
use App\Models\UmkmCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Umkm>
 */
class UmkmFactory extends Factory
{
    protected $model = Umkm::class;

    public function definition(): array
    {
        $namaUsaha = $this->faker->company();

        return [
            'umkm_category_id' => UmkmCategory::factory(),
            'nama_usaha' => $namaUsaha,
            'slug' => Str::slug($namaUsaha),
            'pemilik' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'telepon' => $this->faker->phoneNumber(),
            'deskripsi' => $this->faker->paragraph(),
            'thumbnail' => PlaceholderImage::generate($namaUsaha),
            'latitude' => -6.1 + $this->faker->randomFloat(7, 0, 2),
            'longitude' => 106.7 + $this->faker->randomFloat(7, 0, 2),
        ];
    }
}
