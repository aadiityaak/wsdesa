<?php

namespace App\Console\Commands;

use App\Helpers\PlaceholderImage;
use App\Models\DevelopmentImage;
use App\Models\Download;
use App\Models\GalleryImage;
use App\Models\Post;
use App\Models\Slider;
use App\Models\UmkmImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GeneratePlaceholderImages extends Command
{
    protected $signature = 'app:generate-placeholder-images';

    protected $description = 'Generate placeholder image files for all existing records with missing file paths';

    public function handle(): int
    {
        $count = 0;

        foreach (Slider::all() as $slider) {
            if ($slider->image && ! Storage::disk('public')->exists($slider->image)) {
                $slider->update(['image' => PlaceholderImage::generate($slider->judul)]);
                $count++;
            }
        }

        foreach (GalleryImage::all() as $img) {
            if (! Storage::disk('public')->exists($img->file)) {
                $img->update(['file' => PlaceholderImage::generate($img->judul ?? 'Gallery')]);
                $count++;
            }
        }

        foreach (UmkmImage::all() as $img) {
            if (! Storage::disk('public')->exists($img->file)) {
                $img->update(['file' => PlaceholderImage::generate('UMKM')]);
                $count++;
            }
        }

        foreach (DevelopmentImage::all() as $img) {
            if (! Storage::disk('public')->exists($img->file)) {
                $img->update(['file' => PlaceholderImage::generate($img->keterangan ?? 'Development')]);
                $count++;
            }
        }

        foreach (Download::all() as $d) {
            if (! Storage::disk('public')->exists($d->file)) {
                $d->update(['file' => PlaceholderImage::generatePdf($d->nama)]);
                $count++;
            }
        }

        foreach (Post::all() as $post) {
            if (! $post->thumbnail || ! Storage::disk('public')->exists($post->thumbnail)) {
                $post->update(['thumbnail' => PlaceholderImage::generate($post->judul)]);
                $count++;
            }
        }

        $this->info("Generated {$count} placeholder files.");

        return static::SUCCESS;
    }
}
